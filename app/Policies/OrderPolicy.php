<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $order->customer?->user_id === $user->id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->customer !== null;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->customer?->user_id === $user->id;
    }
}
