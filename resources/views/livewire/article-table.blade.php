<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
    <!-- Filtres -->
    <div class="mb-6 p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Rechercher
                </label>
                <input 
                    type="text" 
                    wire:model.debounce.500ms="search"
                    placeholder="Nom ou marque..."
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
            </div>
            
            <div>
                <label for="family" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Famille
                </label>
                <select 
                    wire:model="filters.family_id"
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="">Toutes</option>
                    @foreach($this->articles->pluck('family.name', 'family.id')->unique() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="sub_family" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Sous-Famille
                </label>
                <select 
                    wire:model="filters.sub_family_id"
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="">Toutes</option>
                    @foreach($this->articles->pluck('subFamily.name', 'sub_family_id')->unique() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Unite
                </label>
                <select 
                    wire:model="filters.unit_id"
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="">Toutes</option>
                    @foreach($this->articles->pluck('unit.name', 'unit_id')->unique() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="packaging" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Conditionnement
                </label>
                <select 
                    wire:model="filters.packaging_id"
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="">Tous</option>
                    @foreach($this->articles->pluck('packaging.name', 'packaging_id')->unique() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="vat" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    TVA
                </label>
                <select 
                    wire:model="filters.vat_id"
                    class="w-full p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="">Toutes</option>
                    @foreach($this->articles->pluck('vat.label', 'vat_id')->unique() as $id => $label)
                    <option value="{{ $id }}">{{ $label }} ({{ $this->articles->where('vat_id', $id)->first()?->vat?->rate }}%)
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex justify-between items-center mt-4">
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Afficher par page
                </label>
                <select 
                    wire:model="perPage"
                    class="p-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            
            <button 
                wire:click="clearFilters"
                class="px-4 py-2 bg-gray-500 dark:bg-slate-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors"
            >
                Effacer les filtres
            </button>
        </div>
    </div>

    <!-- Tableau -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                            <span>Article</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7') : 'M7 11l5-5 5 5M7 19l5-5 5 5' }}"></path>
                            </svg>
                        </button>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        <button wire:click="sortBy('brand')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                            <span>Marque</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $sortField === 'brand' ? ($sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7') : 'M7 11l5-5 5 5M7 19l5-5 5 5' }}"></path>
                            </svg>
                        </button>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Famille
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Sous-Famille
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Unite
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Conditionnement
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        TVA
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Prix
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($articles as $article)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $article->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->brand ?? '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->family?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->subFamily?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->unit?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->packaging?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                        {{ $article->vat?->label ?? '-' }} ({{ $article->vat?->rate ?? '0' }}%)
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                        @if($article->current_price)
                        {{ $article->current_price->price }} {{ $article->current_price->currency }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <div class="flex justify-center space-x-2">
                            <a 
                                href="{{ route('articles.show', $article) }}"
                                class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full"
                                title="Voir"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a 
                                href="{{ route('articles.edit', $article) }}"
                                class="p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-full"
                                title="Editer"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Etes-vous sur de vouloir supprimer cet article ?')">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-full"
                                    title="Supprimer"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        Aucun article trouve.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
</div>