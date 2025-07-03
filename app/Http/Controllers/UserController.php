<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Quotes;
use App\Models\Expenses;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Charger les relations nécessaires si elles existent
        $user->load('customers');
        
        // Calculer les statistiques de l'utilisateur
        $userStats = [
            'projects_count' => $this->getUserProjectsCount($user),
            'quotes_count' => $this->getUserQuotesCount($user),
            'expenses_count' => $this->getUserExpensesCount($user),
            'customers_count' => $user->customers->count(),
            'total_ca' => $this->getUserTotalCA($user),
            'total_expenses' => $this->getUserTotalExpenses($user)
        ];

        // Activité récente (optionnel - à adapter selon vos besoins)
        $recentActivity = $this->getRecentActivity($user);

        return view('users.show', compact('user', 'userStats', 'recentActivity'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'ca_max' => 'nullable|numeric|min:0',
            'charges' => 'nullable|numeric|min:0',
            'admin' => 'boolean'
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user->id)->with('success', 'Profil mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }

    /**
     * Méthodes privées pour calculer les statistiques
     */
    private function getUserProjectsCount(User $user)
    {
        // Si vous avez une relation projects dans le modèle User
        if (method_exists($user, 'projects')) {
            return $user->projects()->count();
        }
        
        // Sinon, compter via les customers
        return Project::whereHas('customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    private function getUserQuotesCount(User $user)
    {
        // Adapter selon votre structure de données
        return Quotes::whereHas('project.customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    private function getUserExpensesCount(User $user)
    {
        // Adapter selon votre structure de données
        return Expenses::whereHas('project.customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    private function getUserTotalCA(User $user)
    {
        // Calculer le CA total basé sur les devis acceptés
        return Quotes::whereHas('project.customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'accepted') // ou votre logique de statut
        ->sum('total_amount'); // adapter le nom de la colonne
    }

    private function getUserTotalExpenses(User $user)
    {
        // Calculer le total des dépenses
        return Expenses::whereHas('project.customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->sum('amount'); // adapter le nom de la colonne
    }

    private function getRecentActivity(User $user)
    {
        // Exemple d'activité récente - à adapter selon vos besoins
        $activities = collect();

        // Récents projets
        $recentProjects = Project::whereHas('customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->take(3)->get();

        foreach ($recentProjects as $project) {
            $activities->push((object)[
                'description' => "Projet '{$project->name}' créé",
                'type' => 'project',
                'created_at' => $project->created_at ?? now()
            ]);
        }

        // Récents devis
        $recentQuotes = Quotes::whereHas('project.customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->take(2)->get();

        foreach ($recentQuotes as $quote) {
            $activities->push((object)[
                'description' => "Devis #{$quote->id} créé",
                'type' => 'quote',
                'created_at' => $quote->created_at ?? now()
            ]);
        }

        return $activities->sortByDesc('created_at')->take(5);
    }
}