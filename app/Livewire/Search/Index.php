<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Jurisdiction;

class Index extends Component
{
    public $address;
    public $city;
    public $jurisdiction;
    public $isProcessing = false;

    #[On('address-updated')]
    public function addressSelected(?array $address): void
    {
        $this->isProcessing = true; // Start manually

        if (is_null($address)) {
            $this->reset(['address', 'city', 'jurisdiction', 'isProcessing']);
            return;
        }

        try {
            $formattedAddress = sprintf('%s, %s, %s %s',
                $address['location'], $address['locality'], $address['state'], $address['postal_code']
            );

            $response = Http::get('https://www.googleapis.com/civicinfo/v2/divisionsByAddress', [
                'address' => $formattedAddress,
                'key' => config('services.google_maps.api_key'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $hasPlace = collect(array_keys($data['divisions'] ?? []))
                    ->contains(fn($k) => str_contains($k, 'place'));

                $this->city = $hasPlace ? $address['locality'] : 'Unincorporated';
                $this->jurisdiction = Jurisdiction::where('name', $this->city)->first();
                $this->address = $hasPlace ? "City of {$this->city}" : "Unincorporated LA County";
            }
        } catch (\Exception $e) {
            $this->dispatch('error-processing');
        }

        $this->isProcessing = false; // Stop manually
    }

    public function render()
    {
        return view('livewire.search.index', [
            'google_maps_api_key' => config('services.google_maps.api_key'),
        ]);
    }
}
