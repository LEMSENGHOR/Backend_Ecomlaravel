<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->order->user_id || $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->roles()->where('name', 'ADMIN')->exists();
    }
}
