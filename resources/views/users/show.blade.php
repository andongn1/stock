@extends('layouts.app')

@section('title', 'Details Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Details de l'utilisateur</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
            Visualisation des informations de l'utilisateur {{ $user->name }}
        </p>
    </div>

    <!-- User Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Informations personnelles
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Nom complet
                        </label>
                        <div class="text-gray-800 dark:text-white">
                            {{ $user->name }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Email
                        </label>
                        <div class="text-gray-800 dark:text-white">
                            {{ $user->email }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Role
                        </label>
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            @if($user->role->value === 'admin')
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @else
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @endif
                        ">
                            {{ ucfirst($user->role->value) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Metadonnees
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            ID
                        </label>
                        <div class="text-gray-800 dark:text-white">
                            {{ $user->id }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Date de creation
                        </label>
                        <div class="text-gray-800 dark:text-white">
                            {{ $user->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Derniere mise a jour
                        </label>
                        <div class="text-gray-800 dark:text-white">
                            {{ $user->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 mt-6">
        <a 
            href="{{ route('users.edit', $user) }}"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Editer</span>
        </a>
        
        <a 
            href="{{ route('users.index') }}"
            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
        >
            Retour a la liste
        </a>
    </div>
</div>
@endsection
