<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Family;
use App\Models\Packaging;
use App\Models\PriceHistory;
use App\Models\SellingPrice;
use App\Models\SubFamily;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Vat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with(['family', 'subFamily', 'unit', 'packaging', 'vat'])
            ->withCount(['priceHistories'])
            ->latest()
            ->get();
        
        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        $families = Family::orderBy('name')->get();
        $subFamilles = SubFamily::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $packagings = Packaging::orderBy('name')->get();
        $vats = Vat::orderBy('label')->get();
        
        return view('articles.create', compact(
            'families', 'subFamilles', 'units', 'packagings', 'vats'
        ));
    }

    public function store(\App\Http\Requests\StoreArticleRequest $request): RedirectResponse
    {
        $article = Article::create($request->validated());
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Article cree avec succes !');
    }

    public function edit(Article $article): View
    {
        $families = Family::orderBy('name')->get();
        $subFamilles = SubFamily::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $packagings = Packaging::orderBy('name')->get();
        $vats = Vat::orderBy('label')->get();
        
        return view('articles.edit', compact(
            'article', 'families', 'subFamilles', 'units', 'packagings', 'vats'
        ));
    }

    public function update(\App\Http\Requests\UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($request->validated());
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Article mis a jour avec succes !');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        
        return redirect()->route('articles.index')
            ->with('success', 'Article supprime avec succes !');
    }

    public function show(Article $article): View
    {
        $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat', 'priceHistories.supplier', 'sellingPrice']);
        $suppliers = Supplier::orderBy('name')->get();
        
        // Get ALL price histories for this article, ordered by date (newest first)
        $allPrices = PriceHistory::where('article_id', $article->id)
            ->with('supplier')
            ->orderBy('valid_from', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Group by supplier to get the latest price for each
        $pricesBySupplier = $allPrices->groupBy('supplier_id')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();
        
        // The current price is the most recent overall
        $currentPrice = $allPrices->first();
        
        // Get other suppliers' prices (all prices except the current one's supplier)
        $otherSupplierPrices = $pricesBySupplier->reject(function ($price) use ($currentPrice) {
            return $price->id === ($currentPrice?->id ?? null);
        });
        
        return view('articles.show', compact('article', 'suppliers', 'otherSupplierPrices', 'currentPrice'));
    }

    public function addPrice(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'price_type' => 'required|string|in:per_unit,per_packaging',
        ]);
        
        // Create new price history with today's date
        // No need to deactivate previous prices - the most recent one will be the current
        $priceHistory = PriceHistory::create([
            'article_id' => $article->id,
            'supplier_id' => $validated['supplier_id'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'price_type' => $validated['price_type'],
            'valid_from' => now()->toDateString(),
            'valid_to' => null,
        ]);
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Prix ajoute avec succes pour ' . $priceHistory->supplier->name);
    }

    public function priceHistories(Article $article): View
    {
        $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat']);
        $priceHistories = PriceHistory::where('article_id', $article->id)
            ->with(['supplier'])
            ->orderBy('valid_from', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('articles.price-histories', compact('article', 'priceHistories'));
    }

    public function setCurrentSupplier(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'price_history_id' => 'required|exists:price_histories,id',
        ]);
        
        $priceHistory = PriceHistory::findOrFail($validated['price_history_id']);
        
        // Verify this price belongs to the article
        if ($priceHistory->article_id !== $article->id) {
            return redirect()->route('articles.show', $article)
                ->with('error', 'Le prix selectionne n\'appartient pas a cet article.');
        }
        
        // Create a new price history with today's date for this supplier
        // This ensures it becomes the most recent and thus the current price
        $newPrice = PriceHistory::create([
            'article_id' => $article->id,
            'supplier_id' => $priceHistory->supplier_id,
            'price' => $priceHistory->price,
            'currency' => $priceHistory->currency,
            'price_type' => $priceHistory->price_type,
            'valid_from' => now()->toDateString(),
            'valid_to' => null,
        ]);
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Fournisseur active avec succes ! Le prix de ' . $priceHistory->supplier->name . ' est maintenant le prix actuel.');
    }

    public function updateSellingPrice(\App\Http\Requests\UpdateSellingPriceRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();
        
        // If quantity is not provided, set it based on sale_type
        if (!isset($validated['quantity']) || $validated['quantity'] === null) {
            $validated['quantity'] = ($validated['sale_type'] === 'per_unit') ? 1 : 1;
        }
        
        // If selling price exists, update it. Otherwise, create a new one.
        $sellingPrice = $article->sellingPrice()->first();
        
        if ($sellingPrice) {
            $sellingPrice->update($validated);
        } else {
            $article->sellingPrice()->create($validated);
        }
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Prix de vente mis a jour avec succes !');
    }
}
