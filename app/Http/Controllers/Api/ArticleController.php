<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return ArticleResource::collection(
            Article::with(['family', 'subFamily', 'unit', 'packaging', 'vat'])
                ->withCount(['priceHistories'])
                ->latest()
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated());
        return new ArticleResource(
            $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat'])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): ArticleResource
    {
        return new ArticleResource(
            $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat', 'priceHistories.supplier'])
                ->loadCount(['priceHistories'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateArticleRequest $request, Article $article): ArticleResource
    {
        $article->update($request->validated());
        return new ArticleResource(
            $article->load(['family', 'subFamily', 'unit', 'packaging', 'vat'])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): \Illuminate\Http\JsonResponse
    {
        $article->delete();
        return response()->json(null, 204);
    }

    /**
     * Search articles by name.
     */
    public function search(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $query = Article::with(['family', 'subFamily', 'unit', 'packaging', 'vat']);
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }
        
        return ArticleResource::collection(
            $query->withCount(['priceHistories'])->latest()->get()
        );
    }
}
