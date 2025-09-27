@extends('layouts.app')


@section('title', 'Clients')


@section('content')
<div class="max-w-6xl mx-auto p-6">
<div class="flex items-center justify-between mb-6">
<h1 class="text-2xl font-semibold">Clients</h1>
<a href="{{ route('clients.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Client</a>
</div>


@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">{{ session('success') }}</div>
@endif


@if($clients->isEmpty())
<div class="p-6 bg-yellow-50 border border-yellow-200 rounded">No clients found.</div>
@else
<div class="overflow-x-auto bg-white rounded shadow-sm">
<table class="w-full table-auto">
<thead>
<tr class="text-left bg-gray-50">
<th class="px-4 py-3">#</th>
<th class="px-4 py-3">Name</th>
<th class="px-4 py-3">Contact Person</th>
<th class="px-4 py-3">Email</th>
<th class="px-4 py-3">Phone</th>
<th class="px-4 py-3">Actions</th>
</tr>
</thead>
<tbody>
@foreach($clients as $client)
<tr class="border-t hover:bg-gray-50">
<td class="px-4 py-3">{{ $client->id }}</td>
<td class="px-4 py-3">{{ $client->name }}</td>
<td class="px-4 py-3">{{ $client->contact_person }}</td>
<td class="px-4 py-3">{{ $client->email ?? '—' }}</td>
<td class="px-4 py-3">{{ $client->phone }}</td>
<td class="px-4 py-3">
<a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:underline mr-2">View</a>
<a href="{{ route('clients.edit', $client) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
<form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this client?');">
@csrf
@method('DELETE')
<button type="submit" class="text-red-600 hover:underline">Delete</button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>


<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
@endif
</div>
@endsection