@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl text-red-500 font-bold mb-6">Gestion des utilisateurs</h1>

    <table class="w-full text-white bg-gray-800 rounded-xl overflow-hidden">
        <thead class="bg-gray-700 text-left">
            <tr>
                <th class="p-4">Nom</th>
                <th class="p-4">Email</th>
                <th class="p-4">Rôle</th>
                <th class="p-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t border-gray-600">
                <td class="p-4">{{ $user->last_name }}</td>
                <td class="p-4">{{ $user->email }}</td>
                <td class="p-4">{{ $user->admin }}</td>
                <td class="p-4">
                    <!-- <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-400 hover:underline">Modifier</a> -->
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block ml-2"
                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection