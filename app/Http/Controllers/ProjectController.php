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
        $userId = auth()->id();
        $query = Project::whereHas('customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        // Recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($q2) use ($request) {
                        $q2->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Gestion des filtres de statut
        // Si des statuts sont spécifiés dans la requête, les utiliser
        if ($request->filled('status') && is_array($request->status)) {
            $query->whereIn('status_id', $request->status);
        }

        // Tri
        $allowedSorts = ['name', 'customer', 'status', 'date_start', 'date_end'];
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        if (in_array($sort, $allowedSorts)) {
            if ($sort == 'customer') {
                $query->leftJoin('customers', 'projects.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $direction)
                    ->select('projects.*');
            } elseif ($sort == 'status') {
                $query->leftJoin('project_status', 'projects.status_id', '=', 'project_status.id')
                    ->orderBy('project_status.name', $direction)
                    ->select('projects.*');
            } else {
                $query->orderBy($sort, $direction);
            }
        }

        $projects = $query->paginate(15);
        $project_status = ProjectsStatus::all();
        $defaultStatusId = ProjectsStatus::where('name', 'démarré')->first()?->id;

        // Passer les paramètres de recherche à la vue pour les conserver
        $search = $request->get('search');

        return view('projects.index', compact('projects', 'project_status', 'defaultStatusId', 'search'));
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
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:projects_status,id', 
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
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:projects_status,id',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);

        // Vérifier si les dates peuvent être modifiées
        $modifiableDates = in_array($project->status_id, [1, 2, 3]);
        
        // Préparer les données à mettre à jour
        $dataToUpdate = $request->only(['name', 'customer_id', 'status_id']);
        
        // Ajouter les dates seulement si elles sont modifiables
        if ($modifiableDates) {
            $dataToUpdate['date_start'] = $request->date_start;
            $dataToUpdate['date_end'] = $request->date_end;
        }
        
        $project->update($dataToUpdate);
        
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
