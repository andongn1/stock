@extends('layouts.app')

@section('title', 'Détails Article')

@section('content')
<div x-data="{ 
    showPriceModal: false,
    showHistoryModal: false,
    selectedSupplier: '',
    price: '',
    currency: 'EUR',
    validFrom: new Date().toISOString().split('T')[0]
}" class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $article->name }} 
            @if($article->brand)
            <span class="text-gray-500 dark:text-gray-400">- {{ $article->brand }}</span>
            @endif
        </h1>
        
        <div class="flex space-x-3">
            <a 
                href="{{ route('articles.edit', $article) }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Editer</span>
            </a>
            <a 
                href="{{ route('articles.index') }}"
                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
            >
                Retour
            </a>
        </div>
    </div>

    <!-- Article Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            Informations de base
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Famille
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->family?->name ?? 'Non défini' }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Sous-Famille
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->subFamily?->name ?? 'Non défini' }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Unite
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->unit?->name ?? 'Non défini' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Conditionnement
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->packaging?->name ?? 'Non défini' }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        TVA
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->vat?->label ?? 'Non défini' }} ({{ $article->vat?->rate ?? '0' }}%)
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Date de creation
                    </label>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-800 dark:text-white">
                            {{ $article->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Price Section -->
    @if($article->current_price)
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-4 border-b border-green-200 dark:border-green-700 pb-4">
            Prix actuel
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-1">
                    Fournisseur
                </label>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-300 font-medium">
                        {{ $article->current_price->supplier->name }}
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-1">
                    Prix ({{ $article->current_price->price_type_label }})
                </label>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-300 font-bold text-xl">
                        {{ $article->current_price->price }} {{ $article->current_price->currency }}
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-1">
                    Prix a l'unite
                </label>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-300 font-medium">
                        {{ number_format($article->current_price->unit_price, 2) }} {{ $article->current_price->currency }} / {{ $article->unit?->name ?? 'unite' }}
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-1">
                    Prix par conditionnement
                </label>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-300 font-medium">
                        {{ number_format($article->current_price->packaging_price, 2) }} {{ $article->current_price->currency }} / {{ $article->packaging?->name ?? 'conditionnement' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Valide depuis le {{ $article->current_price->valid_from->format('d/m/Y') }}
            @if($article->current_price->valid_to)
            jusqu'au {{ $article->current_price->valid_to->format('d/m/Y') }}
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ajouter un prix -->
        <div id="add-price" class="bg-blue-50 dark:bg-blue-900/30 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4 flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Ajouter un prix</span>
            </h3>
            
            <form action="{{ route('articles.add-price', $article) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-blue-700 dark:text-blue-400 mb-1">
                        Fournisseur <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="supplier_id"
                        id="supplier_id"
                        required
                        class="w-full p-3 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez un fournisseur</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2">
                        <a 
                            href="{{ route('suppliers.create') }}"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center space-x-1"
                            target="_blank"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Le fournisseur n'existe pas ? Creez-le</span>
                        </a>
                    </div>
                    @error('supplier_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-blue-700 dark:text-blue-400 mb-1">
                            Prix <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400">€</span>
                            <input 
                                type="number" 
                                name="price" 
                                id="price"
                                required
                                step="0.01"
                                min="0"
                                class="w-full p-3 pl-8 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0.00"
                            >
                        </div>
                        @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-medium text-blue-700 dark:text-blue-400 mb-1">
                            Devise <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="currency" 
                            id="currency"
                            required
                            value="EUR"
                            maxlength="3"
                            class="w-full p-3 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="EUR"
                        >
                        @error('currency')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="price_type" class="block text-sm font-medium text-blue-700 dark:text-blue-400 mb-1">
                            Type de prix <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="price_type"
                            id="price_type"
                            required
                            class="w-full p-3 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Selectionnez un type</option>
                            <option value="per_unit" {{ old('price_type') === 'per_unit' ? 'selected' : '' }}>Par unite</option>
                            <option value="per_packaging" {{ old('price_type') === 'per_packaging' ? 'selected' : '' }}>Par conditionnement</option>
                        </select>
                        @error('price_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Info sur les prix -->
                @if($article->packaging_unit_quantity && $article->unit)
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Conditionnement: {{ $article->packaging_unit_quantity }} {{ $article->unit->name }}. 
                    Selectionnez "Par unite" pour un prix a l'unite (ex: 2,58€/kg) ou "Par conditionnement" pour un prix global (ex: 25€/sac).
                </div>
                @endif
                
                <!-- Date du jour est automatiquement enregistree -->
                <input type="hidden" name="valid_from" value="{{ now()->toDateString() }}">
                <input type="hidden" name="valid_to" value="">
                
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    La date du jour sera automatiquement enregistree comme date de debut
                </div>
                
                <button 
                    type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium"
                >
                    Ajouter ce prix
                </button>
            </form>
        </div>

        <!-- Historique des prix -->
        <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-300 mb-4 flex items-center justify-between">
                <span class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Historique des prix ({{ $article->priceHistories->count() }})</span>
                </span>
                <a 
                    href="{{ route('articles.price-histories', $article) }}"
                    class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium"
                >
                    Voir tout
                </a>
            </h3>
            
            @if($article->priceHistories->isNotEmpty())
            <div class="space-y-3 max-h-48 overflow-y-auto">
                @foreach($article->priceHistories->sortByDesc('valid_from')->take(5) as $priceHistory)
                <div class="p-3 bg-white dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-purple-800 dark:text-purple-300">
                                {{ $priceHistory->supplier->name }}
                            </div>
                            <div class="text-sm text-purple-600 dark:text-purple-400">
                                {{ $priceHistory->price }} {{ $priceHistory->currency }} ({{ $priceHistory->price_type_label }})
                            </div>
                            <div class="text-xs text-purple-500 dark:text-purple-500 mt-1">
                                <span class="font-medium">Unite:</span> {{ number_format($priceHistory->unit_price, 2) }} € / {{ $article->unit?->name ?? 'unité' }}
                                @if($article->packaging_unit_quantity && $article->packaging)
                                | <span class="font-medium">Conditionnement:</span> {{ number_format($priceHistory->packaging_price, 2) }} € / {{ $article->packaging->name }}
                                @endif
                            </div>
                            <div class="text-xs text-purple-500 dark:text-purple-500 mt-1">
                                {{ $priceHistory->valid_from->format('d/m/Y') }}
                                @if($priceHistory->valid_to)
                                - {{ $priceHistory->valid_to->format('d/m/Y') }}
                                @else
                                - Aujourd'hui
                                @endif
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-800/50 text-purple-700 dark:text-purple-400 rounded-full">
                            {{ $priceHistory->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-purple-500 dark:text-purple-400 text-sm">
                Aucun historique de prix pour cet article.
            </p>
            @endif
        </div>
    </div>
</div>
@endsection
