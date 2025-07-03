<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-white">Dépenses ({{ $expenses->count() }})</h2>
        <a href="#" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
            Ajouter une facture
        </a>
    </div>

    @if($expenses->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="py-4 px-2 text-left text-red-400">Description</th>
                        <th class="py-4 px-2 text-left text-red-400">Montant</th>
                        <th class="py-4 px-2 text-left text-red-400">Date de paiement</th>
                        <th class="py-4 px-2 text-left text-red-400">Mode de paiement</th>
                        <th class="py-4 px-2 text-left text-red-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach ($expenses as $expense)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">{{ $expense->product_name }}</td>
                            <td class="py-4 px-2">{{ number_format($expense->calculateAmount(), 2) }} €</td>
                            <td class="py-4 px-2">
                                {{ $expense->date_payment_limit ? \Carbon\Carbon::parse($expense->date_payment_limit)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-4 px-2">{{ $expense->type_payment }}</td>
                            <td class="py-4 px-2 flex space-x-2">
                                <a href="{{ route('expenses.show', [$expense->id]) }}" class="text-blue-400 hover:text-blue-300">Voir</a>
                                @if(!$expense->quote_id)
                                    <form action="#" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center text-gray-400">
            <p>Aucune dépense associée à ce projet.</p>
        </div>
    @endif
</div>
