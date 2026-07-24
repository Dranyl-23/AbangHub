<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get the user's wallet balance.
     */
    public function balance(Request $request)
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['balance' => 0]
        );

        return response()->json([
            'balance' => $wallet->balance
        ]);
    }

    /**
     * Get the user's transaction history.
     */
    public function transactions(Request $request)
    {
        $transactions = Transaction::with(['property', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return TransactionResource::collection($transactions);
    }
}
