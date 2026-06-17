@extends('layouts.app')

@section('title', 'Editer Sous-Famille')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Editer Sous-Famille</h1>
        <a href="{{ route('sub-familles.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Retour
        </a>
    </div>

    <form action="{{ route('sub-familles.update', $subFamille) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nom de la Sous-Famille <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    required
                    value="{{ old('name', $subFamille->name) }}"
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Entrez le nom de la sous-famille"
                >
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="family_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Famille <span class="text-red-500">*</span>
                </label>
                <select 
                    name="family_id"
                    id="family_id"
                    required
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Selectionnez une famille</option>
                    @foreach($families as $family)
                    <option value="{{ $family->id }}" {{ old('family_id', $subFamille->family_id) == $family->id ? 'selected' : '' }}>
                        {{ $family->name }}
                    </option>
                    @endforeach
                </select>
                @error('family_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="flex justify-end mt-8">
            <a href="{{ route('sub-familles.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors mr-3">
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
