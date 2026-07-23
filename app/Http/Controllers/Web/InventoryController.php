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
        $familyId = $request->input('family_id', null);
        
        // Get articles from selected family (or all if no family selected)
        $query = Article::with(['packaging', 'unit', 'family', 'priceHistories']);
        
        if ($familyId) {
            $query->where('family_id', $familyId);
        }
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $articles = $query->orderBy('name')->get();
        
        // Get today's inventories
        $todayInventories = Inventory::with(['article.packaging', 'article.unit', 'article.priceHistories'])
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get families for dropdown
        $families = \App\Models\Family::orderBy('name')->get();
        
        // Get previous stock (last inventory before today) for each article
        $previousStocks = [];
        foreach ($articles as $article) {
            $lastInventory = Inventory::where('article_id', $article->id)
                ->whereDate('date', '<', today())
                ->latest()
                ->first();
            $previousStocks[$article->id] = $lastInventory ? $lastInventory->quantity : 0;
        }
        
        // Get today stock for each article
        $todayStocks = [];
        foreach ($articles as $article) {
            $todayInventory = Inventory::where('article_id', $article->id)
                ->whereDate('date', today())
                ->first();
            $todayStocks[$article->id] = $todayInventory ? $todayInventory->quantity : null;
        }
        
        return view('inventories.index', compact(
            'articles', 
            'search', 
            'todayInventories',
            'families',
            'familyId',
            'previousStocks',
            'todayStocks'
        ));
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
        
        $familyId = $request->input('family_id');
        
        $existing = Inventory::where('article_id', $article->id)
            ->whereDate('date', today())
            ->first();
        
        if ($existing) {
            $request->session()->put('pending_inventory', [
                'article_id' => $article->id,
                'quantity' => $validated['quantity'],
                'existing_id' => $existing->id,
                'existing_quantity' => $existing->quantity,
                'family_id' => $familyId,
            ]);
            
            $redirectRoute = route('inventories.confirm-duplicate', $article);
            if ($familyId) {
                $redirectRoute .= '?family_id=' . $familyId;
            }
            
            return redirect($redirectRoute)
                ->with('warning', 'Cet article a déjà été inventorié aujourd\'hui !');
        }
        
        $article->inventories()->create([
            'quantity' => $validated['quantity'],
            'date' => now(),
        ]);
        
        $redirectRoute = route('inventories.search');
        if ($familyId) {
            $redirectRoute .= '?family_id=' . $familyId;
        }
        
        return redirect($redirectRoute)
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
        
        $familyId = $request->input('family_id', $pending['family_id'] ?? null);
        
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
        
        $redirectRoute = route('inventories.search');
        if ($familyId) {
            $redirectRoute .= '?family_id=' . $familyId;
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Inventaire mis à jour !');
    }
    
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $familyId = $inventory->article?->family_id;
        $inventory->delete();
        
        $redirectRoute = route('inventories.search');
        if ($familyId) {
            $redirectRoute .= '?family_id=' . $familyId;
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Inventaire supprimé avec succès !');
    }
}
