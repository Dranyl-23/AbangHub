<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->user_type === 'landlord') {
            $propertyIds = Property::where('owner_id', $user->id)->pluck('id');
            $transactions = Transaction::with(['user', 'property'])
                ->whereIn('property_id', $propertyIds)
                ->latest()
                ->paginate(15);
                
            $totalReceived = Transaction::whereIn('property_id', $propertyIds)->where('status', 'completed')->sum('amount');
            $pendingAmount = Transaction::whereIn('property_id', $propertyIds)->where('status', 'pending')->sum('amount');
            
        } elseif ($user->user_type === 'admin') {
            $transactions = Transaction::with(['user', 'property'])->latest()->paginate(15);
            $totalReceived = Transaction::where('status', 'completed')->sum('amount');
            $pendingAmount = Transaction::where('status', 'pending')->sum('amount');
        } else {
            // Tenant
            $transactions = Transaction::with(['property'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
                
            $totalReceived = Transaction::where('user_id', $user->id)->where('status', 'completed')->sum('amount');
            $pendingAmount = Transaction::where('user_id', $user->id)->where('status', 'pending')->sum('amount');
        }

        return view('transactions.index', compact('transactions', 'totalReceived', 'pendingAmount'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $user = auth()->user();
        
        // Only landlords can update status of their property's transactions (or admins)
        if ($user->user_type === 'landlord') {
            $isOwner = Property::where('id', $transaction->property_id)->where('owner_id', $user->id)->exists();
            if (!$isOwner) {
                return back()->with('error', 'Unauthorized action.');
            }
        } elseif ($user->user_type !== 'admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:completed,failed'
        ]);

        $transaction->update(['status' => $request->status]);

        $message = $request->status === 'completed' ? 'Transaction approved successfully.' : 'Transaction marked as failed.';
        return back()->with('success', $message);
    }

    public function export(): StreamedResponse
    {
        $user = auth()->user();
        $query = Transaction::with(['user', 'property'])->latest();
        
        if ($user->user_type === 'landlord') {
            $propertyIds = Property::where('owner_id', $user->id)->pluck('id');
            $query->whereIn('property_id', $propertyIds);
        } elseif ($user->user_type === 'tenant') {
            $query->where('user_id', $user->id);
        }

        $transactions = $query->get();

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Property', 'Type', 'Amount', 'Status', 'Reference', 'Date']);
            
            foreach ($transactions as $txn) {
                fputcsv($handle, [
                    $txn->id,
                    $txn->user->full_name ?? $txn->user->username,
                    $txn->property->title ?? 'N/A',
                    ucfirst($txn->type),
                    number_format($txn->amount, 2),
                    ucfirst($txn->status),
                    $txn->reference_number ?? 'N/A',
                    $txn->created_at->format('Y-m-d H:i'),
                ]);
            }
            
            fclose($handle);
        }, 'transactions_' . now()->format('Y-m-d') . '.csv');
    }
}
