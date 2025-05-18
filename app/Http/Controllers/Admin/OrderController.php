<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a paginated list of orders.
     */
    public function index()
    {
        $perPage = request('per_page', 10);

        $orders = Order::with(['user', 'items.product:id,name'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    
        return Inertia::render('admin/orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display order detail (read-only).
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return Inertia::render('admin/orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);

        return Inertia::render('admin/orders/Edit', [
            'order'   => $order,
            'errors'  => session('errors')
                ? session('errors')->getBag('default')->getMessages()
                : (object) [],
        ]);
    }

    /**
     * Update order data.
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->all();

        $validated = Validator::make($data, [
            'status'          => 'required|in:pending,processing,completed,cancelled',
            'payment_status'  => 'required|in:pending,paid,failed',
            'notes'           => 'nullable|string|max:65535',
        ]);

        if ($validated->fails()) {
            $msg = implode(', ', $validated->errors()->all());

            return redirect()
                ->route('admin.orders.edit', $order->id)
                ->with('error', "Validasi gagal! {$msg}")
                ->withInput();
        }

        $order->update($validated->validated());

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil diperbarui.');
    }

    /**
     * Remove an order (and its items).
     */
    public function destroy(Order $order)
    {
        // Hapus semua order items terlebih dahulu
        $order->items()->delete();

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }
}
