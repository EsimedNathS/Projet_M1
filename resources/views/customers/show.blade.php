@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium text-red-400">{{ $customer->name }}</h1>
                <p class="text-gray-400 mt-1">Détails du client</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                    Modifier
                </a>
                <a href="{{ url()->previous() }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <!-- Informations du client -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Informations client</h2>
            <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md grid grid-cols-2 gap-6 text-gray-300">
                <div>
                    <p class="mb-2"><strong class="text-red-400">Nom :</strong> {{ $customer->name }}</p>
                    <p class="mb-2"><strong class="text-red-400">Email :</strong> {{ $customer->email }}</p>
                </div>
                <div>
                    <p class="mb-2"><strong class="text-red-400">Téléphone :</strong> {{ $customer->phone ?? 'Non renseigné' }}</p>
                    <p class="mb-2"><strong class="text-red-400">Adresse :</strong> {{ $customer->address ?? 'Non renseignée' }}</p>
                </div>
            </div>
        </div>

        <!-- Projets du client -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Projets ({{ $projects->count() }})</h2>
            @if($projects->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-600">
                                <th class="py-4 px-2 text-left text-red-400">Nom du projet</th>
                                <th class="py-4 px-2 text-left text-red-400">Statut</th>
                                <th class="py-4 px-2 text-left text-red-400">Date début</th>
                                <th class="py-4 px-2 text-left text-red-400">Date fin</th>
                                <th class="py-4 px-2 text-left text-red-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-300">
                            @foreach ($projects as $project)
                                <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                                    <td class="py-4 px-2">
                                        <a href="{{ route('projects.show', $project) }}" 
                                        class="text-red-400 hover:text-red-300 hover:underline transition-colors">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-2">
                                        <span class="px-2 py-1 rounded text-xs 
                                            @if($project->status && $project->status->name == 'Terminé') 
                                                bg-green-600 text-white
                                            @elseif($project->status && $project->status->name == 'En cours') 
                                                bg-blue-600 text-white
                                            @else 
                                                bg-gray-600 text-white
                                            @endif">
                                            {{ $project->status->name ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-2">{{ $project->date_start ?? '-' }}</td>
                                    <td class="py-4 px-2">{{ $project->date_end ?? '-' }}</td>
                                    <td class="py-4 px-2 flex space-x-2">
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="text-blue-400 hover:text-blue-300">Modifier</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 rounded-2xl border-2 border-gray-700 bg-gray-900 shadow-md text-center text-gray-400">
                    <p>Aucun projet associé à ce client.</p>
                    <a href="{{ route('projects.create') }}?customer_id={{ $customer->id }}" 
                       class="inline-block mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">
                        Créer un projet
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection