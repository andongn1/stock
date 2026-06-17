<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Family;
use App\Models\Packaging;
use App\Models\PriceHistory;
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
        Article::create($request->validated());
        
        return redirect()->route('articles.index')
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
        
        return redirect()->route('articles.index')
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
        $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat', 'priceHistories.supplier']);
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('articles.show', compact('article', 'suppliers'));
    }

    public function addPrice(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);
        
        // Deactivate previous price if exists
        PriceHistory::where('article_id', $article->id)
            ->whereNull('valid_to')
            ->where('supplier_id', $validated['supplier_id'])
            ->update(['valid_to' => now()]);
        
        // Create new price history
        $priceHistory = PriceHistory::create([
            'article_id' => $article->id,
            'supplier_id' => $validated['supplier_id'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'valid_from' => $validated['valid_from'],
            'valid_to' => $validated['valid_to'],
        ]);
        
        return redirect()->route('articles.show', $article)
            ->with('success', 'Prix ajoute avec succes pour ' . $priceHistory->supplier->name);
    }

    public function priceHistories(Article $article): View
    {
        $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat']);
        $priceHistories = PriceHistory::where('article_id', $article->id)
            ->with(['supplier'])
            ->latest()
            ->get();
        
        return view('articles.price-histories', compact('article', 'priceHistories'));
    }
}
