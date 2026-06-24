@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Articles -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ App\Models\Article::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Articles</p>
                </div>
            </div>
        </div>

        <!-- Total Families -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ App\Models\Family::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Familles</p>
                </div>
            </div>
        </div>

        <!-- Total Suppliers -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ App\Models\Supplier::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Fournisseurs</p>
                </div>
            </div>
        </div>

        <!-- Total Units -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ App\Models\Unit::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Unites</p>
                </div>
            </div>
        </div>

        <!-- Total VATs -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ App\Models\Vat::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Taux de TVA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4 flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Actions rapides</span>
            </h3>
            <div class="space-y-3">
                <a 
                    href="{{ route('articles.create') }}"
                    class="flex items-center space-x-3 p-3 bg-white dark:bg-slate-700/50 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-slate-300">Ajouter un nouvel article</span>
                </a>
                <a 
                    href="{{ route('families.create') }}"
                    class="flex items-center space-x-3 p-3 bg-white dark:bg-slate-700/50 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-slate-300">Ajouter une nouvelle famille</span>
                </a>
                <a 
                    href="{{ route('suppliers.create') }}"
                    class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-700/50 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Ajouter un nouveau fournisseur</span>
                </a>
                <a 
                    href="{{ route('inventories.index') }}"
                    class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-700/50 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21-3.582-4-8-4s-8 1.79-8 4"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Faire un inventaire</span>
                </a>
            </div>
        </div>

        <!-- Recent Articles -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center justify-between">
                <span class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Articles recents</span>
                </span>
                <a href="{{ route('articles.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                    Voir tout
                </a>
            </h3>
            
            @php
            $recentArticles = App\Models\Article::with(['family', 'subFamily'])
                ->latest()
                ->take(5)
                ->get();
            @endphp
            
            @if($recentArticles->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentArticles as $article)
                <a 
                    href="{{ route('articles.show', $article) }}"
                    class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                >
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 dark:text-white">{{ $article->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-slate-400">
                            {{ $article->family?->name ?? 'Non defini' }} &raquo; {{ $article->subFamily?->name ?? 'Non defini' }}
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-full">
                        {{ $article->created_at->format('d/m/Y') }}
                    </span>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 dark:text-slate-400 text-sm py-4">
                Aucun article recent trouve.
            </p>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Statistiques des articles</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h4 class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">
                    Articles par Famille
                </h4>
                <div class="space-y-2">
                    @php
                    $articlesByFamily = App\Models\Article::with('family')
                        ->selectRaw('family_id, COUNT(*) as count')
                        ->groupBy('family_id')
                        ->get();
                    $counts = $articlesByFamily->pluck('count')->toArray();
                    $maxCount = max($counts) ?: 1;
                    @endphp
                    
                    @foreach($articlesByFamily as $item)
                    @if($item->family)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-slate-300">{{ $item->family->name }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div 
                                    class="bg-blue-500 h-2 rounded-full" 
                                    style="width: {{ (($item->count ?: 0) / $maxCount) * 100 }}%"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-slate-400">{{ $item->count ?? 0 }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">
                    Articles avec/sans prix
                </h4>
                <div class="space-y-2">
                    @php
                    $articlesWithPrices = App\Models\Article::whereHas('priceHistories')->count();
                    $articlesWithoutPrices = App\Models\Article::doesntHave('priceHistories')->count();
                    @endphp
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-slate-300">Avec prix</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div 
                                    class="bg-green-500 h-2 rounded-full" 
                                    style="width: {{ $articlesWithPrices > 0 ? ($articlesWithPrices / max($articlesWithPrices + $articlesWithoutPrices, 1)) * 100 : 0 }}%"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-slate-400">{{ $articlesWithPrices }}</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-gray-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-slate-300">Sans prix</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div 
                                    class="bg-gray-500 h-2 rounded-full" 
                                    style="width: {{ $articlesWithoutPrices > 0 ? ($articlesWithoutPrices / max($articlesWithPrices + $articlesWithoutPrices, 1)) * 100 : 0 }}%"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-slate-400">{{ $articlesWithoutPrices }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
