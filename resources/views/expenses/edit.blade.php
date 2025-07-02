@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-blue-400">Modifier la Facture #{{ $expense->id }}</h1>
                <p class="text-gray-400 mt-1">{{ $expense->product_name }}</p>
                @if($expense->quote)
                    <p class="text-gray-500 text-sm">Issue du devis #{{ $expense->quote->id }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('expenses.show', $expense->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Annuler
                </a>
            </div>
        </div>

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire d'édition -->
        <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informations de base -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Informations de base</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product_name" class="block text-blue-400 font-semibold mb-2">Produit/Service *</label>
                            <input type="text" 
                                   id="product_name" 
                                   name="product_name" 
                                   value="{{ old('product_name', $expense->product_name) }}"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors"
                                   required>
                        </div>
                        <div>
                            <label for="type_payment" class="block text-blue-400 font-semibold mb-2">Type de paiement</label>
                            <select id="type_payment" 
                                    name="type_payment" 
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors">
                                <option value="">Sélectionner un type</option>
                                <option value="Virement" {{ old('type_payment', $expense->type_payment) == 'Virement' ? 'selected' : '' }}>Virement</option>
                                <option value="Chèque" {{ old('type_payment', $expense->type_payment) == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Espèces" {{ old('type_payment', $expense->type_payment) == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="Carte bancaire" {{ old('type_payment', $expense->type_payment) == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="Prélèvement" {{ old('type_payment', $expense->type_payment) == 'Prélèvement' ? 'selected' : '' }}>Prélèvement</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates de paiement -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Dates de paiement</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date_payment_limit" class="block text-blue-400 font-semibold mb-2">Date limite de paiement</label>
                            <input type="date" 
                                   id="date_payment_limit" 
                                   name="date_payment_limit" 
                                   value="{{ old('date_payment_limit', $expense->date_payment_limit ? \Carbon\Carbon::parse($expense->date_payment_limit)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors">
                        </div>
                        <div>
                            <label for="date_payment_effect" class="block text-blue-400 font-semibold mb-2">Date effective de paiement</label>
                            <input type="date" 
                                   id="date_payment_effect" 
                                   name="date_payment_effect" 
                                   value="{{ old('date_payment_effect', $expense->date_payment_effect ? \Carbon\Carbon::parse($expense->date_payment_effect)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut et devis -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Statut</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status_id" class="block text-blue-400 font-semibold mb-2">Statut</label>
                            <select id="status_id" 
                                    name="status_id" 
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors">
                                <option value="">Sélectionner un statut</option>
                                @foreach($expenseStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id', $expense->status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white mb-4">Notes</h2>
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
                    <label for="note" class="block text-blue-400 font-semibold mb-2">Note additionnelle</label>
                    <textarea id="note" 
                              name="note" 
                              rows="4"
                              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-400 transition-colors resize-none"
                              placeholder="Ajoutez des notes ou commentaires...">{{ old('note', $expense->note) }}</textarea>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('expenses.show', $expense->id) }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-500 transition-colors">
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        <!-- Informations du projet (lecture seule) -->
        @if($expense->quote && $expense->quote->project)
        <div class="mt-8 pt-8 border-t border-gray-700">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations du projet (lecture seule)</h2>
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
    </div>
</div>

<script>
// Script pour gérer l'affichage conditionnel des champs
document.addEventListener('DOMContentLoaded', function() {
    const removeReceiptCheckbox = document.getElementById('remove_receipt');
    const receiptInput = document.getElementById('receipt');
    
    if (removeReceiptCheckbox) {
        removeReceiptCheckbox.addEventListener('change', function() {
            if (this.checked) {
                receiptInput.disabled = true;
                receiptInput.classList.add('opacity-50');
            } else {
                receiptInput.disabled = false;
                receiptInput.classList.remove('opacity-50');
            }
        });
    }
});
</script>
@endsection