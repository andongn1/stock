<!DOCTYPE html>
<html lang="fr" x-data="{ darkMode: false, sidebarOpen: true }" :class="{ 'dark': darkMode }" x-init="
    darkMode = localStorage.getItem('darkMode') === 'true';
    sidebarOpen = window.innerWidth >= 1024 || localStorage.getItem('sidebarOpen') === 'true';
    
    $watch('darkMode', value => {
        localStorage.setItem('darkMode', value);
    });
    $watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>{{ config('app.name', 'Mercuriale App') }} - @yield('title', 'Gestion des Articles')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            content: [
                "./resources/**/*.blade.php",
                "./resources/**/*.js",
            ],
            theme: {
                extend: {}
            },
            plugins: []
        }
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-item:hover { background-color: #f1f5f9; }
        .sidebar-item.active { background-color: #e2e8f0; font-weight: 600; }
        .dark .sidebar-item:hover { background-color: #374151; }
        .dark .sidebar-item.active { background-color: #4b5563; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen" 
            x-transition:enter="transition-all ease-in-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all ease-in-out duration-300"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform lg:translate-x-0 lg:static lg:inset-0 lg:z-auto"
        >
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-xl font-bold text-gray-800 dark:text-white">Mercuriale</span>
                </div>
                <button 
                    @click="sidebarOpen = !sidebarOpen"
                    class="p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-4 px-2">
                <div class="space-y-1">
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Tableau de bord
                    </a>
                    
                    <a 
                        href="{{ route('articles.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('articles.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Articles
                    </a>
                    
                    <a 
                        href="{{ route('families.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('families.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                        </svg>
                        Familles
                    </a>
                    
                    <a 
                        href="{{ route('sub-familles.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('sub-familles.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Sous-Familles
                    </a>
                    
                    <a 
                        href="{{ route('units.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('units.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Unites
                    </a>
                    
                    <a 
                        href="{{ route('packagings.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('packagings.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Conditionnements
                    </a>
                    
                    <a 
                        href="{{ route('vats.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('vats.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        TVA
                    </a>
                    
                    <a 
                        href="{{ route('suppliers.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Fournisseurs
                    </a>
                    
                    <a 
                        href="{{ route('inventories.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('inventories.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21-3.582-4-8-4s-8 1.79-8 4"></path>
                        </svg>
                        Inventaire
                    </a>
                    
                    @if(auth()->user() && auth()->user()->isAdmin())
                    <a 
                        href="{{ route('users.index') }}" 
                        class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg {{ request()->routeIs('users.*') ? 'active' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Utilisateurs
                    </a>
                    @endif
                </div>
                
                <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>
                
                <div class="space-y-1">
                    <button 
                        @click="darkMode = !darkMode"
                        class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <span x-text="darkMode ? 'Mode clair' : 'Mode sombre'"></span>
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button 
                            type="submit"
                            class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Deconnexion
                        </button>
                    </form>
                </div>
            </nav>
        </aside>
        
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0" x-transition:enter="transition-all ease-in-out duration-300" x-transition:enter-start="opacity-0 lg:translate-x-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-all ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 lg:translate-x-0">
            <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 shadow-sm dark:shadow-lg">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <h1 class="text-lg font-semibold text-gray-800 dark:text-white">
                            @yield('title', 'Tableau de bord')
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ auth()->user() ? auth()->user()->name : 'Invite' }}
                        </span>
                        @if(auth()->user() && auth()->user()->isAdmin())
                        <span class="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-full">
                            Admin
                        </span>
                        @endif
                    </div>
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition:leave="transition-all duration-300" x-transition:leave-end="opacity-0 translate-y-4" x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition:leave="transition-all duration-300" x-transition:leave-end="opacity-0 translate-y-4" x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
