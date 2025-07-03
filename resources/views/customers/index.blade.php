@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-medium text-red-400">Clients</h1>

            <div class="flex items-center space-x-4">
                <!-- Search -->
                <form action="{{ route('customers.index') }}" method="GET" class="relative flex items-center">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search"
                        class="bg-white text-black pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Garder les paramètres de tri dans la recherche -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                </form>

                <!-- Add Button -->
                <a href="{{ route('customers.create') }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">Ajouter un client</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <!-- Name Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('customers.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => (request('sort') == 'name' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Nom du client</span>
                                @if(request('sort') == 'name')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Email Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('customers.index', array_merge(request()->all(), ['sort' => 'email', 'direction' => (request('sort') == 'email' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Email</span>
                                @if(request('sort') == 'email')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Phone Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('customers.index', array_merge(request()->all(), ['sort' => 'phone', 'direction' => (request('sort') == 'phone' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Téléphone</span>
                                @if(request('sort') == 'phone')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Address Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('customers.index', array_merge(request()->all(), ['sort' => 'address', 'direction' => (request('sort') == 'address' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Adresse</span>
                                @if(request('sort') == 'address')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <th class="py-4 px-2 text-left text-red-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach ($customers as $customer)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">
                                <a href="{{ route('customers.show', $customer) }}" 
                                class="text-red-400 hover:text-red-300 hover:underline transition-colors">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="py-4 px-2">{{ $customer->email }}</td>
                            <td class="py-4 px-2">{{ $customer->phone }}</td>
                            <td class="py-4 px-2">{{ $customer->address }}</td>
                            <td class="py-4 px-2 flex space-x-2">
                                <a href="{{ route('customers.edit', $customer) }}"
                                    class="text-blue-400 hover:text-blue-300">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    @if ($customer->projects_count == 0)
                                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                                    @else
                                        <span class="text-gray-500 cursor-not-allowed">Delete</span>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $customers->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection