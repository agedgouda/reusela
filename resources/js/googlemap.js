"use strict";
import { APILoader } from 'https://unpkg.com/@googlemaps/extended-component-library@0.6';

async function initAutocomplete() {
    const container = document.getElementById('autocomplete-container');
    if (!container) return;

    // 1. Load the New Places library
    const { PlaceAutocompleteElement } = await APILoader.importLibrary('places');

    // 2. Create the element programmatically (No 'is' attribute needed)
    const autocompleteElement = new PlaceAutocompleteElement();

    // 3. Style it to match your UI
    // We target the internal input of the web component via part or direct styles
    autocompleteElement.classList.add('w-full');
    autocompleteElement.style.setProperty('--gmpx-font-family', 'inherit');
    autocompleteElement.style.setProperty('--gmpx-font-size', '1.125rem');
    autocompleteElement.style.setProperty('--gmpx-border-radius', '0.5rem');

    // 4. Set restrictions (LA County)
    autocompleteElement.locationRestriction = {
        north: 34.8233,
        south: 33.7037,
        east: -117.6458,
        west: -118.9448,
    };

    // 5. Add the Selection Listener
    autocompleteElement.addEventListener('gmp-placeselect', async (event) => {
        const place = event.place;
        if (!place) return;

        await place.fetchFields({
            fields: ['addressComponents', 'location', 'displayName']
        });

        const components = place.addressComponents;
        const county = components.find(c => c.types.includes('administrative_area_level_2'));

        if (county && county.longText === 'Los Angeles County') {
            const getComp = (type, useShort = false) => {
                const c = components.find(comp => comp.types.includes(type));
                return c ? (useShort ? c.shortText : c.longText) : '';
            };

            const addressData = {
                location: `${getComp('street_number')} ${getComp('route')}`,
                locality: getComp('locality'),
                state: getComp('administrative_area_level_1', true),
                postal_code: getComp('postal_code'),
                country: getComp('country'),
                administrativeAreaLevel2: county.longText
            };

            Livewire.dispatch('address-updated', { 'address': addressData });
        } else {
            alert('Please select an address within Los Angeles County.');
            autocompleteElement.value = '';
        }
    });

    // 6. Inject it into the page
    container.innerHTML = ''; // Clear previous if any
    container.appendChild(autocompleteElement);
}

// Run immediately and on Livewire navigation
initAutocomplete();
document.addEventListener('livewire:navigated', initAutocomplete);
