@extends('layouts.app')

@section('title', 'Historique des Prix')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Historique des prix pour : {{ $article->name }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                @if($article->brand)
                    Marque : {{ $article->brand }} | 
                @endif
                Famille : {{ $article->family?->name ?? 'N/A' }} | 
                Sous-Famille : {{ $article->subFamily?->name ?? 'N/A' }}
            </p>
        </div>
        
        <a 
            href="{{ route('articles.show', $article) }}"
            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span>Retour a l'article</span>
        </a>
    </div>

    <!-- Stats Summary -->
    @if($priceHistories->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Records -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ $priceHistories->count() }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Entrées d'historique
            </div>
        </div>
        
        <!-- Current Price -->
        @php
            $currentPrice = $priceHistories->sortByDesc('valid_from')->sortByDesc('id')->first();
            $suppliersCount = $priceHistories->pluck('supplier_id')->unique()->count();
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ $currentPrice ? $currentPrice->price . ' ' . $currentPrice->currency : 'N/A' }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Prix le plus recent
            </div>
        </div>
        
        <!-- Suppliers Count -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ $suppliersCount }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Fournisseurs
            </div>
        </div>
        
        <!-- Price Range -->
        @php
            $minPrice = $priceHistories->min('price');
            $maxPrice = $priceHistories->max('price');
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
            <div class="text-lg font-bold text-orange-600 dark:text-orange-400">
                {{ $minPrice }} - {{ $maxPrice }} {{ $currentPrice ? $currentPrice->currency : 'EUR' }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Fourchette de prix
            </div>
        </div>
    </div>
    @endif

    <!-- Add Price Button -->
    <div class="flex justify-end">
        <a 
            href="{{ route('articles.show', $article) }}#add-price"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Ajouter un nouveau prix</span>
        </a>
    </div>

    <!-- Price History Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Fournisseur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Prix (Type)
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Prix a l'unite
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Prix par conditionnement
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Date
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($priceHistories as $history)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $history->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $history->supplier?->name ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                        {{ $history->price }} {{ $history->currency }} ({{ $history->price_type_label }})
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ number_format($history->unit_price, 2) }} {{ $history->currency }} / {{ $article->unit?->name ?? 'unite' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ number_format($history->packaging_price, 2) }} {{ $history->currency }} / {{ $article->packaging?->name ?? 'conditionnement' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $history->valid_from->format('d/m/Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        Aucun historique de prix trouve pour cet article. 
                        <a href="{{ route('articles.show', $article) }}#add-price" class="text-blue-600 dark:text-blue-400 hover:underline">
                            Ajoutez un prix maintenant.
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Total Count -->
    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{ $priceHistories->count() }} entree(s) d'historique
    </div>
</div>
@endsection
