@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-gray-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-medium text-red-400">Projets</h1>

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
                    <!-- Les status seront gérés par JavaScript -->
                    <div id="hidden-status-inputs"></div>
                </form>

                <!-- Add Button -->
                <a href="{{ route('projects.create') }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition-colors">Ajouter un Projet
                </a>
            </div>
        </div>

        <!-- Filtres de statut -->
        <div class="mb-6 relative">
            <form action="{{ route('projects.index') }}" method="GET" class="flex flex-wrap items-center gap-4" id="filter-form">
                <!-- Garder les autres paramètres -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction') }}">
                <!-- Container pour les inputs status cachés -->
                <div id="status-inputs-container"></div>

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
                                        data-status-id="{{ $status->id }}"
                                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 status-checkbox">
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
                                <button type="button" onclick="applyFilters()" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition-colors">
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
                            <a href="javascript:void(0);" onclick="sortTable('name')" class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Nom du projet</span>
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
                            <a href="javascript:void(0);" onclick="sortTable('customer')" class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Clients</span>
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
                            <a href="javascript:void(0);" onclick="sortTable('status')" class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Statuts</span>
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
                            <a href="javascript:void(0);" onclick="sortTable('date_start')" class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Date début</span>
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
                            <a href="javascript:void(0);" onclick="sortTable('date_end')" class="flex items-center space-x-1 hover:text-red-300 transition-colors">
                                <span>Date fin</span>
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
                                <a href="{{ route('projects.edit', $project) }}" class="text-blue-400 hover:text-blue-300">Modifier</a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Supprimer</button>
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
const STORAGE_KEY = 'projectStatusFilter';
const DEFAULT_STATUS_ID = '{{ $defaultStatusId }}';

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous avons des filtres dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const statusParams = urlParams.getAll('status[]');
    
    if (statusParams.length > 0) {
        // Si nous avons des filtres dans l'URL, les utiliser et les sauvegarder
        saveFiltersToStorage(statusParams);
        loadFiltersFromURL();
    } else {
        // Si pas de filtres dans l'URL, charger depuis localStorage et appliquer
        loadFiltersFromStorage();
        applyStoredFilters();
    }
    
    updateHiddenInputs();
});

// Charger les filtres depuis l'URL
function loadFiltersFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const statusParams = urlParams.getAll('status[]');
    const checkboxes = document.querySelectorAll('.status-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = statusParams.includes(cb.value);
    });
}

// Charger les filtres depuis localStorage
function loadFiltersFromStorage() {
    const checkboxes = document.querySelectorAll('.status-checkbox');
    let savedFilters = getSavedFilters();
    
    // Si aucun filtre sauvegardé, utiliser le statut par défaut
    if (!savedFilters || savedFilters.length === 0) {
        savedFilters = [DEFAULT_STATUS_ID];
        saveFiltersToStorage(savedFilters);
    }
    
    // Appliquer les filtres sauvegardés
    checkboxes.forEach(cb => {
        cb.checked = savedFilters.includes(cb.value);
    });
}

// Appliquer les filtres stockés automatiquement
function applyStoredFilters() {
    const currentFilters = getCurrentFilters();
    
    // Vérifier si nous avons des filtres à appliquer
    if (currentFilters.length > 0) {
        // Construire l'URL avec les filtres
        const url = new URL(window.location.href);
        
        // Supprimer les anciens filtres de statut
        url.searchParams.delete('status[]');
        
        // Ajouter les nouveaux filtres
        currentFilters.forEach(statusId => {
            url.searchParams.append('status[]', statusId);
        });
        
        // Rediriger uniquement si l'URL est différente
        if (url.toString() !== window.location.href) {
            window.location.href = url.toString();
        }
    }
}

// Récupérer les filtres sauvegardés
function getSavedFilters() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        return saved ? JSON.parse(saved) : null;
    } catch (e) {
        console.error('Erreur lors de la lecture du localStorage:', e);
        return null;
    }
}

// Sauvegarder les filtres dans localStorage
function saveFiltersToStorage(filters) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(filters));
    } catch (e) {
        console.error('Erreur lors de la sauvegarde dans localStorage:', e);
    }
}

// Obtenir les filtres actuellement sélectionnés
function getCurrentFilters() {
    const checkboxes = document.querySelectorAll('.status-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Mettre à jour les inputs cachés pour les formulaires
function updateHiddenInputs() {
    const currentFilters = getCurrentFilters();
    
    // Mettre à jour le container du formulaire de filtres
    const filterContainer = document.getElementById('status-inputs-container');
    if (filterContainer) {
        filterContainer.innerHTML = '';
        
        currentFilters.forEach(statusId => {
            const filterInput = document.createElement('input');
            filterInput.type = 'hidden';
            filterInput.name = 'status[]';
            filterInput.value = statusId;
            filterContainer.appendChild(filterInput);
        });
    }
    
    // Mettre à jour le container du formulaire de recherche
    const searchContainer = document.getElementById('hidden-status-inputs');
    if (searchContainer) {
        searchContainer.innerHTML = '';
        
        currentFilters.forEach(statusId => {
            const searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'status[]';
            searchInput.value = statusId;
            searchContainer.appendChild(searchInput);
        });
    }
}

// Appliquer les filtres
function applyFilters() {
    const currentFilters = getCurrentFilters();
    saveFiltersToStorage(currentFilters);
    updateHiddenInputs();
    
    // Soumettre le formulaire
    document.getElementById('filter-form').submit();
}

// Basculer tous les statuts
function toggleAllProject_status() {
    const checkboxes = document.querySelectorAll('.status-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

// Réinitialiser au statut par défaut
function resetToDefault() {
    const checkboxes = document.querySelectorAll('.status-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = cb.value === DEFAULT_STATUS_ID;
    });
}

// Gérer le tri
function sortTable(sortField) {
    const currentSort = '{{ request("sort") }}';
    const currentDirection = '{{ request("direction") }}';
    
    let newDirection = 'asc';
    if (currentSort === sortField && currentDirection === 'asc') {
        newDirection = 'desc';
    }
    
    // Construire l'URL avec les filtres actuels
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortField);
    url.searchParams.set('direction', newDirection);
    
    // Ajouter les filtres de statut
    url.searchParams.delete('status[]');
    const currentFilters = getCurrentFilters();
    currentFilters.forEach(statusId => {
        url.searchParams.append('status[]', statusId);
    });
    
    window.location.href = url.toString();
}

// Gérer le dropdown
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