@extends('layouts.app')

@section('title', 'Nouvelle TVA')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Nouvelle TVA</h1>
        <a href="{{ route('vats.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Retour
        </a>
    </div>

    <form action="{{ route('vats.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Taux (%) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="rate" 
                        id="rate"
                        required
                        step="0.01"
                        min="0"
                        max="100"
                        value="{{ old('rate') }}"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ex: 20"
                    >
                    @error('rate')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Libelle <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="label" 
                        id="label"
                        required
                        value="{{ old('label') }}"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ex: TVA 20%"
                    >
                    @error('label')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-8">
            <a href="{{ route('vats.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors mr-3">
                Annuler
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
