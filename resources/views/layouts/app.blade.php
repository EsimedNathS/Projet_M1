<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Application</title>
    @vite('resources/css/app.css') <!-- si tu utilises Vite -->
</head>
<body class="bg-gradient-to-r from-black via-black/90 to-gray-900">
    <div class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="bg-gradient-to-r from-red-700 to-red-600 text-white p-4 flex justify-between items-center shadow-md">
        <div class="flex items-center space-x-6">
            <a href="{{ route('dashboard') }}" class="text-2xl font-bold hover:text-gray-300 transition-colors duration-300">
            Dashboard
            </a>

            @auth
            @if(auth()->user()->admin)
                <a href="{{ route('admin.users.index') }}" 
                class="ml-4 px-3 py-1 rounded-md bg-white text-red-700 font-semibold hover:bg-red-100 transition-colors duration-300">
                Administration
                </a>
            @endif
            @endauth
        </div>

        <nav class="space-x-6 flex items-center">
            <a href="{{ route('quotes.index') }}" class="hover:text-gray-300 transition-colors duration-300">Devis</a>
            <a href="{{ route('expenses.index') }}" class="hover:text-gray-300 transition-colors duration-300">Factures</a>
            <a href="{{ route('projects.index') }}" class="hover:text-gray-300 transition-colors duration-300">Projets</a>
            <a href="{{ route('customers.index') }}" class="hover:text-gray-300 transition-colors duration-300">Clients</a>

            <!-- Icon Logout -->
            <a href="{{ route('logout') }}" 
            onclick="event.preventDefault(); 
                        if(confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                            document.getElementById('logout-form').submit();
                        }" 
            class="hover:text-gray-300 transition-colors duration-300 flex items-center space-x-1"
            title="Se déconnecter">
                <!-- Icone porte de sortie -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                </svg>
            </a>

            <!-- Icon Profil -->
            <a href="{{ route('profile.show', ['user' => auth()->user()->id]) }}" class="hover:text-gray-300 transition-colors duration-300 flex items-center ml-4" title="Profil">
                <!-- Icone bonhomme -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M5.121 17.804A4 4 0 018 15h8a4 4 0 012.879 2.804M12 12a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
            </a>


            <!-- Formulaire logout (Laravel standard) -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </nav>
        </header>


        <!-- Page Content -->
        <main class="flex-grow p-6">
            @yield('content')
            @stack('scripts')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white text-center p-4">
            © 2025 Mon Application
        </footer>

    </div>
</body>
</html>
