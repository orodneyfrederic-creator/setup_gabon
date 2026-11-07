{{-- Système visuel SETUP-Gabon : partagé par layouts.app et layouts.auth --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>
    // Palette dérivée du logo : bleu roi #283888, vert et jaune du « U » (couleurs du Gabon).
    // Les autres teintes décoratives sont ramenées au bleu pour une interface cohérente.
    const bleuSetup = {
        50: '#eef1fa', 100: '#dde3f4', 200: '#bcc6e8', 300: '#93a3d8', 400: '#6578c2',
        500: '#4458ad', 600: '#30449d', 700: '#283888', 800: '#212f71', 900: '#1b275b', 950: '#10183a',
    };
    const vertGabon = {
        50: '#ecf8f0', 100: '#d2efdc', 200: '#a6debb', 300: '#6fc692', 400: '#3aaa69',
        500: '#1c9150', 600: '#137a42', 700: '#106337', 800: '#0f4f2e', 900: '#0c4026', 950: '#052414',
    };
    const jauneGabon = {
        50: '#fffbe6', 100: '#fff3bf', 200: '#ffe680', 300: '#fdd841', 400: '#f7c91a',
        500: '#e0ad06', 600: '#b98603', 700: '#8f6207', 800: '#764e0d', 900: '#644110', 950: '#3a2204',
    };
    const neutre = {
        50: '#f6f7fa', 100: '#eef0f5', 200: '#e1e4ec', 300: '#c9cedb', 400: '#9299ad',
        500: '#666e85', 600: '#4b536a', 700: '#373e53', 800: '#232a3d', 900: '#151b2b', 950: '#0b0f1b',
    };

    tailwind.config = {
        // Le site est conçu en clair : les variantes dark: des vues Laravel (pagination) ne doivent pas suivre le mode sombre du téléphone
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    blue: bleuSetup, indigo: bleuSetup, violet: bleuSetup, purple: bleuSetup,
                    sky: bleuSetup, cyan: bleuSetup, teal: bleuSetup, fuchsia: bleuSetup,
                    emerald: vertGabon, green: vertGabon,
                    amber: jauneGabon, yellow: jauneGabon, orange: jauneGabon,
                    slate: neutre, gray: neutre,
                },
                fontFamily: {
                    sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                fontWeight: {
                    black: '800',
                },
                borderRadius: {
                    xl: '0.625rem',
                    '2xl': '0.75rem',
                    '3xl': '1rem',
                },
                boxShadow: {
                    sm: '0 1px 2px rgba(16, 24, 58, 0.06)',
                    DEFAULT: '0 1px 3px rgba(16, 24, 58, 0.08), 0 1px 2px rgba(16, 24, 58, 0.04)',
                    md: '0 4px 10px -2px rgba(16, 24, 58, 0.08), 0 2px 4px -2px rgba(16, 24, 58, 0.05)',
                    lg: '0 10px 24px -6px rgba(16, 24, 58, 0.12), 0 4px 8px -4px rgba(16, 24, 58, 0.06)',
                    xl: '0 18px 36px -12px rgba(16, 24, 58, 0.18), 0 6px 12px -6px rgba(16, 24, 58, 0.08)',
                    '2xl': '0 28px 56px -16px rgba(16, 24, 58, 0.24)',
                },
            },
        },
    };
</script>
<style>
    [x-cloak] { display: none !important; }

    :root { color-scheme: light; accent-color: #283888; caret-color: #283888; }
    html { -webkit-text-size-adjust: 100%; scroll-padding-top: 6rem; }
    body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; }

    ::selection { background: #283888; color: #fff; }

    :focus-visible { outline: 2px solid #30449d; outline-offset: 2px; border-radius: 6px; }
    input:focus-visible, select:focus-visible, textarea:focus-visible { outline-offset: 0; }

    a { text-underline-offset: 3px; }

    /* Les prix et quantités restent alignés quand les chiffres changent */
    .chiffres { font-variant-numeric: tabular-nums; letter-spacing: -0.01em; }

    * { scrollbar-width: thin; scrollbar-color: #c9cedb transparent; }
    *::-webkit-scrollbar { width: 10px; height: 10px; }
    *::-webkit-scrollbar-thumb { background: #c9cedb; border-radius: 999px; border: 3px solid transparent; background-clip: content-box; }
    *::-webkit-scrollbar-thumb:hover { background-color: #9299ad; }

    /* Défilement au doigt sur mobile : barre masquée. À la souris (grand écran) : barre fine visible */
    @media (max-width: 1023px) {
        .scrollbar-none { scrollbar-width: none; }
        .scrollbar-none::-webkit-scrollbar { display: none; }
    }
    @media (min-width: 1024px) {
        .scrollbar-none { padding-bottom: 6px; }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
