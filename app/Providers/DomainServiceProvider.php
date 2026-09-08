<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\WishlistService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * These services have no interfaces and only scalar-free constructor
     * dependencies, so Laravel's container resolves them automatically —
     * these bindings exist mainly to document the services as singletons.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(CouponService::class);
        $this->app->singleton(WishlistService::class);
        $this->app->singleton(OrderService::class);
    }
}
