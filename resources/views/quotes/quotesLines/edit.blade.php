@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Modifier la ligne de devis</h1>
        
        <!-- Informations du devis -->
        <div class="mb-6 p-4 bg-gray-700 rounded-lg">
            <h2 class="text-lg font-medium text-gray-300 mb-2">Devis : {{ $quote->description }}</h2>
            <p class="text-gray-400 text-sm">
                Montant total : {{ number_format($quote->amount, 2) }} €
            </p>
        </div>

        <form action="{{ route('quotes.lines.update', [$quote, $line]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Libellé -->
            <div>
                <label for="wording" class="block mb-2 text-white font-medium">Libellé</label>
                <input
                    type="text"
                    name="wording"
                    id="wording"
                    value="{{ old('wording', $line->wording) }}"
                    required
                    placeholder="Description de la prestation"
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
                @error('wording')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prix unitaire -->
            <div>
                <label for="unit_price" class="block mb-2 text-white font-medium">Prix unitaire (€)</label>
                <input
                    type="number"
                    name="unit_price"
                    id="unit_price"
                    value="{{ old('unit_price', $line->unit_price) }}"
                    required
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
                @error('unit_price')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantité -->
            <div>
                <label for="quantity" class="block mb-2 text-white font-medium">Quantité</label>
                <input
                    type="number"
                    name="quantity"
                    id="quantity"
                    value="{{ old('quantity', $line->quantity) }}"
                    required
                    min="1"
                    placeholder="1"
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
                @error('quantity')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Aperçu du total -->
            <div class="p-4 bg-gray-700 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Total de cette ligne :</span>
                    <span class="text-white font-semibold text-lg" id="lineTotal">
                        {{ number_format($line->unit_price * $line->quantity, 2) }} €
                    </span>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('quotes.show', $quote) }}" 
                   class="text-red-400 hover:text-red-600 font-semibold transition-colors">
                    Annuler
                </a>
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
                >
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Calcul automatique du total
document.addEventListener('DOMContentLoaded', function() {
    const unitPriceInput = document.getElementById('unit_price');
    const quantityInput = document.getElementById('quantity');
    const lineTotalElement = document.getElementById('lineTotal');

    function updateTotal() {
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = unitPrice * quantity;
        lineTotalElement.textContent = total.toFixed(2) + ' €';
    }

    unitPriceInput.addEventListener('input', updateTotal);
    quantityInput.addEventListener('input', updateTotal);
});
</script>
@endsection