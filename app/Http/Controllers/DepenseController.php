<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepenseStoreRequest;
use App\Models\Depense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DepenseController extends Controller
{
    public function index()
    {
        $depenses = Depense::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $totalDepenses = Depense::where('user_id', Auth::id())->sum('montant');
        $nombreDepenses = Depense::where('user_id', Auth::id())->count();

        return view('depense.index', compact('depenses', 'totalDepenses', 'nombreDepenses'));
    }

    public function create()
    {
        $categories = ['Loyer', 'Nourriture', 'Transport', 'Loisirs', 'Santé', 'Autres'];
        $modes_paiement = ['Espèces', 'Carte bancaire', 'Virement', 'Chèque', 'Mobile Money'];

        return view('depense.create', compact('categories', 'modes_paiement'));
    }

    public function store(DepenseStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('justificatif')) {
            $validated['justificatif'] = $request->file('justificatif')
                ->store('justificatifs', 'public');
        }

        Depense::create($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    public function show(Depense $depense)
    {
        $this->authorizeDepense($depense);

        return view('depense.show', compact('depense'));
    }

    public function edit(Depense $depense)
    {
        $this->authorizeDepense($depense);

        $categories = ['Loyer', 'Nourriture', 'Transport', 'Loisirs', 'Santé', 'Autres'];
        $modes_paiement = ['Espèces', 'Carte bancaire', 'Virement', 'Chèque', 'Mobile Money'];

        return view('depense.edit', compact('depense', 'categories', 'modes_paiement'));
    }

    public function update(DepenseStoreRequest $request, Depense $depense)
    {
        $this->authorizeDepense($depense);

        $validated = $request->validated();

        if ($request->hasFile('justificatif')) {

            if ($depense->justificatif) {
                Storage::disk('public')->delete($depense->justificatif);
            }

            $validated['justificatif'] = $request->file('justificatif')
                ->store('justificatifs', 'public');
        }

        $depense->update($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Depense $depense)
    {
        $this->authorizeDepense($depense);

        if ($depense->justificatif) {
            Storage::disk('public')->delete($depense->justificatif);
        }

        $depense->delete();

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }
    private function authorizeDepense(Depense $depense)
    {
        if ($depense->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
