/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './vendor/livewire/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"SofiaPro-Bold"', 'sans-serif'], // your Typekit font
      },
      fontSize: {
        jurisdiction: '40px', // use text-jurisdiction
      },
      lineHeight: {
        jurisdiction: '36px', // use leading-jurisdiction
      },
      letterSpacing: {
        jurisdiction: '-0.05em', // use tracking-jurisdiction
      },
      colors: {
        'jurisdiction-text': '#15121b', // use text-jurisdiction-text
      },
    },
  },
  plugins: [],
};
