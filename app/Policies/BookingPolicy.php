<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->id === $booking->user_id || $user->isAdmin()) {
            return true;
        }

        // Allow assigned guide to view the booking details
        if ($user->isGuide() && $booking->guide_id === (string) $user->id) {
            return true;
        }

        return false;
    }
}
