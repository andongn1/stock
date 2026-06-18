@extends('layouts.app')

@section('title', 'Editer Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Editer l'utilisateur</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">
            Modifiez les informations de l'utilisateur {{ $user->name }}.
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    autocomplete="name"
                    class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 dark:border-red-500 @enderror"
                    placeholder="Entrez le nom complet"
                >
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="email"
                    class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 dark:border-red-500 @enderror"
                    placeholder="exemple@domaine.com"
                >
                @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Nouveau mot de passe
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    autocomplete="new-password"
                    minlength="8"
                    class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 dark:border-red-500 @enderror"
                    placeholder="Laisser vide pour ne pas modifier"
                >
                @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Confirmer le nouveau mot de passe
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation"
                    autocomplete="new-password"
                    class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Confirmez le nouveau mot de passe"
                >
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Role <span class="text-red-500">*</span>
                </label>
                <select 
                    name="role" 
                    id="role"
                    required
                    class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 dark:border-red-500 @enderror"
                >
                    <option value="" disabled>Selectionnez un role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role }}" {{ old('role', $user->role?->value) == $role ? 'selected' : '' }}>
                        {{ ucfirst($role) }}
                    </option>
                    @endforeach
                </select>
                @error('role')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current User Info -->
            <div class="p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-slate-400">
                    <strong>Utilisateur actuel :</strong> {{ $user->email }} ({{ ucfirst($user->role->value) }})<br>
                    <strong>Date de creation :</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                <a 
                    href="{{ route('users.index') }}"
                    class="px-4 py-2 text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    Annuler
                </a>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Mettre a jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
