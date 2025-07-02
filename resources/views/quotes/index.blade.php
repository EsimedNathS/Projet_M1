@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-medium text-red-400">Tous les devis</h1>

            <div class="flex items-center space-x-4">
                <!-- Search -->
                <form action="{{ route('quotes.index') }}" method="GET" class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher"
                        class="bg-white text-black pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Maintenir les paramètres de tri lors de la recherche -->
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('direction'))
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @endif
                </form>

                <!-- Add Button -->
                <a href="{{ route('quotes.create') }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">Ajouter un devis</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="py-4 px-2 text-left">
                            <a href="{{ route('quotes.index', array_merge(request()->all(), [
                                'sort' => 'description',
                                'direction' => (request('sort') === 'description' && request('direction') === 'asc') ? 'desc' : 'asc'
                            ])) }}" class="text-red-400 hover:text-red-300 flex items-center space-x-1">
                                <span>Description</span>
                                @include('partials._sort_icon', ['column' => 'description'])
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left">
                            <a href="{{ route('quotes.index', array_merge(request()->all(), [
                                'sort' => 'amount',
                                'direction' => (request('sort') === 'amount' && request('direction') === 'asc') ? 'desc' : 'asc'
                            ])) }}" class="text-red-400 hover:text-red-300 flex items-center space-x-1">
                                <span>Montant</span>
                                @include('partials._sort_icon', ['column' => 'amount'])
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left">
                            <a href="{{ route('quotes.index', array_merge(request()->all(), [
                                'sort' => 'date_edition',
                                'direction' => (request('sort') === 'date_edition' && request('direction') === 'asc') ? 'desc' : 'asc'
                            ])) }}" class="text-red-400 hover:text-red-300 flex items-center space-x-1">
                                <span>Date</span>
                                @include('partials._sort_icon', ['column' => 'date_edition'])
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left">
                            <a href="{{ route('quotes.index', array_merge(request()->all(), [
                                'sort' => 'status',
                                'direction' => (request('sort') === 'status' && request('direction') === 'asc') ? 'desc' : 'asc'
                            ])) }}" class="text-red-400 hover:text-red-300 flex items-center space-x-1">
                                <span>Statut</span>
                                @include('partials._sort_icon', ['column' => 'status'])
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left">
                            <a href="{{ route('quotes.index', array_merge(request()->all(), [
                                'sort' => 'project',
                                'direction' => (request('sort') === 'project' && request('direction') === 'asc') ? 'desc' : 'asc'
                            ])) }}" class="text-red-400 hover:text-red-300 flex items-center space-x-1">
                                <span>Projet</span>
                                @include('partials._sort_icon', ['column' => 'project'])
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left text-red-400">Actions</th>
                    </tr>

                </thead>
                <tbody class="text-gray-300">
                    @foreach ($quotes as $quote)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">
                                <a href="{{ route('quotes.show', $quote) }}" 
                                   class="text-red-400 hover:text-red-300 hover:underline">
                                    {{ $quote->description }}
                                </a>
                            </td>
                            <td class="py-4 px-2">{{ number_format($quote->calculateAmount(), 2) }} €</td>
                            <td class="py-4 px-2">{{ $quote->date_edition ? $quote->date_edition->format('d/m/Y') : '-' }}</td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-1 rounded text-xs 
                                    @if($quote->status && $quote->status->name == 'Accepté') 
                                        bg-green-600 text-white
                                    @elseif($quote->status && $quote->status->name == 'En attente') 
                                        bg-yellow-600 text-black
                                    @elseif($quote->status && $quote->status->name == 'Refusé')
                                        bg-red-600 text-white
                                    @else 
                                        bg-gray-600 text-white
                                    @endif">
                                    {{ $quote->status->name ?? 'Non défini' }}
                                </span>
                            </td>
                            <td class="py-4 px-2">
                                @if ($quote->project)
                                    <a href="{{ route('projects.show', $quote->project->id) }}" class="text-red-400 hover:text-red-300 hover:underline">
                                        {{ $quote->project->name }}
                                    </a>
                                @else
                                    Non défini
                                @endif
                            </td>
                            <td class="py-4 px-2 flex space-x-2">
                                <a href="{{ route('quotes.edit', $quote->id) }}" class="text-blue-400 hover:text-blue-300">Modifier</a>
                                <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" onsubmit="return confirm('Supprimer ce devis ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $quotes->appends(request()->only('search', 'sort', 'direction'))->links() }}
        </div>
    </div>
</div>
@endsection