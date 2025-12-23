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

            $cacheKey = 'civic_lookup_' . Str::slug($formattedAddress);

            /**
             * We use Cache::tags(['jurisdictions']) so that this entire result
             * can be cleared whenever a Jurisdiction model is updated.
             */
            $data = Cache::tags(['jurisdictions'])->remember($cacheKey, now()->addDays(7), function () use ($formattedAddress, $address) {
                $response = Http::get('https://www.googleapis.com/civicinfo/v2/divisionsByAddress', [
                    'address' => $formattedAddress,
                    'key' => config('services.google_maps.api_key'),
                ]);

                if ($response->successful()) {
                    $civicData = $response->json();
                    $hasPlace = collect(array_keys($civicData['divisions'] ?? []))
                        ->contains(fn($k) => str_contains($k, 'place'));

                    $city = $hasPlace ? $address['locality'] : 'Unincorporated';

                    // We fetch the model inside the cache closure so the
                    // entire object is stored in Redis.
                    $model = Jurisdiction::where('name', $city)->first();

                    return [
                        'city' => $city,
                        'address_label' => $hasPlace ? "City of {$city}" : "Unincorporated LA County",
                        'jurisdiction' => $model
                    ];
                }
                return null;
            });

            if ($data) {
                $this->city = $data['city'];
                $this->address = $data['address_label'];
                $this->jurisdiction = $data['jurisdiction'];
            }

        } catch (\Exception $e) {
            // Log the error for debugging on Forge
            logger()->error('Search Error: ' . $e->getMessage());
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
