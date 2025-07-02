@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl max-w-2xl mx-auto">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Create Project</h1>

        <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Project Name -->
            <div>
                <label for="name" class="block mb-1 text-white">Project Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Customer -->
            <div>
                <label for="customer_id" class="block mb-1 text-white">Customer</label>
                <select name="customer_id" id="customer_id" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
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
                    <option value="">-- Select Status --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- List Price -->
            <div>
                <label for="list_price" class="block mb-1 text-white">List Price (€)</label>
                <input type="number" step="0.01" name="list_price" id="list_price" value="{{ old('list_price') }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Discount -->
            <div>
                <label for="discount" class="block mb-1 text-white">Discount (%)</label>
                <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount') }}"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('projects.index') }}" class="text-red-400 hover:text-red-600">Cancel</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
