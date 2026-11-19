/**
 * Address search box. Suggestions come from a JSON endpoint and place details from the
 * Livewire component, both on the server, so no Google API key is ever sent to the browser.
 */
export default function addressAutocomplete(suggestionsUrl) {
    return {
        suggestions: [],
        showInput: true,
        loading: false,
        latestQuery: '',

        async getSuggestions(query) {
            this.latestQuery = query;

            if (query.trim().length < 3) {
                this.suggestions = [];
                return;
            }

            try {
                const response = await fetch(`${suggestionsUrl}?query=${encodeURIComponent(query)}`, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                const suggestions = response.ok ? await response.json() : [];

                // Ignore responses for queries the user has already typed past.
                if (query === this.latestQuery) {
                    this.suggestions = Array.isArray(suggestions) ? suggestions : [];
                }
            } catch (e) {
                this.suggestions = [];
            }
        },

        async selectPlace(suggestion) {
            this.suggestions = [];
            this.showInput = false; // Swap UI immediately
            this.loading = true;

            try {
                await this.$wire.selectPlace(suggestion.id);
            } catch (e) {
                this.resetUI();
            } finally {
                this.loading = false;
            }
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

            // Focus the input again so the user can start typing immediately
            this.$nextTick(() => {
                if (input) input.focus();
            });
        }
    }
}
