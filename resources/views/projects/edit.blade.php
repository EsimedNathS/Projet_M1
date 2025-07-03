@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Modifier le porjet</h1>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Project Name -->
            <div>
                <label for="name" class="block mb-1 text-white">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Customer -->
            <div>
                <label for="customer_id" class="block mb-1 text-white">Client</label>
                <select name="customer_id" id="customer_id" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $project->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status_id" class="block mb-1 text-white">Statuts</label>

                @if($project->status_id < 4)
                    <!-- Si le projet est au statut < 4, on n'autorise pas la modification -->
                    <input type="text" disabled readonly
                        value="{{ $project->status->name }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-300 text-black cursor-not-allowed">
                    <input type="hidden" name="status_id" value="{{ $project->status_id }}">
                @else
                    <!-- Sinon, on autorise à choisir dans les statuts >= 4 -->
                    <select name="status_id" id="status_id"
                        class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                        @foreach($statuses->where('id', '>', 3) as $status)
                            <option value="{{ $status->id }}" {{ $project->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            @php
                $modifiableDates = in_array($project->status_id, [1, 2, 3]);
            @endphp

            <!-- Start Date -->
            <div>
                <label for="date_start" class="block mb-1 text-white">Date de début</label>
                @if($modifiableDates)
                    <input 
                        type="date" 
                        name="date_start" 
                        id="date_start" 
                        value="{{ old('date_start', $project->date_start ? $project->date_start->format('Y-m-d') : '') }}" 
                        class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                    >
                @else
                    <input 
                        type="date" 
                        disabled
                        value="{{ $project->date_start ? $project->date_start->format('Y-m-d') : '' }}" 
                        class="w-full px-4 py-2 rounded-lg bg-gray-300 text-black cursor-not-allowed"
                    >
                    <input type="hidden" name="date_start" value="{{ $project->date_start ? $project->date_start->format('Y-m-d') : '' }}">
                @endif
            </div>

            <!-- End Date -->
            <div>
                <label for="date_end" class="block mb-1 text-white">Date de fin</label>
                @if($modifiableDates)
                    <input 
                        type="date" 
                        name="date_end" 
                        id="date_end" 
                        value="{{ old('date_end', $project->date_end ? $project->date_end->format('Y-m-d') : '') }}" 
                        class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                    >
                @else
                    <input 
                        type="date" 
                        disabled
                        value="{{ $project->date_end ? $project->date_end->format('Y-m-d') : '' }}" 
                        class="w-full px-4 py-2 rounded-lg bg-gray-300 text-black cursor-not-allowed"
                    >
                    <input type="hidden" name="date_end" value="{{ $project->date_end ? $project->date_end->format('Y-m-d') : '' }}">
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('projects.index') }}" class="text-red-400 hover:text-red-600">Annuler</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>

    </div>
</div>
@endsection