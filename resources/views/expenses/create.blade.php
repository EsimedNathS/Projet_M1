@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-900 rounded-2xl p-8 shadow-lg text-white">
            @if(isset($quote))
                <h2 class="text-2xl font-semibold mb-6 text-red-400">Créer une facture à partir du devis</h2>
                
                <!-- Informations du devis source -->
                <div class="mb-8 p-6 rounded-2xl border-2 border-gray-700 bg-gray-800">
                    <h3 class="text-lg font-semibold text-white mb-4">Devis source</h3>
                    <div class="grid grid-cols-2 gap-6 text-gray-300">
                        <div>
                            <p class="mb-2"><strong class="text-red-400">Devis :</strong> #{{ $quote->id }}</p>
                            <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $quote->description }}</p>
                            <p class="mb-2"><strong class="text-red-400">Montant :</strong> 
                                <span class="text-green-400 font-bold">{{ number_format($quote->calculateAmount(), 2) }} €</span>
                            </p>
                        </div>
                        <div>
                            <p class="mb-2"><strong class="text-red-400">Projet :</strong> {{ $quote->project->name }}</p>
                            <p class="mb-2"><strong class="text-red-400">Client :</strong> {{ $quote->project->customer->name ?? 'Non assigné' }}</p>
                            <p class="mb-2"><strong class="text-red-400">Date devis :</strong> {{ $quote->date_edition ? \Carbon\Carbon::parse($quote->date_edition)->format('d/m/Y') : 'Non définie' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <h2 class="text-2xl font-semibold mb-6 text-red-400">Créer une nouvelle facture</h2>
                <p class="mb-6 text-gray-300">Veuillez sélectionner un devis existant pour créer une facture :</p>

                <!-- Formulaire de sélection d'un devis -->
                <form action="{{ route('expenses.create') }}" method="GET" class="mb-8">
                    <div class="flex items-center gap-4">
                        <select name="quote_id" required 
                                class="flex-1 p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                            <option value="">-- Sélectionnez un devis --</option>
                            @foreach($availableQuotes as $availableQuote)
                                <option value="{{ $availableQuote->id }}">
                                    Devis #{{ $availableQuote->id }} - {{ $availableQuote->description }} ({{ number_format($availableQuote->amount, 2) }} €)
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-500 transition-colors">
                            Sélectionner
                        </button>
                    </div>
                </form>

                <p class="mb-6 text-gray-400">Ou remplissez les champs manuellement si vous ne souhaitez pas partir d'un devis.</p>
            @endif

            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf

                @if(isset($quote))
                    <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                    <input type="hidden" name="project_id" value="{{ $quote->project_id }}">
                @else
                    <input type="hidden" name="quote_id" value="">
                    <input type="hidden" name="project_id" value="{{ old('project_id') }}">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Colonne gauche -->
                    <div>
                        <div class="mb-4">
                            <label for="product_name" class="block mb-2 text-red-400">Nom du produit / Description de la facture *</label>
                            <input type="text" name="product_name" id="product_name" 
                                value="{{ old('product_name', isset($quote) ? 'Facture - ' . $quote->description : '') }}"
                                class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white" 
                                required>
                            @error('product_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                        <div class="mb-4">
                            <label for="date_payment_limit" class="block mb-2 text-red-400">Date de paiement limite</label>
                            <input type="date" name="date_payment_limit" id="date_payment_limit" 
                                value="{{ old('date_payment', date('Y-m-d')) }}"
                                class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white" 
                                required>
                            @error('date_payment_limit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="date_payment_effect" class="block mb-2 text-red-400">Date de paiement effectif</label>
                            <input type="date" name="date_payment_effect" id="date_payment_effect" 
                                value="{{ old('date_payment') }}"
                                class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                            @error('date_payment_effect')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-400 text-sm mt-1">Laissez vide si non encore payé</p>
                        </div>
                    </div>

                    <!-- Colonne droite -->
                    <div>
                        <div class="mb-4">
                            <label for="type_payment" class="block mb-2 text-red-400">Moyen de paiement</label>
                            <select name="type_payment" id="type_payment" 
                                    class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                                <option value="">-- Sélectionnez un moyen de paiement --</option>
                                <option value="virement" {{ old('type_payment') == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="cheque" {{ old('type_payment') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                <option value="especes" {{ old('type_payment') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                <option value="carte" {{ old('type_payment') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="paypal" {{ old('type_payment') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="autre" {{ old('type_payment') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type_payment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(isset($customers) && $customers->count() > 0)
                        <div class="mb-4">
                            <label for="customer_id" class="block mb-2 text-red-400">Fournisseur</label>
                            <select name="customer_id" id="customer_id" 
                                    class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                                <option value="">-- Sélectionnez un fournisseur --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="expense_number" class="block mb-2 text-red-400">Numéro de dépense</label>
                            <input type="text" name="expense_number" id="expense_number" 
                                value="{{ old('expense_number', $suggestedNumber) }}"
                                class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white" 
                                placeholder="001">
                            @error('expense_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section des lignes -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-white mb-4">Lignes</h3>
                    
                    @if(isset($quote) && $quote->lines && $quote->lines->count() > 0)
                        <div class="mb-4 p-4 rounded-lg bg-gray-800 border border-gray-700">
                            <h4 class="text-red-400 font-semibold mb-4">Lignes du devis automatiquement reprises :</h4>
                            <div class="space-y-2">
                                @foreach($quote->lines as $index => $line)
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-3 rounded bg-gray-700 text-gray-300 border border-gray-600 mb-4">
                                        <div class="font-medium text-white flex-1">{{ $line->wording }}</div>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-400">
                                            <span>Quantité: <span class="text-white font-semibold">{{ $line->quantity }}</span></span>
                                            <span>PU: <span class="text-white font-semibold">{{ number_format($line->unit_price, 2) }} €</span></span>
                                            <span>Total: <span class="text-white font-semibold">{{ number_format($line->unit_price * $line->quantity, 2) }} €</span></span>
                                        </div>
                                        <input type="hidden" name="lines[{{ $index }}][wording]" value="{{ $line->wording }}">
                                        <input type="hidden" name="lines[{{ $index }}][unit_price]" value="{{ $line->unit_price }}">
                                        <input type="hidden" name="lines[{{ $index }}][quantity]" value="{{ $line->quantity }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('lines')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('lines.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes additionnelles -->
                <div class="mt-6">
                    <label for="notes" class="block mb-2 text-red-400">Notes / Commentaires</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white" 
                              placeholder="Notes additionnelles sur cette facture...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-700">
                    @if(isset($quote))
                        <a href="{{ route('quotes.show', $quote->id) }}" 
                           class="px-6 py-3 rounded-lg bg-gray-600 text-white hover:bg-gray-500 transition-colors">
                            &larr; Retour au devis
                        </a>
                    @else
                        <a href="{{ route('expenses.index') }}" 
                           class="px-6 py-3 rounded-lg bg-gray-600 text-white hover:bg-gray-500 transition-colors">
                            &larr; Retour à la liste des factures
                        </a>
                    @endif
                    
                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="save" 
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-500 transition-colors">
                            Créer la facture
                        </button>
                        <button type="submit" name="action" value="save_and_return" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-500 transition-colors">
                            Créer et retourner au devis
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('lines-container');

    // Copier les lignes du devis source dans le formulaire
    const copyLinesBtn = document.getElementById('copy-quote-lines');
    if(copyLinesBtn) {
        copyLinesBtn.addEventListener('click', () => {
            if (!container) return;
            container.innerHTML = ''; // Vide les lignes actuelles

            @if(isset($quote) && $quote->lines)
                const lines = @json($quote->lines->pluck('text'));
                lines.forEach(text => {
                    const div = document.createElement('div');
                    div.classList.add('mb-4', 'line-item');
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <input type="text" name="lines[]" value="${text}" class="flex-1 p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-white" placeholder="Description de la ligne" required>
                            <button type="button" class="remove-line bg-red-600 text-white px-3 py-3 rounded hover:bg-red-500 transition-colors" title="Supprimer cette ligne">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>`;
                    container.appendChild(div);
                });
            @endif
        });
    }
});
</script>
@endsection