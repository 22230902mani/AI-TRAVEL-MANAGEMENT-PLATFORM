<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class GuideDashboardController extends Controller
{
    public function index()
    {
        $guide = auth()->user();

        // Block non-approved guides
        if (!$guide->isApprovedGuide()) {
            abort(403, 'Access denied.');
        }

        // Bookings assigned to this guide
        $assignedBookings = Booking::where('guide_id', (string) $guide->id)
            ->with('package.destination')
            ->latest()
            ->get();

        // Upcoming trips (check_in >= today)
        $upcomingTrips = Booking::where('guide_id', (string) $guide->id)
            ->whereNotNull('check_in')
            ->whereDate('check_in', '>=', now())
            ->orderBy('check_in', 'asc')
            ->with('package.destination')
            ->get();

        // Stats
        $stats = [
            'total_assigned'   => $assignedBookings->count(),
            'upcoming_count'   => $upcomingTrips->count(),
            'completed_trips'  => $assignedBookings->where('booking_status', 'completed')->count(),
            'confirmed_trips'  => $assignedBookings->where('booking_status', 'confirmed')->count(),
        ];

        return view('guide.dashboard', compact('guide', 'assignedBookings', 'upcomingTrips', 'stats'));
    }

    public function assignedBookings()
    {
        $guide = auth()->user();

        // Block non-approved guides
        if (!$guide->isApprovedGuide()) {
            abort(403, 'Access denied.');
        }

        // Bookings assigned to this guide with traveler details
        $assignedBookings = Booking::where('guide_id', (string) $guide->id)
            ->with(['user.profile', 'package.destination', 'hotel'])
            ->latest()
            ->paginate(10);

        return view('guide.assigned_bookings', compact('guide', 'assignedBookings'));
    }

    public function downloadManifestPdf(Booking $booking)
    {
        $guide = auth()->user();

        // Block non-approved guides
        if (!$guide->isApprovedGuide()) {
            abort(403, 'Access denied.');
        }

        // Security check: ensure this guide is indeed assigned to this booking, or user is admin
        if ($booking->guide_id !== (string) $guide->id && !$guide->isAdmin()) {
            abort(403, 'Unauthorized access to manifest.');
        }

        // Load relationships
        $booking->load(['user.profile', 'package.destination', 'itinerary', 'hotel']);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('guide.manifest_pdf', compact('booking', 'guide'));

        return $pdf->download('TravelMate-Manifest-' . $booking->booking_reference . '.pdf');
    }
}
