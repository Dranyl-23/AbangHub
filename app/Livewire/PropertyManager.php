<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PropertyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus(int $propertyId, string $status)
    {
        $property = Property::where('id', $propertyId)->where('owner_id', Auth::id())->first();
        
        if ($property) {
            $property->update(['status' => $status]);
            session()->flash('success', 'Property status updated to ' . ucfirst($status) . '.');
            
            // If marked as rented, we should probably update pending applications too, 
            // but we'll keep it manual for now based on the open question.
        }
    }

    public function deleteProperty(int $propertyId)
    {
        $property = Property::where('id', $propertyId)->where('owner_id', Auth::id())->first();
        
        if ($property) {
            // Real deletion logic requires deleting images too, let's keep it simple or call a controller.
            // But we can do it here.
            foreach ($property->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }
            $property->delete();
            session()->flash('success', 'Property deleted successfully.');
        }
    }

    public function render()
    {
        $query = Property::where('owner_id', Auth::id())
            ->with('images')
            ->withCount(['applications' => function($q) {
                $q->where('status', 'pending');
            }])
            ->withCount(['transactions as active_tenants_count' => function($q) {
                $q->where('status', 'completed')->distinct('user_id');
            }]);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $properties = $query->latest()->paginate(10);

        return view('livewire.property-manager', [
            'properties' => $properties
        ]);
    }
}
