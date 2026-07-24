<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->id;

        // Get the active lease
        $activeLease = Lease::with(['property.primaryImage', 'property.owner'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();

        $data = [
            'active_lease' => $activeLease,
            'pending_invoices' => 0,
            'recent_maintenance' => [],
        ];

        if ($activeLease) {
            $data['pending_invoices'] = Invoice::where('lease_id', $activeLease->id)
                ->where('status', '!=', 'paid')
                ->count();

            $data['recent_maintenance'] = MaintenanceRequest::where('user_id', $tenantId)
                ->where('property_id', $activeLease->property_id)
                ->latest()
                ->take(3)
                ->get();
        }

        return response()->json(['data' => $data]);
    }
}
