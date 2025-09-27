<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        // Require authentication; change or add role/permission middleware as needed
        $this->middleware('auth');
    }

    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = Order::withCount('items')->latest()->paginate(15);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        // You may pass customers/products lists here
        return view('orders.create');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
            // Optional: items[] with product_id, qty, price
            'items' => 'nullable|array',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, &$order) {
            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            if ($request->filled('items')) {
                foreach ($request->input('items') as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_price'];
                    $order->items()->create([
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $lineTotal,
                    ]);
                    $total += $lineTotal;
                }
            }

            $order->update(['total' => $total]);
        });

        return redirect()->route('orders.index')->with('success', 'Order created.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('items', 'payments');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load('items');
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('orders.show', $order)->with('success', 'Order updated.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    /**
     * Add an item to an order.
     */
    public function addItem(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $lineTotal = $validated['quantity'] * $validated['unit_price'];

        $order->items()->create([
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'line_total' => $lineTotal,
        ]);

        // Recalculate total
        $order->refresh();
        $newTotal = $order->items()->sum('line_total');
        $order->update(['total' => $newTotal]);

        return back()->with('success', 'Item added.');
    }

    /**
     * Remove an item from an order.
     */
    public function removeItem(Order $order, OrderItem $item)
    {
        // Ensure the item belongs to the order
        if ($item->order_id !== $order->id) {
            abort(404);
        }

        $item->delete();

        // Recalculate total
        $newTotal = $order->items()->sum('line_total');
        $order->update(['total' => $newTotal]);

        return back()->with('success', 'Item removed.');
    }

    /**
     * Mark order as paid by creating a Payment record.
     */
    public function markAsPaid(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($order, $validated) {
            Payment::create([
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'reference' => $validated['reference'] ?? null,
                'user_id' => auth()->id(),
                'order_id' => $order->id,
            ]);

            // Optionally change order status to completed/processing if fully paid
            $paid = $order->payments()->sum('amount');
            if ($paid >= $order->total) {
                $order->update(['status' => 'completed']);
            }
        });

        return back()->with('success', 'Payment recorded.');
    }
}
