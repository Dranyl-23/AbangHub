<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Transaction;

$user = User::where('user_type', 'landlord')->first();
$property = Property::where('owner_id', $user->id)->first();

if($user && $property) {
    Transaction::insert([
        ['user_id' => $user->id, 'property_id' => $property->id, 'type' => 'rent', 'amount' => 15000, 'status' => 'completed', 'reference_number' => 'REF-001', 'created_at' => now()->subMonths(2), 'updated_at' => now()->subMonths(2)],
        ['user_id' => $user->id, 'property_id' => $property->id, 'type' => 'rent', 'amount' => 15000, 'status' => 'completed', 'reference_number' => 'REF-002', 'created_at' => now()->subMonths(1), 'updated_at' => now()->subMonths(1)],
        ['user_id' => $user->id, 'property_id' => $property->id, 'type' => 'rent', 'amount' => 15000, 'status' => 'completed', 'reference_number' => 'REF-003', 'created_at' => now(), 'updated_at' => now()],
        ['user_id' => $user->id, 'property_id' => $property->id, 'type' => 'deposit', 'amount' => 5000, 'status' => 'completed', 'reference_number' => 'REF-004', 'created_at' => now(), 'updated_at' => now()],
        ['user_id' => $user->id, 'property_id' => $property->id, 'type' => 'rent', 'amount' => 15000, 'status' => 'pending', 'reference_number' => 'REF-005', 'created_at' => now(), 'updated_at' => now()]
    ]);
    echo "Seeded successfully\n";
} else {
    echo "No user or property found\n";
}
