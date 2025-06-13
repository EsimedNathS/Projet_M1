<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Inscription</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-gray-700">
                    Nom complet
                </label>
                <input 
                    id="name" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 placeholder-gray-400" 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Votre nom complet"
                    required 
                    autofocus 
                    autocomplete="name"
                />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">
                    Email
                </label>
                <input 
                    id="email" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 placeholder-gray-400" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="votre@email.com"
                    required 
                    autocomplete="username"
                />
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Mot de passe
                </label>
                <input 
                    id="password" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 placeholder-gray-400"
                    type="password" 
                    name="password" 
                    placeholder="Votre mot de passe"
                    required 
                    autocomplete="new-password"
                />
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                    Confirmer le mot de passe
                </label>
                <input 
                    id="password_confirmation" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 placeholder-gray-400"
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Confirmez votre mot de passe"
                    required 
                    autocomplete="new-password"
                />
                @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-red-300"
            >
                S'inscrire
            </button>

            <p class="text-center text-gray-600 text-sm">
                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-800 underline transition-colors duration-300">
                    Déjà un compte ? Se connecter
                </a>
            </p>
        </form>
    </div>
</body>
</html>