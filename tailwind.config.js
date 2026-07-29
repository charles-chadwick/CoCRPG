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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary: Dark Purple
                primary: {
                    DEFAULT: 'oklch(0.558 0.200 302.32 / <alpha-value>)',
                    foreground: 'oklch(1 0 0 / <alpha-value>)',
                    50: 'oklch(0.977 0.014 308.3)',
                    100: 'oklch(0.946 0.033 307.28)',
                    200: 'oklch(0.900 0.067 308.07)',
                    300: 'oklch(0.824 0.120 306.38)',
                    400: 'oklch(0.714 0.168 304.79)',
                    500: 'oklch(0.627 0.195 303.90)',
                    600: 'oklch(0.558 0.200 302.32)',
                    700: 'oklch(0.496 0.181 302.54)',
                    800: 'oklch(0.438 0.148 303.07)',
                    900: 'oklch(0.381 0.116 304.41)',
                    950: 'oklch(0.269 0.075 306.71)',
                },
                // Secondary: Medium Lime Green (accent)
                secondary: {
                    DEFAULT: 'oklch(0.842 0.238 128.88 / <alpha-value>)',
                    foreground: 'oklch(0.151 0.004 49.82 / <alpha-value>)',
                    50: 'oklch(0.986 0.031 120.72)',
                    100: 'oklch(0.967 0.067 122.33)',
                    200: 'oklch(0.938 0.127 124.32)',
                    300: 'oklch(0.897 0.196 126.67)',
                    400: 'oklch(0.842 0.238 128.88)',
                    500: 'oklch(0.768 0.233 130.85)',
                    600: 'oklch(0.648 0.200 131.68)',
                    700: 'oklch(0.532 0.157 131.59)',
                    800: 'oklch(0.444 0.119 130.93)',
                    900: 'oklch(0.374 0.089 130.23)',
                    950: 'oklch(0.276 0.063 129.38)',
                },
                // Stone Grays
                darker: {
                    50: 'oklch(0.985 0.001 106.42)',
                    100: 'oklch(0.970 0.001 106.42)',
                    200: 'oklch(0.936 0.003 48.72)',
                    300: 'oklch(0.869 0.005 56.37)',
                    400: 'oklch(0.709 0.010 56.26)',
                    500: 'oklch(0.553 0.013 58.07)',
                    600: 'oklch(0.444 0.011 73.64)',
                    700: 'oklch(0.375 0.010 67.56)',
                    800: 'oklch(0.279 0.007 56.26)',
                    900: 'oklch(0.216 0.006 56.37)',
                    950: 'oklch(0.151 0.004 49.82)',
                },
                // Shadcn semantic colors
                background: 'oklch(0.151 0.004 49.82)',
                foreground: 'oklch(0.985 0.001 106.42)',
                card: {
                    DEFAULT: 'oklch(0.216 0.006 56.37)',
                    foreground: 'oklch(0.985 0.001 106.42)',
                },
                popover: {
                    DEFAULT: 'oklch(0.216 0.006 56.37)',
                    foreground: 'oklch(0.985 0.001 106.42)',
                },
                muted: {
                    DEFAULT: 'oklch(0.279 0.007 56.26)',
                    foreground: 'oklch(0.709 0.010 56.26)',
                },
                accent: {
                    DEFAULT: 'oklch(0.842 0.238 128.88 / <alpha-value>)',
                    foreground: 'oklch(0.151 0.004 49.82)',
                },
                destructive: {
                    DEFAULT: '#ef4444',
                },
                border: 'oklch(0.375 0.010 67.56)',
                input: 'oklch(0.375 0.010 67.56 / <alpha-value>)',
                ring: 'oklch(0.627 0.195 303.90 / <alpha-value>)',
            },
        },
    },

    plugins: [forms],
};
