@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">Edit Product</div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input name="price" type="number" step="0.01" class="form-control" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input name="stock" type="number" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
