<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        $cart = \App\Models\Cart::where('user_id', auth()->id())->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart->items as $item) {
            $cartItems[] = [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->price * $item->quantity,
            ];
            $total += $item->price * $item->quantity;
        }

        return Inertia::render('shop/Checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validated = Validator::make($data, [
            'shipping_fullname' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zipcode' => 'required|string|max:20',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cash,bank_transfer',
        ]);

        if ($validated->fails()) {
            $errorMessages = implode(', ', $validated->errors()->all());

            return redirect()
                ->route('checkout.index')
                ->with('error', 'Validasi gagal! ' . $errorMessages)
                ->withInput();
        }

        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart->items as $item) {
            $cartItems[] = [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->price * $item->quantity,
            ];
            $total += $item->price * $item->quantity;
        }

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Create order
        try {
            DB::beginTransaction();

            // Generate a unique order number
            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . rand(10000, 99999);

            // Create the order with the order number included
            $order = new Order();
            $order->user_id = auth()->id();
            $order->order_number = $orderNumber;
            $order->total_amount = $total;
            $order->status = 'pending';
            $order->payment_method = $request->payment_method;
            $order->payment_status = 'pending';
            $order->shipping_fullname = $request->shipping_fullname;
            $order->shipping_address = $request->shipping_address;
            $order->shipping_city = $request->shipping_city;
            $order->shipping_state = $request->shipping_state;
            $order->shipping_zipcode = $request->shipping_zipcode;
            $order->shipping_phone = $request->shipping_phone;
            $order->notes = $request->notes;
            $order->save();

            // Create order items
            foreach ($cartItems as $item) {
                // Check stock first
                $product = Product::find($item['id']);
                if (!$product || $product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . ($product ? $product->name : 'Unknown'));
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);

                // Update product stock
                $product->decrement('quantity', $item['quantity']);
            }

            // Clear cart after successful order
            session()->forget('cart');
            $cart->items()->delete();
            $cart->delete();
            DB::commit();

            return redirect()->route('checkout.thankyou', ['order' => $order->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }
    /**
     * Display the thank you page.
     */
    public function thankYou(Order $order)
    {
        return Inertia::render('shop/ThankYou', [
            'order' => $order,
        ]);
    }
}
