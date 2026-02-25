<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name', 'CRM System') }}</title>

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
                const theme = storedTheme ? ? (prefersDark ? 'dark' : 'light');
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
            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">{{ __('Login') }}</h1>

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="rounded border-[#e3e3e0] dark:border-[#3E3E3A] text-blue-500 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Remember me') }}</label>
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    {{ __('Login') }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-600 font-medium">{{ __('Register') }}</a>
                </p>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-8 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h3 class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Demo Credentials') }}</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">{{ __('Admin') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">admin@admin.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-xs font-medium text-green-700 dark:text-green-300 mb-1">{{ __('Sales Supervisor') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">supervisor@test.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">{{ __('Sales Agent') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">agent@test.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">{{ __('Sales Agent 1') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">agent1@test.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">{{ __('Sales Agent 2') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">agent2@test.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                        <div class="text-xs font-medium text-orange-700 dark:text-orange-300 mb-1">{{ __('Units Manager') }}</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">units@test.com</div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-mono">password</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
