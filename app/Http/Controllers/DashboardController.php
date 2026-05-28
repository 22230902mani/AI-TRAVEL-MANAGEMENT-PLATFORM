<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Booking;
use App\Models\TravelNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct() {}

    public function index()
    {
        if (auth()->user()->isGuide()) {
            return redirect()->route('guide.dashboard');
        }

        $user     = auth()->user()->load('profile','bookings','itineraries');
        $profile  = $user->profile;

        $recentBookings = $user->bookings()
            ->with('package.destination')
            ->latest()->limit(5)->get();

        $activeItineraries = $user->itineraries()
            ->whereIn('status',['planning','active'])
            ->with('destination')->latest()->limit(3)->get();

        $notifications = TravelNotification::where('user_id', $user->id)
            ->latest()->limit(5)->get();

        $totalBookingCost = $user->bookings()->whereIn('payment_status', ['paid', 'completed'])->sum('total_amount');
        $totalExpenses = \App\Models\Expense::where('user_id', $user->id)->sum('amount');
        $totalBudget = $user->itineraries()->sum('budget');
        
        $stats = [
            'total_bookings'     => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('booking_status','confirmed')->count(),
            'total_booking_cost' => $totalBookingCost,
            'total_expenses'     => $totalExpenses,
            'overall_spent'      => $totalBookingCost + $totalExpenses,
            'budget_remaining'   => max(0, $totalBudget - $totalExpenses),
            'itineraries'        => $user->itineraries()->count(),
            'reviews_given'      => $user->reviews()->count(),
            'wishlist_count'     => $user->wishlists()->count(),
            'loyalty_points'     => $profile?->total_points ?? 0,
            'loyalty_level'      => $profile?->loyalty_level_name ?? 'Bronze',
            'premium_bought'     => $user->bookings()->where('booking_type', 'itinerary')->whereIn('payment_status', ['paid', 'completed'])->count(),
            'packages_bought'    => $user->bookings()->whereIn('booking_type', ['package', 'destination'])->whereIn('payment_status', ['paid', 'completed'])->count(),
        ];

        $upcomingBooking = $user->bookings()->whereNotNull('check_in')->whereDate('check_in', '>=', now())->orderBy('check_in', 'asc')->first();
        if ($upcomingBooking) {
            $stats['upcoming_trip_text'] = ($upcomingBooking->package?->destination?->name ?? 'Trip') . ' (' . \Carbon\Carbon::parse($upcomingBooking->check_in)->format('M d') . ')';
        } else {
            $stats['upcoming_trip_text'] = 'None planned';
        }

        // Fetch user's real expenses and paid bookings for the charts
        $expenses = \App\Models\Expense::where('user_id', $user->id)->get();
        $paidBookings = $user->bookings()->whereIn('payment_status', ['paid', 'completed'])->get();
        
        $packageCost = $user->bookings()->whereIn('booking_type', ['package', 'destination'])->whereIn('payment_status', ['paid', 'completed'])->sum('total_amount');
        
        $categoryData = collect([
            'Tour Packages Amount' => $packageCost,
            'Packages Bought' => $stats['packages_bought'],
            'Premium Plans Bought' => $stats['premium_bought'],
        ]);
        
        // Month breakdown (merging both expenses and paid bookings)
        $monthData = collect();
        foreach ($expenses as $e) {
            $m = $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('M') : 'Unknown';
            $monthData[$m] = ($monthData[$m] ?? 0) + $e->amount;
        }
        foreach ($paidBookings as $b) {
            $m = $b->created_at ? \Carbon\Carbon::parse($b->created_at)->format('M') : 'Unknown';
            $monthData[$m] = ($monthData[$m] ?? 0) + $b->total_amount;
        }
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyExpenses = [];
        foreach($months as $m) {
            $monthlyExpenses[] = $monthData->get($m, 0);
        }

        $chartData = [
            'categories' => $categoryData->keys()->toArray(),
            'category_amounts' => $categoryData->values()->toArray(),
            'visual_amounts' => [1, 1, 1],
            'months' => $months,
            'monthly_amounts' => $monthlyExpenses
        ];

        return view('dashboard', compact(
            'user','profile','recentBookings','activeItineraries','notifications','stats', 'chartData'
        ));
    }

    public function profile()
    {
        $user    = auth()->user()->load('profile');
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        $stats = [
            'total_itineraries' => $user->itineraries()->count(),
            'total_bookings'    => $user->bookings()->count(),
        ];
        
        return view('profile.index', compact('user','profile','stats'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'nullable|string|max:20',
            'nationality'      => 'nullable|string|max:100',
            'bio'              => 'nullable|string|max:500',
            'travel_interests' => 'nullable|array',
            'avatar'           => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name]);

        $profileData = $request->only('phone','nationality','bio','travel_interests','preferred_language','preferred_currency');

        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars','public');
        }

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return back()->with('success', 'Profile updated successfully!');
    }

    public function wishlist()
    {
        $wishlist = auth()->user()->wishlists()
            ->with(['destination','package.destination'])->paginate(9);
        return view('profile.wishlist', compact('wishlist'));
    }

    public function toggleWishlist(Request $request)
    {
        $user = auth()->user();
        $type = $request->type; // destination, package
        $id   = $request->id;

        $existing = $user->wishlists()
            ->where("{$type}_id", $id)
            ->where('wishlistable_type', $type)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['wishlisted' => false]);
        }

        $user->wishlists()->create([
            "{$type}_id"       => $id,
            'wishlistable_type'=> $type,
        ]);

        return response()->json(['wishlisted' => true]);
    }

    public function notifications()
    {
        $notifications = TravelNotification::where('user_id', auth()->id())
            ->latest()->paginate(20);

        TravelNotification::where('user_id', auth()->id())
            ->where('is_read', false)->update(['is_read' => true]);

        return view('profile.notifications', compact('notifications'));
    }
}
