<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VatResource;
use App\Models\Vat;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return VatResource::collection(
            Vat::withCount(['articles'])->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreVatRequest $request): VatResource
    {
        $vat = Vat::create($request->validated());
        return new VatResource($vat);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vat $vat): VatResource
    {
        return new VatResource($vat->loadCount(['articles']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateVatRequest $request, Vat $vat): VatResource
    {
        $vat->update($request->validated());
        return new VatResource($vat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vat $vat): \Illuminate\Http\JsonResponse
    {
        $vat->delete();
        return response()->json(null, 204);
    }
}
