@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Edit Quote</h1>

        <form action="{{ route('quotes.update', [$quote->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Description -->
            <div>
                <label for="description" class="block mb-2 text-white font-medium">Description</label>
                <input
                    type="text"
                    name="description"
                    id="description"
                    value="{{ old('description', $quote->description) }}"
                    required
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
            </div>

            <!-- Date Edition -->
            <div>
                <label for="date_edition" class="block mb-2 text-white font-medium">Date</label>
                <input
                    type="date"
                    name="date_edition"
                    id="date_edition"
                    value="{{ old('date_edition', $quote->date_edition ? $quote->date_edition->format('Y-m-d') : '') }}"
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
            </div>

            <!-- Status -->
            <div>
                <label for="status_id" class="block mb-2 text-white font-medium">Status</label>
                <select
                    name="status_id"
                    id="status_id"
                    class="w-full px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400 bg-white"
                >
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id', $quote->status_id) == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-8">
                <a href="{{ url()->previous() }}" class="text-red-400 hover:text-red-600 font-semibold">Cancel</a>
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
                >
                    Update
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
