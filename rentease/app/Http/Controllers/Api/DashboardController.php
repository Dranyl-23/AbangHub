<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\Application;
use App\Models\Lease;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard metrics for the landlord.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ownerId = $user->id;

        // 1. Available Properties Count
        $availableProperties = Property::where('owner_id', $ownerId)
            ->where('status', 'available')
            ->count();

        // 2. Total Income This Month
        // Get invoices belonging to landlord's properties that are PAID this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $totalIncome = Invoice::where('status', 'paid')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->whereNull('paid_at')->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
                  });
            })
            ->whereHas('lease.property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->sum('amount');

        // 3. Pending Applications
        $pendingApplications = Application::where('status', 'pending')
            ->whereHas('property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->count();

        // 4. Occupancy Rate
        $totalProperties = Property::where('owner_id', $ownerId)
            ->where('is_banned', false)
            ->count();
        $occupiedProperties = Property::where('owner_id', $ownerId)
            ->where('status', 'rented')
            ->where('is_banned', false)
            ->count();
        $occupancyRate = $totalProperties > 0 ? round(($occupiedProperties / $totalProperties) * 100) : 0;

        // 5. Expiring Leases (next 30 days)
        $thirtyDaysFromNow = Carbon::now()->addDays(30);
        $expiringLeases = Lease::with(['tenant', 'property'])
            ->whereHas('property', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $thirtyDaysFromNow)
            ->where('end_date', '>=', Carbon::now())
            ->orderBy('end_date', 'asc')
            ->get()
            ->map(function ($lease) {
                return [
                    'id' => $lease->id,
                    'tenant_id' => $lease->tenant->id,
                    'tenant_name' => $lease->tenant?->full_name ?? 'N/A',
                    'property_id' => $lease->property->id,
                    'property_title' => $lease->property->title,
                    'end_date' => $lease->end_date->format('Y-m-d'),
                    'days_left' => Carbon::now()->diffInDays($lease->end_date, false)
                ];
            });

        // 6. Expenses and Net Income
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $totalExpenses = Expense::where('owner_id', $ownerId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $netIncome = $totalIncome - $totalExpenses;

        return response()->json([
            'data' => [
                'available_properties' => $availableProperties,
                'total_income_this_month' => (float) $totalIncome,
                'total_expenses_this_month' => (float) $totalExpenses,
                'net_income' => (float) $netIncome,
                'pending_applications' => $pendingApplications,
                'occupancy_rate' => $occupancyRate,
                'expiring_leases' => $expiringLeases
            ]
        ]);
    }
}
