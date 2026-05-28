<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Self-healing: Ensure any paid itineraries have a corresponding Booking record
        try {
            $paidItineraries = auth()->user()->itineraries()->where('is_paid', true)->get();
            foreach ($paidItineraries as $itinerary) {
                $exists = auth()->user()->bookings()->where('itinerary_id', $itinerary->id)->exists();
                if (!$exists) {
                    \App\Models\Booking::create([
                        'user_id'        => auth()->id(),
                        'itinerary_id'   => $itinerary->id,
                        'booking_type'   => 'itinerary',
                        'total_amount'   => 99.00,
                        'paid_amount'    => 99.00,
                        'payment_status' => 'paid',
                        'booking_status' => 'confirmed',
                        'payment_method' => 'razorpay',
                        'transaction_id' => 'retroactive_' . strtolower(\Illuminate\Support\Str::random(12)),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Self-healing Bookings creation failed: ' . $e->getMessage());
        }

        $bookings = auth()->user()->bookings()
            ->with(['package.destination', 'hotel', 'itinerary.destination'])
            ->latest()
            ->paginate(15);

        // Stats
        $allBookings = auth()->user()->bookings()->get();
        $stats = [
            'total_spent'    => $allBookings->where('payment_status', 'paid')->sum('total_amount'),
            'total_bookings' => $allBookings->count(),
            'confirmed'      => $allBookings->where('booking_status', 'confirmed')->count(),
            'pending'        => $allBookings->where('booking_status', 'pending')->count(),
            'cancelled'      => $allBookings->where('booking_status', 'cancelled')->count(),
        ];

        // Monthly spend data for chart (last 6 months)
        $monthlySpend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlySpend[] = [
                'label' => $month->format('M Y'),
                'amount' => $allBookings
                    ->where('payment_status', 'paid')
                    ->filter(fn($b) => \Carbon\Carbon::parse($b->created_at)->format('Y-m') === $month->format('Y-m'))
                    ->sum('total_amount'),
            ];
        }

        // Spending by type
        $byType = [
            'package' => $allBookings->where('booking_type', 'package')->where('payment_status', 'paid')->sum('total_amount'),
            'hotel'   => $allBookings->where('booking_type', 'hotel')->where('payment_status', 'paid')->sum('total_amount'),
            'other'   => $allBookings->whereNotIn('booking_type', ['package','hotel'])->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('transactions.index', compact('bookings', 'stats', 'monthlySpend', 'byType'));
    }
}
