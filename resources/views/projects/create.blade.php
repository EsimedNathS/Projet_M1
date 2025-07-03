@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl max-w-2xl mx-auto">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Création de projet</h1>

        <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Project Name -->
            <div>
                <label for="name" class="block mb-1 text-white">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Customer -->
            <div>
                <label for="customer_id" class="block mb-1 text-white">lient</label>
                <select name="customer_id" id="customer_id" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    <option value="">-- Selection client --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status_id" class="block mb-1 text-white">Statuts</label>
                <select name="status_id" id="status_id"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    <option value="">-- Selection statut --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $dateStart = old('date_start') ?? null;
                $dateEnd = old('date_end') ?? null;
            @endphp

            <div>
                <label for="date_start" class="block mb-1 text-white">Date de début</label>
                <input 
                    type="date" 
                    name="date_start" 
                    id="date_start" 
                    value="{{ $dateStart }}"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                >
            </div>

            <div>
                <label for="date_end" class="block mb-1 text-white">Date de fin</label>
                <input 
                    type="date" 
                    name="date_end" 
                    id="date_end" 
                    value="{{ $dateEnd }}"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                >
            </div>


            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('projects.index') }}" class="text-red-400 hover:text-red-600">Annuler</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
