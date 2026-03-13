import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                'talkheals': {
                    'gold': '#c9a96e',
                    'gold-l': '#e8d5b0',
                    'gold-p': '#f7f0e3',
                    'rose': '#c49a8a',
                    'rose-l': '#e8cfc6',
                    'rose-p': '#f8f0ed',
                    'sage': '#7a9e8e',
                    'sage-l': '#b4d0c4',
                    'sage-p': '#e8f3ef',
                    'deep': '#2a2420',
                    'mid': '#5c4a42',
                    'muted': '#9a8880',
                    'cream': '#faf7f2',
                    'sky': '#8bb0c4',
                    'sky-p': '#dceaf3',
                }
            },
            fontFamily: {
                sans: ['Jost', ...defaultTheme.fontFamily.sans],
                serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
