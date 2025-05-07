<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Get the user's cart or create a new one
     */
    private function getCart()
    {
        if (Auth::check()) {
            // User is logged in, get or create their cart
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        } else {
            // User is not logged in, use session ID to track cart
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }

        return $cart;
    }

    /**
     * Display the user's cart
     */
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = $cart->items()->with('product')->get();
        
        $totalPrice = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        dd($cartItems,$totalPrice);
        return Inertia::render('shop/Cart', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     * Add a product to the cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = $this->getCart();

        // Check if the product is already in the cart
        $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            // Update existing cart item
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
            ]);
        } else {
            // Create new cart item
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully');
    }

    /**
     * Update the quantity of a cart item
     */
    public function updateCartItem(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $cartItem = $cart->items()->findOrFail($id);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated successfully');
    }

    /**
     * Remove an item from the cart
     */
    public function removeCartItem($id)
    {
        $cart = $this->getCart();
        $cart->items()->findOrFail($id)->delete();

        return back()->with('success', 'Item removed from cart');
    }

    /**
     * Clear the entire cart
     */
    public function clearCart()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return back()->with('success', 'Cart cleared successfully');
    }

    /**
     * Get cart count for navbar display
     */
    public function getCartCount()
    {
        $cart = $this->getCart();
        $count = $cart->items()->sum('quantity');
        
        return response()->json(['count' => $count]);
    }

    /**
     * Merge guest cart with user cart after login
     */
    public function mergeCart()
    {
        if (Auth::check()) {
            $sessionId = session()->getId();
            
            // Find guest cart by session ID
            $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            
            if ($guestCart) {
                // Find or create user cart
                $userCart = Cart::firstOrCreate([
                    'user_id' => Auth::id(),
                ]);
                
                // Merge guest cart items to user cart
                foreach ($guestCart->items as $item) {
                    $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();
                    
                    if ($existingItem) {
                        // Update quantity if product already exists in user cart
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $item->quantity,
                        ]);
                    } else {
                        // Create new cart item in user cart
                        $userCart->items()->create([
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ]);
                    }
                }
                
                // Delete guest cart
                $guestCart->delete();
            }
        }
        
        return redirect()->route('cart.index');
    }
}