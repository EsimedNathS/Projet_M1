@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-blue-400">Facture #{{ $expense->id }}</h1>
                <p class="text-gray-400 mt-1">{{ $expense->product_name }}</p>
                @if($expense->quote)
                    <p class="text-gray-500 text-sm">Issue du devis #{{ $expense->quote->id }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('expenses.edit', $expense->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ route('expenses.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Retour
                </a>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Informations du projet -->
        @if($expense->quote && $expense->quote->project)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations du projet</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-blue-400">Nom :</strong> {{ $expense->quote->project->name }}</p>
                    <p class="mb-2"><strong class="text-blue-400">Client :</strong> {{ $expense->quote->project->customer->name ?? 'Non assigné' }}</p>
                    <p class="mb-2"><strong class="text-blue-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2
                            @if($expense->quote->project->status && $expense->quote->project->status->name == 'Démarré') bg-green-600
                            @elseif($expense->quote->project->status && $expense->quote->project->status->name == 'En cours') bg-blue-600
                            @elseif($expense->quote->project->status && $expense->quote->project->status->name == 'Terminé') bg-gray-600
                            @else bg-yellow-600 @endif text-white">
                            {{ $expense->quote->project->status->name ?? 'Non défini' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="mb-2"><strong class="text-blue-400">Date début :</strong> {{ $expense->quote->project->date_start ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-blue-400">Date fin :</strong> {{ $expense->quote->project->date_end ?? 'Non définie' }}</p>
                    <p class="mb-2"><strong class="text-blue-400">Description :</strong> {{ $expense->quote->project->description ?? 'Aucune description' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Informations de la facture -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Détails de la facture</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-blue-400">Produit/Service :</strong> {{ $expense->product_name }}</p>
                    <p class="mb-2"><strong class="text-blue-400">Montant :</strong> 
                        <span class="text-2xl font-bold text-red-400">{{ number_format($expense->calculateAmount(), 2) }} €</span>
                    </p>
                    <p class="mb-2"><strong class="text-blue-400">Date d'édition :</strong> 
                        {{ $expense->date_edition ? \Carbon\Carbon::parse($expense->date_edition)->format('d/m/Y') : 'Non définie' }}
                    </p>
                    @if($expense->type_payment)
                    <p class="mb-2"><strong class="text-blue-400">Type de paiement :</strong> {{ $expense->type_payment }}</p>
                    @endif
                </div>
                <div>
                    @if($expense->date_payment_limit)
                    <p class="mb-2"><strong class="text-blue-400">Date prévue de paiement :</strong> 
                        {{ \Carbon\Carbon::parse($expense->date_payment_limit)->format('d/m/Y') }}
                    </p>
                    @endif
                    @if($expense->date_payment_effect)
                    <p class="mb-2"><strong class="text-blue-400">Date effective de paiement :</strong> 
                        <span class="text-green-400 font-semibold">{{ \Carbon\Carbon::parse($expense->date_payment_effect)->format('d/m/Y') }}</span>
                    </p>
                    @else
                    <p class="mb-2"><strong class="text-blue-400">Statut paiement :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2 bg-yellow-600 text-white">En attente</span>
                    </p>
                    @endif
                    @if($expense->expenses_status)
                    <p class="mb-2"><strong class="text-blue-400">Statut :</strong> 
                        <span class="px-2 py-1 rounded text-xs ml-2 bg-blue-600 text-white">
                            {{ $expense->expenses_status->name }}
                        </span>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($expense->note)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Notes</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                <p class="text-gray-300">{{ $expense->note }}</p>
            </div>
        </div>
        @endif

        <!-- Devis associé -->
        @if($expense->quote)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Devis associé</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2">Devis #{{ $expense->quote->id }}</h3>
                        <div class="grid grid-cols-2 gap-4 text-gray-300">
                            <div>
                                <p class="mb-1"><strong class="text-blue-400">Description :</strong> {{ $expense->quote->description }}</p>
                                <p class="mb-1"><strong class="text-blue-400">Montant devis :</strong> 
                                    <span class="text-green-400 font-bold">{{ number_format($expense->quote->calculateAmount(), 2) }} €</span>
                                </p>
                            </div>
                            <div>
                                <p class="mb-1"><strong class="text-blue-400">Statut :</strong> 
                                    <span class="px-2 py-1 rounded text-xs ml-2
                                        @if($expense->quote->status && $expense->quote->status->name == 'Accepté') bg-green-600
                                        @elseif($expense->quote->status && $expense->quote->status->name == 'En attente') bg-yellow-600
                                        @elseif($expense->quote->status && $expense->quote->status->name == 'Refusé') bg-red-600
                                        @else bg-gray-600 @endif text-white">
                                        {{ $expense->quote->status->name ?? 'Non défini' }}
                                    </span>
                                </p>
                                <p class="mb-1"><strong class="text-blue-400">Date édition :</strong> 
                                    {{ $expense->quote->date_edition ? \Carbon\Carbon::parse($expense->quote->date_edition)->format('d/m/Y') : 'Non définie' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('quotes.show', $expense->quote->id) }}" 
                           class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 transition-colors text-sm">
                            Voir le devis
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Lignes de la facture -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Lignes de la facture</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                <div class="space-y-3 mb-4">
                    @foreach($expense->lines as $line)
                        <div class="flex flex-col md:flex-row md:items-center justify-between p-3 bg-gray-800 rounded-lg">
                            <div class="flex items-center mb-2 md:mb-0">
                                <div class="w-2 h-2 bg-red-400 rounded-full mr-3"></div>
                                <p class="text-gray-300 font-medium">{{ $line->wording }}</p>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center text-gray-400 text-sm gap-4">
                                <span>Quantité: <span class="text-white font-semibold">{{ $line->quantity }}</span></span>
                                <span>Prix unitaire: <span class="text-white font-semibold">{{ number_format($line->unit_price, 2) }} €</span></span>
                                <span>Total: <span class="text-white font-semibold">{{ number_format($line->unit_price * $line->quantity, 2) }} €</span></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Reçu/Justificatif -->
        @if($expense->html_file_path)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Justificatif</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-blue-400 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Justificatif disponible</p>
                                <p class="text-gray-400 text-sm">Facture N° {{ $expense->expense_number }}</p>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex space-x-3">
                            <!-- Bouton Voir -->
                            <button 
                                onclick="openInvoiceModal()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Voir</span>
                            </button>
                            
                            <!-- Bouton Télécharger -->
                            <a href="{{ asset($expense->html_file_path) }}" 
                            download="facture_{{ $expense->expense_number }}.html"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Télécharger</span>
                            </a>
                            
                            <!-- Bouton Ouvrir dans nouvel onglet -->
                            <a href="{{ asset($expense->html_file_path) }}" 
                            target="_blank"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                <span>Nouvel onglet</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL POUR AFFICHER LA FACTURE -->
            <div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
                    <!-- En-tête de la modal -->
                    <div class="bg-gray-900 text-white p-4 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Facture N° {{ $expense->expense_number }}</h3>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Bouton Télécharger dans la modal -->
                            <a href="{{ asset($expense->html_file_path) }}" 
                            download="facture_{{ $expense->expense_number }}.html"
                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors text-sm">
                                Télécharger
                            </a>
                            
                            <!-- Bouton Fermer -->
                            <button 
                                onclick="closeInvoiceModal()"
                                class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Contenu de la modal -->
                    <div class="overflow-auto max-h-[calc(90vh-80px)]">
                        <iframe 
                            id="invoiceFrame"
                            src="{{ asset($expense->html_file_path) }}"
                            class="w-full h-full min-h-[600px]"
                            frameborder="0">
                        </iframe>
                    </div>
                </div>
            </div>

            <script>
            function openInvoiceModal() {
                const modal = document.getElementById('invoiceModal');
                const iframe = document.getElementById('invoiceFrame');
                
                // Afficher la modal
                modal.classList.remove('hidden');
                
                // Ajouter un effet de transition
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                }, 10);
                
                // Empêcher le scroll de la page
                document.body.style.overflow = 'hidden';
            }

            function closeInvoiceModal() {
                const modal = document.getElementById('invoiceModal');
                
                // Masquer la modal
                modal.classList.add('hidden');
                modal.classList.remove('opacity-100');
                
                // Réactiver le scroll de la page
                document.body.style.overflow = 'auto';
            }

            // Fermer la modal avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeInvoiceModal();
                }
            });

            // Fermer la modal en cliquant sur l'arrière-plan
            document.getElementById('invoiceModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeInvoiceModal();
                }
            });
            </script>

            @else
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Justificatif</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg">Aucun justificatif disponible</p>
                        <p class="text-sm mt-2">Le fichier HTML sera généré automatiquement lors de la création de la facture</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Styles pour la modal */
#invoiceModal {
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    #invoiceModal .bg-white {
        margin: 10px;
        max-height: calc(100vh - 20px);
    }
    
    .flex.space-x-3 {
        flex-direction: column;
        space-x: 0;
        gap: 8px;
    }
    
    .flex.space-x-3 > * {
        width: 100%;
        justify-content: center;
    }
}
</style>

@endsection