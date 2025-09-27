@extends('layouts.app')

@section('title', 'Edit Order #' . ($order->id ?? ''))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Order #{{ $order->id }}</h2>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer name</label>
                        <input name="customer_name" class="form-control" value="{{ old('customer_name', $order->customer_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer email</label>
                        <input name="customer_email" type="email" class="form-control" value="{{ old('customer_email', $order->customer_email) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <!-- Existing items (editable) -->
                    <div class="col-12">
                        <h6 class="mt-3">Items</h6>
                        <div id="itemsContainer">
                            @foreach($order->items as $i => $item)
                                <div class="row g-2 item-row mb-2">
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                    <div class="col-md-6">
                                        <input name="items[{{ $i }}][product_name]" class="form-control" value="{{ old("items.$i.product_name", $item->product_name) }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input name="items[{{ $i }}][quantity]" type="number" value="{{ old("items.$i.quantity", $item->quantity) }}" min="1" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input name="items[{{ $i }}][unit_price]" type="number" step="0.01" value="{{ old("items.$i.unit_price", $item->unit_price) }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-remove-item w-100">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-2">
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-outline-primary">Add another item</button>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-success">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        let idx = {{ $order->items->count() }};
        document.getElementById('addItemBtn').addEventListener('click', function(){
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('div');
            row.className = 'row g-2 item-row mt-2';
            row.innerHTML = `
                <div class="col-md-6">
                    <input name="items[${idx}][product_name]" class="form-control" placeholder="Product name" required>
                </div>
                <div class="col-md-2">
                    <input name="items[${idx}][quantity]" type="number" value="1" min="1" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input name="items[${idx}][unit_price]" type="number" step="0.01" value="0.00" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove-item w-100">Remove</button>
                </div>
            `;
            container.appendChild(row);
            idx++;
        });

        document.addEventListener('click', function(e){
            if (e.target.matches('.btn-remove-item')) {
                const row = e.target.closest('.item-row');
                if (row) row.remove();
            }
        });
    })();
</script>
@endpush
