@extends('layouts.app')

@section('title', 'Product Management - Construction Materials & Supplies | SiteLedger')
@section('meta_description', 'Manage construction materials, products, and supplies inventory. Track product prices, monitor stock levels, and manage supplier information for construction projects.')
@section('meta_keywords', 'product management, construction materials, supplies inventory, material tracking, product catalog, construction supplies')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Products</h2>
        <div class="d-flex gap-2">
            {{-- Download Buttons --}}
            <x-download-buttons 
                route="products.export" 
                filename="products" 
                size="sm" />
            
            <a href="{{ route('products.create') }}" class="btn btn-success">Add Product</a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No products found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $products->links() }}
    </div>
</div>
@endsection
