<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Packaging;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PackagingController extends Controller
{
    public function index(): View
    {
        $packagings = Packaging::withCount(['articles'])->latest()->get();
        return view('packagings.index', compact('packagings'));
    }

    public function create(): View
    {
        return view('packagings.create');
    }

    public function store(\App\Http\Requests\StorePackagingRequest $request): RedirectResponse
    {
        Packaging::create($request->validated());
        return redirect()->route('packagings.index')
            ->with('success', 'Conditionnement cree avec succes !');
    }

    public function edit(Packaging $packaging): View
    {
        return view('packagings.edit', compact('packaging'));
    }

    public function update(\App\Http\Requests\UpdatePackagingRequest $request, Packaging $packaging): RedirectResponse
    {
        $packaging->update($request->validated());
        return redirect()->route('packagings.index')
            ->with('success', 'Conditionnement mis a jour avec succes !');
    }

    public function destroy(Packaging $packaging): RedirectResponse
    {
        $packaging->delete();
        return redirect()->route('packagings.index')
            ->with('success', 'Conditionnement supprime avec succes !');
    }
}
