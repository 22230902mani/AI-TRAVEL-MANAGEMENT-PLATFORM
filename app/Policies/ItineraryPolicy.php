<?php

namespace App\Policies;

use App\Models\Itinerary;
use App\Models\User;

class ItineraryPolicy
{
    public function view(User $user, Itinerary $itinerary): bool
    {
        if ($user->id === $itinerary->user_id || $itinerary->is_public || $user->isAdmin()) {
            return true;
        }

        // Allow assigned guide to view the itinerary details
        if ($user->isGuide()) {
            return \App\Models\Booking::where('itinerary_id', $itinerary->id)
                ->where('guide_id', (string) $user->id)
                ->exists();
        }

        return false;
    }

    public function update(User $user, Itinerary $itinerary): bool
    {
        return $user->id === $itinerary->user_id || $user->isAdmin();
    }

    public function delete(User $user, Itinerary $itinerary): bool
    {
        return $user->id === $itinerary->user_id || $user->isAdmin();
    }
}
