<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\ExpensesLine;
use Illuminate\Http\Request;

class ExpenseLineController extends Controller
{
    /**
     * Afficher le formulaire pour créer des lignes de dépense
     */
    public function create(Expenses $expense)
    {
        return view('expenses.expensesLines.create', compact('expense'));
    }

    /**
     * Enregistrer les nouvelles lignes de dépense
     */
    public function store(Request $request, Expenses $expense)
    {

        $request->validate([
            'lines' => 'required|array|min:1',
            'lines.*.wording' => 'required|string|max:255',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|integer|min:1'
        ]);

        foreach ($request->lines as $lineData) {
            ExpensesLine::create([
                'expense_id' => $expense->id,
                'wording' => $lineData['wording'],
                'unit_price' => $lineData['unit_price'],
                'quantity' => $lineData['quantity']
            ]);
        }

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Les lignes ont été ajoutées avec succès à la dépense.');
    }

    /**
     * Afficher le formulaire d'édition d'une ligne
     */
    public function edit(Expenses $expense, ExpensesLine $expenseLine)
    {
        if ($expenseLine->expense_id !== $expense->id) {
            abort(404);
        }

        return view('expenses.expensesLines.edit', compact('expense', 'expenseLine'));
    }

    /**
     * Mettre à jour une ligne de dépense
     */
    public function update(Request $request, Expenses $expense, ExpensesLine $expenseLine)
    {
        if ($expenseLine->expense_id !== $expense->id) {
            abort(404);
        }

        $request->validate([
            'wording' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1'
        ]);

        $expenseLine->update([
            'wording' => $request->wording,
            'unit_price' => $request->unit_price,
            'quantity' => $request->quantity
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'La ligne a été mise à jour avec succès.');
    }

    /**
     * Supprimer une ligne de dépense
     */
    public function destroy(Expenses $expense, ExpensesLine $expenseLine)
    {
        if ($expenseLine->expense_id !== $expense->id) {
            abort(404);
        }

        $expenseLine->delete();

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'La ligne a été supprimée avec succès.');
    }
}
