<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
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
        $this->isProcessing = true;

        if (is_null($address)) {
            $this->reset(['address', 'city', 'jurisdiction', 'isProcessing']);
            return;
        }

        try {
            $formattedAddress = sprintf('%s, %s, %s %s',
                $address['location'], $address['locality'], $address['state'], $address['postal_code']
            );

            // 1. Create a unique cache key based on the formatted address
            $cacheKey = 'civic_jurisdiction_' . Str::slug($formattedAddress);

            // 2. Wrap the logic in Cache::remember
            $cachedData = Cache::remember($cacheKey, now()->addDays(7), function () use ($formattedAddress, $address) {
                $response = Http::get('https://www.googleapis.com/civicinfo/v2/divisionsByAddress', [
                    'address' => $formattedAddress,
                    'key' => config('services.google_maps.api_key'),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $hasPlace = collect(array_keys($data['divisions'] ?? []))
                        ->contains(fn($k) => str_contains($k, 'place'));

                    $city = $hasPlace ? $address['locality'] : 'Unincorporated';

                    return [
                        'city' => $city,
                        'address_label' => $hasPlace ? "City of {$city}" : "Unincorporated LA County",
                    ];
                }
                return null;
            });

            if ($cachedData) {
                $this->city = $cachedData['city'];
                $this->address = $cachedData['address_label'];

                // 3. Cache the DB lookup for the Jurisdiction model specifically
                $this->jurisdiction = Cache::remember("jurisdiction_model_{$this->city}", now()->addDay(), function () {
                    return Jurisdiction::where('name', $this->city)->first();
                });
            }

        } catch (\Exception $e) {
            $this->dispatch('error-processing');
        }

        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.search.index', [
            'google_maps_api_key' => config('services.google_maps.api_key'),
        ]);
    }
}
