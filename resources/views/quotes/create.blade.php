@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-12 p-6 bg-gray-900 rounded-2xl shadow-lg text-white">
    <h2 class="text-2xl font-semibold mb-6">Ajouter une nouvelle quote</h2>

    <form action="{{ route('quotes.store') }}" method="POST">
        @csrf

        <!-- Informations générales -->
        <div class="mb-4">
            <label for="description" class="block mb-2 text-red-400">Description</label>
            <input type="text" name="description" id="description" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500" required>
        </div>

        <div class="mb-4">
            <label for="date" class="block mb-2 text-red-400">Date</label>
            <input type="date" name="date" id="date" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div class="mb-4">
            <label for="status_id" class="block mb-2 text-red-400">Statut</label>
            <select name="status_id" id="status_id" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="project_id" class="block mb-2 text-red-400">Projet</label>
            @if(isset($project) && $project->id)
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="text" value="{{ $project->name }}" readonly
                    class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 text-gray-300 cursor-not-allowed">
            @else
                <select name="project_id" id="project_id" required
                        class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Sélectionnez un projet --</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                            {{ $proj->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Lignes de devis -->
        <div id="lines-container" class="space-y-6">
            <!-- Ligne initiale -->
            <div class="line-item p-6 bg-gray-800 border border-gray-700 rounded-2xl space-y-4" data-index="0">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-white font-semibold">Ligne #1</h4>
                    <span class="text-sm text-gray-400 total-label" id="total-0">Total: 0.00 €</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-red-400 text-sm font-medium">Description *</label>
                        <input type="text" name="lines[0][wording]" placeholder="Description de la ligne"
                            class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 text-red-400 text-sm font-medium">Prix unitaire (€) *</label>
                        <input type="number" step="0.01" name="lines[0][unit_price]" placeholder="0.00"
                            class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent unit-price"
                            data-index="0" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-red-400 text-sm font-medium">Quantité *</label>
                        <input type="number" name="lines[0][quantity]" value="1" min="1"
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

        <!-- Bouton pour ajouter une ligne -->
        <div class="mt-6">
            <button type="button" onclick="addLine()"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-2xl">
                Ajouter une ligne
            </button>
        </div>

        <!-- Bouton d'enregistrement -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 transition-colors">
                &larr; Retour
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-500 transition-colors">
                Enregistrer
            </button>
        </div>
    </form>
</div>

<script>
    let index = 1;

    function addLine() {
        const container = document.getElementById('lines-container');

        const template = `
        <div class="line-item p-6 bg-gray-800 border border-gray-700 rounded-2xl space-y-4" data-index="${index}">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-white font-semibold">Ligne #${index + 1}</h4>
                <span class="text-sm text-gray-400 total-label" id="total-${index}">Total: 0.00 €</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block mb-2 text-red-400 text-sm font-medium">Description *</label>
                    <input type="text" name="lines[${index}][wording]" placeholder="Description de la ligne"
                        class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400" required>
                </div>

                <div>
                    <label class="block mb-2 text-red-400 text-sm font-medium">Prix unitaire (€) *</label>
                    <input type="number" step="0.01" name="lines[${index}][unit_price]" placeholder="0.00"
                        class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 unit-price"
                        data-index="${index}" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-red-400 text-sm font-medium">Quantité *</label>
                    <input type="number" name="lines[${index}][quantity]" value="1" min="1"
                        class="w-full p-4 rounded-2xl bg-gray-700 border border-gray-600 text-white quantity"
                        data-index="${index}" required>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeLine(this)"
                        class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-4 rounded-2xl transition-colors">
                        Supprimer cette ligne
                    </button>
                </div>
            </div>
        </div>`;
        
        container.insertAdjacentHTML('beforeend', template);
        index++;
    }

    function removeLine(button) {
        button.closest('.line-item').remove();
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('unit-price') || e.target.classList.contains('quantity')) {
            updateLineTotal(e.target.dataset.index);
        }
    });

    function updateLineTotal(index) {
        const unitPriceInput = document.querySelector(`.unit-price[data-index="${index}"]`);
        const quantityInput = document.querySelector(`.quantity[data-index="${index}"]`);
        const totalLabel = document.getElementById(`total-${index}`);

        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = (unitPrice * quantity).toFixed(2);

        totalLabel.textContent = `Total: ${total} €`;
    }
</script>
@endsection
