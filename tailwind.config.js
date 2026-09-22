import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans:    ['Inter', ...defaultTheme.fontFamily.sans],
        display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
        mono:    ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
      },
      colors: {
        primary: {
          50:  '#FFF5F5',
          100: '#FEE2E2',
          200: '#FECACA',
          300: '#FCA5A5',
          400: '#F87171',
          500: '#A13431',
          600: '#8C2826',
          700: '#731E1C',
          800: '#5C1210',
          900: '#380E0D',
        },
        maroon: {
          50:  '#FFF5F5',
          100: '#FEE2E2',
          200: '#FECACA',
          300: '#FCA5A5',
          400: '#F87171',
          500: '#A13431',
          600: '#8C2826',
          700: '#731E1C',
          800: '#5C1210',
          900: '#380E0D',
        },
        gold: {
          50:  '#FEFDF8',
          100: '#FEF9EE',
          200: '#FDF1D6',
          300: '#FBE4B3',
          400: '#F5C870',
          500: '#C8973A',
          600: '#B48228',
          700: '#94661D',
          800: '#774F1B',
          900: '#523412',
        },
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
      },
      boxShadow: {
        'card':      '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
        'card-md':   '0 4px 16px rgba(0,0,0,0.08)',
        'card-lg':   '0 8px 30px rgba(0,0,0,0.10)',
        'blue-glow': '0 4px 20px rgba(59,130,246,0.25)',
      },
      animation: {
        'fade-up':     'fadeSlideUp 250ms ease forwards',
        'slide-right': 'slideInRight 300ms ease forwards',
        'modal-in':    'modalIn 200ms ease forwards',
        'pulse-slow':  'pulse 3s ease-in-out infinite',
      },
      keyframes: {
        fadeSlideUp: {
          'from': { opacity: '0', transform: 'translateY(12px)' },
          'to':   { opacity: '1', transform: 'translateY(0)' },
        },
        slideInRight: {
          'from': { opacity: '0', transform: 'translateX(100%)' },
          'to':   { opacity: '1', transform: 'translateX(0)' },
        },
        modalIn: {
          'from': { opacity: '0', transform: 'scale(0.95) translateY(-8px)' },
          'to':   { opacity: '1', transform: 'scale(1) translateY(0)' },
        },
      },
    },
  },
  plugins: [forms, typography],
}
