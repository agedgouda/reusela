"use strict";
// Import the Extended Component Library for Google Maps
import { APILoader

 } from 'https://unpkg.com/@googlemaps/extended-component-library@0.6';

const CONFIGURATION = {
  "mapsApiKey": "YOUR_API_KEY",
  "capabilities": {
    "addressAutocompleteControl": true
  }
};

// Address component types that should use their short names
const SHORT_NAME_ADDRESS_COMPONENT_TYPES = new Set([
  'street_number',
  'administrative_area_level_1',
  'postal_code'
]);

// Address components to extract for the form
const ADDRESS_COMPONENT_TYPES_IN_FORM = [
  'location',
  'locality',
  'administrative_area_level_1',
  'postal_code',
  'country',
  'administrative_area_level_2' // County level, e.g., "Los Angeles County"
];

function getFormInputElement(componentType) {
  return document.getElementById(`${componentType}-input`);
}

function fillInAddress(place) {
  function getComponentName(componentType) {
    for (const component of place.address_components || []) {
      if (component.types.includes(componentType)) {
        return SHORT_NAME_ADDRESS_COMPONENT_TYPES.has(componentType)
          ? component.short_name
          : component.long_name;
      }
    }
    return '';
  }

  function getComponentText(componentType) {
    if (componentType === 'location') {
      return `${getComponentName('street_number')} ${getComponentName('route')}`;
    } else {
      return getComponentName(componentType);
    }
  }

  const address = {
    location: getComponentText('location'),
    locality: getComponentText('locality'),
    state: getComponentText('administrative_area_level_1'),
    postal_code: getComponentText('postal_code'),
    country: getComponentText('country'),
    administrativeAreaLevel2: getComponentText('administrative_area_level_2') // Expected to fetch county
  };

  Livewire.dispatch('address-updated', { 'address': address });
}

async function initAutocomplete() {
  const { Autocomplete } = await APILoader.importLibrary('places');

  const autocomplete = new Autocomplete(getFormInputElement('location'), {
    fields: ['address_components', 'geometry', 'name'],
    types: ['address'],
    componentRestrictions: { country: 'us' },
    strictBounds: true
  });

  const losAngelesBounds = new google.maps.LatLngBounds(
    { lat: 33.7037, lng: -118.9448 },  // Southwest corner
    { lat: 34.8233, lng: -117.6458 }   // Northeast corner
  );

  autocomplete.setBounds(losAngelesBounds);

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      window.alert(`No details available for input: '${place.name}'`);
      return;
    }

    const countyComponent = place.address_components.find(
      component => component.types.includes('administrative_area_level_2')
    );

    if (countyComponent && countyComponent.long_name === 'Los Angeles County') {
      fillInAddress(place);
    } else {
      window.alert('Please select an address within Los Angeles County.');
      getFormInputElement('location').value = '';
    }
  });
}

initAutocomplete();
