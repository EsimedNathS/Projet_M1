<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use App\Models\Expenses;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectsStatus;
use App\Models\Quotes;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        // Gestion de la recherche
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('customer', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        // Gestion du filtre par statut
        if ($request->filled('status')) {
            $query->whereIn('status_id', $request->status);
        } else {
            // Filtre par défaut : seulement les projets "démarré"
            $defaultStatus = ProjectsStatus::where('name', 'démarré')->first();
            if ($defaultStatus) {
                $query->where('status_id', $defaultStatus->id);
            }
        }

        // Liste des colonnes autorisées pour le tri
        $allowedSorts = ['name', 'customer', 'status', 'date_start', 'date_end'];

        // Gestion du tri
        $sort = $request->get('sort', 'name'); // Par défaut tri par 'name'
        $direction = $request->get('direction', 'asc'); // Par défaut tri ascendant

        if (in_array($sort, $allowedSorts)) {
            if ($sort == 'customer') {
                $query->leftJoin('customers', 'projects.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $direction)
                    ->select('projects.*'); // Important pour éviter les colonnes ambigües
            } elseif ($sort == 'status') {
                $query->leftJoin('project_statuses', 'projects.status_id', '=', 'project_statuses.id')
                    ->orderBy('project_statuses.name', $direction)
                    ->select('projects.*'); // Important ici aussi
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            // Tri par défaut sécurisé
            $query->orderBy('name', 'asc');
        }

        $projects = $query->paginate(15);
        $project_status = ProjectsStatus::all();

        // Passer le statut par défaut à la vue
        $defaultStatusId = ProjectsStatus::where('name', 'démarré')->first()?->id;

        return view('projects.index', [
            'projects' => $projects,
            'search' => $request->search,
            'project_status' => $project_status,
            'defaultStatusId' => $defaultStatusId
        ]);
    }

    public function create()
    {
        $customers = Customer::all();
        $statuses = ProjectsStatus::all();
        return view('projects.create', compact('customers', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,customer_id',
            'status_id' => 'required|exists:statuses,status_id',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index')->with('success', 'Projet créé avec succès.');
    }

    public function edit(Project $project)
    {
        $customers = Customer::all();
        $statuses = ProjectsStatus::all();
        return view('projects.edit', compact('project', 'customers', 'statuses'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,customer_id',
            'status_id' => 'required|exists:statuses,status_id',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Projet mis à jour avec succès.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projet supprimé avec succès.');
    }

    public function show(Project $project)
    {
        $quotes = $project->quotes;

        // Récupérer toutes les dépenses associées aux quotes du projet
        $expenses = \App\Models\Expenses::whereIn('quote_id', $quotes->pluck('id'))->get();

        return view('projects.show', compact('project', 'quotes', 'expenses'));
    }
}
