<?php
use App\Models\User;
use App\Models\Wallet;

$user = User::where('user_type', 'landlord')->first();
if ($user) {
    $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
    $wallet->balance = 15000;
    $wallet->save();
    echo "Wallet seeded for " . $user->email . "\n";
}
