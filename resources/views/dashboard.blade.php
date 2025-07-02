@extends('layouts.app')

@section('content')
<div class="h-screen overflow-auto p-8 text-white">
    <!-- Résumé Trimestriel -->
    <div class="mb-8">
        <!-- Header + boutons -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-white">Résumé trimestriel</h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard', ['quarter_offset' => $quarterOffset - 1]) }}" 
                   class="text-red-400 border border-red-400 p-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-300">
                    ←
                </a>
                <a href="{{ route('dashboard', ['quarter_offset' => $quarterOffset + 1]) }}" 
                   class="text-red-400 border border-red-400 p-2 rounded-full hover:bg-red-600 hover:text-white transition-colors duration-300">
                    →
                </a>
            </div>
        </div>
        
        <!-- Contenu -->
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md">
            <div class="grid grid-cols-2 gap-6 text-red-400">
                <div>
                    <p><strong>Période actuelle :</strong> {{ $quarterlyData['periode'] }}</p>
                    <p><strong>CA payé :</strong> {{ number_format($quarterlyData['ca_paye'], 2, ',', ' ') }} €</p>
                    <p><strong>CA estimé :</strong> {{ number_format($quarterlyData['ca_estime'], 2, ',', ' ') }} €</p>
                </div>
                <div>
                    <p><strong>Charges à payer :</strong> {{ number_format($quarterlyData['charges_a_payer'], 2, ',', ' ') }} €</p>
                    <p><strong>Charges estimées à payer :</strong> {{ number_format($quarterlyData['charges_estimees'], 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé Annuel -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-white mb-4">Résumé annuel</h2>
        <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-red-400">
            <div>
                <p><strong>CA annuel :</strong> {{ number_format($annualData['ca_annuel_paye'], 2, ',', ' ') }} €</p>
                <p><strong>CA annuel max :</strong> {{ number_format($annualData['ca_annuel_max'], 2, ',', ' ') }} €</p>
                <p><strong>CA annuel restant à faire :</strong> {{ number_format($annualData['ca_restant'], 2, ',', ' ') }} €</p>
                <p><strong>Paiements en attente :</strong> {{ number_format($annualData['paiements_en_attente'], 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p><strong>Factures éditées non envoyées :</strong> {{ number_format($annualData['factures_editees'], 2, ',', ' ') }} €</p>
                
                @php
                    $percentageCompleted = $annualData['ca_annuel_max'] > 0 ? 
                        ($annualData['ca_annuel_paye'] / $annualData['ca_annuel_max']) * 100 : 0;
                @endphp
                
                <div class="mt-4">
                    <p><strong>Progression annuelle :</strong> {{ number_format($percentageCompleted, 1) }}%</p>
                    <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentageCompleted }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-8">
        <div class="bg-gray-900 p-6 rounded-lg text-white shadow-lg border-2 border-gray-700">
            <h3 class="text-xl font-semibold mb-4">CA Mensuel</h3>
            <div class="h-64">
                <canvas id="monthlyChart" class="w-full h-full"></canvas>
            </div>
        </div>
        
        <div class="bg-gray-900 p-6 rounded-lg text-white shadow-lg border-2 border-gray-700">
            <h3 class="text-xl font-semibold mb-4">CA Annuel Cumulé</h3>
            <div class="h-64">
                <canvas id="annualChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les graphiques -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour les graphiques
        const monthlyData = @json($monthlyChartData);
        const annualData = @json($annualChartData);
        
        console.log('Monthly data:', monthlyData);
        console.log('Annual data:', annualData);
        
        // Configuration commune
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: '#374151'
                    }
                },
                y: {
                    ticks: {
                        color: '#ffffff',
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'EUR',
                                minimumFractionDigits: 0
                            }).format(value);
                        }
                    },
                    grid: {
                        color: '#374151'
                    }
                }
            }
        };
        
        // Vérifier que les éléments canvas existent
        const monthlyCanvas = document.getElementById('monthlyChart');
        const annualCanvas = document.getElementById('annualChart');
        
        if (!monthlyCanvas || !annualCanvas) {
            console.error('Canvas elements not found');
            return;
        }
        
        // Graphique mensuel
        const monthlyCtx = monthlyCanvas.getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'CA Mensuel',
                    data: monthlyData.map(item => item.amount),
                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }]
            },
            options: commonOptions
        });
        
        // Graphique annuel cumulé
        const annualCtx = annualCanvas.getContext('2d');
        new Chart(annualCtx, {
            type: 'line',
            data: {
                labels: annualData.map(item => item.month),
                datasets: [{
                    label: 'CA Cumulé',
                    data: annualData.map(item => item.cumulative),
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: commonOptions
        });
        
        console.log('Charts initialized successfully');
    });
</script>
@endsection