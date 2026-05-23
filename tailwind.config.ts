import type { Config } from 'tailwindcss'

const config: Config = {
  content: [
    './src/pages/**/*.{js,ts,jsx,tsx,mdx}',
    './src/components/**/*.{js,ts,jsx,tsx,mdx}',
    './src/app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        // Premium color palette
        'ink': {
          50: '#F8F7F5',
          100: '#F0EEEA',
          200: '#E1DDD5',
          300: '#D2CCBE',
          400: '#C3BBA7',
          500: '#B4AA90',
          600: '#A59979',
          700: '#968862',
          800: '#87774B',
          900: '#78663A',
          950: '#10233F', // Deep Ink
        },
        'royal': '#1F3A5F',
        'ivory': '#FAF8F4',
        'beige': '#F4EFE6',
        'gold': {
          50: '#FEF9F0',
          100: '#FEF3E1',
          200: '#FDE7C3',
          300: '#FCDBA5',
          400: '#FBCF87',
          500: '#FAC369',
          600: '#C89B3C', // Luxury Gold
          700: '#B67A2D', // Muted Copper
          800: '#A46A24',
          900: '#925A1B',
        },
        'text': {
          primary: '#1B1B1B',
          secondary: '#6B7280',
        },
      },
      fontFamily: {
        'playfair': ['var(--font-playfair)', 'serif'],
        'inter': ['var(--font-inter)', 'sans-serif'],
      },
      fontSize: {
        'hero': ['64px', { lineHeight: '1.2', fontWeight: '700' }],
        'hero-sm': ['48px', { lineHeight: '1.2', fontWeight: '700' }],
        'h1': ['48px', { lineHeight: '1.2', fontWeight: '700' }],
        'h2': ['36px', { lineHeight: '1.3', fontWeight: '700' }],
        'h3': ['28px', { lineHeight: '1.4', fontWeight: '600' }],
        'h4': ['22px', { lineHeight: '1.4', fontWeight: '600' }],
        'body': ['16px', { lineHeight: '1.7' }],
        'body-lg': ['18px', { lineHeight: '1.7' }],
        'body-sm': ['14px', { lineHeight: '1.6' }],
      },
      spacing: {
        'section': '80px',
        'section-sm': '60px',
      },
      borderRadius: {
        'luxury': '18px',
        'premium': '24px',
      },
      boxShadow: {
        'soft': '0 4px 6px rgba(0, 0, 0, 0.07)',
        'medium': '0 10px 15px rgba(0, 0, 0, 0.1)',
        'luxury': '0 20px 25px rgba(0, 0, 0, 0.08)',
        'glow': '0 0 30px rgba(200, 155, 60, 0.15)',
        'glow-lg': '0 0 50px rgba(200, 155, 60, 0.2)',
      },
      backgroundImage: {
        'gradient-luxury': 'linear-gradient(135deg, #FAF8F4 0%, #F4EFE6 100%)',
        'gradient-dark': 'linear-gradient(135deg, #10233F 0%, #1F3A5F 100%)',
        'radial-gold': 'radial-gradient(circle at 50% 50%, rgba(200, 155, 60, 0.1) 0%, transparent 70%)',
      },
      backdropBlur: {
        'luxury': '12px',
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
        'glow': 'glow 3s ease-in-out infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        glow: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.7' },
        },
      },
    },
  },
  plugins: [],
}
export default config
