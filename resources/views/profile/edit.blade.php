@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-3xl shadow-2xl w-full max-w-2xl">
        <h1 class="text-3xl font-semibold text-red-400 mb-6">Modifier l'utilisateur</h1>

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <!-- First Name -->
            <div>
                <label for="first_name" class="block mb-1 text-white">Prénom</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block mb-1 text-white">Nom</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-1 text-white">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block mb-1 text-white">Téléphone</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>
            @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror


            <!-- Address -->
            <div>
                <label for="adresse" class="block mb-1 text-white">Adresse</label>
                <textarea name="adresse" id="adresse" rows="3"
                    class="w-full px-4 py-2 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('adresse', $user->adresse) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('profile.show') }}" class="text-red-400 hover:text-red-600">Annuler</a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>

    </div>
</div>
@endsection