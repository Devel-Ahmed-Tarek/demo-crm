<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register - {{ config('app.name', 'CRM System') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(file_exists(public_path('build/manifest.json')))
        @php
            try {
                $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
                if (isset($manifest['resources/css/app.css']['file'])) {
                    $cssFile = $manifest['resources/css/app.css']['file'];
                }
                if (isset($manifest['resources/js/app.js']['file'])) {
                    $jsFile = $manifest['resources/js/app.js']['file'];
                }
            } catch (\Exception $e) {
                // Manifest file exists but couldn't be read
            }
        @endphp
        @isset($cssFile)
            <link rel="stylesheet" href="{{ asset('build/assets/' . $cssFile) }}">
        @endisset
        @isset($jsFile)
            <script type="module" src="{{ asset('build/assets/' . $jsFile) }}"></script>
        @endisset
    @endif
    <script>
        (() => {
            try {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme ?? (prefersDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                document.documentElement.dataset.theme = theme;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
</head>
<body class="app-shell min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8">
        <div class="app-card rounded-2xl p-8 border border-[#e3e3e0] dark:border-[#3E3E3A] backdrop-blur">
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">{{ __('Register') }}</h1>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Confirm Password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    {{ __('Register') }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ __("Already have an account?") }}
                    <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600 font-medium">{{ __('Login') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
