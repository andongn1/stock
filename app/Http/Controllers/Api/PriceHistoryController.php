<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PriceHistoryResource;
use App\Models\PriceHistory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PriceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return PriceHistoryResource::collection(
            PriceHistory::with(['article.supplier', 'supplier'])
                ->latest()
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StorePriceHistoryRequest $request): PriceHistoryResource
    {
        $priceHistory = PriceHistory::create($request->validated());
        return new PriceHistoryResource(
            $priceHistory->load(['article', 'supplier'])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(PriceHistory $priceHistory): PriceHistoryResource
    {
        return new PriceHistoryResource(
            $priceHistory->load(['article', 'supplier'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdatePriceHistoryRequest $request, PriceHistory $priceHistory): PriceHistoryResource
    {
        $priceHistory->update($request->validated());
        return new PriceHistoryResource(
            $priceHistory->load(['article', 'supplier'])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceHistory $priceHistory): \Illuminate\Http\JsonResponse
    {
        $priceHistory->delete();
        return response()->json(null, 204);
    }

    /**
     * Get price history for a specific article.
     */
    public function byArticle(\App\Models\Article $article): AnonymousResourceCollection
    {
        return PriceHistoryResource::collection(
            PriceHistory::where('article_id', $article->id)
                ->with(['supplier'])
                ->latest()
                ->get()
        );
    }

    /**
     * Get current prices for all articles.
     */
    public function currentPrices(): AnonymousResourceCollection
    {
        return PriceHistoryResource::collection(
            PriceHistory::with(['article', 'supplier'])
                ->where('valid_from', '<=', now())
                ->where(function ($query) {
                    $query->where('valid_to', '>=', now())
                          ->orWhereNull('valid_to');
                })
                ->latest()
                ->get()
        );
    }
}
