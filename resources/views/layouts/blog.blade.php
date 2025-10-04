<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Blog') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('description', 'A modern blog built with Laravel and Filament')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .content-wrapper {
            background: white;
            min-height: 100vh;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: white;
        }
        .prose img {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="antialiased">
    <div class="content-wrapper">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('blog.index') }}" class="text-xl font-bold text-gray-800">
                            📝 Blog
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        
                        @auth
                            @if(auth()->user()->hasAnyRole(['Super Admin','Admin','Editor','Author']))
                                <a href="{{ url('/admin') }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Admin Panel
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">Çıkış</button>
                            </form>
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Giriş</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900 font-medium">Kayıt Ol</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h3 class="text-lg font-semibold mb-2">Laravel Blog</h3>
                    <p class="text-gray-400">Built with Laravel & Filament</p>
                    <div class="mt-4 flex justify-center space-x-6">
                        @auth
                            @if(auth()->user()->hasAnyRole(['Super Admin','Admin','Editor','Author']))
                                <a href="{{ url('/admin') }}" class="text-gray-400 hover:text-white transition-colors">
                                    Admin Panel
                                </a>
                            @endif
                        @endauth
                        <a href="https://github.com" class="text-gray-400 hover:text-white transition-colors">
                            GitHub
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
