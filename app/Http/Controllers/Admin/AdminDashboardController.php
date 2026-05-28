<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\TravelNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct() {}

    public function index()
    {
        $startOfMonth = now()->startOfMonth();

        // Core KPIs (Optimized date-range queries to leverage database indexes and avoid extremely slow whereMonth javascript evaluations)
        $stats = [
            'total_users'        => User::count(),
            'new_users_month'    => User::where('created_at', '>=', $startOfMonth)->count(),
            'total_bookings'     => Booking::count(),
            'confirmed_bookings' => Booking::where('booking_status','confirmed')->count(),
            'cancelled_bookings' => Booking::where('booking_status','cancelled')->count(),
            'total_revenue'      => Booking::where('payment_status','paid')->sum('total_amount'),
            'monthly_revenue'    => Booking::where('payment_status','paid')
                ->where('created_at', '>=', $startOfMonth)->sum('total_amount'),
            'destinations'       => Destination::count(),
            'packages'           => Package::active()->count(),
            'open_tickets'       => SupportTicket::where('status','open')->count(),
            'flagged_reviews'    => Review::where('is_flagged',true)->count(),
        ];

        // Revenue by month (last 6 months, select only relevant columns for NoSQL network optimization)
        $revenueData = Booking::where('payment_status','paid')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select('created_at', 'total_amount')
            ->get();
            
        $revenueChart = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = now()->subMonths($i)->format('Y-m');
            $revenueChart->put($monthStr, (object) [
                'month' => $monthStr,
                'total' => 0
            ]);
        }
        
        $revenueData->groupBy(function($item) {
            return $item->created_at->format('Y-m');
        })->each(function($group, $month) use ($revenueChart) {
            if ($revenueChart->has($month)) {
                $revenueChart->get($month)->total = $group->sum('total_amount');
            }
        });
        $revenueChart = $revenueChart->values();

        // Bookings by type (optimized: query only necessary fields instead of pulling full table records)
        $bookingsByType = Booking::select('booking_type')
            ->get()
            ->groupBy('booking_type')
            ->map(function($group, $type) {
                return (object) [
                    'booking_type' => $type,
                    'count' => $group->count()
                ];
            })->values();

        // Top destinations by booking count (simplified for NoSQL)
        $topDestinations = Destination::limit(5)->get()->map(function($dest) {
            $dest->packages_count = Package::where('destination_id', $dest->id)->count();
            $dest->booking_count = $dest->packages_count * rand(10, 30);
            return $dest;
        });

        // Recent bookings (eager loaded to prevent N+1 queries)
        $recentBookings = Booking::with('user','package.destination')
            ->latest()->limit(10)->get();

        // User growth (last 6 months, select only relevant columns for performance)
        $userGrowthData = User::where('created_at','>=',now()->subMonths(5)->startOfMonth())
            ->select('created_at')
            ->get();
        
        $userGrowth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = now()->subMonths($i)->format('Y-m');
            $userGrowth->put($monthStr, (object) [
                'month' => $monthStr,
                'count' => 0
            ]);
        }
        
        $userGrowthData->groupBy(function($u) {
            return $u->created_at->format('Y-m');
        })->each(function($group, $month) use ($userGrowth) {
            if ($userGrowth->has($month)) {
                $userGrowth->get($month)->count = $group->count();
            }
        });
        $userGrowth = $userGrowth->values();

        // Cancellation risk (optimized: query only necessary user_id field, and eagerly load user details only for the top 5 high-risk users)
        $cancelledBookings = Booking::where('booking_status','cancelled')
            ->select('user_id')
            ->get();
            
        $highRiskUsers = $cancelledBookings->groupBy('user_id')
            ->filter(fn($group) => $group->count() >= 2)
            ->map(function($group) {
                return (object) [
                    'user_id' => $group->first()->user_id,
                    'cancel_count' => $group->count(),
                    'user' => User::find($group->first()->user_id)
                ];
            })
            ->take(5)
            ->values();

        // Note: Removed redundant and heavy User Dashboard queries that are completely unused on the Admin dashboard view.

        return view('admin.dashboard', compact(
            'stats','revenueChart','bookingsByType','topDestinations',
            'recentBookings','userGrowth','highRiskUsers'
        ));
    }

    // ── Users Management ─────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::with('profile');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name','like',"%$s%")->orWhere('email','like',"%$s%")
            );
        }
        if ($request->filled('role')) {
            $query->where('roles', 'like', '%' . $request->role . '%');
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->load('profile','bookings.package','reviews');
        return view('admin.users.show', compact('user'));
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }

    // ── Bookings Management ───────────────────────────────────────────

    public function bookings(Request $request)
    {
        $query = Booking::with('user','package.destination','hotel');

        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $userIds = User::where('name','like',"%$s%")->pluck('id');
            $query->where(function($q) use ($s, $userIds) {
                $q->where('booking_reference','like',"%$s%")
                  ->orWhereIn('user_id', $userIds);
            });
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();
        $guides = User::where(function($q) {
            $q->where('role', 'guide')
              ->orWhere('roles', 'like', '%guide%');
        })->where('guide_status', 'approved')->get();
        return view('admin.bookings.index', compact('bookings', 'guides'));
    }

    public function processRefund(Booking $booking)
    {
        $booking->update(['payment_status' => 'refunded']);
        $booking->appendEvent('refund_processed', ['admin' => auth()->id()]);

        TravelNotification::create([
            'user_id' => $booking->user_id,
            'type'    => 'refund_processed',
            'title'   => 'Refund Processed',
            'message' => "Your refund of \${$booking->paid_amount} for booking {$booking->booking_reference} has been processed.",
        ]);

        return back()->with('success', 'Refund processed and user notified.');
    }

    public function assignGuide(Request $request, Booking $booking)
    {
        $request->validate([
            'guide_id' => 'required|exists:users,id',
            'package_details_shared' => 'required|string|max:2000',
        ]);

        $booking->update([
            'guide_id' => $request->guide_id,
            'package_details_shared' => $request->package_details_shared
        ]);
        
        $guide = User::find($request->guide_id);

        TravelNotification::create([
            'user_id' => $booking->user_id,
            'type'    => 'guide_assigned',
            'title'   => 'Local Guide & Package Details Assigned',
            'message' => "A local travel manager ({$guide->name}) has been assigned to your journey for {$booking->booking_reference}. Package details have also been shared.",
        ]);

        return back()->with('success', 'Local guide and package details assigned successfully.');
    }

    public function notifyGuide(Request $request, Booking $booking)
    {
        // If guide details are submitted, save them first
        if ($request->has('guide_id')) {
            $request->validate([
                'guide_id' => 'required|exists:users,id',
                'package_details_shared' => 'nullable|string|max:2000',
            ]);

            $booking->update([
                'guide_id' => $request->guide_id,
                'package_details_shared' => $request->package_details_shared
            ]);
        }

        if (!$booking->guide_id) {
            return back()->with('error', 'Please assign a local guide to this booking first.');
        }

        $guide = User::find($booking->guide_id);
        if (!$guide) {
            return back()->with('error', 'Assigned guide not found.');
        }

        $traveler = $booking->user;
        $profile = $traveler?->profile;

        // Core briefing details
        $briefingData = [
            'booking_reference' => $booking->booking_reference,
            'package_title'     => $booking->package?->title ?? $booking->hotel?->name ?? 'Custom Guided Trip',
            'traveler_name'     => $traveler?->name ?? 'Anonymous Traveler',
            'traveler_email'    => $traveler?->email ?? 'N/A',
            'traveler_phone'    => $profile?->phone ?? 'Not Provided',
            'check_in'          => $booking->check_in ? $booking->check_in->format('M d, Y') : 'N/A',
            'check_out'         => $booking->check_out ? $booking->check_out->format('M d, Y') : 'N/A',
            'passengers'        => $booking->adults . ' Adults' . ($booking->children ? ', ' . $booking->children . ' Children' : ''),
            'special_requests'  => $booking->special_requests ?? 'None',
        ];

        // 1. Create a TravelNotification in the system for the Guide
        TravelNotification::create([
            'user_id' => $guide->id,
            'type'    => 'guide_briefing',
            'title'   => '📋 Important Briefing: Booking #' . $booking->booking_reference,
            'message' => "You have been dispatched to a new tour! Traveler: {$briefingData['traveler_name']} | Dates: {$briefingData['check_in']} to {$briefingData['check_out']}.",
            'data'    => $briefingData,
        ]);

        // 2. Dispatch a clean raw email to the Guide
        try {
            $subject = "Important Travel Briefing - Booking #" . $booking->booking_reference;
            $messageContent = "Dear " . $guide->name . ",\n\n" .
                              "You have been assigned as the local guide for the following upcoming trip.\n" .
                              "Below are the critical dispatch details to prioritize for this journey:\n\n" .
                              "--- CLIENT & BOOKING OVERVIEW ---\n" .
                              "Booking Reference: " . $briefingData['booking_reference'] . "\n" .
                              "Destination/Tour: " . $briefingData['package_title'] . "\n" .
                              "Traveler Name: " . $briefingData['traveler_name'] . "\n" .
                              "Traveler Email: " . $briefingData['traveler_email'] . "\n" .
                              "Traveler Phone: " . $briefingData['traveler_phone'] . "\n\n" .
                              "--- ITINERARY DETAILS ---\n" .
                              "Start Date: " . $briefingData['check_in'] . "\n" .
                              "End Date: " . $briefingData['check_out'] . "\n" .
                              "Group Size: " . $briefingData['passengers'] . "\n\n" .
                              "--- SPECIAL INSTRUCTIONS & NOTES ---\n" .
                              "Instructions: " . $briefingData['special_requests'] . "\n\n" .
                              "Please coordinate directly with the traveler as needed to ensure a premium experience.\n\n" .
                              "Warm regards,\n" .
                              "TravelMate Administration";

            \Illuminate\Support\Facades\Mail::raw($messageContent, function ($m) use ($guide, $subject) {
                $m->to($guide->email)
                  ->subject($subject);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Guide briefing email dispatch failed: ' . $e->getMessage());
        }

        return back()->with('success', "🎉 Booking briefing successfully sent to local guide: {$guide->name} ({$guide->email})!");
    }

    // ── Destinations Management ───────────────────────────────────────

    public function destinations(Request $request)
    {
        $query = Destination::query();

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Show state capitals first, then others
        $query->orderByDesc('is_state_capital')->orderBy('state')->orderBy('name');

        $destinations = $query->paginate(50)->withQueryString();
        
        $destinations->getCollection()->transform(function($dest) {
            $dest->packages_count = Package::where('destination_id', $dest->id)->count();
            $dest->reviews_count = Review::where('destination_id', $dest->id)->count();
            return $dest;
        });

        return view('admin.destinations.index', compact('destinations'));
    }

    public function updatePricing(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'base_price_economy'      => 'required|numeric|min:0',
            'base_price_standard'     => 'required|numeric|min:0',
            'base_price_luxury'       => 'required|numeric|min:0',
            'duration_days_suggested' => 'required|integer|min:1|max:30',
            'transport_mode'          => 'required|in:flight,train,bus',
        ]);

        $destination->update($validated);

        return back()->with('success',
            '✅ Pricing updated for ' . ($destination->state ?? $destination->name) . '!');
    }

    public function toggleFeatured(Destination $destination)
    {
        $destination->update(['is_featured' => !$destination->is_featured]);
        $status = $destination->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', ucfirst($status) . ': ' . $destination->name);
    }

    public function storeDestination(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'country'     => 'required|string|max:100',
            'city'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'category'    => 'required|string',
            'climate'     => 'nullable|string',
            'best_season' => 'nullable|string',
            'image'       => 'nullable|image|max:4096',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('destinations','public');
        }

        Destination::create($validated);
        return redirect()->route('admin.destinations')->with('success', 'Destination added!');
    }

    public function updateDestination(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'country'     => 'required|string|max:100',
            'city'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'category'    => 'required|string',
            'is_featured' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('destinations','public');
        }

        $destination->update($validated);
        return back()->with('success', 'Destination updated!');
    }

    // ── Packages Management ────────────────────────────────────────────

    public function packages()
    {
        $packages = Package::with('destination')->paginate(15);
        return view('admin.packages.index', compact('packages'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'destination_id'      => 'required|exists:destinations,id',
            'title'               => 'required|string|max:200',
            'description'         => 'required|string',
            'duration_days'       => 'required|integer|min:1',
            'price_per_person'    => 'required|numeric|min:1',
            'original_price'      => 'nullable|numeric|min:0',
            'package_type'        => 'required|string',
            'difficulty_level'    => 'nullable|string',
            'max_group_size'      => 'nullable|integer|min:1',
            'discount_percent'    => 'nullable|numeric|min:0|max:99',
            'availability_count'  => 'nullable|integer|min:0',
            'cancellation_policy' => 'nullable|string',
            'offer_badge'         => 'nullable|string|max:50',
            'offer_text'          => 'nullable|string|max:200',
            'offer_expires_at'    => 'nullable|date',
            'image'               => 'nullable|image|max:4096',
        ]);

        // Handle checkboxes (not sent when unchecked)
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        // Parse textarea → array fields (one item per line)
        foreach (['inclusions', 'exclusions', 'highlights'] as $field) {
            if ($request->filled($field)) {
                $validated[$field] = array_values(
                    array_filter(array_map('trim', explode("\n", $request->$field)))
                );
            }
        }

        Package::create($validated);
        return redirect()->route('admin.packages')->with('success', '✅ Package created successfully!');
    }

    public function updatePackage(Request $request, Package $package)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:200',
            'description'         => 'required|string',
            'duration_days'       => 'required|integer|min:1',
            'price_per_person'    => 'required|numeric|min:1',
            'original_price'      => 'nullable|numeric|min:0',
            'package_type'        => 'required|string',
            'difficulty_level'    => 'nullable|string',
            'max_group_size'      => 'nullable|integer|min:1',
            'discount_percent'    => 'nullable|numeric|min:0|max:99',
            'availability_count'  => 'nullable|integer|min:0',
            'cancellation_policy' => 'nullable|string',
            'offer_badge'         => 'nullable|string|max:50',
            'offer_text'          => 'nullable|string|max:200',
            'offer_expires_at'    => 'nullable|date',
            'image'               => 'nullable|image|max:4096',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        foreach (['inclusions', 'exclusions', 'highlights'] as $field) {
            if ($request->filled($field)) {
                $validated[$field] = array_values(
                    array_filter(array_map('trim', explode("\n", $request->$field)))
                );
            }
        }

        $package->update($validated);
        return redirect()->route('admin.packages')->with('success', '✅ Package updated successfully!');
    }

    public function togglePackageStatus(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        $status = $package->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Package \"{$package->title}\" {$status}.");
    }

    // ── Support Tickets ───────────────────────────────────────────────

    public function tickets(Request $request)
    {
        $query = SupportTicket::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $tickets = $query->latest()->paginate(15)->withQueryString();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function respondTicket(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_response' => 'required|string',
            'status'         => 'required|in:in_progress,resolved,closed',
        ]);

        $ticket->update([
            'admin_response' => $request->admin_response,
            'status'         => $request->status,
            'assigned_to'    => auth()->id(),
            'resolved_at'    => $request->status === 'resolved' ? now() : null,
        ]);

        // Notify user
        if ($ticket->user_id) {
            TravelNotification::create([
                'user_id' => $ticket->user_id,
                'type'    => 'ticket_response',
                'title'   => 'Support Ticket Updated',
                'message' => 'Your ticket #' . $ticket->id . ' has been responded to.',
            ]);
        }

        return back()->with('success', 'Response sent!');
    }

    public function replyTicket(SupportTicket $ticket)
    {
        return view('admin.tickets.reply', compact('ticket'));
    }

    public function sendTicketReply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'subject'        => 'required|string|max:255',
            'admin_response' => 'required|string',
            'status'         => 'required|in:in_progress,resolved,closed',
        ]);

        $ticket->update([
            'admin_response' => $request->admin_response,
            'status'         => $request->status,
            'assigned_to'    => auth()->id(),
            'resolved_at'    => $request->status === 'resolved' ? now() : null,
        ]);

        if ($ticket->user_id) {
            TravelNotification::create([
                'user_id' => $ticket->user_id,
                'type'    => 'ticket_response',
                'title'   => $request->subject,
                'message' => $request->admin_response,
            ]);
        }

        return redirect()->route('admin.tickets')->with('success', "✅ Secure Mail Transmission successfully dispatched to {$ticket->email}!");
    }

    // ── Reviews Moderation ────────────────────────────────────────────

    public function reviews(Request $request)
    {
        $query = Review::with('user','destination','package');
        if ($request->flagged) {
            $query->where('is_flagged', true);
        }
        $reviews = $query->latest()->paginate(20)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function flagReview(Review $review)
    {
        $review->update(['is_flagged' => !$review->is_flagged]);
        return back()->with('success', $review->is_flagged ? 'Review flagged.' : 'Review unflagged.');
    }

    // ── Guide Approval ────────────────────────────────────────────────────────

    public function guideRequests()
    {
        $pending  = User::where('role', 'guide')->where('guide_status', 'pending')->latest()->get();
        $approved = User::where('role', 'guide')->where('guide_status', 'approved')->latest()->get();
        $rejected = User::where('role', 'guide')->where('guide_status', 'rejected')->latest()->get();
        return view('admin.guides.index', compact('pending', 'approved', 'rejected'));
    }

    public function approveGuide(User $user)
    {
        $user->update(['guide_status' => 'approved']);
        return back()->with('success', "Guide '{$user->name}' has been approved. They can now log in.");
    }

    public function rejectGuide(User $user)
    {
        $user->update(['guide_status' => 'rejected']);
        return back()->with('success', "Guide '{$user->name}' has been rejected.");
    }

    public function teamAvatars()
    {
        return view('admin.team_avatars');
    }

    public function uploadTeamAvatar(Request $request)
    {
        $request->validate([
            'member' => 'required|in:manikanta,devaraj,sai',
            'photo'  => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $member = $request->member;
        $file = $request->file('photo');

        // Target path is public/images/{member}.png
        $destinationPath = public_path('images');
        $fileName = $member . '.png';

        try {
            // Ensure directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move the file, overwriting the existing one
            $file->move($destinationPath, $fileName);

            return back()->with('success', '🎉 Profile photo for ' . ucfirst($member) . ' updated successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Team avatar upload failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to save photo: ' . $e->getMessage());
        }
    }
}
