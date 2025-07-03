{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bienvenue</title>
    @vite('resources/css/app.css') {{-- C’est ici que ta CSS compilée (avec Tailwind) est chargée --}}
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="max-w-md text-center p-8 bg-gray-800 rounded-3xl shadow-lg">
        <h1 class="text-4xl font-bold mb-6 text-red-400">Bienvenue sur votre application de gestion</h1>
        <p class="mb-8 text-gray-300">Connectez-vous ou inscrivez-vous pour commencer à utiliser l'application.</p>

        <div class="flex justify-center gap-6">
            <a href="{{ route('login') }}" 
               class="px-6 py-3 bg-red-600 rounded-lg hover:bg-red-500 transition-colors font-semibold">
                Se connecter
            </a>
            <a href="{{ route('register') }}" 
               class="px-6 py-3 border border-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors font-semibold">
                S'inscrire
            </a>
        </div>
    </div>
</body>
</html>
