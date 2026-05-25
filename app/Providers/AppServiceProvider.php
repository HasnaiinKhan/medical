<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\RefundServiceInterface;
use App\Services\RazorpayRefundService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->bind(RefundServiceInterface::class, RazorpayRefundService::class);
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
