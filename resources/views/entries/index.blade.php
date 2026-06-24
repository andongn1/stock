@extends('layouts.app')

@section('title', 'Entrées de Stock')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Entrées de stock</h1>
    
    <!-- Search Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" action="{{ route('entries.search') }}" class="space-y-4">
            <div class="flex gap-3">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search', '') }}"
                    placeholder="Rechercher un article (nom)..."
                    class="flex-1 p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    required
                >
                <button 
                    type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Rechercher Article</span>
                </button>
            </div>
        </form>
        
        <!-- Search Results -->
        @if(!empty($search))
        <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">
                Résultats de recherche ({{ $articles->count() }})
            </h3>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($articles as $article)
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $article->name }}</span>
                        @if($article->brand)
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">- {{ $article->brand }}</span>
                        @endif
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $article->packaging?->name ?? 'N/A' }} 
                            @if($article->packaging_unit_quantity)
                            ({{ $article->packaging_unit_quantity }} {{ $article->unit?->name ?? '' }})
                            @endif
                        </div>
                    </div>
                    <a 
                        href="{{ route('entries.create', $article) }}"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors font-medium"
                    >
                        Ajouter une entrée
                    </a>
                </div>
                @empty
                <div class="p-4 text-gray-500 dark:text-gray-400 text-center">
                    Aucun article trouvé.
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
    
    <!-- Entries Table -->
    @if($todayEntries->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
            Entrées de stock du {{ now()->format('d/m/Y') }}
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Article</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Conditionnement</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Qté/Conditionnement</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Qté Reçue</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Valeur Totale (Quantité)</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Valeur Totale (€)</th>
                        <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayEntries as $entry)
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 px-4 text-gray-800 dark:text-white">
                            {{ $entry->article->name }}
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                            {{ $entry->article->packaging?->name ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                            {{ $entry->article->packaging_unit_quantity }} {{ $entry->article->unit?->name ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-gray-800 dark:text-white">
                            {{ $entry->quantity }}
                        </td>
                        <td class="py-3 px-4 text-gray-800 dark:text-white">
                            {{ number_format($entry->total_quantity, 2) }} {{ $entry->article->unit?->name ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-gray-800 dark:text-white">
                            {{ number_format($entry->monetary_value, 2) }} €
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form method="POST" action="{{ route('entries.destroy', $entry) }}">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    onclick="return confirm('Etes-vous sûr de vouloir supprimer cette entrée ?')"
                                    class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium"
                                >
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Total: {{ $todayEntries->count() }} entrées enregistrées aujourd'hui
        </div>
    </div>
    @endif
</div>
@endsection
