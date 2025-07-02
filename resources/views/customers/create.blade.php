@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl max-w-2xl mx-auto">
        <h1 class="text-2xl font-medium text-red-400 mb-6">Add New Customer</h1>

        <form action="{{ route('customers.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-gray-300 mb-2" for="name">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-300 mb-2" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-red-400"
                    required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-gray-300 mb-2" for="phone">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full px-4 py-2 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label class="block text-gray-300 mb-2" for="address">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                    class="w-full px-4 py-2 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-red-400">
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between">
                <a href="{{ route('customers.index') }}" class="text-red-400 hover:text-red-600">Cancel</a>
                <button type="submit"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
