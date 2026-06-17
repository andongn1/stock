<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return FamilyResource::collection(
            Family::withCount(['subFamilles', 'articles'])->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreFamilyRequest $request): FamilyResource
    {
        $family = Family::create($request->validated());
        return new FamilyResource($family);
    }

    /**
     * Display the specified resource.
     */
    public function show(Family $family): FamilyResource
    {
        return new FamilyResource(
            $family->loadCount(['subFamilles', 'articles'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateFamilyRequest $request, Family $family): FamilyResource
    {
        $family->update($request->validated());
        return new FamilyResource($family);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Family $family): \Illuminate\Http\JsonResponse
    {
        $family->delete();
        return response()->json(null, 204);
    }
}
