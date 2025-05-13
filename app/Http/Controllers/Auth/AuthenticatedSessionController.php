<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        // Store the intended URL in session if coming from checkout
        if ($request->input('redirect') === 'checkout') {
            session()->put('url.intended', route('checkout.index'));
        }

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function createAdmin(Request $request): Response
    {
        // Store the intended URL in session if coming from checkout
        return Inertia::render('auth/LoginAdmin', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Get session ID before it changes
        $sessionId = $request->session()->getId();
        $request->authenticate();

        $request->session()->regenerate();

        $this->mergeGuestCart($sessionId);
        return redirect()->intended(route('home', absolute: false));
        // return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Periksa apakah kolom is_admin bernilai true
        if (!$user->is_admin) {
            // Logout jika bukan admin
            Auth::logout();

            // Redirect ke halaman login dengan pesan error
            return redirect()->route('login')->with('error', 'Akses ditolak. Hanya admin yang bisa login.');
        }

        // Regenerasi session setelah login berhasil
        $request->session()->regenerate();

        // Redirect ke dashboard admin
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Merge the guest cart to the user cart after login
     */
    private function mergeGuestCart($sessionId)
    {
        // Check if there's a guest cart with this session ID
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

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
}
