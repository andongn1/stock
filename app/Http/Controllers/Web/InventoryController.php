<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
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
        
        $todayInventories = Inventory::with(['article.packaging', 'article.unit', 'article.priceHistories'])
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('inventories.index', compact('articles', 'search', 'todayInventories'));
    }
    
    public function create(Article $article): View
    {
        $todayInventories = Inventory::with(['article.packaging', 'article.unit'])
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('inventories.create', compact('article', 'todayInventories'));
    }
    
    public function store(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
        
        $existing = Inventory::where('article_id', $article->id)
            ->whereDate('date', today())
            ->first();
        
        if ($existing) {
            $request->session()->put('pending_inventory', [
                'article_id' => $article->id,
                'quantity' => $validated['quantity'],
                'existing_id' => $existing->id,
                'existing_quantity' => $existing->quantity,
            ]);
            
            return redirect()->route('inventories.confirm-duplicate', $article)
                ->with('warning', 'Cet article a déjà été inventorié aujourd\'hui !');
        }
        
        $article->inventories()->create([
            'quantity' => $validated['quantity'],
            'date' => now(),
        ]);
        
        return redirect()->route('inventories.index')
            ->with('success', 'Inventaire enregistré avec succès !');
    }
    
    public function confirmDuplicate(Article $article, Request $request): View
    {
        $pending = $request->session()->get('pending_inventory');
        
        if (!$pending) {
            return redirect()->route('inventories.index');
        }
        
        $existing = Inventory::find($pending['existing_id']);
        
        return view('inventories.confirm-duplicate', compact('article', 'existing', 'pending'));
    }
    
    public function processDuplicate(Article $article, Request $request): RedirectResponse
    {
        $pending = $request->session()->pull('pending_inventory');
        
        if (!$pending) {
            return redirect()->route('inventories.index');
        }
        
        $existing = Inventory::find($pending['existing_id']);
        $action = $request->input('action');
        
        switch ($action) {
            case 'add':
                $existing->update([
                    'quantity' => $existing->quantity + $pending['quantity'],
                ]);
                break;
            case 'replace':
                $existing->update([
                    'quantity' => $pending['quantity'],
                ]);
                break;
            case 'cancel':
            default:
                break;
        }
        
        return redirect()->route('inventories.index')
            ->with('success', 'Inventaire mis à jour !');
    }
    
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $inventory->delete();
        
        return redirect()->route('inventories.index')
            ->with('success', 'Inventaire supprimé avec succès !');
    }
}
