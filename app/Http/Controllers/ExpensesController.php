<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Quotes;
use Illuminate\Http\Request;
use App\Models\Expenses;
use App\Models\ExpensesLine;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;


class ExpensesController extends Controller
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'quote_id', // Si la dépense vient d'une quote convertie
        'description',
        'date',
        'receipt_path', // Pour stocker les reçus
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Scope pour les dépenses directes (pas issues d'une quote)
    public function scopeDirect($query)
    {
        return $query->whereNull('quote_id');
    }

    // Scope pour les dépenses issues de quotes
    public function scopeFromQuote($query)
    {
        return $query->whereNotNull('quote_id');
    }

    public function create(Request $request)
    {
        $quoteId = $request->get('quote_id');
        $quote = Quotes::with(['project.customer', 'lines'])->find($quoteId);


        $project = $quote->project;
        // Récupérer la plus grande valeur d'expense_number existante pour ce projet
        $maxNumber = $project->quotes()
            ->with('expenses')
            ->get()
            ->pluck('expenses')
            ->flatten()
            ->max(function ($expense) {
                return (int) $expense->expense_number;
            });

        // Proposer un numéro supérieur automatiquement (ou 1 si aucune facture)
        $suggestedNumber = str_pad(($maxNumber + 1), 3, '0', STR_PAD_LEFT);

        // Récupérer les devis disponibles pour la sélection
        $availableQuotes = Quotes::with('project')->orderBy('created_at', 'desc')->get();

        return view('expenses.create', [
            'quote' => $quote,
            'availableQuotes' => $availableQuotes,
            'suggestedNumber' => $suggestedNumber
        ]);
    }

    public function store(Request $request)
    {


        // Validation des données
        $validated = $request->validate([
            'status_id' => 'nullable|exists:expenses_status,id',
            'quote_id' => 'required|exists:quotes,id',
            'expense_number' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'date_edition' => 'nullable|date',
            'type_payment' => 'nullable|string|max:100',
            'date_payment_limit' => 'date',
            'date_payment_effect' => 'nullable|date',
            'lines' => 'required|array|min:1',
            'lines.*.wording' => 'required|string|max:255',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        if (empty($validated['date_edition'])) {
            $validated['date_edition'] = now();
        }

        // Création de l'Expense
        $expense = Expenses::create([
            'status_id' => $validated['status_id'] ?? null,
            'quote_id' => $validated['quote_id'],
            'expense_number' => $validated['expense_number'],
            'date_edition' => $validated['date_edition'],
            'type_payment' => $validated['type_payment'] ?? null,
            'date_payment_limit' => $validated['date_payment_limit'],
            'date_payment_effect' => $validated['date_payment_effect'] ?? null,
        ]);

        foreach ($validated['lines'] as $line) {
            ExpensesLine::create([
                'expense_id' => $expense->id,
                'wording' => $line['wording'],
                'unit_price' => $line['unit_price'],
                'quantity' => $line['quantity'],
            ]);
        }

        $this->generateInvoiceHtml($expense);

        return redirect()->route('expenses.show', $expense->id)
            ->with('success', 'La facture a bien été créée.');
    }

    public function show(Expenses $expense)
    {
        // Chargement des relations nécessaires
        // On passe par quote pour accéder au projet et au customer
        $expense->load([
            'quote.project.customer',
            'quote.project.status',
            'quote.status',
            'expenses_status'
        ]);

        return view('expenses.show', compact('expense'));
    }

    public function index(Request $request)
    {
        $query = Expenses::with('quote.project');
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sort = $request->get('sort', 'date_payment_limit');
        $direction = $request->get('direction', 'desc'); // Desc pour avoir les plus récents en premier
        
        // Liste des colonnes triables (adapter selon tes vraies colonnes)
        $sortable = ['product_name', 'amount', 'date_payment_limit'];
        
        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        }
        
        $expenses = $query->paginate(10);
        
        return view('expenses.index', compact('expenses'));
    }

    public function destroy(Expenses $expense)
    {
        // Suppression de la dépense
        $expense->delete();

        $this->deleteInvoiceHtml($expense);

        // Redirection avec message de succès
        return redirect()->route('expenses.index')->with('success', 'La dépense a bien été supprimée.');
    }

    public function edit(Expenses $expense)
    {
        // Chargement des relations nécessaires pour l'affichage
        $expense->load([
            'quote.project.customer',
            'quote.project.status',
            'quote.status',
            'expenses_status'
        ]);

        // Récupération des données nécessaires pour les select
        $expenseStatuses = \App\Models\ExpensesStatus::orderBy('name')->get();
        $quotes = Quotes::with('project')->orderBy('created_at', 'desc')->get();

        return view('expenses.edit', compact('expense', 'expenseStatuses', 'quotes'));
    }

    public function update(Request $request, Expenses $expense)
    {
        // Validation des données
        $validated = $request->validate([
            'status_id' => 'nullable|exists:expenses_status,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'product_name' => 'required|string|max:255',
            'date_edition' => 'nullable|date',
            'type_payment' => 'nullable|string|max:100',
            'date_payment_limit' => 'required|date',
            'date_payment_effect' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        if (empty($validated['date_edition'])) {
            $validated['date_edition'] = now();
        }

        $this->deleteInvoiceHtml($expense);

        // Mise à jour de l'expense sans gestion de fichiers
        $expense->update([
            'status_id' => $validated['status_id'] ?? $expense->status_id,
            'quote_id' => $validated['quote_id'] ?? $expense->quote_id,
            'product_name' => $validated['product_name'],
            'date_edition' => $validated['date_edition'],
            'type_payment' => $validated['type_payment'],
            'date_payment_limit' => $validated['date_payment_limit'],
            'date_payment_effect' => $validated['date_payment_effect'],
            'note' => $validated['note'],
        ]);

        $this->generateInvoiceHtml($expense);

        return redirect()->route('expenses.show', $expense->id)
            ->with('success', 'La facture a été mise à jour avec succès.');
    }

    /**
     * Génère et sauvegarde le fichier HTML de la facture
     */
    private function generateInvoiceHtml($expense)
    {
        // Charger l'expense avec toutes ses relations
        $expense->load([
            'lines',
            'expenses_status',
            'quote.project.customer'
        ]);

        // Générer le contenu HTML
        $htmlContent = $this->getInvoiceHtmlContent($expense);
        
        // Créer le dossier s'il n'existe pas
        $invoicesPath = public_path('invoices');
        if (!File::exists($invoicesPath)) {
            File::makeDirectory($invoicesPath, 0755, true);
        }
        
        // Nom du fichier
        $filename = 'facture_' . $expense->expense_number . '_' . $expense->id . '.html';
        $filePath = $invoicesPath . '/' . $filename;
        
        // Sauvegarder le fichier
        File::put($filePath, $htmlContent);
        
        // Optionnel : sauvegarder le chemin dans la base de données
        $expense->update(['html_file_path' => 'invoices/' . $filename]);
    }

    /**
     * Supprime le fichier HTML de la facture
     */
    private function deleteInvoiceHtml($expense)
    {
        $filename = 'facture_' . $expense->expense_number . '_' . $expense->id . '.html';
        $filePath = public_path('invoices/' . $filename);
        
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        
        // Nettoyer le chemin dans la base de données
        $expense->update(['html_file_path' => null]);
    }

    /**
     * Génère le contenu HTML de la facture
     */
    private function getInvoiceHtmlContent($expense)
    {
        $total = $expense->lines->sum(function($line) {
            return $line->unit_price * $line->quantity;
        });

        $htmlContent = '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Facture ' . $expense->expense_number . '</title>
                <style>
                    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
                    .invoice { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                    .header h1 { color: #333; margin: 0; }
                    .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
                    .info-box { width: 45%; }
                    .info-box h3 { background: #f0f0f0; padding: 10px; margin: 0 0 10px 0; border-left: 4px solid #007cba; }
                    .info-row { margin-bottom: 8px; }
                    .label { font-weight: bold; display: inline-block; width: 120px; }
                    .lines-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .lines-table th { background: #333; color: white; padding: 12px; text-align: left; }
                    .lines-table td { padding: 12px; border-bottom: 1px solid #ddd; }
                    .lines-table tr:nth-child(even) { background: #f9f9f9; }
                    .total-section { text-align: right; margin-top: 20px; border-top: 2px solid #333; padding-top: 15px; }
                    .total-row { margin-bottom: 5px; }
                    .final-total { font-size: 1.2em; font-weight: bold; color: #007cba; }
                    .status { display: inline-block; padding: 5px 15px; border-radius: 20px; color: white; font-weight: bold; }
                    .status-paid { background: #28a745; }
                    .status-pending { background: #ffc107; color: #333; }
                    .status-overdue { background: #dc3545; }
                    .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; }
                </style>
            </head>
            <body>
                <div class="invoice">
                    <div class="header">
                        <h1>FACTURE</h1>
                        <h2>N° ' . $expense->expense_number . '</h2>
                        <div class="status status-' . strtolower($expense->expenses_status->name ?? 'pending') . '">
                            Statut: ' . ($expense->expenses_status->name ?? 'En attente') . '
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="info-box">
                            <h3>CLIENT</h3>
                            <div class="info-row">
                                <span class="label">Nom:</span>
                                ' . ($expense->quote->project->customer->name ?? 'N/A') . '
                            </div>
                            <div class="info-row">
                                <span class="label">Email:</span>
                                ' . ($expense->quote->project->customer->email ?? 'N/A') . '
                            </div>
                            <div class="info-row">
                                <span class="label">Projet:</span>
                                ' . ($expense->quote->project->name ?? 'N/A') . '
                            </div>
                        </div>

                        <div class="info-box">
                            <h3>FACTURE</h3>
                            <div class="info-row">
                                <span class="label">Date édition:</span>
                                ' . \Carbon\Carbon::parse($expense->date_edition)->format('d/m/Y') . '
                            </div>
                            ' . ($expense->date_payment_limit ? '<div class="info-row"><span class="label">Date paiement:</span>' . \Carbon\Carbon::parse($expense->date_payment_limit)->format('d/m/Y') . '</div>' : '') . '
                            ' . ($expense->type_payment_limit ? '<div class="info-row"><span class="label">Type paiement:</span>' . $expense->type_payment . '</div>' : '') . '
                        </div>
                    </div>

                    <table class="lines-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>';

        // Ajouter les lignes de la facture
        foreach($expense->lines as $line) {
            $lineTotal = $line->unit_price * $line->quantity;
            $htmlContent .= '<tr>
                    <td>' . htmlspecialchars($line->wording) . '</td>
                    <td>' . number_format($line->unit_price, 2) . ' €</td>
                    <td>' . $line->quantity . '</td>
                    <td>' . number_format($lineTotal, 2) . ' €</td>
                </tr>';
        }

        $htmlContent .= '</tbody>
                    </table>

                    <div class="total-section">
                        <div class="total-row">Sous-total: ' . number_format($total, 2) . ' €</div>
                        <div class="total-row">TVA (20%): ' . number_format($total * 0.20, 2) . ' €</div>
                        <div class="total-row final-total">TOTAL TTC: ' . number_format($total * 1.20, 2) . ' €</div>
                    </div>

                    <div class="footer">
                        <p>Facture générée automatiquement le ' . now()->format('d/m/Y à H:i') . '</p>
                        <p>Fichier: facture_' . $expense->expense_number . '_' . $expense->id . '.html</p>
                    </div>
                </div>
            </body>
        </html>';

        return $htmlContent;
    }

    /**
     * Télécharger le fichier HTML de la facture
     */
    public function downloadInvoice($id)
    {
        $expense = Expenses::findOrFail($id);
        $filename = 'facture_' . $expense->expense_number . '_' . $expense->id . '.html';
        $filePath = public_path('invoices/' . $filename);
        
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }
        
        return redirect()->back()->with('error', 'Fichier HTML de la facture introuvable.');
    }
}