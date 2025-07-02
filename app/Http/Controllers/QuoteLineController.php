<?php

namespace App\Http\Controllers;

use App\Models\Quotes;
use App\Models\QuotesLines;
use Illuminate\Http\Request;

class QuoteLineController extends Controller
{
    /**
     * Afficher le formulaire pour créer des lignes de devis
     */
    public function create(Quotes $quote)
    {
        return view('quotes.quotesLines.create', compact('quote'));
    }

    /**
     * Enregistrer les nouvelles lignes de devis
     */
    public function store(Request $request, Quotes $quote)
    {
        $request->validate([
            'lines' => 'required|array|min:1',
            'lines.*.wording' => 'required|string|max:255',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|integer|min:1'
        ]);

        // Facultatif : Si tu veux recalculer le total, décommente
        // $totalAmount = 0;

        foreach ($request->lines as $lineData) {
            QuotesLines::create([
                'quote_id' => $quote->id,
                'wording' => $lineData['wording'],
                'unit_price' => $lineData['unit_price'],
                'quantity' => $lineData['quantity']
            ]);

            // Facultatif : Si tu veux recalculer le total, décommente
            // $totalAmount += $lineData['unit_price'] * $lineData['quantity'];
        }

        // Facultatif : Si tu veux mettre à jour le montant total
        // $quote->update([
        //     'amount' => $quote->amount + $totalAmount
        // ]);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Les lignes ont été ajoutées avec succès au devis.');
    }

    /**
     * Afficher le formulaire d'édition d'une ligne
     */
    public function edit(Quotes $quote, QuotesLines $line)
    {
        if ($line->quote_id !== $quote->id) {
            abort(404);
        }

        return view('quotes.quotesLines.edit', compact('quote', 'line'));
    }

    /**
     * Mettre à jour une ligne de devis
     */
    public function update(Request $request, Quotes $quote, QuotesLines $line)
    {
        if ($line->quote_id !== $quote->id) {
            abort(404);
        }

        $request->validate([
            'wording' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1'
        ]);

        $line->update([
            'wording' => $request->wording,
            'unit_price' => $request->unit_price,
            'quantity' => $request->quantity
        ]);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'La ligne a été mise à jour avec succès.');
    }

    /**
     * Supprimer une ligne de devis
     */
    public function destroy(Quotes $quote, QuotesLines $line)
    {
        if ($line->quote_id !== $quote->id) {
            abort(404);
        }

        $line->delete();

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'La ligne a été supprimée avec succès.');
    }
}
