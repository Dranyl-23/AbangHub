<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Application;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first tenant user
        $tenant = User::where('user_type', 'tenant')->first();
        if (!$tenant) {
            $this->command->info('No tenant found. Please run DatabaseSeeder first.');
            return;
        }

        $properties = Property::all();
        if ($properties->count() < 3) {
            $this->command->info('Not enough properties. Please run DatabaseSeeder first.');
            return;
        }

        // 1. Create a Lease for the tenant
        $leaseProperty = $properties->first();
        $lease = Lease::firstOrCreate(
            ['tenant_id' => $tenant->id, 'property_id' => $leaseProperty->id],
            [
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(10),
                'monthly_rent' => $leaseProperty->monthly_rent,
                'status' => 'active'
            ]
        );

        // 2. Create Invoices
        // One Paid invoice
        Invoice::firstOrCreate(
            ['lease_id' => $lease->id, 'description' => 'Rent for ' . Carbon::now()->subMonth()->format('F Y')],
            [
                'amount' => $lease->monthly_rent,
                'due_date' => Carbon::now()->subMonth()->startOfMonth(),
                'status' => 'paid'
            ]
        );
        // One Pending invoice (Upcoming Bill)
        Invoice::firstOrCreate(
            ['lease_id' => $lease->id, 'description' => 'Rent for ' . Carbon::now()->format('F Y')],
            [
                'amount' => $lease->monthly_rent,
                'due_date' => Carbon::now()->addDays(5),
                'status' => 'pending'
            ]
        );

        // 3. Create Favorites
        $favoriteProperties = $properties->skip(1)->take(3);
        foreach ($favoriteProperties as $prop) {
            $tenant->favorites()->syncWithoutDetaching([$prop->id]);
        }

        // 4. Create Applications
        Application::firstOrCreate(
            ['user_id' => $tenant->id, 'property_id' => $properties[1]->id],
            [
                'message' => 'I would love to rent this place!',
                'status' => 'pending'
            ]
        );
        Application::firstOrCreate(
            ['user_id' => $tenant->id, 'property_id' => $properties[2]->id],
            [
                'message' => 'Is this still available?',
                'status' => 'rejected'
            ]
        );
        // The one for the active lease
        Application::firstOrCreate(
            ['user_id' => $tenant->id, 'property_id' => $leaseProperty->id],
            [
                'message' => 'Applying for this property.',
                'status' => 'approved'
            ]
        );

        // 5. Create Maintenance Request
        MaintenanceRequest::firstOrCreate(
            ['user_id' => $tenant->id, 'property_id' => $leaseProperty->id, 'title' => 'Leaking Faucet'],
            [
                'description' => 'The kitchen faucet is leaking continuously.',
                'status' => 'pending'
            ]
        );

        $this->command->info('Dashboard fake data seeded for tenant: ' . $tenant->full_name);
    }
}
