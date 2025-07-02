<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Quotes;
use App\Models\QuotesStatus;
use App\Models\QuotesLines;


class QuotesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc'); // Par défaut 'asc'

        $quotesQuery = Quotes::query()->with(['status', 'project']);

        // Recherche
        if ($search) {
            $quotesQuery->where('description', 'like', '%' . $search . '%');
        }

        // Tri avec direction
        if ($sort) {    
            if ($sort === 'status') {
                $quotesQuery->join('quotes_status', 'quotes.status_id', '=', 'quotes_status.id')
                            ->orderBy('quotes_status.name', $direction);
            } elseif ($sort === 'project') {
                $quotesQuery->join('projects', 'quotes.project_id', '=', 'projects.id')
                            ->orderBy('projects.name', $direction);
            } else {
                $quotesQuery->orderBy($sort, $direction);
            }
        } else {
            $quotesQuery->orderByDesc('date_edition');
        }

        // Pagination avec relations chargées
        $quotes = $quotesQuery->paginate(10)->withQueryString();

        return view('quotes.index', compact('quotes', 'search', 'sort', 'direction'));
    }

    // Scope pour les quotes acceptées
    public function scopeAccepted($query)
    {
        return $query->where('status', 'acceptée');
    }

    // Scope pour les quotes en attente
    public function scopePending($query)
    {
        return $query->where('status', 'en_attente');
    }

    public function create(Request $request)
    {

        $projectId = $request->query('project'); // récupère project=26 dans l'URL
        if ($projectId) {
            $project = Project::find($projectId);
        } else {
            $project = null;
        }
        $statuses = QuotesStatus::all();

        if ($projectId) {
            $project = Project::findOrFail($projectId);
            return view('quotes.create', compact('project', 'statuses'));
        } else {
            $projects = Project::all();
            return view('quotes.create', compact('projects', 'statuses'));
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'date' => 'nullable|date',
            'status_id' => 'required|exists:quotes_status,id',
            'project_id' => 'required|exists:projects,id',
            'lines' => 'required|array|min:1',
            'lines.*.wording' => 'required|string|max:255',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        $quote = Quotes::create([
            'description' => $validated['description'],
            'date_edition' => $validated['date'] ?? now()->toDateString(),
            'status_id' => $validated['status_id'],
            'project_id' => $validated['project_id'],
        ]);

        foreach ($validated['lines'] as $line) {
            QuotesLines::create([
                'quote_id' => $quote->id,
                'wording' => $line['wording'],
                'unit_price' => $line['unit_price'],
                'quantity' => $line['quantity'],
            ]);
        }

        $previousUrl = url()->previous();
        if (str_contains($previousUrl, 'project')) {
            return redirect()->route('projects.show', $quote->project_id)
                ->with('success', 'Devis et lignes ajoutés avec succès.');
        }

        return redirect()->route('quotes.index')->with('success', 'Devis et lignes ajoutés avec succès.');
    }

    public function edit($quoteId)
    {
        $quote = Quotes::findOrFail($quoteId);
        $project = $quote->project;  // Relation project() définie dans Quotes

        $statuses = QuotesStatus::all();

        return view('quotes.edit', compact('project', 'quote', 'statuses'));
    }

    public function update(Request $request, $quoteId)
    {
        $quote = Quotes::findOrFail($quoteId);

        $request->validate([
            'description' => 'required|string',
            'date_edition' => 'required|date',
            'status_id' => 'required|exists:quotes_status,id',
        ]);

        $quote->update([
            'description' => $request->description,
            'date_edition' => $request->date_edition,
            'status_id' => $request->status_id,
        ]);

        $quote->save();

        return redirect()->route('projects.show', $quote->project_id)
                        ->with('success', 'Quote updated successfully.');
    }

    public function show(Quotes $quote)
    {
        $quote->load(['project.customer', 'project.status', 'status', 'lines']);
        
        // Récupérer les dépenses associées à ce devis
        $expenses = $quote->expenses()->get();
        
        return view('quotes.show', compact('quote', 'expenses'));
    }

    public function destroy($quoteId)
    {
        $quote = Quotes::findOrFail($quoteId);
        $quote->delete();

        return redirect()->route('quotes.index')->with('success', 'Devis supprimé avec succès.');
    }

    public function addLine(Request $request, Quotes $quote)
    {
        $validated = $request->validate([
            'lines' => 'required|array',
            'lines.*.text' => 'required|string',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.price' => 'required|numeric|min:0',
        ]);
        
        dd($validated);

        foreach ($validated['lines'] as $lineData) {
            $quote->lines()->create([
                'wording' => $lineData['text'],
                'quantity' => $lineData['quantity'],
                'unit_price' => $lineData['price'],
            ]);
        }

        // Correction de la redirection
        return redirect()->route('quotes.show', $quote)->with('success', 'Lignes ajoutées avec succès.');
    }

    public function showAddLines(Quotes $quote)
    {
        return view('quotes.addLine', compact('quote'));
    }

    public function manageLines(Quotes $quote)
    {
        return view('quotes.lines.manage', compact('quote'));
    }

    public function storeLines(Request $request, Quotes $quote)
    {
        $request->validate([
            'lines' => 'required|array|min:1',
            'lines.*.wording' => 'required|string|max:255',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|numeric|min:0',
        ]);

        foreach ($request->lines as $lineData) {
            $quote->lines()->create([
                'text' => $lineData['wording'], // ou 'wording' selon votre structure DB
                'unit_price' => $lineData['unit_price'],
                'quantity' => $lineData['quantity'],
            ]);
        }

        return redirect()->route('quotes.show', $quote)
                    ->with('success', 'Lignes ajoutées avec succès au devis.');
    }

    public function destroyLine(Quotes $quote, $line)
    {
        // Pour supprimer une ligne spécifique si besoin
        $quote->lines()->where('id', $line)->delete();
        
        return redirect()->route('quotes.lines.manage', $quote)
                    ->with('success', 'Ligne supprimée avec succès.');
    }

    public function sendQuote(Quotes $quote)
    {
        // Exemple simple : changer le statut en "Envoyé"
        $acceptedStatus = QuotesStatus::where('name', 'Envoyé')->first();
        if ($acceptedStatus) {
            $quote->status_id = $acceptedStatus->id;
            $quote->save();
        }

        return redirect()->back()->with('success', 'Le devis a été envoyé.');
    }

    public function validateQuote(Quotes $quote)
    {
        $acceptedStatus = QuotesStatus::where('name', 'Validé')->first();
        if ($acceptedStatus) {
            $quote->status_id = $acceptedStatus->id;
            $quote->save();
        }

        return redirect()->back()->with('success', 'Le devis a été validé.');
    }


}