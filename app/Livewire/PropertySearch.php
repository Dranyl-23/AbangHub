<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;

class PropertySearch extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $bedrooms = '';
    public $min_price = '';
    public $max_price = '';

    protected $queryString = ['search', 'type', 'bedrooms', 'min_price', 'max_price'];

    public function updating($property)
    {
        if (in_array($property, ['search', 'type', 'bedrooms', 'min_price', 'max_price'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Property::with('images')->where('status', 'available');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('barangay', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->type)) {
            $query->where('property_type', $this->type);
        }

        if (!empty($this->bedrooms)) {
            if ($this->bedrooms == '4+') {
                $query->where('bedrooms', '>=', 4);
            } else {
                $query->where('bedrooms', $this->bedrooms);
            }
        }

        if (!empty($this->min_price)) {
            $query->where('monthly_rent', '>=', $this->min_price);
        }

        if (!empty($this->max_price)) {
            $query->where('monthly_rent', '<=', $this->max_price);
        }

        $properties = $query->latest()->paginate(9);

        // Prepare data for map (extract only what we need to keep payload small)
        $mapData = $properties->getCollection()->map(function($prop) {
            return [
                'id' => $prop->id,
                'title' => $prop->title,
                'monthly_rent' => $prop->monthly_rent,
                'latitude' => $prop->latitude,
                'longitude' => $prop->longitude,
                'url' => route('properties.show', $prop)
            ];
        })->toArray();

        // Dispatch browser event to update map markers
        $this->dispatch('update-map-markers', markers: $mapData);

        return view('livewire.property-search', [
            'properties' => $properties,
            'initialMapData' => json_encode($mapData)
        ]);
    }
}
