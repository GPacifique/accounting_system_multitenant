@extends('layouts.app')


@section('title', 'Create Client')


@section('content')
<div class="max-w-3xl mx-auto p-6">
<h1 class="text-2xl font-semibold mb-4">Create Client</h1>


@if($errors->any())
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
<ul class="list-disc pl-5">
@foreach($errors->all() as $error)
<li class="text-sm text-red-700">{{ $error }}</li>
@endforeach
</ul>
</div>
@endif


<form action="{{ route('clients.store') }}" method="POST">
@csrf


<div class="mb-4">
<label for="name" class="block text-sm font-medium mb-1">Name</label>
<input id="name" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
</div>


<div class="mb-4">
<label for="contact_person" class="block text-sm font-medium mb-1">Contact Person</label>
<input id="contact_person" name="contact_person" value="{{ old('contact_person') }}" class="w-full border rounded px-3 py-2" required>
</div>


<div class="mb-4">
<label for="email" class="block text-sm font-medium mb-1">Email</label>
<input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
</div>


<div class="mb-4">
<label for="phone" class="block text-sm font-medium mb-1">Phone</label>
<input id="phone" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2" required>
</div>


<div class="mb-4">
<label for="address" class="block text-sm font-medium mb-1">Address</label>
<input id="address" name="address" value="{{ old('address') }}" class="w-full border rounded px-3 py-2">
</div>


<div class="flex items-center gap-2">
<button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save Client</button>
@endsection