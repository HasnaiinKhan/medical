<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private CartService $cart) {}

    // ── Register ──────────────────────────────────────────────────────────────

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Capture guest cart before session is regenerated
        $guestCart = $request->session()->get('shop_cart', []);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // Move guest items into the new user's DB cart
        $this->cart->mergeGuestCartOnLogin($guestCart);

        return redirect()->intended(route('home'))
            ->with('status', 'Welcome, '.$user->name.'! Your account has been created.');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Capture guest session cart BEFORE session regenerate clears it
        $guestCart = $request->session()->get('shop_cart', []);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Merge guest items into the user's existing DB cart (transactional)
            // The user's DB cart already has their previous items — merge adds on top
            $this->cart->mergeGuestCartOnLogin($guestCart);

            return redirect()->intended(route('home'))
                ->with('status', 'Welcome back, '.Auth::user()->name.'!');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        // DB cart is NOT touched — it stays in cart_items for next login.
        // Session is invalidated → guest starts with an empty session cart.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }
}
