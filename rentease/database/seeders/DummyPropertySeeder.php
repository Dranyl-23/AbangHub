<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyImage;

class DummyPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create an owner user
        $owner = User::firstOrCreate(
            ['email' => 'landlord@rentease.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Landlord',
                'phone_number' => '09123456789',
                'password' => bcrypt('password123'),
                'is_verified' => true,
            ]
        );

        $dummyProperties = [
            [
                'title' => 'Cozy Studio Apartment in Poblacion',
                'property_type' => 'apartment',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'monthly_rent' => 5000.00,
                'address' => 'Rizal Avenue, Poblacion',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'A very cozy studio apartment perfect for students or single professionals. Near the market and terminals.',
                'furnishing_status' => 'semi_furnished',
                'image1' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1080&auto=format&fit=crop',
                'image2' => 'https://images.unsplash.com/photo-1502672260266-1c1e5240980c?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Spacious 2BR House for Family',
                'property_type' => 'house',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'monthly_rent' => 8500.00,
                'address' => 'San Jose Village',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'A spacious house inside a quiet subdivision. Good for small families. With parking space.',
                'furnishing_status' => 'unfurnished',
                'image1' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1080&auto=format&fit=crop',
                'image2' => 'https://images.unsplash.com/photo-1583608205776-bfd35f0d9f83?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Modern Boarding House - Bedspace',
                'property_type' => 'boarding_house',
                'bedrooms' => 1,
                'bathrooms' => 2,
                'monthly_rent' => 1500.00,
                'address' => 'Tres de Mayo',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Affordable bedspace for male boarders. Free water and wifi. Electricity is sub-metered.',
                'furnishing_status' => 'furnished',
                'image1' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=1080&auto=format&fit=crop',
                'image2' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Premium Condo-Style Unit',
                'property_type' => 'condo',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'monthly_rent' => 12000.00,
                'address' => 'Aplaya Boulevard',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Experience luxury living in this condo-style unit with sea breeze. Fully airconditioned with premium furniture.',
                'furnishing_status' => 'furnished',
                'image1' => 'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?q=80&w=1080&auto=format&fit=crop',
                'image2' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?q=80&w=1080&auto=format&fit=crop',
            ],
            [
                'title' => 'Affordable Room for Rent',
                'property_type' => 'room',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'monthly_rent' => 3000.00,
                'address' => 'Zone 3, Barangay Zone 3',
                'city' => 'Digos City',
                'province' => 'Davao del Sur',
                'description' => 'Simple room for rent. Own CR. Just one ride to downtown.',
                'furnishing_status' => 'unfurnished',
                'image1' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?q=80&w=1080&auto=format&fit=crop',
                'image2' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?q=80&w=1080&auto=format&fit=crop',
            ],
        ];

        foreach ($dummyProperties as $propData) {
            $image1 = $propData['image1'];
            $image2 = $propData['image2'];
            unset($propData['image1'], $propData['image2']);

            $property = Property::create(array_merge($propData, [
                'owner_id' => $owner->id,
                'status' => 'available'
            ]));

            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $image1,
                'is_primary' => true,
                'display_order' => 1
            ]);

            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $image2,
                'is_primary' => false,
                'display_order' => 2
            ]);
        }
    }
}
