/**
 * Tailwind preset для WordPress-темы (если будете подключать Tailwind через build).
 * Использование: tailwind.config.js → presets: [require('./wordpress-package/tokens/tailwind.preset.js')]
 */
module.exports = {
  theme: {
    extend: {
      colors: {
        brand: {
          blue:   '#2563eb',
          green:  '#10b981',
          amber:  '#f59e0b',
          danger: '#ef4444',
          ink:    '#172033',
          muted:  '#647084',
          line:   '#e5edf5',
          soft:   '#f6fbff',
          dark:   '#0f172a',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
      },
      fontWeight: {
        normal: '500',
      },
      borderRadius: {
        sm:  '8px',
        md:  '14px',
        lg:  '22px',
        xl:  '28px',
        '2xl': '32px',
        pill: '999px',
      },
      boxShadow: {
        card:   '0 12px 28px rgba(15, 23, 42, 0.045)',
        hover:  '0 22px 45px rgba(15, 23, 42, 0.08)',
        sticky: '0 12px 36px rgba(15, 23, 42, 0.05)',
      },
      backgroundImage: {
        'hero-gradient': 'radial-gradient(circle at 75% 15%, rgba(16,185,129,0.14), transparent 25%), radial-gradient(circle at 15% 18%, rgba(37,99,235,0.13), transparent 28%), linear-gradient(180deg, #f8fbff, #eefdf6 78%, #fff)',
        'page-gradient': 'linear-gradient(180deg, #f7fbff 0, #fff 420px)',
      },
      letterSpacing: {
        tightest: '-0.02em',
      },
      maxWidth: {
        '7xl': '80rem',
      },
    },
  },
};
