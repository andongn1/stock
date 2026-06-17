<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Vat;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VatController extends Controller
{
    public function index(): View
    {
        $vats = Vat::withCount(['articles'])->latest()->get();
        return view('vats.index', compact('vats'));
    }

    public function create(): View
    {
        return view('vats.create');
    }

    public function store(\App\Http\Requests\StoreVatRequest $request): RedirectResponse
    {
        Vat::create($request->validated());
        return redirect()->route('vats.index')
            ->with('success', 'TVA creee avec succes !');
    }

    public function edit(Vat $vat): View
    {
        return view('vats.edit', compact('vat'));
    }

    public function update(\App\Http\Requests\UpdateVatRequest $request, Vat $vat): RedirectResponse
    {
        $vat->update($request->validated());
        return redirect()->route('vats.index')
            ->with('success', 'TVA mise a jour avec succes !');
    }

    public function destroy(Vat $vat): RedirectResponse
    {
        $vat->delete();
        return redirect()->route('vats.index')
            ->with('success', 'TVA supprimee avec succes !');
    }
}
