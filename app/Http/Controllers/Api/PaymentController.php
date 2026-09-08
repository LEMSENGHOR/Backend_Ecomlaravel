<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
    // POST /api/payments
    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->validated());

        if ($payment->status === 'COMPLETED') {
            $payment->order->update([
                'status' => 'PROCESSING'
            ]);
        }

        return response()->json([
            'message' => 'Payment created successfully.',
            'payment' => $payment,
        ], 201);
    }

    // GET /api/payments/{payment}
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return response()->json([
            'message' => 'Payment retrieved successfully.',
            'payment' => $payment->load('order'),
        ]);
    }
}