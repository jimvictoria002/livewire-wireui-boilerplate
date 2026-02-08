<meta charset="utf-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    {{ isset($title) && $title ? $title . ' - ' . config('app.name') : config('app.name') }}
</title>

<link rel="icon"
      href="/favicon.ico"
      sizes="any">
<link rel="icon"
      href="/favicon.svg"
      type="image/svg+xml">
<link rel="apple-touch-icon"
      href="/apple-touch-icon.png">

<link rel="preconnect"
      href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
      rel="stylesheet"/>

<script>
    (() => {
        const prefersDark = () => window.matchMedia('(prefers-color-scheme: dark)').matches;
        const applyAppearance = (appearance) => {
            const resolved = appearance === 'system' ? (prefersDark() ? 'dark' : 'light') : appearance;

            document.documentElement.classList.toggle('dark', resolved === 'dark');
        };

        const applyThemePreference = () => {
            const storedAppearance = localStorage.getItem('appearance') || 'system';

            localStorage.setItem('appearance', storedAppearance);
            applyAppearance(storedAppearance);
        };

        applyThemePreference();

        window.setAppearance = (appearance) => {
            localStorage.setItem('appearance', appearance);
            applyAppearance(appearance);
        };

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if ((localStorage.getItem('appearance') || 'system') === 'system') {
                applyAppearance('system');
            }
        });

        document.addEventListener('livewire:navigating', (event) => {
            event.detail.onSwap(() => {
                applyThemePreference();
            });
        });

        document.addEventListener('livewire:navigated', () => {
            applyThemePreference();
        });
    })();
</script>

@wireUiScripts
@vite(['resources/css/app.css', 'resources/js/app.js'])
