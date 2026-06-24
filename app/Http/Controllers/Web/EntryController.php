<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Entry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntryController extends Controller
{
    public function index(Request $request): View
    {
        return $this->search($request);
    }
    
    public function search(Request $request): View
    {
        $search = $request->input('search', '');
        
        $articles = [];
        if (!empty($search)) {
            $articles = Article::with(['packaging', 'unit'])
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(10)
                ->get();
        }
        
        $todayEntries = Entry::with(['article.packaging', 'article.unit', 'article.priceHistories'])
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('entries.index', compact('articles', 'search', 'todayEntries'));
    }
    
    public function create(Article $article): View
    {
        $todayEntries = Entry::with(['article.packaging', 'article.unit'])
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('entries.create', compact('article', 'todayEntries'));
    }
    
    public function store(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
        
        $article->entries()->create([
            'quantity' => $validated['quantity'],
            'date' => now(),
        ]);
        
        return redirect()->route('entries.index')
            ->with('success', 'Entrée de stock enregistrée avec succès !');
    }
    
    public function destroy(Entry $entry): RedirectResponse
    {
        $entry->delete();
        
        return redirect()->route('entries.index')
            ->with('success', 'Entrée de stock supprimée avec succès !');
    }
}
