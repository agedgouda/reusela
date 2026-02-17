<div x-data="addressAutocomplete()"
     x-init="showInput = {{ $address ? 'false' : 'true' }}; loading = false;"
     @@error-processing.window="resetUI();"
     @@jurisdiction-not-found.window="resetUI();">

    <script async src="https://maps.googleapis.com/maps/api/js?key={{ $google_maps_api_key }}&libraries=places&v=weekly&loading=async"></script>

    <div class="flex flex-col min-h-[250px] relative">

        {{-- SECTION 1: SEARCH INPUT --}}
        <div x-show="showInput" x-transition.opacity.duration.300ms wire:ignore>
            <div class="p-5 bg-gray-100 w-full rounded-lg shadow-inner">
                <div class="relative" @click.away="suggestions = []">
                    <input
                        id="location-input"
                        type="text"
                        class="text-lg py-3 px-4 block w-full bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Enter Address"
                        @input.debounce.300ms="getSuggestions($event.target.value)"
                        autocomplete="off">

                    <div x-show="suggestions.length > 0" class="absolute z-50 w-full bg-white border border-gray-200 rounded-b-lg shadow-xl mt-1 overflow-hidden">
                        <template x-for="(s, index) in suggestions" :key="index">
                            <div @click="selectPlace(s)" class="p-3 hover:bg-blue-50 cursor-pointer border-b last:border-0 text-sm" x-text="s.label"></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: SMOOTH LOADING & RESULTS --}}
        <div x-show="!showInput" x-cloak x-transition.opacity.duration.400ms>
            <div class="w-full">

                {{-- THE SPINNER (Managed by Alpine for zero-latency appearance) --}}
                <div x-show="loading || @js($isProcessing)" class="py-12 flex flex-col items-center justify-center transition-all">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-blue-600 mb-4"></div>
                    <p class="text-gray-500 font-medium animate-pulse">Verifying Jurisdiction...</p>
                </div>

                {{-- THE RESULTS (Only show when loading is totally finished) --}}
                <div x-show="!loading && !@js($isProcessing)" x-transition.opacity.duration.500ms>
                    @if($jurisdiction)
                        <livewire:jurisdiction.show :jurisdiction="$jurisdiction" :editable="false" :wire:key="'jur-'.$jurisdiction->id" />
                    @elseif($address)
                        <div class="p-10 text-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p class="text-gray-600 mb-4">Jurisdiction details for <strong>{{ $city }}</strong> are not available.</p>
                            <flux:button @click="resetUI" variant="ghost" color="gray">Try Another Address</flux:button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
