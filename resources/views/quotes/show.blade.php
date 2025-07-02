@extends('layouts.app')

@section('content')

@php
    $isEditable = $quote->status && in_array($quote->status->name, ['Créé']);
@endphp

<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-red-400">Devis #{{ $quote->id }}</h1>
                <p class="text-gray-400 mt-1">{{ $quote->description }}</p>
                <p class="text-gray-500 text-sm">Projet : {{ $quote->project->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('quotes.edit', $quote->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ route('quotes.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Retour
                </a>
            </div>
            <div class="flex row space-x-2">
                @if ($quote->status)
                    @php
                        $hasLines = $quote->lines->isNotEmpty();
                    @endphp

                    @if ($quote->status && in_array($quote->status->name, ['Créé', 'Envoyé']))
                        <form action="{{ $quote->status->name === 'Créé' ? route('quotes.send', $quote->id) : route('quotes.validate', $quote->id) }}" method="POST" class="relative group inline-block">
                            @csrf
                            <button
                                type="submit"
                                @if (!$hasLines) disabled @endif
                                class="px-4 py-2 rounded-lg transition-colors
                                    {{ $hasLines
                                        ? ($quote->status->name === 'Créé' ? 'bg-blue-500 text-white hover:bg-blue-400' : 'bg-green-500 text-white hover:bg-green-400')
                                        : 'bg-gray-500 text-gray-600 cursor-not-allowed' }}"
                            >
                                {{ $quote->status->name === 'Créé' ? 'Envoyer le devis' : 'Valider le devis' }}
                            </button>

                            @if (!$hasLines)
                                <!-- Tooltip personnalisé -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-red-500 text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                    Pas de ligne pour ce devis
                                </div>
                            @endif
                        </form>
                    @endif
                @endif

                @php
                    $canCreateInvoice = $quote->status && $quote->status->name === 'Validé';
                @endphp

                <form action="{{ route('expenses.create') }}" method="GET" class="relative group inline-block">
                    <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                    @csrf
                    <button
                        type="submit"
                        @unless($canCreateInvoice) disabled @endunless
                        class="px-4 py-2 rounded-lg transition-colors
                            {{ $canCreateInvoice 
                                ? 'bg-green-600 text-white hover:bg-green-500' 
                                : 'bg-gray-300 text-gray-600 cursor-not-allowed' }}"
                    >
                        Créer une facture
                    </button>

                    @unless($canCreateInvoice)
                        <!-- Tooltip personnalisé -->
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-3 py-1 bg-gray-800 text-red-500 text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                            Le devis doit être au statut validé
                        </div>
                    @endunless
                </form>

            </div>
        </div>

        <!-- Informations du projet -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations du projet</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-red-400">Nom :</strong> {{ $quote->project->name }}</p>
                    <p class="mb-2"><strong class="text-red-400">Client :</strong> {{ $quote->project->customer->name ?? 'Non assigné' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2
                            @if($quote->project->status && $quote->project->status->name == 'Démarré') bg-green-600
                            @elseif($quote->project->status && $quote->project->status->name == 'En cours') bg-blue-600
                            @elseif($quote->project->status && $quote->project->status->name == 'Terminé') bg-gray-600
                            @else bg-yellow-600 @endif text-white">
                            {{ $quote->project->status->name ?? 'Non défini' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="mb-2"><strong class="text-red-400">Date début :</strong> {{ $quote->project->date_start ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Date fin :</strong> {{ $quote->project->date_end ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $quote->project->description ?? 'Aucune description' }}</p>
                </div>
            </div>
        </div>

        <!-- Informations du devis -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Détails du devis</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $quote->description }}</p>
                    <p class="mb-2"><strong class="text-red-400">Montant :</strong> 
                        <span class="text-2xl font-bold text-green-400">{{ number_format($quote->calculateAmount(), 2) }} €</span>
                    </p>
                    <p class="mb-2"><strong class="text-red-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2
                            @if($quote->status && $quote->status->name == 'Accepté') bg-green-600
                            @elseif($quote->status && $quote->status->name == 'En attente') bg-yellow-600
                            @elseif($quote->status && $quote->status->name == 'Refusé') bg-red-600
                            @else bg-gray-600 @endif text-white">
                            {{ $quote->status->name ?? 'Non défini' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="mb-2"><strong class="text-red-400">Date d'édition :</strong> {{ $quote->date_edition ? \Carbon\Carbon::parse($quote->date_edition)->format('d/m/Y') : 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Créé le :</strong> {{ $quote->created_at->format('d/m/Y à H:i') }}</p>
                    <p class="mb-2"><strong class="text-red-400">Modifié le :</strong> {{ $quote->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Lignes du devis -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Lignes du devis</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                <div class="space-y-3 mb-4">
                    @foreach($quote->lines as $line)
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between p-3 bg-gray-800 rounded-lg">
                        <!-- Informations de la ligne -->
                        <div class="flex items-center mb-2 lg:mb-0 flex-1">
                            <div class="w-2 h-2 bg-red-400 rounded-full mr-3"></div>
                            <p class="text-gray-300 font-medium">{{ $line->wording }}</p>
                        </div>
                        
                        <!-- Détails prix et quantité -->
                        <div class="flex flex-col md:flex-row md:items-center text-gray-400 text-sm gap-4 mb-3 lg:mb-0">
                            <span>Quantité: <span class="text-white font-semibold">{{ $line->quantity }}</span></span>
                            <span>Prix unitaire: <span class="text-white font-semibold">{{ number_format($line->unit_price, 2) }} €</span></span>
                            <span>Total: <span class="text-white font-semibold">{{ number_format($line->unit_price * $line->quantity, 2) }} €</span></span>
                        </div>
                        
                        <!-- Boutons d'action -->
                        @if ($isEditable)
                            <div class="flex gap-2 lg:ml-4">
                                <!-- Bouton Modifier -->
                                <a href="{{ route('quotes.lines.edit', [$quote, $line]) }}" 
                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg transition-colors duration-200 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Modifier
                                </a>
                                
                                <!-- Bouton Supprimer -->
                                <form action="{{ route('quotes.lines.destroy', [$quote, $line]) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')"
                                            class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg transition-colors duration-200 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- Bouton pour ajouter des lignes -->
                @if ($isEditable)
                    <div class="mt-4">
                        <a href="{{ route('quotes.lines.create', $quote) }}"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-2xl transition-colors duration-200 flex items-center gap-2 w-fit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter des lignes
                        </a>
                    </div>
                @endif
            </div>
        </div>


        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Section des dépenses associées -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-white">Factures associées</h2>
                @if($quote->status && $quote->status->name == 'Accepté')
                <a href="{{ route('expenses.create', ['quote' => $quote->id]) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-500 transition-colors">
                    Ajouter une dépense
                </a>
                @endif
            </div>

            @if($expenses && $expenses->count() > 0)
            <div class="space-y-4">
                @foreach($expenses as $expense)
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-white mb-2">{{ $expense->description }}</h3>
                            <div class="grid grid-cols-2 gap-4 text-gray-300">
                                <div>
                                    <p class="mb-1"><strong class="text-red-400">Numéro :</strong> 
                                        <span class="text-red-400 font-bold">{{ $expense->expense_number }}</span>
                                    </p>
                                    <p class="mb-1"><strong class="text-red-400">Date :</strong> 
                                        {{ $expense->date_payment_limit ? \Carbon\Carbon::parse($expense->date)->format('d/m/Y') : 'Non définie' }}
                                    </p>
                                </div>
                                <div>
                                    @if($expense->supplier)
                                    <p class="mb-1"><strong class="text-red-400">Fournisseur :</strong> {{ $expense->supplier->name }}</p>
                                    @endif
                                    @if($expense->category)
                                    <p class="mb-1"><strong class="text-red-400">Catégorie :</strong> {{ $expense->category->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('expenses.edit', $expense->id) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-500 transition-colors text-sm">
                                Modifier
                            </a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')"
                                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 transition-colors text-sm">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Récapitulatif financier -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 ">
                <div class="md:col-start-2">
                    <div class="p-4 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                        <h3 class="text-red-400 font-semibold mb-2">Total des factures</h3>
                        <p class="text-2xl font-bold text-white">
                            {{ number_format($expenses->sum(function($expense) {
                                return $expense->calculateAmount();
                            }), 2) }} €
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg">Aucune facture associée à ce devis</p>
                    <p class="text-sm mt-2">Les factures apparaîtront ici une fois le devis accepté et facturé</p>
                </div>
                @if($quote->status && $quote->status->name == 'Accepté')
                <a href="{{ route('expenses.create', ['quote' => $quote->id]) }}" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-500 transition-colors inline-block">
                    Ajouter la première dépense
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection