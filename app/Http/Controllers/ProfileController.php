<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $user = auth()->user();

        $userStats = [
            'total_ca' => $user->getTotalCA(),
            'total_expenses' => $user->getTotalExpenses(),
            'projects_count' => $user->projects()->count(),
            'customers_count' => $user->customers()->count(),
            'quotes_count' => $user->quotes()->count(),
            'expenses_count' => $user->expenses()->count(),
        ];

        return view('profile.edit', compact('user', 'userStats'));
    }

    public function show()
    {
        $user = auth()->user();

        $userStats = [
            'total_ca' => $user->getTotalCA(),
            'total_expenses' => $user->getTotalExpenses(),
            'projects_count' => $user->projects()->count(),
            'customers_count' => $user->customers()->count(),
            'quotes_count' => $user->quotes()->count(),
            'expenses_count' => $user->expenses()->count(),
        ];

        return view('profile.show', compact('user', 'userStats'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Nettoyage si virgule utilisée pour les décimales
        if ($request->has('ca_max')) {
            $request->merge([
                'ca_max' => str_replace(',', '.', $request->input('ca_max'))
            ]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => ['nullable', 'regex:/^\+?[0-9\s\-]{7,15}$/'],
            'adresse' => 'nullable|string|max:500',
            'ca_max' => ['nullable', 'regex:/^\d+(\.\d{1,2})?$/'],
            'charges' => ['nullable', 'integer', 'between:0,100'],
        ], [
            'phone.regex' => 'Le numéro de téléphone invalide',
        ]);


        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès');
    }



    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
