@php
    $isSorted = request('sort') === $column;
    $direction = request('direction');
@endphp

@if ($isSorted)
    @if ($direction === 'asc')
        {{-- Flèche vers le haut --}}
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 8l5-5 5 5H5z"/>
        </svg>
    @else
        {{-- Flèche vers le bas --}}
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M15 12l-5 5-5-5h10z"/>
        </svg>
    @endif
@else
    {{-- Icône neutre --}}
    <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>
@endif
