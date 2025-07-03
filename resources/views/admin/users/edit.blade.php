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
                <a href="{{ route('users.edit', $user->id) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ url()->previous() }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    
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
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations financières</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <h3 class="text-red-400 font-semibold mb-3">CA Maximum</h3>
                    <p class="text-2xl font-bold text-white mb-2">
                        {{ $user->formatted_ca_max }}
                    </p>
                    <p class="text-gray-400 text-sm">Plafond autorisé</p>
                </div>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <h3 class="text-red-400 font-semibold mb-3">Charges</h3>
                    <p class="text-2xl font-bold text-white mb-2">
                        {{ $user->formatted_charges }}
                    </p>
                    <p class="text-gray-400 text-sm">Charges déclarées</p>
                </div>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <h3 class="text-red-400 font-semibold mb-3">CA Réalisé</h3>
                    <p class="text-2xl font-bold text-white mb-2">
                        {{ number_format($userStats['total_ca'] ?? 0, 2) }} €
                    </p>
                    @if($user->ca_max)
                        <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ min(100, $user->getCARatio()) }}%"></div>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">{{ number_format($user->getCARatio(), 1) }}% du plafond</p>
                    @else
                        <p class="text-gray-400 text-sm">Pas de plafond défini</p>
                    @endif
                </div>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <h3 class="text-red-400 font-semibold mb-3">Résultat Net</h3>
                    <p class="text-2xl font-bold {{ ($userStats['total_ca'] - $userStats['total_expenses']) >= 0 ? 'text-green-400' : 'text-red-400' }} mb-2">
                        {{ number_format(($userStats['total_ca'] ?? 0) - ($userStats['total_expenses'] ?? 0), 2) }} €
                    </p>
                    <p class="text-gray-400 text-sm">CA - Dépenses</p>
                </div>
            </div>
        </div>

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
                <a href="{{ route('quotes.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">📋</div>
                    <h3 class="text-white font-semibold">Nouveau devis</h3>
                    <p class="text-gray-400 text-sm">Créer un devis</p>
                </a>
                <a href="{{ route('expenses.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">💰</div>
                    <h3 class="text-white font-semibold">Nouvelle dfacture</h3>
                    <p class="text-gray-400 text-sm">Ajouter une facture</p>
                </a>
                <a href="{{ route('customers.create') }}" class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 hover:bg-gray-800 shadow-md text-center transition-colors">
                    <div class="text-red-400 text-2xl mb-2">👥</div>
                    <h3 class="text-white font-semibold">Nouveau client</h3>
                    <p class="text-gray-400 text-sm">Ajouter un client</p>
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