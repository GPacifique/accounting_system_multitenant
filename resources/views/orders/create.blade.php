@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>New Order</h2>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer name</label>
                        <input name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer email</label>
                        <input name="customer_email" type="email" class="form-control" value="{{ old('customer_email') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Items: dynamic rows with product dropdown -->
                    <div class="col-12">
                        <h6 class="mt-3">Items</h6>
                        <div id="itemsContainer">
                            <div class="row g-2 item-row">
                                <div class="col-md-6">
                                    <select name="items[0][product_id]" class="form-select product-select" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input name="items[0][quantity]" type="number" value="1" min="1" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <input name="items[0][price]" type="number" step="0.01" value="0.00" class="form-control price-input" required readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-remove-item w-100">Remove</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-outline-primary">Add another item</button>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-success" type="submit" data-loading-text="Creating...">Create Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    let idx = 1;
    const products = @json($products->mapWithKeys(fn($p) => [$p->id => $p->price]));
    document.getElementById('addItemBtn').addEventListener('click', function(){
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 item-row mt-2';
        row.innerHTML = `
            <div class="col-md-6">
                <select name="items[${idx}][product_id]" class="form-select product-select" required>
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input name="items[${idx}][quantity]" type="number" value="1" min="1" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input name="items[${idx}][price]" type="number" step="0.01" value="0.00" class="form-control price-input" required readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove-item w-100">Remove</button>
            </div>
        `;
        container.appendChild(row);
        idx++;
    });

    document.addEventListener('change', function(e){
        if (e.target.matches('.product-select')) {
            const price = e.target.options[e.target.selectedIndex].getAttribute('data-price') || 0;
            const priceInput = e.target.closest('.item-row').querySelector('.price-input');
            if (priceInput) priceInput.value = price;
        }
    });

    document.addEventListener('click', function(e){
        if (e.target.matches('.btn-remove-item')) {
            const row = e.target.closest('.item-row');
            if (row) row.remove();
        }
    });

    // Set price for first row if product is selected
    document.querySelectorAll('.product-select').forEach(function(select){
        select.addEventListener('change', function(){
            const price = select.options[select.selectedIndex].getAttribute('data-price') || 0;
            const priceInput = select.closest('.item-row').querySelector('.price-input');
            if (priceInput) priceInput.value = price;
        });
    });
})();
</script>
@endpush
@endsection
