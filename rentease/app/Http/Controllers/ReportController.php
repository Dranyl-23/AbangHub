<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Ensure only landlords or admins can see reports
        if ($user->user_type !== 'landlord' && $user->user_type !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        // Analytics Data
        $totalRevenue = Transaction::whereIn('property_id', $propertyIds)
            ->where('status', 'completed')
            ->sum('amount');

        $activeLeases = Transaction::whereIn('property_id', $propertyIds)
            ->where('status', 'completed')
            ->distinct('property_id')
            ->count('property_id');

        $pendingPayments = Transaction::whereIn('property_id', $propertyIds)
            ->where('status', 'pending')
            ->sum('amount');

        // Monthly Revenue Data (Last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $amount = Transaction::whereIn('property_id', $propertyIds)
                ->where('status', 'completed')
                ->where('created_at', 'like', $month . '%')
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => now()->subMonths($i)->format('M Y'),
                'amount' => (float) $amount
            ];
        }

        $propertyBreakdown = Transaction::whereIn('property_id', $propertyIds)
            ->where('status', 'completed')
            ->select('property_id', DB::raw('SUM(amount) as total'))
            ->groupBy('property_id')
            ->with('property')
            ->get();

        return view('reports.index', compact('totalRevenue', 'activeLeases', 'pendingPayments', 'monthlyRevenue', 'propertyBreakdown'));
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');

        $data = Transaction::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Tenant', 'Property', 'Type', 'Amount', 'Status']);
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->created_at->format('Y-m-d'),
                    $row->user->full_name ?? $row->user->username,
                    $row->property->title ?? 'N/A',
                    ucfirst($row->type),
                    number_format($row->amount, 2),
                    ucfirst($row->status),
                ]);
            }
            fclose($handle);
        }, 'RentEase_Report_' . now()->format('Y-m-d') . '.csv');
    }
}
