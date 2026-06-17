<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubFamilyResource;
use App\Models\SubFamily;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubFamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return SubFamilyResource::collection(
            SubFamily::with(['family'])->withCount(['articles'])->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreSubFamilyRequest $request): SubFamilyResource
    {
        $subFamily = SubFamily::create($request->validated());
        return new SubFamilyResource($subFamily->load('family'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubFamily $subFamily): SubFamilyResource
    {
        return new SubFamilyResource(
            $subFamily->load(['family'])->loadCount(['articles'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateSubFamilyRequest $request, SubFamily $subFamily): SubFamilyResource
    {
        $subFamily->update($request->validated());
        return new SubFamilyResource($subFamily->load('family'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubFamily $subFamily): \Illuminate\Http\JsonResponse
    {
        $subFamily->delete();
        return response()->json(null, 204);
    }
}
