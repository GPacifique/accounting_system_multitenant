{{-- resources/views/exports/products-pdf.blade.php --}}
@extends('exports.pdf-template')

@section('content')
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price (RWF)</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name ?? 'N/A' }}</td>
            <td>{{ $product->description ?? 'N/A' }}</td>
            <td class="text-right amount">RWF {{ number_format($product->price ?? 0, 0) }}</td>
            <td class="text-center">{{ $product->quantity ?? 0 }}</td>
            <td>{{ $product->category ?? 'N/A' }}</td>
            <td class="text-center">{{ $product->created_at->format('Y-m-d') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No products found</td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr class="total-row">
            <td colspan="3"><strong>Total Products:</strong></td>
            <td class="text-right amount"><strong>{{ $data->count() }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection