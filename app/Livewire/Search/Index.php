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
    public $validationResult;
    public $isProcessing = false;

    #[On('address-updated')]
    public function addressSelected(array $address): void
    {
        $formattedAddress = $this->formatAddress($address);

        $response = $this->fetchCivicInfo($formattedAddress);

        if ($response->successful()) {
            $this->processCivicInfoResponse($response->json(), $address['locality']);
        } else {
            $this->dispatch('error-processing');
        }
    }

        private function formatAddress(array $address): string
    {
        return sprintf(
            '%s, %s, %s %s, %s',
            $address['location'],
            $address['locality'],
            $address['locality'],
            $address['postal_code'],
            $address['country']
        );
    }

    private function fetchCivicInfo(string $formattedAddress)
    {
        $url = 'https://www.googleapis.com/civicinfo/v2/divisionsByAddress';

        return Http::get($url, [
            'address' => $formattedAddress,
            'key' => config('services.google_maps.api_key'),
        ]);
    }
    private function processCivicInfoResponse(array $data, string $locality): void
    {
        $hasPlaceKey = collect(array_keys($data['divisions']))
            ->contains(fn($key) => Str::contains($key, 'place'));

        if ($hasPlaceKey) {
            $this->address = "City of $locality";
            $this->city = $locality;
        } else {
            $this->address = 'Unincorporated LA County';
            $this->city = 'Unincorporated';
        }
        $this->jurisdiction = Jurisdiction::where('name',$this->city)->first();
    }
    public function render()
    {
        return view('livewire.search.index', [
            'google_maps_api_key' => config('services.google_maps.api_key'),
        ]);
    }
}
