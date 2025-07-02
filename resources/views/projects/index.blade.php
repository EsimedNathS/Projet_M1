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
                    
                    <!-- Garder les paramètres de tri et filtres dans la recherche -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @if(request('status'))
                        @foreach(request('status') as $status)
                            <input type="hidden" name="status[]" value="{{ $status }}">
                        @endforeach
                    @endif
                </form>

                <!-- Add Button -->
                <a href="{{ route('projects.create') }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">Add Project
                </a>
            </div>
        </div>

        <!-- Filtres de statut -->
        <div class="mb-6 relative">
            <form action="{{ route('projects.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <!-- Garder les autres paramètres -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction') }}">

                <div class="relative">
                    <button type="button" onclick="toggleDropdown()" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                        Filtrer par statut
                        <svg class="w-4 h-4 ml-2 transform transition-transform" id="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div id="dropdown-menu" class="absolute mt-2 bg-gray-700 border border-gray-600 rounded-lg shadow-lg hidden z-10 w-56 p-4">
                        <!-- Liste des statuts -->
                        <div class="space-y-2 mb-4">
                            @foreach($project_status as $status)
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-600 p-1 rounded">
                                    <input type="checkbox" name="status[]" value="{{ $status->id }}"
                                        {{ 
                                            request()->filled('status') 
                                                ? (in_array($status->id, request('status', [])) ? 'checked' : '') 
                                                : ($status->id == $defaultStatusId ? 'checked' : '')
                                        }}
                                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                    <span class="text-gray-300">{{ $status->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Boutons d'action -->
                        <div class="border-t border-gray-600 pt-3 space-y-2">
                            <div class="flex space-x-2">
                                <button type="button" onclick="toggleAllProject_status()" class="text-xs text-red-400 hover:text-red-300 underline">
                                    Tout/Rien
                                </button>
                                <button type="button" onclick="resetToDefault()" class="text-xs text-blue-400 hover:text-blue-300 underline">
                                    Défaut
                                </button>
                            </div>
                            
                            <!-- Boutons de validation -->
                            <div class="flex space-x-2 pt-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Appliquer
                                </button>
                                <button type="button" onclick="closeDropdown()" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <!-- Project Name Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('projects.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => (request('sort') == 'name' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Project Name</span>
                                @if(request('sort') == 'name')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Customer Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('projects.index', array_merge(request()->all(), ['sort' => 'customer', 'direction' => (request('sort') == 'customer' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Customer</span>
                                @if(request('sort') == 'customer')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Status Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('projects.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => (request('sort') == 'status' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Status</span>
                                @if(request('sort') == 'status')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- Start Date Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('projects.index', array_merge(request()->all(), ['sort' => 'date_start', 'direction' => (request('sort') == 'date_start' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Start Date</span>
                                @if(request('sort') == 'date_start')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </th>

                        <!-- End Date Column -->
                        <th class="py-4 px-2 text-left text-red-400">
                            <a href="{{ route('projects.index', array_merge(request()->all(), ['sort' => 'date_end', 'direction' => (request('sort') == 'date_end' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>End Date</span>
                                @if(request('sort') == 'date_end')
                                    @if(request('direction') == 'asc')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
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
                    @foreach ($projects as $project)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="py-4 px-2">
                                <a href="{{ route('projects.show', $project) }}" 
                                class="text-red-400 hover:text-red-300 hover:underline transition-colors">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="py-4 px-2">
                                @if ($project->customer)
                                    {{ $project->customer->name }}
                                @else
                                    <span class="text-gray-500 italic">No Customer</span>
                                @endif
                            </td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($project->status && $project->status->name == 'Démarré')
                                        bg-green-100 text-green-800
                                    @elseif($project->status && $project->status->name == 'En cours')
                                        bg-blue-100 text-blue-800
                                    @elseif($project->status && $project->status->name == 'Terminé')
                                        bg-gray-100 text-gray-800
                                    @else
                                        bg-yellow-100 text-yellow-800
                                    @endif
                                ">
                                    {{ $project->status->name ?? 'No Status' }}
                                </span>
                            </td>
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
            {{ $projects->appends(request()->all())->links() }}
        </div>
    </div>
</div>

<script>
function toggleAllProject_status() {
    const checkboxes = document.querySelectorAll('input[name="status[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

function resetToDefault() {
    const checkboxes = document.querySelectorAll('input[name="status[]"]');
    const defaultStatusId = '{{ $defaultStatusId }}';
    
    checkboxes.forEach(cb => {
        cb.checked = cb.value === defaultStatusId;
    });
}

function toggleDropdown() {
    const dropdown = document.getElementById('dropdown-menu');
    const icon = document.getElementById('dropdown-icon');
    
    dropdown.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function closeDropdown() {
    const dropdown = document.getElementById('dropdown-menu');
    const icon = document.getElementById('dropdown-icon');
    
    dropdown.classList.add('hidden');
    icon.classList.remove('rotate-180');
}

// Fermer le dropdown en cliquant ailleurs
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown-menu');
    const button = event.target.closest('button[onclick="toggleDropdown()"]');
    const dropdownContent = event.target.closest('#dropdown-menu');
    
    if (!button && !dropdownContent && !dropdown.classList.contains('hidden')) {
        closeDropdown();
    }
});
</script>
@endsection