/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#FF8C00',
          50: '#FFF5E6',
          100: '#FFE6CC',
          200: '#FFCC99',
          300: '#FFB366',
          400: '#FF9933',
          500: '#FF8C00',
          600: '#E67E00',
          700: '#CC7000',
          800: '#B36300',
          900: '#994D00',
          hover: '#FF9D1F',
          light: '#FFB84D',
        },
        accent: {
          DEFAULT: '#10B981',
          hover: '#059669',
          light: '#34D399',
        },
        dark: {
          base: '#0F0F0F',
          elevated: '#1A1A1A',
          surface: '#2A2A2A',
          card: '#2A2A2A',
          border: '#3A3A3A',
          input: '#3A3A3A',
          hover: '#333333',
        },
        status: {
          success: '#10B981',
          'success-bg': '#1E4620',
          'success-border': '#2E7D32',
          'success-text': '#66BB6A',
          warning: '#F59E0B',
          'warning-bg': '#FFF3CD',
          'warning-text': '#856404',
          error: '#EF4444',
          'error-bg': '#4A1A1A',
          'error-border': '#D32F2F',
          'error-text': '#EF5350',
          info: '#3B82F6',
          pending: '#F59E0B',
          paid: '#10B981',
          failed: '#EF4444',
        },
        text: {
          primary: '#FFFFFF',
          secondary: '#A3A3A3',
          tertiary: '#737373',
          disabled: '#525252',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      fontSize: {
        'xs': ['0.75rem', { lineHeight: '1rem' }],
        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
        'base': ['1rem', { lineHeight: '1.5rem' }],
        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
        '5xl': ['3rem', { lineHeight: '1' }],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
      },
      boxShadow: {
        'glow-orange': '0 0 20px rgba(255, 140, 0, 0.3)',
        'glow-orange-lg': '0 0 30px rgba(255, 140, 0, 0.4)',
        'glow-green': '0 0 20px rgba(16, 185, 129, 0.3)',
        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2)',
        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3)',
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-in',
        'fade-in-up': 'fadeInUp 0.5s ease-out',
        'slide-up': 'slideUp 0.4s ease-out',
        'slide-down': 'slideDown 0.4s ease-out',
        'pulse-glow': 'pulseGlow 2s infinite',
        'spin-slow': 'spin 3s linear infinite',
        'bounce-slow': 'bounce 3s infinite',
        'shimmer': 'shimmer 2s infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideUp: {
          '0%': { transform: 'translateY(100%)' },
          '100%': { transform: 'translateY(0)' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-100%)' },
          '100%': { transform: 'translateY(0)' },
        },
        pulseGlow: {
          '0%, 100%': { boxShadow: '0 0 20px rgba(255, 140, 0, 0.3)' },
          '50%': { boxShadow: '0 0 30px rgba(255, 140, 0, 0.6)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-1000px 0' },
          '100%': { backgroundPosition: '1000px 0' },
        },
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
        'shimmer-gradient': 'linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent)',
      },
    },
  },
  plugins: [],
}
