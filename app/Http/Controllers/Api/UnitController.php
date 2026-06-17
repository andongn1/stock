<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return UnitResource::collection(
            Unit::withCount(['articles'])->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreUnitRequest $request): UnitResource
    {
        $unit = Unit::create($request->validated());
        return new UnitResource($unit);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit): UnitResource
    {
        return new UnitResource($unit->loadCount(['articles']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateUnitRequest $request, Unit $unit): UnitResource
    {
        $unit->update($request->validated());
        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit): \Illuminate\Http\JsonResponse
    {
        $unit->delete();
        return response()->json(null, 204);
    }
}
