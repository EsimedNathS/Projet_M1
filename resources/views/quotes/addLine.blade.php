@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-gray-900 border-2 border-gray-700 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold mb-6 text-red-400">Ajouter des lignes au devis</h2>

    <!-- Informations du devis source -->
    <div class="mb-8 p-6 rounded-2xl border-2 border-gray-700 bg-gray-800">
        <h3 class="text-lg font-semibold text-white mb-4">Devis source</h3>
        <div class="grid grid-cols-2 gap-6 text-gray-300">
            <div>
                <p class="mb-2"><strong class="text-red-400">Devis :</strong> #{{ $quote->id }}</p>
                <p class="mb-2"><strong class="text-red-400">Description :</strong> {{ $quote->description }}</p>
                <p class="mb-2"><strong class="text-red-400">Montant :</strong> 
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

    <form id="addLineForm" method="POST" action="{{ route('quotes.addLine', $quote) }}" class="space-y-6">
        @csrf

        <div id="linesContainer" class="space-y-6">
            <!-- Ligne initiale -->
            <div class="line-item p-6 bg-gray-800 border border-gray-700 rounded-2xl space-y-4">
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-red-400 text-sm">Description *</label>
                        <input type="text" name="lines[0][text]" placeholder="Description de la ligne"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                               required>
                    </div>

                    <div>
                        <label class="block mb-2 text-red-400 text-sm">Prix unitaire (€) *</label>
                        <input type="number" step="0.01" name="lines[0][unit_price]" placeholder="0.00"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                               required>
                    </div>

                    <div>
                        <label class="block mb-2 text-red-400 text-sm">Quantité *</label>
                        <input type="number" name="lines[0][quantity]" value="1" min="1"
                               class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                               required>
                    </div>
                </div>

                <button type="button" onclick="removeLine(this)"
                        class="mt-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 px-4 rounded-2xl">
                    Supprimer cette ligne
                </button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="button" onclick="addLine()"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-2xl">
                Ajouter une ligne
            </button>

            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-2xl">
                Enregistrer les lignes
            </button>
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
        <div class="space-y-4">
            <div>
                <label class="block mb-2 text-red-400 text-sm">Description *</label>
                <input type="text" name="lines[\${lineIndex}][text]" placeholder="Description de la ligne"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                       required>
            </div>

            <div>
                <label class="block mb-2 text-red-400 text-sm">Prix unitaire (€) *</label>
                <input type="number" step="0.01" name="lines[\${lineIndex}][unit_price]" placeholder="0.00"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                       required>
            </div>

            <div>
                <label class="block mb-2 text-red-400 text-sm">Quantité *</label>
                <input type="number" name="lines[\${lineIndex}][quantity]" value="1" min="1"
                       class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                       required>
            </div>
        </div>

        <button type="button" onclick="removeLine(this)"
                class="mt-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 px-4 rounded-2xl">
            Supprimer cette ligne
        </button>
    `;
    container.appendChild(newLine);
    lineIndex++;
}

function removeLine(button) {
    const container = document.getElementById('linesContainer');
    if (container.children.length > 1) {
        button.closest('.line-item').remove();
    } else {
        alert('Vous devez garder au moins une ligne.');
    }
}
</script>
@endsection
