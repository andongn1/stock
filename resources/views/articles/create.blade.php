@extends('layouts.app')

@section('title', 'Nouvel Article')

@section('content')
<div x-data="{ 
    subFamilies: @json($subFamilles->groupBy('family_id')),
    filteredSubFamilies: [],
    selectedFamily: ''
}" x-init="
    updateSubFamilies();
    $watch('selectedFamily', () => updateSubFamilies());
" class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Nouvel Article</h1>
        <a href="{{ route('articles.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Retour
        </a>
    </div>

    <form action="{{ route('articles.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom de l'Article <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        required
                        value="{{ old('name') }}"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Entrez le nom de l'article"
                    >
                    @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Marque
                    </label>
                    <input 
                        type="text" 
                        name="brand" 
                        id="brand"
                        value="{{ old('brand') }}"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Entrez la marque"
                    >
                    @error('brand')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Classifications -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="family_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Famille <span class="text-red-500">*</span>
                    </label>
                    <select 
                        x-model="selectedFamily"
                        name="family_id"
                        id="family_id"
                        required
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez une famille</option>
                        @foreach($families as $family)
                        <option value="{{ $family->id }}" {{ old('family_id') == $family->id ? 'selected' : '' }}>
                            {{ $family->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('family_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sub_family_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sous-Famille <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="sub_family_id"
                        id="sub_family_id"
                        required
                        x-model="$wire.sub_family_id"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez une sous-famille</option>
                        <template x-for="subFamily in filteredSubFamilies">
                            <option :value="subFamily.id" x-text="subFamily.name"></option>
                        </template>
                        @foreach($subFamilles as $subFamily)
                        @if(old('family_id') == $subFamily->family_id)
                        <option value="{{ $subFamily->id }}" {{ old('sub_family_id') == $subFamily->id ? 'selected' : '' }}>
                            {{ $subFamily->name }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                    @error('sub_family_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Unités et Conditionnements -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Unite <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="unit_id"
                        id="unit_id"
                        required
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez une unite</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="packaging_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Conditionnement <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="packaging_id"
                        id="packaging_id"
                        required
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Selectionnez un conditionnement</option>
                        @foreach($packagings as $packaging)
                        <option value="{{ $packaging->id }}" {{ old('packaging_id') == $packaging->id ? 'selected' : '' }}>
                            {{ $packaging->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('packaging_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="packaging_unit_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quantite par conditionnement
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="packaging_unit_quantity" 
                            id="packaging_unit_quantity"
                            value="{{ old('packaging_unit_quantity') }}"
                            step="0.01"
                            min="0"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: 10"
                        >
                        @error('packaging_unit_quantity')
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
                    Exemple: Un sac contenant 10kg de riz = Conditionnement: Sac, Unite: kg, Quantite: 10
                </p>
            </div>
            </div>

            <!-- TVA -->
            <div>
                <label for="vat_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    TVA <span class="text-red-500">*</span>
                </label>
                <select 
                    name="vat_id"
                    id="vat_id"
                    required
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Selectionnez une TVA</option>
                    @foreach($vats as $vat)
                    <option value="{{ $vat->id }}" {{ old('vat_id') == $vat->id ? 'selected' : '' }}>
                        {{ $vat->label }} ({{ $vat->rate }}%)
                    </option>
                    @endforeach
                </select>
                @error('vat_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="flex justify-end mt-8">
            <a href="{{ route('articles.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors mr-3">
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

<script>
    function updateSubFamilies() {
        const familyId = document.getElementById('family_id').value;
        const subFamilySelect = document.getElementById('sub_family_id');
        
        // Clear existing options
        subFamilySelect.innerHTML = '<option value="">Selectionnez une sous-famille</option>';
        
        if (familyId) {
            // Get sub-families for selected family
            const subFamilles = @json($subFamilles->groupBy('family_id'));
            if (subFamilles[familyId]) {
                subFamilles[familyId].forEach(subFamily => {
                    const option = document.createElement('option');
                    option.value = subFamily.id;
                    option.textContent = subFamily.name;
                    subFamilySelect.appendChild(option);
                });
            }
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('family_id').addEventListener('change', updateSubFamilies);
        updateSubFamilies();
    });
</script>
@endsection
