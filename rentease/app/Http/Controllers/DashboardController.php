<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Transaction;
use App\Models\Message;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->user_type === 'admin') {
            return $this->adminDashboard($user);
        } elseif ($user->user_type === 'landlord') {
            return $this->landlordDashboard($user);
        } else {
            return $this->tenantDashboard($user);
        }
    }

    private function tenantDashboard(User $user): View
    {
        $stats = [
            'activeLeases' => \App\Models\Lease::where('tenant_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'pendingApplications' => \App\Models\Application::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'totalPaid' => \App\Models\Invoice::whereHas('lease', function ($query) use ($user) {
                    $query->where('tenant_id', $user->id);
                })
                ->where('status', 'paid')
                ->sum('amount'),
            'unreadMessages' => Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];

        $recentActivity = Transaction::with('property')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recommendedProperties = Property::with('images')
            ->where('status', 'available')
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        // New features data
        $activeLease = \App\Models\Lease::with(['property.images', 'property.owner'])
            ->where('tenant_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        $upcomingBills = \App\Models\Invoice::with('lease.property')
            ->whereHas('lease', function ($query) use ($user) {
                $query->where('tenant_id', $user->id);
            })
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->get();
            
        $applications = \App\Models\Application::with('property.images')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
            
        $maintenanceRequests = \App\Models\MaintenanceRequest::with('property')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
            
        $savedProperties = $user->favorites()->with('images')->get();

        return view('dashboard.tenant', compact(
            'stats', 
            'recentActivity', 
            'recommendedProperties',
            'activeLease',
            'upcomingBills',
            'applications',
            'maintenanceRequests',
            'savedProperties'
        ));
    }

    private function landlordDashboard(User $user): View
    {
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        $stats = [
            'totalProperties' => count($propertyIds),
            'vacantUnits' => Property::where('owner_id', $user->id)->where('status', 'available')->count(),
            'totalRevenue' => Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'completed')
                ->sum('amount'),
            'monthlyRevenue' => Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'totalExpenses' => \App\Models\MaintenanceRequest::whereIn('property_id', $propertyIds)
                ->where('status', 'resolved')
                ->sum('cost'),
            'pendingPayments' => Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'pending')
                ->sum('amount'),
            'activeTenants' => Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'completed')
                ->distinct('user_id')
                ->count('user_id'),
            'unreadMessages' => Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];

        $stats['netIncome'] = $stats['totalRevenue'] - $stats['totalExpenses'];

        // 1. Pending Applications
        $pendingApplications = \App\Models\Application::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 2. Recent Unread Messages
        $recentMessages = Message::with('sender')
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(3)
            ->get();

        // 3. Ongoing Maintenance Requests
        $maintenanceRequests = \App\Models\MaintenanceRequest::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();

        // 3.5. Active Leases (Tenants)
        $activeLeases = \App\Models\Lease::with(['tenant.tenantReviewsReceived', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->get();

        // 4. Analytics Data (Past 6 Months)
        $chartData = [
            'labels' => [],
            'income' => [],
            'expenses' => [],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');

            // Income for this month (completed transactions)
            $income = Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $chartData['income'][] = $income;

            // Expenses for this month (resolved maintenance requests)
            $expenses = \App\Models\MaintenanceRequest::whereIn('property_id', $propertyIds)
                ->where('status', 'resolved')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->sum('cost');
            $chartData['expenses'][] = $expenses;
        }

        // 5. Occupancy Rate
        $totalProps = count($propertyIds);
        $rentedProps = Property::where('owner_id', $user->id)->where('status', 'rented')->count();
        $occupancyRate = $totalProps > 0 ? round(($rentedProps / $totalProps) * 100) : 0;
        $vacantRate = 100 - $occupancyRate;

        return view('dashboard.landlord', compact('stats', 'pendingApplications', 'recentMessages', 'maintenanceRequests', 'activeLeases', 'chartData', 'occupancyRate', 'vacantRate'));
    }

    private function adminDashboard(User $user): View
    {
        $stats = [
            'totalUsers' => \App\Models\User::count(),
            'totalProperties' => Property::count(),
            'totalTransactions' => Transaction::count(),
            'totalPlatformVolume' => Transaction::where('status', 'completed')->sum('amount'),
            // Assuming the platform takes a 5% fee on all completed transactions
            'platformRevenue' => Transaction::where('status', 'completed')->sum('amount') * 0.05,
            'activeLeases' => \App\Models\Lease::where('status', 'active')->count(),
            'pendingMaintenance' => \App\Models\MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        $pendingKyc = \App\Models\LandlordDocument::with('user')->where('status', 'pending')->latest()->get();

        return view('dashboard.admin', compact('stats', 'pendingKyc'));
    }
}

