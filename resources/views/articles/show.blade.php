@extends('layouts.app')

@section('title', 'Détails Article')

@section('content')
<div x-data="{ 
    showPriceModal: false,
    showHistoryModal: false,
    showConfirmSupplierChange: false,
    selectedPriceHistoryId: null,
    selectedSupplierName: '',
    price: '',
    currency: 'EUR',
    validFrom: new Date().toISOString().split('T')[0],
    sellingSaleType: '{{ old('sale_type', $article->current_selling_price?->sale_type->value ?? 'per_unit') }}',
    confirmSupplierChange(priceHistoryId, supplierName) {
        this.selectedPriceHistoryId = priceHistoryId;
        this.selectedSupplierName = supplierName;
        this.showConfirmSupplierChange = true;
    },
    cancelSupplierChange() {
        this.showConfirmSupplierChange = false;
        this.selectedPriceHistoryId = null;
        this.selectedSupplierName = '';
    },
    updateQuantityVisibility() {
        // Alpine.js will automatically handle the x-show based on the model value
    }
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
    @if($currentPrice)
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
                        {{ $currentPrice->supplier->name }}
                    </span>
                    <span class="text-xs bg-green-100 dark:bg-green-800/50 text-green-700 dark:text-green-300 px-2 py-1 rounded-full">
                        Actif
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-1">
                    Prix ({{ $currentPrice->price_type_label }})
                </label>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-300 font-bold text-xl">
                        {{ $currentPrice->price }} {{ $currentPrice->currency }}
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
                        {{ number_format($currentPrice->unit_price, 2) }} {{ $currentPrice->currency }} / {{ $article->unit?->name ?? 'unite' }}
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
                        {{ number_format($currentPrice->packaging_price, 2) }} {{ $currentPrice->currency }} / {{ $article->packaging?->name ?? 'conditionnement' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Enregistre le {{ $currentPrice->valid_from->format('d/m/Y') }}
        </div>
        
        <!-- Other Suppliers Section -->
        @if($otherSupplierPrices && $otherSupplierPrices->count() > 0)
        <div class="mt-6 pt-4 border-t border-green-200 dark:border-green-700">
            <h3 class="text-sm font-medium text-green-700 dark:text-green-400 mb-3">
                Autres fournisseurs (prix actuels)
            </h3>
            <div class="space-y-3">
                @foreach($otherSupplierPrices as $otherPrice)
                <div class="flex items-center justify-between p-3 bg-white dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors">
                    <div class="flex-1">
                        <div class="font-medium text-green-800 dark:text-green-300">
                            {{ $otherPrice->supplier->name }}
                        </div>
                        <div class="text-sm text-green-600 dark:text-green-400">
                            {{ $otherPrice->price }} {{ $otherPrice->currency }} ({{ $otherPrice->price_type_label }}) 
                            - <span class="font-medium">Unite:</span> {{ number_format($otherPrice->unit_price, 2) }} € / {{ $article->unit?->name ?? 'unité' }}
                            @if($article->packaging_unit_quantity && $article->packaging)
                            | <span class="font-medium">Conditionnement:</span> {{ number_format($otherPrice->packaging_price, 2) }} € / {{ $article->packaging->name }}
                            @endif
                        </div>
                        <div class="text-xs text-green-500 dark:text-green-500">
                            Enregistre le {{ $otherPrice->valid_from->format('d/m/Y') }}
                        </div>
                    </div>
                    <button 
                        @click="confirmSupplierChange('{{ $otherPrice->id }}', '{{ addslashes($otherPrice->supplier->name) }}')"
                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        </svg>
                        <span>Activer</span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Selling Price Section -->
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/30 dark:to-amber-900/30 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-orange-800 dark:text-orange-300 mb-4 border-b border-orange-200 dark:border-orange-700 pb-4">
            Prix de vente
        </h2>
        
        <form action="{{ route('articles.update-selling-price', $article) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Prix TTC <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-orange-500 dark:text-orange-400">€</span>
                        <input 
                            type="number" 
                            name="price_ttc" 
                            id="selling_price_ttc"
                            required
                            step="0.01"
                            min="0"
                            value="{{ old('price_ttc', $article->current_selling_price?->price_ttc ?? 0) }}"
                            class="w-full p-3 pl-8 border border-orange-300 dark:border-orange-700 rounded-lg bg-white dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            placeholder="0.00"
                        >
                    </div>
                    @error('price_ttc')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Type de vente <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="sale_type"
                        id="selling_sale_type"
                        required
                        x-model="sellingSaleType"
                        @change="updateQuantityVisibility"
                        class="w-full p-3 border border-orange-300 dark:border-orange-700 rounded-lg bg-white dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez un type</option>
                        <option value="per_unit" {{ old('sale_type', $article->current_selling_price?->sale_type->value ?? 'per_unit') === 'per_unit' ? 'selected' : '' }}>Par unite</option>
                        <option value="per_packaging" {{ old('sale_type', $article->current_selling_price?->sale_type->value ?? 'per_packaging') === 'per_packaging' ? 'selected' : '' }}>Par conditionnement</option>
                    </select>
                    @error('sale_type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="sellingSaleType === 'per_unit'" x-transition>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Quantite <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="quantity"
                            id="selling_quantity"
                            step="0.0001"
                            min="0"
                            value="{{ old('quantity', $article->current_selling_price?->quantity ?? 1) }}"
                            class="w-full p-3 border border-orange-300 dark:border-orange-700 rounded-lg bg-white dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            placeholder="1.00"
                        >
                    </div>
                    @error('quantity')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Devise <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="currency"
                        id="selling_currency"
                        required
                        value="{{ old('currency', $article->current_selling_price?->currency ?? 'EUR') }}"
                        maxlength="3"
                        class="w-full p-3 border border-orange-300 dark:border-orange-700 rounded-lg bg-white dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="EUR"
                    >
                    @error('currency')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-lg text-sm text-orange-700 dark:text-orange-300">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                @if($article->packaging_unit_quantity && $article->unit)
                Conditionnement: {{ $article->packaging_unit_quantity }} {{ $article->unit->name }}.
                @endif
                Selectionnez "Par unite" pour un prix a l'unite (ex: 5€/kg) ou "Par conditionnement" pour un prix global (ex: 50€/sac).
            </div>
            
            <button 
                type="submit"
                class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Mettre a jour le prix de vente</span>
            </button>
        </form>
        
        <!-- Selling Price Info -->
        @if($article->current_selling_price)
        <div class="mt-6 pt-4 border-t border-orange-200 dark:border-orange-700">
            <h3 class="text-sm font-medium text-orange-700 dark:text-orange-400 mb-3">
                Prix de vente actuel
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Prix TTC
                    </label>
                    <div class="flex items-center space-x-2">
                        <span class="text-orange-800 dark:text-orange-300 font-bold text-xl">
                            {{ number_format($article->current_selling_price->price_ttc, 2) }} {{ $article->current_selling_price->currency }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Type
                    </label>
                    <div class="flex items-center space-x-2">
                        <span class="text-orange-800 dark:text-orange-300 font-medium">
                            {{ $article->current_selling_price->sale_type_label }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-orange-700 dark:text-orange-400 mb-1">
                        Quantite
                    </label>
                    <div class="flex items-center space-x-2">
                        <span class="text-orange-800 dark:text-orange-300 font-medium">
                            {{ $article->current_selling_price->quantity }} {{ $article->unit?->name ?? 'unite' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Selling Ratio Section -->
    @if($currentPrice && $article->current_selling_price)
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-purple-800 dark:text-purple-300 mb-4 border-b border-purple-200 dark:border-purple-700 pb-4">
            Ratio de vente par fournisseur
        </h2>
        
        @php
        // Calculate VAT rate
        $vatRate = $article->vat?->rate ?? 0;
        $vatMultiplier = 1 + ($vatRate / 100);
        
        // Get selling price info
        $sellingPrice = $article->current_selling_price;
        $sellingPriceTtc = $sellingPrice->price_ttc;
        $sellingSaleType = $sellingPrice->sale_type->value ?? 'per_unit';
        $sellingQuantity = $sellingSaleType === 'per_unit' ? $sellingPrice->quantity : 1;
        
        // Group all prices by supplier
        $supplierPrices = $article->priceHistories->groupBy('supplier_id')->map->first();
        
        // Find the supplier with the lowest ratio (best margin)
        $bestSupplierId = null;
        $lowestRatio = PHP_FLOAT_MAX;
        foreach ($supplierPrices as $priceHistory) {
            if ($priceHistory->price_type === App\Enums\PriceType::PER_UNIT) {
                $purchasePricePerUnit = $priceHistory->price;
            } else {
                $purchasePricePerUnit = $priceHistory->price / ($article->packaging_unit_quantity > 0 ? $article->packaging_unit_quantity : 1);
            }
            
            if ($sellingSaleType === 'per_unit') {
                $totalPurchasePrice = $purchasePricePerUnit * $sellingQuantity;
            } else {
                if ($priceHistory->price_type === App\Enums\PriceType::PER_UNIT) {
                    $totalPurchasePrice = $purchasePricePerUnit * ($article->packaging_unit_quantity > 0 ? $article->packaging_unit_quantity : 1);
                } else {
                    $totalPurchasePrice = $priceHistory->price;
                }
            }
            
            $ratio = ($totalPurchasePrice * $vatMultiplier) / $sellingPriceTtc;
            if ($ratio < $lowestRatio) {
                $lowestRatio = $ratio;
                $bestSupplierId = $priceHistory->supplier_id;
            }
        }
        @endphp
        
        @if($supplierPrices->count() > 0)
        <div class="space-y-3">
            @foreach($supplierPrices as $priceHistory)
            @php
            // Calculate purchase price based on type
            if ($priceHistory->price_type === App\Enums\PriceType::PER_UNIT) {
                $purchasePricePerUnit = $priceHistory->price;
            } else {
                // Per packaging - need to divide by packaging_unit_quantity to get per unit
                $purchasePricePerUnit = $priceHistory->price / ($article->packaging_unit_quantity > 0 ? $article->packaging_unit_quantity : 1);
            }
            
            // Calculate purchase price for the selling quantity
            if ($sellingSaleType === 'per_unit') {
                $totalPurchasePrice = $purchasePricePerUnit * $sellingQuantity;
            } else {
                // Per packaging - quantity is 1, so we need to get the packaging price
                if ($priceHistory->price_type === App\Enums\PriceType::PER_UNIT) {
                    $totalPurchasePrice = $purchasePricePerUnit * ($article->packaging_unit_quantity > 0 ? $article->packaging_unit_quantity : 1);
                } else {
                    $totalPurchasePrice = $priceHistory->price;
                }
            }
            
            // Calculate ratio: (purchase_price * VAT) / selling_price_ttc
            $ratio = ($totalPurchasePrice * $vatMultiplier) / $sellingPriceTtc;
            
            // Calculate margin percentage
            $marginPercentage = $sellingPriceTtc > 0 ? (1 - $ratio) * 100 : 0;
            @endphp
            
            <div class="p-3 bg-white dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-medium text-purple-800 dark:text-purple-300 flex items-center space-x-2">
                            {{ $priceHistory->supplier->name }}
                            @if($priceHistory->supplier_id === $bestSupplierId)
                            <span class="text-yellow-500 text-xl">⭐</span>
                            @endif
                        </div>
                        <div class="text-sm text-purple-600 dark:text-purple-400">
                            Prix d'achat: {{ number_format($totalPurchasePrice, 2) }} {{ $priceHistory->currency }}
                            @if($sellingSaleType === 'per_unit')
                                ({{ number_format($purchasePricePerUnit, 4) }} €/{{ $article->unit?->name ?? 'unite' }} × {{ $sellingQuantity }})
                            @else
                                (par conditionnement)
                            @endif
                        </div>
                        <div class="text-sm text-purple-600 dark:text-purple-400">
                            Prix de vente: {{ number_format($sellingPriceTtc, 2) }} {{ $sellingPrice->currency }}
                            @if($sellingSaleType === 'per_unit')
                                ({{ $sellingQuantity }} {{ $article->unit?->name ?? 'unite' }})
                            @endif
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        <div class="text-lg font-bold 
                            @if($marginPercentage >= 20) text-green-600
                            @elseif($marginPercentage >= 10) text-orange-600
                            @else text-red-600 @endif"
                        >
                            {{ number_format($ratio * 100, 2) }}%
                        </div>
                        <div class="text-xs text-purple-500 dark:text-purple-500">
                            Ratio achat/vente
                        </div>
                        <div class="text-xs text-purple-500 dark:text-purple-500 mt-1">
                            Marge: {{ number_format($marginPercentage, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-purple-500 dark:text-purple-400 text-sm">
            Aucun prix fournisseur disponible pour calculer le ratio.
        </p>
        @endif
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
                    La date du jour sera automatiquement enregistree
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
                                Enregistre le {{ $priceHistory->valid_from->format('d/m/Y') }}
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

    <!-- Confirm Supplier Change Modal -->
    <div 
        x-show="showConfirmSupplierChange" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity"
                @click="cancelSupplierChange()"
            >
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            
            <!-- Modal content -->
            <div 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-headline"
            >
                <div class="bg-yellow-50 dark:bg-yellow-900/30 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-800/50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-yellow-800 dark:text-yellow-200" id="modal-headline">
                                Confirmer le changement de fournisseur
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Vous etes sur le point de changer le fournisseur actuel pour cet article. 
                                    Le prix de <strong x-text="selectedSupplierName"></strong> deviendra le prix actuel.
                                </p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-2">
                                    Le prix actuel sera desactive et le nouveau prix sera active a partir d'aujourd'hui.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" action="{{ route('articles.set-current-supplier', $article) }}" class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="price_history_id" x-model="selectedPriceHistoryId">
                        <button 
                            type="submit"
                            class="w-full sm:w-auto px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800"
                        >
                            Confirmer
                        </button>
                    </form>
                    <button 
                        type="button"
                        @click="cancelSupplierChange()"
                        class="mt-3 w-full sm:mt-0 sm:ml-3 sm:w-auto px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800"
                    >
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
