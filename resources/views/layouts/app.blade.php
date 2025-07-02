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
            <a href="{{ route('dashboard') }}" class="text-2xl font-bold hover:text-gray-300 transition-colors duration-300">
                Dashboard
            </a>
            <nav class="space-x-6 flex items-center">
                @auth
                    @if(auth()->user()->admin)
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-white hover:underline">Administration</a>
                    @endif
                @endauth
                <a href="{{ route('quotes.index') }}" class="hover:text-gray-300 transition-colors duration-300">Quotes</a>
                <a href="{{ route('expenses.index') }}" class="hover:text-gray-300 transition-colors duration-300">Expenses</a>
                <a href="{{ route('projects.index') }}" class="hover:text-gray-300 transition-colors duration-300">Projects</a>
                <a href="{{ route('customers.index') }}" class="hover:text-gray-300 transition-colors duration-300">Customers</a>
                <a href="#" class="hover:text-gray-300 transition-colors duration-300">Logout</a>
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
