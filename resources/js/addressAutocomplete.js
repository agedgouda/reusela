export default function addressAutocomplete() {
    return {
        suggestions: [],
        service: null,
        sessionToken: null,
        showInput: true,

        async initAutocompleteService() {
            if (this.service) return;
            try {
                if (typeof google === 'undefined') return;
                const { AutocompleteSessionToken, AutocompleteSuggestion } = await google.maps.importLibrary("places");
                this.service = AutocompleteSuggestion;
                this.sessionToken = new AutocompleteSessionToken();
            } catch (e) { console.error(e); }
        },

        async getSuggestions(query) {
            if (query.length < 3) { this.suggestions = []; return; }
            await this.initAutocompleteService();
            if (!this.sessionToken) {
                const { AutocompleteSessionToken } = await google.maps.importLibrary("places");
                this.sessionToken = new AutocompleteSessionToken();
            }
            try {
                const { suggestions } = await this.service.fetchAutocompleteSuggestions({
                    input: query,
                    sessionToken: this.sessionToken,
                    locationRestriction: { north: 34.8233, south: 33.7037, east: -117.6458, west: -118.9448 }
                });
                this.suggestions = suggestions.map(s => ({
                    id: s.placePrediction.placeId,
                    label: s.placePrediction.text.text,
                    raw: s.placePrediction
                }));
            } catch (e) { this.suggestions = []; }
        },

        async selectPlace(suggestion) {
            this.suggestions = [];
            this.showInput = false; // Swap UI immediately

            try {
                const place = suggestion.raw.toPlace();
                await place.fetchFields({ fields: ['addressComponents'] });
                const components = place.addressComponents;

                const getComp = (t, short = false) => {
                    const c = components.find(comp => comp.types.includes(t));
                    return c ? (short ? c.shortText : c.longText) : '';
                };

                const addressData = {
                    location: `${getComp('street_number')} ${getComp('route')}`,
                    locality: getComp('locality'),
                    state: getComp('administrative_area_level_1', true),
                    postal_code: getComp('postal_code'),
                };

                Livewire.dispatch('address-updated', { address: addressData });
            } catch (e) { this.resetUI(); }
        },

        resetUI() {
            this.showInput = true;
            this.loading = false;
            this.suggestions = [];

            // Manually clear the physical input field
            const input = document.getElementById('location-input');
            if (input) {
                input.value = '';
            }

            // Tell Livewire to reset the server-side state
            Livewire.dispatch('address-updated', { address: null });

            // Optional: Focus the input again so the user can start typing immediately
            this.$nextTick(() => {
                if (input) input.focus();
            });
        }
    }
}
