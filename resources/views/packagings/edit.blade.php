@extends('layouts.app')

@section('title', 'Editer Conditionnement')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Editer Conditionnement</h1>
        <a href="{{ route('packagings.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Retour
        </a>
    </div>

    <form action="{{ route('packagings.update', $packaging) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nom du Conditionnement <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    required
                    value="{{ old('name', $packaging->name) }}"
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Ex: Sac, Boite, Carton"
                >
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Unite
                    </label>
                    <select 
                        name="unit_id" 
                        id="unit_id"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez une unite</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $packaging->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="unit_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quantite unitaire
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="unit_quantity" 
                            id="unit_quantity"
                            value="{{ old('unit_quantity', $packaging->unit_quantity) }}"
                            step="0.01"
                            min="0"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: 10"
                        >
                        @error('unit_quantity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Exemple: Pour un sac de 10kg de riz, selectionnez "kg" comme unite et entrez "10" comme quantite unitaire.
                </p>
            </div>
        </div>
        
        <div class="flex justify-end mt-8">
            <a href="{{ route('packagings.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors mr-3">
                Annuler
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
                Mettre a jour
            </button>
        </div>
    </form>
</div>
@endsection
