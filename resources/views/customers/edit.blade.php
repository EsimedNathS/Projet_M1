@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Edit Customer</h1>

        <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block mb-1 text-white">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-1 text-white">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block mb-1 text-white">Téléphone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block mb-1 text-white">Adresse</label>
                <input type="text" name="address" id="address" value="{{ old('address', $customer->address) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('customers.index') }}" class="text-red-400 hover:text-red-600">Annuler</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
