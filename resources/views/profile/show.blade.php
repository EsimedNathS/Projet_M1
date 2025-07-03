@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-red-400">Profil utilisateur</h1>
                <p class="text-gray-400 mt-1">{{ $user->first_name }} {{ $user->last_name }}</p>
                @if($user->admin)
                    <span class="inline-block bg-red-600 text-white px-2 py-1 rounded-full text-xs mt-2">
                        Administrateur
                    </span>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ url()->previous() }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Informations personnelles -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations personnelles</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-3"><strong class="text-red-400">Prénom :</strong> {{ $user->first_name }}</p>
                    <p class="mb-3"><strong class="text-red-400">Nom :</strong> {{ $user->last_name }}</p>
                    <p class="mb-3"><strong class="text-red-400">Email :</strong> {{ $user->email }}</p>
                    <p class="mb-3"><strong class="text-red-400">Téléphone :</strong> {{ $user->phone ?? 'Non renseigné' }}</p>
                </div>
                <div>
                    <p class="mb-3"><strong class="text-red-400">Adresse :</strong> {{ $user->adresse ?? 'Non renseignée' }}</p>
                    <p class="mb-3"><strong class="text-red-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2 {{ $user->admin ? 'bg-red-600' : 'bg-blue-600' }} text-white">
                            {{ $user->admin ? 'Administrateur' : 'Utilisateur' }}
                        </span>
                    </p>
                    <p class="mb-3"><strong class="text-red-400">Compte créé le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Informations financières -->
        <form method="POST" action="{{ route('profile.update') }}" class="mb-8">
            @csrf
            @method('PATCH')

            <h2 class="text-2xl font-semibold text-white mb-4">Informations financières</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- CA Maximum modifiable -->
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <label for="ca_max" class="block text-red-400 font-semibold mb-3">CA Maximum</label>
                    <input 
                        type="text" 
                        name="ca_max" 
                        id="ca_max" 
                        value="{{ old('ca_max', $user->ca_max) }}" 
                        pattern="^\d+([.,]\d{1,2})?$" 
                        inputmode="decimal" 
                        class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-red-500" 
                        placeholder="Plafond autorisé" />
                </div>

                <!-- Charges (%) modifiable -->
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <label for="charges" class="block text-red-400 font-semibold mb-3">Charges (%)</label>
                    <input 
                        type="text" 
                        name="charges" 
                        id="charges" 
                        value="{{ old('charges', $user->charges) }}" 
                        pattern="^\d+([.,]\d{1,2})?$" 
                        inputmode="decimal" 
                        class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-red-500" 
                        placeholder="Ex: 15 pour 15%" />
                </div>

                <!-- Charges estimées calculées (non modifiable) -->
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <label class="block text-red-400 font-semibold mb-3">Charges estimées</label>
                    <p class="text-2xl font-bold text-white mb-2">
                        {{ number_format(($user->ca_max ?? 0) * (($user->charges ?? 0) / 100), 2) }} €
                    </p>
                    <p class="text-gray-400 text-sm">Calculées automatiquement</p>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>



        <!-- Statistiques de l'utilisateur -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Statistiques</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Projets</h3>
                    <p class="text-2xl font-bold text-white">{{ $userStats['projects_count'] ?? 0 }}</p>
                    <p class="text-gray-400 text-sm">Total des projets</p>
                </div>
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Clients</h3>
                    <p class="text-2xl font-bold text-white">{{ $userStats['customers_count'] ?? 0 }}</p>
                    <p class="text-gray-400 text-sm">Total des clients</p>
                </div>
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Devis</h3>
                    <p class="text-2xl font-bold text-white">{{ $userStats['quotes_count'] ?? 0 }}</p>
                    <p class="text-gray-400 text-sm">Total des devis</p>
                </div>
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Factures</h3>
                    <p class="text-2xl font-bold text-white">{{ $userStats['expenses_count'] ?? 0 }}</p>
                    <p class="text-gray-400 text-sm">Total des factures</p>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Actions rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('projects.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">📁</div>
                    <h3 class="text-white font-semibold">Nouveau projet</h3>
                    <p class="text-gray-400 text-sm">Créer un projet</p>
                </a>
                <a href="{{ route('customers.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">👥</div>
                    <h3 class="text-white font-semibold">Nouveau client</h3>
                    <p class="text-gray-400 text-sm">Ajouter un client</p>
                </a>
                <a href="{{ route('quotes.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">📋</div>
                    <h3 class="text-white font-semibold">Nouveau devis</h3>
                    <p class="text-gray-400 text-sm">Créer un devis</p>
                </a>
                <a href="{{ route('expenses.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">💰</div>
                    <h3 class="text-white font-semibold">Nouvelle facture</h3>
                    <p class="text-gray-400 text-sm">Ajouter une facture</p>
                </a>
            </div>
        </div>

        <!-- Activité récente (optionnel) -->
        @if(isset($recentActivity) && $recentActivity->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Activité récente</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                @foreach($recentActivity as $activity)
                <div class="flex items-center justify-between py-3 border-b border-gray-700 last:border-b-0">
                    <div>
                        <p class="text-white">{{ $activity->description }}</p>
                        <p class="text-gray-400 text-sm">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="px-2 py-1 bg-red-600 text-white text-xs rounded-full">
                        {{ $activity->type }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection