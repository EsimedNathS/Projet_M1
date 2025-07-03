<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $userId = auth()->id(); // Récupère l'ID de l'utilisateur connecté

        $search = $request->input('search');
        $sort = $request->input('sort', 'name'); // Tri par défaut
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['name', 'email', 'phone', 'address'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $customers = Customer::query()
            ->where('user_id', $userId) // 🔒 Restriction par utilisateur
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            })
            ->orderBy($sort, $direction)
            ->withCount('projects')
            ->paginate(10);

        return view('customers.index', compact('customers', 'search', 'sort', 'direction'));
    }


    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->projects()->exists()) {
            return redirect()->route('customers.index')->with('error', 'Cannot delete customer with associated projects.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function show(Customer $customer)
    {
        // Récupérer les projets du customer avec leurs informations
        $projects = $customer->projects()->with('status')->get();

        // Calculer les statistiques (tu peux adapter selon tes besoins)
        $caAnnuel = $projects->sum('montant_total') ?? 0; // Suppose que tu as un champ montant_total
        $caAnnuelMax = 100000; // À définir selon ta logique
        $caRestant = $caAnnuelMax - $caAnnuel;
        $facturesNonEnvoyees = 0; // À calculer selon ta logique

        return view('customers.show', compact('customer', 'projects', 'caAnnuel', 'caAnnuelMax', 'caRestant', 'facturesNonEnvoyees'));
    }
}