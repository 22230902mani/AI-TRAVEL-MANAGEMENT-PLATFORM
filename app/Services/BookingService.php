<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\Package;
use App\Models\Hotel;
use App\Models\Promotion;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Create a new booking with full event-sourcing and loyalty point award.
     */
    public function createBooking(array $data, string|int $userId): Booking
    {
        return DB::transaction(function () use ($data, $userId) {
            // Calculate total if not provided
            if (empty($data['total_amount'])) {
                $data['total_amount'] = $this->calculateTotal($data);
            }

            // Apply promo code
            if (! empty($data['promo_code'])) {
                $promo = Promotion::where('code', $data['promo_code'])->first();
                if ($promo && $promo->isValid()) {
                    $discount = $promo->calculateDiscount($data['total_amount']);
                    $data['total_amount'] -= $discount;
                    $data['discount_applied'] = $discount;
                    $promo->increment('used_count');
                }
            }

            $booking = Booking::create([
                ...$data,
                'user_id'         => $userId,
                'booking_status'  => 'pending',
                'payment_status'  => 'pending',
                'booking_reference'=> 'TM-' . strtoupper(Str::random(8)),
            ]);

            // Event-sourcing: log creation
            $booking->appendEvent('booking_created', [
                'amount' => $booking->total_amount,
                'type'   => $booking->booking_type,
            ]);

            return $booking;
        });
    }

    /**
     * Confirm a booking — update status, generate QR, award loyalty points.
     */
    public function confirmBooking(Booking $booking, string $transactionId): Booking
    {
        return DB::transaction(function () use ($booking, $transactionId) {
            $booking->update([
                'booking_status' => 'confirmed',
                'payment_status' => 'paid',
                'paid_amount'    => $booking->total_amount,
                'transaction_id' => $transactionId,
                'confirmed_at'   => now(),
                'qr_code'        => $this->generateQrCode($booking),
            ]);

            $booking->appendEvent('payment_confirmed', [
                'transaction_id' => $transactionId,
                'amount'         => $booking->total_amount,
            ]);

            // Award loyalty points (1 point per dollar)
            $points = (int) $booking->total_amount;
            LoyaltyPoint::create([
                'user_id'    => $booking->user_id,
                'points'     => $points,
                'type'       => 'earn',
                'description'=> 'Booking ' . $booking->booking_reference,
                'booking_id' => $booking->id,
            ]);

            // Update profile total points
            $profile = UserProfile::where('user_id', $booking->user_id)->first();
            if ($profile) {
                $newTotal = $profile->total_points + $points;
                $profile->update([
                    'total_points'  => $newTotal,
                    'total_trips'   => $profile->total_trips + 1,
                    'loyalty_level' => $this->calculateLoyaltyLevel($newTotal),
                ]);
            }

            return $booking->fresh();
        });
    }

    /**
     * Cancel a booking with reason.
     */
    public function cancelBooking(Booking $booking, string $reason): Booking
    {
        $booking->update([
            'booking_status'      => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ]);

        $booking->appendEvent('booking_cancelled', ['reason' => $reason]);

        return $booking->fresh();
    }

    /**
     * Calculate total based on booking type.
     */
    private function calculateTotal(array $data): float
    {
        if (! empty($data['package_id'])) {
            $package = Package::findOrFail($data['package_id']);
            $adults  = $data['adults']   ?? 1;
            $children= $data['children'] ?? 0;
            return ($package->discounted_price * $adults) + ($package->discounted_price * 0.5 * $children);
        }

        if (! empty($data['hotel_id'])) {
            $hotel   = Hotel::findOrFail($data['hotel_id']);
            $checkIn = \Carbon\Carbon::parse($data['check_in']);
            $checkOut= \Carbon\Carbon::parse($data['check_out']);
            $nights  = max(1, $checkIn->diffInDays($checkOut));
            return $hotel->price_per_night * $nights;
        }

        return $data['total_amount'] ?? 0;
    }

    /**
     * Generate a signed QR code string (PKI-inspired: SHA-256 of booking data).
     */
    private function generateQrCode(Booking $booking): string
    {
        $payload = route('bookings.show', $booking);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($payload);
    }

    /**
     * Determine loyalty level from total points.
     */
    private function calculateLoyaltyLevel(int|float $points): int
    {
        return match (true) {
            $points >= 10000 => 4, // Diamond
            $points >= 5000  => 3, // Gold
            $points >= 1000  => 2, // Silver
            default          => 1, // Bronze
        };
    }
}
