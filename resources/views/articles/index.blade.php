@extends('layouts.app')

@section('title', 'Articles')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestion des Articles</h1>
        
        <a 
            href="{{ route('articles.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Ajouter un Article</span>
        </a>
    </div>

    <!-- Tableau dynamique -->
    <livewire:article-table />
</div>
@endsection
