<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::withCount(['articles'])->latest()->get();
        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(\App\Http\Requests\StoreUnitRequest $request): RedirectResponse
    {
        Unit::create($request->validated());
        return redirect()->route('units.index')
            ->with('success', 'Unite creee avec succes !');
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(\App\Http\Requests\UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());
        return redirect()->route('units.index')
            ->with('success', 'Unite mise a jour avec succes !');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'Unite supprimee avec succes !');
    }
}
