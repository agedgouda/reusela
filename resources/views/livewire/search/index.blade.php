<div>
    <gmpx-api-loader key="{{ $google_maps_api_key }}" solution-channel="GMP_QB_addressselection_v3_cAB" wire:ignore>
    </gmpx-api-loader>
    <div class="flex">
        <x-action-message on='error-processing' class="mb-3">
            Unable to retrieve coordinates for the address.
        </x-action-message>

            @if(!$address)
                <div class="p-5 bg-gray-100 w-full">
                    @if($isProcessing)
                        <span>Processing...</span>
                    @else
                        <!-- Address input field when no address is selected -->
                        <x-text-input id="location-input" class="text-lg py-3 px-4 block w-full" placeholder="Enter Address" />
                    @endif
                </div>
            @else
                <!-- Display selected address and option to reset -->
                <div class="w-full text-lg font-semibold mb-2">
                    <div class="flex justify-end">
                        <flux:button onclick="location.reload();" variant="primary" color="blue">
                            Change Address
                        </flux:button>
                    </div>

                    <livewire:jurisdiction.show
                        :jurisdiction="$jurisdiction"
                        :editable="false"
                    />
                </div>

            @endif

    </div>
    <script type="module" src="{{ Vite::asset('resources/js/googlemap.js') }}"></script>
</div>
