<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use App\Models\Status;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort');

        $query = Project::with('customer', 'status');

        // Recherche sur le nom du projet ou le nom du client
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        // Tri dynamique
        if ($sort == 'name') {
            $query->orderBy('name');
        } elseif ($sort == 'customer') {
            $query->join('customers', 'projects.customer_id', '=', 'customers.id')
                  ->orderBy('customers.name')
                  ->select('projects.*'); // Important pour la pagination
        } elseif ($sort == 'status') {
            $query->join('projects_status', 'projects.status_id', '=', 'projects_status.id')
                  ->orderBy('projects_status.name')
                  ->select('projects.*');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $projects = $query->paginate(10);

        return view('projects.index', compact('projects', 'search', 'sort'));
    }

    public function create()
    {
        $customers = Customer::all();
        $status = Status::all();
        return view('projects.create', compact('customers', 'status'));
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
        $statuses = Status::all();
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
}
