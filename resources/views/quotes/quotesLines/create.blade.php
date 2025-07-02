@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-gray-900 border-2 border-gray-700 rounded-2xl shadow-lg">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-red-400">Ajouter des lignes au devis</h2>
        <nav class="text-sm text-gray-400 mt-2">
            <a href="{{ route('quotes.index') }}" class="hover:text-red-400">Devis</a>
            <span class="mx-2">/</span>
            <a href="{{ route('quotes.show', $quote) }}" class="hover:text-red-400">Devis #{{ $quote->id }}</a>
            <span class="mx-2">/</span>
            <span class="text-red-400">Ajouter des lignes</span>
        </nav>
    </div>

    <!-- Informations du devis source -->
    <div class="mb-8 p-6 rounded-2xl border-2 border-gray-700 bg-gray-800">
        <h3 class="text-lg font-semibold text-white mb-4">Devis source</h3>
        <div class="grid grid-cols-2 gap-6 text-gray-300">
            <div>
                <p class="mb-2"><strong class="text-red-400">Devis :</strong> #{{ $quote->id }}</p>
                <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $quote->description }}</p>
                <p class="mb-2"><strong class="text-red-400">Montant actuel :</strong> 
                    <span class="text-green-400 font-bold">{{ number_format($quote->amount, 2) }} €</span>
                </p>
            </div>
            <div>
                <p class="mb-2"><strong class="text-red-400">Projet :</strong> {{ $quote->project->name }}</p>
                <p class="mb-2"><strong class="text-red-400">Client :</strong> {{ $quote->project->customer->name ?? 'Non assigné' }}</p>
                <p class="mb-2"><strong class="text-red-400">Date devis :</strong> {{ $quote->date_edition ? \Carbon\Carbon::parse($quote->date_edition)->format('d/m/Y') : 'Non définie' }}</p>
            </div>
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

    <form id="addLineForm" method="POST" action="{{ route('quotes.lines.store', $quote) }}" class="space-y-6">
        @csrf

        <div id="linesContainer" class="space-y-6">
            <!-- Ligne initiale -->
            <div class="line-item p-6 bg-gray-800 border border-gray-700 rounded-2xl space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-white font-semibold">Ligne #1</h4>
                    <span class="text-sm text-gray-400" id="total-0">Total: 0.00 €</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-red-400 text-sm font-medium">Description *</label>
                        <input type="text" name="lines[0][wording]" placeholder="Description de la ligne"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               value="{{ old('lines.0.text') }}" required>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-red-400 text-sm font-medium">Prix unitaire (€) *</label>
                        <input type="number" step="0.01" name="lines[0][unit_price]" placeholder="0.00"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent unit-price"
                               value="{{ old('lines.0.unit_price') }}" data-index="0" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-red-400 text-sm font-medium">Quantité *</label>
                        <input type="number" name="lines[0][quantity]" value="{{ old('lines.0.quantity', 1) }}" min="1"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent quantity"
                               data-index="0" required>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeLine(this)"
                                class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-4 rounded-2xl transition-colors">
                            Supprimer cette ligne
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="p-6 bg-gray-800 border border-gray-700 rounded-2xl">
            <div class="flex justify-between items-center text-lg">
                <span class="text-white font-semibold">Total des nouvelles lignes :</span>
                <span id="grandTotal" class="text-green-400 font-bold">0.00 €</span>
            </div>
            <div class="text-sm text-gray-400 mt-2">
                <span>Montant actuel du devis : {{ number_format($quote->amount, 2) }} €</span>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="button" onclick="addLine()"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-2xl transition-colors">
                <i class="fas fa-plus mr-2"></i>Ajouter une ligne
            </button>

            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-2xl transition-colors">
                <i class="fas fa-save mr-2"></i>Enregistrer les lignes
            </button>

            <a href="{{ route('quotes.show', $quote) }}"
               class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-3 px-6 rounded-2xl transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Annuler
            </a>
        </div>
    </form>
</div>

<script>
let lineIndex = 1;

function addLine() {
    const container = document.getElementById('linesContainer');
    const newLine = document.createElement('div');
    newLine.className = 'line-item p-6 bg-gray-800 border border-gray-700 rounded-2xl space-y-4';
    newLine.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-white font-semibold">Ligne #${lineIndex + 1}</h4>
            <span class="text-sm text-gray-400" id="total-${lineIndex}">Total: 0.00 €</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block mb-2 text-red-400 text-sm font-medium">Description *</label>
                <input type="text" name="lines[${lineIndex}][text]" placeholder="Description de la ligne"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       required>
            </div>
            
            <div>
                <label class="block mb-2 text-red-400 text-sm font-medium">Prix unitaire (€) *</label>
                <input type="number" step="0.01" name="lines[${lineIndex}][unit_price]" placeholder="0.00"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent unit-price"
                       data-index="${lineIndex}" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-2 text-red-400 text-sm font-medium">Quantité *</label>
                <input type="number" name="lines[${lineIndex}][quantity]" value="1" min="1"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent quantity"
                       data-index="${lineIndex}" required>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="removeLine(this)"
                        class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-4 rounded-2xl transition-colors">
                    Supprimer cette ligne
                </button>
            </div>
        </div>
    `;
    container.appendChild(newLine);
    lineIndex++;
    
    // Ajouter les event listeners pour les calculs
    attachCalculationListeners(newLine);
}

function removeLine(button) {
    const container = document.getElementById('linesContainer');
    if (container.children.length > 1) {
        button.closest('.line-item').remove();
        updateGrandTotal();
    } else {
        alert('Vous devez garder au moins une ligne.');
    }
}

function attachCalculationListeners(element = document) {
    const unitPriceInputs = element.querySelectorAll('.unit-price');
    const quantityInputs = element.querySelectorAll('.quantity');
    
    [...unitPriceInputs, ...quantityInputs].forEach(input => {
        input.addEventListener('input', function() {
            calculateLineTotal(this.dataset.index);
        });
    });
}

function calculateLineTotal(index) {
    const unitPrice = parseFloat(document.querySelector(`input[name="lines[${index}][unit_price]"]`).value) || 0;
    const quantity = parseInt(document.querySelector(`input[name="lines[${index}][quantity]"]`).value) || 0;
    const total = unitPrice * quantity;
    
    const totalElement = document.getElementById(`total-${index}`);
    if (totalElement) {
        totalElement.textContent = `Total: ${total.toFixed(2)} €`;
    }
    
    updateGrandTotal();
}

function updateGrandTotal() {
    let grandTotal = 0;
    
    document.querySelectorAll('.unit-price').forEach((input, index) => {
        const unitPrice = parseFloat(input.value) || 0;
        const quantityInput = document.querySelector(`input[name="lines[${input.dataset.index}][quantity]"]`);
        const quantity = parseInt(quantityInput ? quantityInput.value : 0) || 0;
        grandTotal += unitPrice * quantity;
    });
    
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' €';
}

// Initialiser les event listeners au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    attachCalculationListeners();
});
</script>
@endsection