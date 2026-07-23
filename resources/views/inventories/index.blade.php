@extends('layouts.app')

@section('title', 'Inventaire')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Inventaire des articles</h1>
    
    <!-- Family Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="POST" action="{{ route('inventories.search') }}" class="space-y-4">
            @csrf
            <div>
                <label for="family_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Selectionner une famille
                    <span class="text-red-500">*</span>
                </label>
                <select 
                    id="family_id"
                    name="family_id"
                    required
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">-- Selectionner une famille --</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}" {{ (isset($familyId) && $familyId == $family->id) ? 'selected' : '' }}>
                            {{ $family->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if(isset($familyId) && $familyId)
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search', '') }}"
                        placeholder="Rechercher un article dans cette famille..."
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                </div>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Rechercher</span>
                </button>
                @if(request('search'))
                <a 
                    href="{{ route('inventories.search') }}?family_id={{ $familyId }}"
                    class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Effacer</span>
                </a>
                @endif
            </div>
            @endif
        </form>
    </div>
    
    @if(isset($familyId) && $familyId && $articles->isNotEmpty())
    <form method="POST" action="{{ route('inventories.search') }}">
        @csrf
        <input type="hidden" name="family_id" value="{{ $familyId }}">
        <input type="hidden" name="search" value="{{ request('search', '') }}">
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Article</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Conditionnement</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Qté/Cond.</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Stock Précédent</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Stock Aujourd'hui</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                        @php
                            $hasTodayInventory = isset($todayStocks[$article->id]) && $todayStocks[$article->id] !== null;
                            $previousStock = $previousStocks[$article->id] ?? 0;
                            $todayStock = $todayStocks[$article->id] ?? null;
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-700 {{ $hasTodayInventory ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                            <td class="py-3 px-4 text-gray-800 dark:text-white">
                                {{ $article->name }}
                                @if($article->brand)
                                <br><span class="text-sm text-gray-500 dark:text-gray-400">{{ $article->brand }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                {{ $article->packaging?->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                {{ $article->packaging_unit_quantity }} {{ $article->unit?->name ?? '' }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">
                                {{ $previousStock }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-800 dark:text-white">
                                {{ $todayStock !== null ? $todayStock : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a 
                                    href="{{ route('inventories.create', $article) }}?family_id={{ $familyId }}"
                                    class="px-4 py-2 {{ $hasTodayInventory ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm rounded-lg transition-colors font-medium"
                                >
                                    {{ $hasTodayInventory ? 'Modifier inventaire' : 'Inventorier cet article' }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ $articles->count() }} articles dans cette famille
            </div>
        </div>
    </form>
    @elseif(isset($familyId) && $familyId && $articles->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center text-gray-500 dark:text-gray-400">
        Aucun article trouvé dans cette famille.
    </div>
    @endif
</div>
@endsection
