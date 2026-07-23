@extends('layouts.app')

@section('title', 'Confirmer Inventaire')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
        Confirmer l'inventaire
    </h1>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/30 px-4 pt-5 pb-4 rounded-lg mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg leading-6 font-medium text-yellow-800 dark:text-yellow-200">
                        Article déjà inventorié aujourd'hui
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            L'article <strong>{{ $article->name }}</strong> a déjà été inventorié aujourd'hui 
                            avec une quantité de <strong>{{ $existing->quantity ?? 0 }}</strong> {{ $article->packaging?->name ?? '' }}.
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-2">
                            Vous avez saisi une nouvelle quantité de <strong>{{ $pending['quantity'] ?? 0 }}</strong> {{ $article->packaging?->name ?? '' }}.
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-2">
                            Que souhaitez-vous faire ?
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-3">
            <form method="POST" action="{{ route('inventories.process-duplicate', $article) }}" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                @csrf
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="family_id" value="{{ request('family_id', '') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-white">Ajouter au stock existant</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Le nouveau stock sera ajouté à l'ancien. 
                            @if($article->packaging?->name)
                            Ex: {{ $existing->quantity ?? 0 }} + {{ $pending['quantity'] ?? 0 }} = {{ ($existing->quantity ?? 0) + ($pending['quantity'] ?? 0) }} {{ $article->packaging?->name }}
                            @endif
                        </p>
                    </div>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Ajouter</span>
                    </button>
                </div>
            </form>
            
            <form method="POST" action="{{ route('inventories.process-duplicate', $article) }}" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                @csrf
                <input type="hidden" name="action" value="replace">
                <input type="hidden" name="family_id" value="{{ request('family_id', '') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-white">Remplacer le stock existant</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Le stock actuel sera remplacé par le nouveau. 
                            @if($article->packaging?->name)
                            Ex: {{ $pending['quantity'] ?? 0 }} {{ $article->packaging?->name }} (au lieu de {{ $existing->quantity ?? 0 }})
                            @endif
                        </p>
                    </div>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Remplacer</span>
                    </button>
                </div>
            </form>
            
            <form method="POST" action="{{ route('inventories.process-duplicate', $article) }}" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                @csrf
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="family_id" value="{{ request('family_id', '') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-white">Annuler</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Ne pas enregistrer cette modification et revenir à la liste.
                        </p>
                    </div>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Annuler</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
