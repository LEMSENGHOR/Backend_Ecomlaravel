<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidCouponException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    // GET /api/orders
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(20);

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'orders' => $orders,
        ]);
    }

    // POST /api/orders
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->checkoutFromCart(
                $request->user(),
                $request->shipping_address,
                $request->coupon_code
            );
        } catch (InsufficientStockException|InvalidCouponException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order,
        ], 201);
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return response()->json([
            'message' => 'Order retrieved successfully.',
            'order' => $order->load('items.product', 'payment'),
        ]);
    }

    // PATCH /api/orders/{order}/status
    // ADMIN ONLY
    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ) {
        $order = $this->orderService->updateStatus(
            $order,
            $request->status
        );

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order,
        ]);
    }
}