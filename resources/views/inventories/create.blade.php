@extends('layouts.app')

@section('title', "Inventaire - {$article->name}")

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
        Inventaire: {{ $article->name }}
    </h1>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- Article Info -->
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
            <h2 class="font-semibold text-gray-800 dark:text-white mb-2">
                Informations de l'article
            </h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nom:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $article->name }}</span>
                </div>
                @if($article->brand)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Marque:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $article->brand }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Conditionnement:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $article->packaging?->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Quantité par conditionnement:</span>
                    <span class="font-medium text-gray-800 dark:text-white">
                        {{ $article->packaging_unit_quantity }} {{ $article->unit?->name ?? '' }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('inventories.store', $article) }}" class="space-y-6">
            @csrf
            <input type="hidden" name="family_id" value="{{ request('family_id', '') }}">
            
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Quantité stockée (en {{ $article->packaging?->name ?? 'conditionnements' }})
                    <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="quantity" 
                    id="quantity"
                    value="{{ old('quantity', 0) }}"
                    step="0.0001"
                    min="0"
                    required
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Ex: 5"
                >
            </div>
            
            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Exemple: Si le conditionnement est "Sac" et contient 10kg, entrez le nombre de sacs (ex: 5). 
                La valeur totale sera calculée automatiquement (ex: 5 sacs = 50kg).
            </div>
            
            <div class="flex space-x-4">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium"
                >
                    Enregistrer l'inventaire
                </button>
                <a 
                    href="{{ route('inventories.search') }}?family_id={{ request('family_id', '') }}"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
