<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackagingResource;
use App\Models\Packaging;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackagingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return PackagingResource::collection(
            Packaging::withCount(['articles'])->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StorePackagingRequest $request): PackagingResource
    {
        $packaging = Packaging::create($request->validated());
        return new PackagingResource($packaging);
    }

    /**
     * Display the specified resource.
     */
    public function show(Packaging $packaging): PackagingResource
    {
        return new PackagingResource($packaging->loadCount(['articles']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdatePackagingRequest $request, Packaging $packaging): PackagingResource
    {
        $packaging->update($request->validated());
        return new PackagingResource($packaging);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Packaging $packaging): \Illuminate\Http\JsonResponse
    {
        $packaging->delete();
        return response()->json(null, 204);
    }
}
