@extends('layouts.app')

@section('title', 'Edit Trainer')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3">Edit Trainer</h1>
        <a href="{{ route('gym.trainers.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('gym.trainers.update', $trainer) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $trainer->first_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $trainer->last_name) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $trainer->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $trainer->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specializations</label>
                            <select name="specializations[]" class="form-select" multiple>
                                @foreach(['strength_training','cardio','crossfit','yoga','pilates','nutrition'] as $opt)
                                    <option value="{{ $opt }}" {{ in_array($opt, (array)$trainer->specializations ?? []) ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $opt)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Certifications</label>
                            <textarea name="certifications" class="form-control">{{ old('certifications', $trainer->certifications) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('gym.trainers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 text-center">
            <img src="{{ $trainer->profile_image ? asset('storage/' . $trainer->profile_image) : 'https://via.placeholder.com/150' }}" class="rounded-circle mb-3" width="150" height="150" style="object-fit:cover;">
            <div class="card">
                <div class="card-body">
                    <label class="form-label">Update Photo</label>
                    <form action="{{ route('gym.trainers.update', $trainer) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="file" name="photo" class="form-control mb-2">
                        <button class="btn btn-sm btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
