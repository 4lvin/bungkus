<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
             ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'cartCount' => function () {
                $cartCount = 0;

                if (auth()->check()) {
                    // User is logged in, get cart by user_id
                    $cart = Cart::where('user_id', auth()->id())->first();
                } else {
                    // User is not logged in, get cart by session_id
                    $cart = Cart::where('session_id', session()->getId())->first();
                }

                // Calculate total quantity from cart_items if cart exists
                if ($cart) {
                    $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');
                }

                return $cartCount;
            },
            'ziggy' => [
                 ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => Session::get('success'),
                'error' => Session::get('error'),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
