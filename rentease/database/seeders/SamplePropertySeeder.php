<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamplePropertySeeder extends Seeder
{
    public function run(): void
    {
        $landlord = User::where('username', 'landlord1')->first();
        $tenant = User::where('username', 'tenant1')->first();

        if (!$landlord || !$tenant) {
            return;
        }

        $properties = [
            // Lapu-Lapu Ext
            [
                'title' => 'Cozy Studio Apartment',
                'property_type' => 'apartment',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 35.5,
                'monthly_rent' => 8500.00,
                'security_deposit' => 8500.00,
                'address' => 'Lapu-Lapu Ext, Zone 1',
                'barangay' => 'Lapu-Lapu Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'A cozy and modern studio apartment perfect for a single professional or couple.',
                'status' => 'available',
                'furnishing_status' => 'semi_furnished',
                'parking_spaces' => 1,
                'pet_policy' => 'negotiable',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Spacious Family House',
                'property_type' => 'house',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'floor_area' => 120.0,
                'monthly_rent' => 15000.00,
                'security_deposit' => 15000.00,
                'address' => 'Lapu-Lapu Ext, Zone 1',
                'barangay' => 'Lapu-Lapu Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Large family house with a small garden and secure parking.',
                'status' => 'available',
                'furnishing_status' => 'unfurnished',
                'parking_spaces' => 2,
                'pet_policy' => 'allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Budget Boarding House',
                'property_type' => 'boarding_house',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 15.0,
                'monthly_rent' => 3000.00,
                'security_deposit' => 3000.00,
                'address' => 'Lapu-Lapu Ext, Zone 1',
                'barangay' => 'Lapu-Lapu Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Affordable room for students near the highway.',
                'status' => 'available',
                'furnishing_status' => 'furnished',
                'parking_spaces' => 0,
                'pet_policy' => 'not_allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Modern Condo Unit',
                'property_type' => 'condo',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'floor_area' => 60.0,
                'monthly_rent' => 18000.00,
                'security_deposit' => 18000.00,
                'address' => 'Lapu-Lapu Ext, Zone 1',
                'barangay' => 'Lapu-Lapu Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'High-end condominium with complete amenities.',
                'status' => 'available',
                'furnishing_status' => 'furnished',
                'parking_spaces' => 1,
                'pet_policy' => 'allowed',
                'owner_id' => $landlord->id,
            ],

            // Luna Ext
            [
                'title' => 'Luna Ext Dormitory',
                'property_type' => 'boarding_house',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 12.0,
                'monthly_rent' => 2500.00,
                'security_deposit' => 2500.00,
                'address' => 'Luna Ext, Zone 3',
                'barangay' => 'Luna Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Clean and safe dormitory for female students.',
                'status' => 'rented',
                'furnishing_status' => 'furnished',
                'parking_spaces' => 0,
                'pet_policy' => 'not_allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Luna Ext Commercial Space',
                'property_type' => 'apartment',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'floor_area' => 45.0,
                'monthly_rent' => 12000.00,
                'security_deposit' => 12000.00,
                'address' => 'Luna Ext, Zone 3',
                'barangay' => 'Luna Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Ideal for small business owners who want to live near their shop.',
                'status' => 'available',
                'furnishing_status' => 'unfurnished',
                'parking_spaces' => 1,
                'pet_policy' => 'negotiable',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Family Apartment Unit',
                'property_type' => 'apartment',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'floor_area' => 85.0,
                'monthly_rent' => 14000.00,
                'security_deposit' => 14000.00,
                'address' => 'Luna Ext, Zone 3',
                'barangay' => 'Luna Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Spacious apartment for a growing family.',
                'status' => 'rented',
                'furnishing_status' => 'semi_furnished',
                'parking_spaces' => 1,
                'pet_policy' => 'allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Luna Ext Single Room',
                'property_type' => 'room',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 10.0,
                'monthly_rent' => 2000.00,
                'security_deposit' => 2000.00,
                'address' => 'Luna Ext, Zone 3',
                'barangay' => 'Luna Ext',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Basic room for rent. Common CR.',
                'status' => 'available',
                'furnishing_status' => 'unfurnished',
                'parking_spaces' => 0,
                'pet_policy' => 'not_allowed',
                'owner_id' => $landlord->id,
            ],

            // Strada Street
            [
                'title' => 'Strada Street Townhouse',
                'property_type' => 'house',
                'bedrooms' => 3,
                'bathrooms' => 3,
                'floor_area' => 150.0,
                'monthly_rent' => 25000.00,
                'security_deposit' => 25000.00,
                'address' => 'Strada Street, Aplaya',
                'barangay' => 'Strada Street',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Luxurious townhouse in a quiet neighborhood.',
                'status' => 'available',
                'furnishing_status' => 'furnished',
                'parking_spaces' => 2,
                'pet_policy' => 'allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Strada Street Apartment A',
                'property_type' => 'apartment',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'floor_area' => 50.0,
                'monthly_rent' => 10000.00,
                'security_deposit' => 10000.00,
                'address' => 'Strada Street, Aplaya',
                'barangay' => 'Strada Street',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Newly renovated apartment near the beach.',
                'status' => 'rented',
                'furnishing_status' => 'unfurnished',
                'parking_spaces' => 1,
                'pet_policy' => 'negotiable',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Strada Street Apartment B',
                'property_type' => 'apartment',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 30.0,
                'monthly_rent' => 7000.00,
                'security_deposit' => 7000.00,
                'address' => 'Strada Street, Aplaya',
                'barangay' => 'Strada Street',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Perfect for couples. Very accessible location.',
                'status' => 'available',
                'furnishing_status' => 'semi_furnished',
                'parking_spaces' => 0,
                'pet_policy' => 'not_allowed',
                'owner_id' => $landlord->id,
            ],
            [
                'title' => 'Strada Executive Room',
                'property_type' => 'boarding_house',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'floor_area' => 20.0,
                'monthly_rent' => 5000.00,
                'security_deposit' => 5000.00,
                'address' => 'Strada Street, Aplaya',
                'barangay' => 'Strada Street',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Airconditioned room with private bathroom.',
                'status' => 'available',
                'furnishing_status' => 'furnished',
                'parking_spaces' => 1,
                'pet_policy' => 'not_allowed',
                'owner_id' => $landlord->id,
            ]
        ];

        foreach ($properties as $propData) {
            // Generate random coordinate near Digos City center (6.7486, 125.3556)
            $propData['latitude'] = 6.7486 + (rand(-200, 200) / 10000);
            $propData['longitude'] = 125.3556 + (rand(-200, 200) / 10000);

            $property = Property::create($propData);

            $amenities = ['WiFi', 'Water Heater', 'Air Conditioning'];
            foreach (array_slice($amenities, 0, rand(1, 3)) as $amenity) {
                PropertyAmenity::create([
                    'property_id' => $property->id,
                    'amenity_name' => $amenity,
                ]);
            }

            if ($property->status === 'rented') {
                Transaction::create([
                    'user_id' => $tenant->id,
                    'property_id' => $property->id,
                    'type' => 'rental',
                    'amount' => $property->monthly_rent,
                    'status' => 'completed',
                    'reference_number' => 'REF-' . strtoupper(uniqid()),
                    'notes' => 'Initial month rent payment',
                ]);
            }
        }
    }
}
