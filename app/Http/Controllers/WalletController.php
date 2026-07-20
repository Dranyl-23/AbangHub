<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\PayoutRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ensure landlord or admin
        if ($user->user_type === 'tenant') {
            abort(403);
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Force 15000 balance for demo if it's 0
        if ($wallet->balance == 0) {
            $wallet->balance = 15000;
            $wallet->save();
        }

        // Fetch pending payout requests
        $payoutRequests = PayoutRequest::where('user_id', $user->id)
            ->latest()
            ->get();
            
        // Calculate total earnings vs withdrawn
        // For demo, we'll just show the total completed transactions
        $totalEarnings = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        return view('wallet.index', compact('wallet', 'payoutRequests', 'totalEarnings'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'method' => 'required|in:gcash,maya,bank',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        // Deduct from wallet
        $wallet->balance -= $request->amount;
        $wallet->save();

        // Create Payout Request
        PayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully! Your funds will be processed shortly.');
    }
}
