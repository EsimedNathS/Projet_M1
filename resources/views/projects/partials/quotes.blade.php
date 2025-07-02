<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-white">Devis ({{ $quotes->count() }})</h2>
        <a href="{{ route('quotes.create', ['project' => $project->id]) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
            Ajouter une quote
        </a>
    </div>

    @if($quotes->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="py-4 px-2 text-left text-red-400">Description</th>
                        <th class="py-4 px-2 text-left text-red-400">Montant</th>
                        <th class="py-4 px-2 text-left text-red-400">Statut</th>
                        <th class="py-4 px-2 text-left text-red-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach ($quotes as $quote)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">{{ $quote->description }}</td>
                            <td class="py-4 px-2 font-semibold">{{ number_format($quote->calculateAmount(), 2) }} €</td>
                            <td class="py-4 px-2">
                                @php
                                    $statusName = $quote->status ? $quote->status->name : 'brouillon';
                                @endphp
                                <span class="px-2 py-1 rounded text-xs
                                    @if($statusName == 'acceptée')
                                        bg-green-600 text-white
                                    @elseif($statusName == 'en_attente')
                                        bg-yellow-600 text-white
                                    @elseif($statusName == 'refusée')
                                        bg-red-600 text-white
                                    @else
                                        bg-gray-600 text-white
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $statusName)) }}
                                </span>
                            </td>   
                            <td class="py-4 px-2 flex space-x-2">
                                <a href="{{ route('quotes.edit', [$quote->id]) }}" class="text-blue-400 hover:text-blue-300">Voir</a>
                                @if($quote->status == 'acceptée')
                                    <button onclick="convertToExpense({{ $quote->id }})" class="text-green-400 hover:text-green-300">
                                        Convertir en dépense
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center text-gray-400">
            <p>Aucune quote associée à ce projet.</p>
        </div>
    @endif
</div>
