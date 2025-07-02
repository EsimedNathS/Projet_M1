@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Edit Project</h1>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Project Name -->
            <div>
                <label for="name" class="block mb-1 text-white">Project Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Customer -->
            <div>
                <label for="customer_id" class="block mb-1 text-white">Customer</label>
                <select name="customer_id" id="customer_id" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ $project->customer_id == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status_id" class="block mb-1 text-white">Status</label>
                <select name="status_id" id="status_id"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ $project->status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- List Price -->
            <div>
                <label for="list_price" class="block mb-1 text-white">List Price (€)</label>
                <input type="number" step="0.01" name="list_price" id="list_price" value="{{ old('list_price', $project->list_price) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Discount -->
            <div>
                <label for="discount" class="block mb-1 text-white">Discount (%)</label>
                <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', $project->discount) }}"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('projects.index') }}" class="text-red-400 hover:text-red-600">Cancel</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
