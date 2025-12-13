<div>
    <gmpx-api-loader key="{{ $google_maps_api_key }}" solution-channel="GMP_QB_addressselection_v3_cAB" wire:ignore>
    </gmpx-api-loader>
    <div class="flex">
        <x-action-message on='error-processing' class="mb-3">
            Unable to retrieve coordinates for the address.
        </x-action-message>
        <div class="p-5 bg-gray-100 w-full">
            @if(!$address)
                @if($isProcessing)
                    <span>Processing...</span>
                @else
                    <!-- Address input field when no address is selected -->
                    <x-text-input id="location-input" class="block w-full" placeholder="Enter Address" />
                @endif
            @else
                <!-- Display selected address and option to reset -->
                <div class="text-lg flex justify-between items-center font-semibold mb-2">
                    <div>{{ $address }}</div>

                    <x-primary-button class="ml-auto w-fit" onclick="location.reload();">
                        Change Address
                    </x-primary-button>
                </div>
                <livewire:jurisdiction.show :jurisdiction='$jurisdiction' :editable="false" />
            @endif

        </div>

    </div>
    <script type="module" src="{{ Vite::asset('resources/js/googlemap.js') }}"></script>
</div>
