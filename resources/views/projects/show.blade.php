@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-red-400">{{ $project->name }}</h1>
                <p class="text-gray-400 mt-1">Détails du projet</p>
                @if($project->customer)
                    <p class="text-gray-500 text-sm">Client : {{ $project->customer->name }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('projects.edit', $project->id) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ route('projects.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <!-- Informations du projet -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations projet</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-red-400">Nom :</strong> {{ $project->name }}</p>
                    <p class="mb-2"><strong class="text-red-400">Client :</strong> {{ $project->customer->name ?? 'Non assigné' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2
                            @if($project->status && $project->status->name == 'Démarré') bg-green-600
                            @elseif($project->status && $project->status->name == 'En cours') bg-blue-600
                            @elseif($project->status && $project->status->name == 'Terminé') bg-gray-600
                            @else bg-yellow-600 @endif text-white">
                            {{ $project->status->name ?? 'Non défini' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="mb-2"><strong class="text-red-400">Date début :</strong> {{ $project->date_start ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Date fin :</strong> {{ $project->date_end ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $project->description ?? 'Aucune description' }}</p>
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Résumé financier</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Total Devis</h3>
                    <p class="text-2xl font-bold text-white">
                        {{ number_format($quotes->sum(function($quote) {
                                return $quote->calculateAmount();
                            }), 2) }} €
                    </p>
                    <p class="text-gray-400 text-sm">{{ $quotes->count() }} quote(s)</p>
                </div>
                <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <h3 class="text-red-400 font-semibold mb-2">Total Factures</h3>
                    <p class="text-2xl font-bold text-white">                            
                        {{ number_format($expenses->sum(function($expense) {
                                return $expense->calculateAmount();
                            }), 2) }} €</p>
                    <p class="text-gray-400 text-sm">{{ $expenses->count() }} facture(s)</p>
                </div>
            </div>
        </div>

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Toggle & Sections -->
        <div class="mb-8">
            <!-- Toggle centré -->
            <div class="flex justify-center mb-8">
                <div class="inline-flex bg-gray-700 rounded-full p-1 space-x-2">
                    <button
                        id="quotes-btn"
                        class="bg-red-600 text-white px-6 py-2 rounded-full transition-colors font-semibold"
                        onclick="switchToTab('quotes')"
                        type="button">
                        Devis
                    </button>
                    <button
                        id="expenses-btn"
                        class="bg-gray-900 text-gray-400 hover:bg-gray-800 px-6 py-2 rounded-full transition-colors font-semibold"
                        onclick="switchToTab('expenses')"
                        type="button">
                        Dépenses
                    </button>
                </div>
            </div>

            <!-- Section Devis-->
            <div id="quotes-section">
                @include('projects.partials.quotes', ['quotes' => $quotes])
            </div>

            <!-- Expenses Section -->
            <div id="expenses-section" style="display: none;">
                @include('projects.partials.expenses', ['expenses' => $expenses])
            </div>
        </div>
    </div>
</div>

<script>
function switchToTab(tab) {
    // Cacher toutes les sections
    document.getElementById('quotes-section').style.display = 'none';
    document.getElementById('expenses-section').style.display = 'none';
    
    // Réinitialiser les styles des boutons
    document.getElementById('quotes-btn').className = 'bg-gray-900 text-gray-400 hover:bg-gray-800 px-6 py-2 rounded-full transition-colors font-semibold';
    document.getElementById('expenses-btn').className = 'bg-gray-900 text-gray-400 hover:bg-gray-800 px-6 py-2 rounded-full transition-colors font-semibold';
    
    // Afficher la section active et mettre à jour le bouton
    if (tab === 'quotes') {
        document.getElementById('quotes-section').style.display = 'block';
        document.getElementById('quotes-btn').className = 'bg-red-600 text-white px-6 py-2 rounded-full transition-colors font-semibold';
    } else if (tab === 'expenses') {
        document.getElementById('expenses-section').style.display = 'block';
        document.getElementById('expenses-btn').className = 'bg-red-600 text-white px-6 py-2 rounded-full transition-colors font-semibold';
    }
}

function convertToExpense(quoteId) {
    if (confirm('Voulez-vous convertir cette quote en dépense ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '#'.replace(':id', quoteId);

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection