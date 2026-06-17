<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Family;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $families = Family::withCount(['subFamilles', 'articles'])
            ->latest()
            ->get();
        
        return view('families.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('families.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreFamilyRequest $request): RedirectResponse
    {
        Family::create($request->validated());
        
        return redirect()->route('families.index')
            ->with('success', 'Famille creee avec succes !');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Family $family): View
    {
        return view('families.edit', compact('family'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateFamilyRequest $request, Family $family): RedirectResponse
    {
        $family->update($request->validated());
        
        return redirect()->route('families.index')
            ->with('success', 'Famille mise a jour avec succes !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Family $family): RedirectResponse
    {
        $family->delete();
        
        return redirect()->route('families.index')
            ->with('success', 'Famille supprimee avec succes !');
    }
}
