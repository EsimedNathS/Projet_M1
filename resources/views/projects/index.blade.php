@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-medium text-red-400">All Projects</h1>

            <div class="flex items-center space-x-4">
                <!-- Search -->
                <form action="{{ route('projects.index') }}" method="GET" class="relative flex items-center">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search"
                        class="bg-white text-black pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Sort Dropdown -->
                    <select name="sort" onchange="this.form.submit()"
                        class="ml-4 bg-white text-black px-5 py-2 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                        <option value="">Sort by:</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Project Name</option>
                        <option value="customer" {{ request('sort') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                </form>

                <!-- Add Button -->
                <a href="{{ route('projects.create') }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">Add Project</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="py-4 px-2 text-left text-red-400">Project Name</th>
                        <th class="py-4 px-2 text-left text-red-400">Customer</th>
                        <th class="py-4 px-2 text-left text-red-400">Status</th>
                        <th class="py-4 px-2 text-left text-red-400">Start Date</th>
                        <th class="py-4 px-2 text-left text-red-400">End Date</th>
                        <th class="py-4 px-2 text-left text-red-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach ($projects as $project)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">{{ $project->name }}</td>
                            <td class="py-4 px-2">
                                @if ($project->customer)
                                    {{ $project->customer->name }}
                                @else
                                    <span class="text-gray-500 italic">No Customer</span>
                                @endif
                            </td>
                            <td class="py-4 px-2">{{ $project->status->name ?? 'No Status' }}</td>
                            <td class="py-4 px-2">{{ $project->date_start ?? '-' }}</td>
                            <td class="py-4 px-2">{{ $project->date_end ?? '-' }}</td>
                            <td class="py-4 px-2 flex space-x-2">
                                <a href="{{ route('projects.edit', $project) }}" class="text-blue-400 hover:text-blue-300">Edit</a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $projects->appends(['search' => request('search'), 'sort' => request('sort')])->links() }}
        </div>
    </div>
</div>
@endsection
