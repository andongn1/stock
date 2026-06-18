<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\SubFamily;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubFamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $subFamilles = SubFamily::with(['family'])->withCount(['articles'])
            ->latest()
            ->get();
        
        $families = Family::orderBy('name')->get();
        
        return view('sub-familles.index', compact('subFamilles', 'families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $families = Family::orderBy('name')->get();
        return view('sub-familles.create', compact('families'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreSubFamilyRequest $request): RedirectResponse
    {
        SubFamily::create($request->validated());
        
        return redirect()->route('sub-familles.index')
            ->with('success', 'Sous-famille creee avec succes !');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubFamily $subFamille): View
    {
        $families = Family::orderBy('name')->get();
        return view('sub-familles.edit', compact('subFamille', 'families'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateSubFamilyRequest $request, SubFamily $subFamille): RedirectResponse
    {
        $subFamille->update($request->validated());
        
        return redirect()->route('sub-familles.index')
            ->with('success', 'Sous-famille mise a jour avec succes !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubFamily $subFamille): RedirectResponse
    {
        $subFamille->delete();
        
        return redirect()->route('sub-familles.index')
            ->with('success', 'Sous-famille supprimee avec succes !');
    }
}
