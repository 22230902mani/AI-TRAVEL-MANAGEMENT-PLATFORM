<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->isGuide()) {
                return redirect()->route('guide.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $featured_destinations = Destination::active()->featured()->with('packages')->limit(6)->get();
        $featured_packages     = Package::active()->featured()->with('destination')->limit(6)->get();
        $recent_reviews        = Review::verified()->notFlagged()->with('user')->latest()->limit(6)->get();
        
        $stats = [
            'destinations' => Destination::active()->count(),
            'packages'     => Package::active()->count(),
            'travelers'    => \App\Models\User::count(),
            'reviews'      => Review::verified()->count(),
        ];

        // ── Unified Analytics Dashboard Data ──────────────────────────
        $analytics = [
            'total_spent'     => 0,
            'total_savings'   => 0,
            'budget_forecast' => 0,
            'monthly_labels'  => [],
            'monthly_amounts' => [],
            'category_shares' => [0, 0, 0, 0, 0], // Accommodation, Food, Transport, Shopping, Activities
        ];

        if (auth()->check()) {
            $user = auth()->user();
            $bookings = $user->bookings()->where('payment_status', 'paid')->get();
            $analytics['total_spent'] = $bookings->sum('total_amount');
            
            // Savings estimate (simulated 18% savings via smart optimizer)
            $analytics['total_savings'] = round($analytics['total_spent'] * 0.18);
            
            // Next trip budget forecast (simulated gradient-boosted regression)
            $analytics['budget_forecast'] = $analytics['total_spent'] > 0 ? round($analytics['total_spent'] * 0.92) : 15000;

            // Last 6 months spend trend
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $analytics['monthly_labels'][] = $month->format('M Y');
                $analytics['monthly_amounts'][] = $bookings
                    ->filter(fn($b) => \Carbon\Carbon::parse($b->created_at)->format('Y-m') === $month->format('Y-m'))
                    ->sum('total_amount');
            }

            // Category breakdown
            $packages = $bookings->where('booking_type', 'package')->sum('total_amount');
            $hotels   = $bookings->where('booking_type', 'hotel')->sum('total_amount');
            
            // Distribute category shares
            $analytics['category_shares'] = [
                $hotels ?: 3500, // Accommodation
                round($analytics['total_spent'] * 0.20) ?: 2000, // Food
                $packages ?: 5000, // Transport
                round($analytics['total_spent'] * 0.10) ?: 1000, // Shopping
                round($analytics['total_spent'] * 0.15) ?: 1500, // Activities
            ];
        } else {
            // High fidelity landing stats for non-logged-in users
            $analytics['total_spent'] = 48500;
            $analytics['total_savings'] = 11200;
            $analytics['budget_forecast'] = 39800;
            $analytics['monthly_labels'] = ['Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026'];
            $analytics['monthly_amounts'] = [12000, 24000, 15000, 32000, 18000, 29000];
            $analytics['category_shares'] = [18000, 9500, 12000, 4000, 5000];
        }

        return view('home', compact('featured_destinations', 'featured_packages', 'recent_reviews', 'stats', 'analytics'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:20',
        ]);

        $ticket = \App\Models\SupportTicket::create([
            'user_id' => auth()->id(),
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        $admins = \App\Models\User::where('roles', 'like', '%admin%')->get();
        foreach ($admins as $admin) {
            \App\Models\TravelNotification::create([
                'user_id' => $admin->id,
                'type'    => 'new_contact_message',
                'title'   => 'New Contact: ' . $ticket->subject,
                'message' => "From: {$ticket->name} ({$ticket->email}). " . \Illuminate\Support\Str::limit($ticket->message, 80),
                'link'    => route('admin.tickets.reply', $ticket->id),
            ]);
        }

        return back()->with('success', '✅ Your message has been sent! We\'ll respond within 24 hours.');
    }
}
