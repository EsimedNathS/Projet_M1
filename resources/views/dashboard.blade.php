@extends('layouts.app')

@section('content')
<div class="min-h-screen p-8 text-white">

    <!-- Résumé Trimestriel -->
    <div class="mb-8">
        <!-- Header + boutons -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-white">Résumé trimestriel</h2>
            <div class="flex space-x-2">
                <button class="text-red-400 border border-red-400 p-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-300">←</button>
                <button class="text-red-400 border border-red-400 p-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-300">→</button>
            </div>
        </div>

        <!-- Contenu -->
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
            <div class="grid grid-cols-2 gap-6 text-red-400">
                <div>
                    <p><strong>Période actuelle :</strong></p>
                    <p><strong>CA payé :</strong></p>
                    <p><strong>CA estimé :</strong></p>
                </div>
                <div>
                    <p><strong>Charges à payer :</strong></p>
                    <p><strong>Charges estimées à payer :</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé Annuel -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-white mb-4">Résumé annuel</h2>
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-red-400">
            <div>
                <p><strong>CA annuel :</strong></p>
                <p><strong>CA annuel max :</strong></p>
                <p><strong>CA annuel restant à faire :</strong></p>
            </div>
            <div>
                <p><strong>Factures éditées non envoyées :</strong></p>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gray-900 p-6 rounded-lg text-white shadow-lg border-2 border-gray-700">
            <h3 class="text-xl font-semibold mb-4">Mensuel</h3>
            <div class="w-full h-48 bg-red-500 bg-opacity-30 flex items-center justify-center rounded">Graphique</div>
        </div>
        <div class="bg-gray-900 p-6 rounded-lg text-white shadow-lg border-2 border-gray-700">
            <h3 class="text-xl font-semibold mb-4">Annuel</h3>
            <div class="w-full h-48 bg-red-500 bg-opacity-30 flex items-center justify-center rounded">Graphique</div>
        </div>
    </div>
</div>
@endsection
