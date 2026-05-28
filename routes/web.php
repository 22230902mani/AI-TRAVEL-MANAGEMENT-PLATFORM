<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\TripPlannerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuideDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;


// ── Public Routes ─────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'storeContact'])->name('contact.store');

// Trip Planner (public estimate, auth for premium)
Route::get('/planner', [TripPlannerController::class, 'index'])->name('planner.index');
Route::post('/planner/calculate', [TripPlannerController::class, 'calculate'])->name('planner.calculate');
Route::post('/planner/premium/order', [TripPlannerController::class, 'createPremiumOrder'])->middleware('auth')->name('planner.premium.order');
Route::post('/planner/premium/verify', [TripPlannerController::class, 'verifyPremium'])->middleware('auth')->name('planner.premium.verify');
Route::get('/planner/premium', [TripPlannerController::class, 'premium'])->middleware('auth')->name('planner.premium');


// Destinations
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/destinations/{destination}/book', [DestinationController::class, 'book'])->name('destinations.book')->middleware('auth');

// Packages
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Shared itinerary (public)
Route::get('/itinerary/share/{token}', [ItineraryController::class, 'share'])->name('itineraries.share');

// ── Socialite Auth Routes ───────────────────────────────────────────────────
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Auth Routes (Breeze) ──────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Authenticated User Routes ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/wishlist', [DashboardController::class, 'wishlist'])->name('wishlist');

    // Proxy API for City Search
    Route::get('/api/city-search', [ItineraryController::class, 'searchCity'])->name('api.city.search');
    Route::post('/wishlist/toggle', [DashboardController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/razorpay/order', [BookingController::class, 'createRazorpayOrder'])->name('bookings.razorpay.order');
    Route::post('/bookings/{booking}/razorpay/verify', [BookingController::class, 'verifyRazorpayPayment'])->name('bookings.razorpay.verify');
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'processPayment'])->name('bookings.process_payment');
    Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/book-guide', [BookingController::class, 'bookGuide'])->name('bookings.book-guide');
    Route::post('/bookings/apply-promo', [BookingController::class, 'applyPromo'])->name('bookings.apply_promo');

    // Hotels
    Route::get('/bookings/{booking}/hotels',          [HotelController::class, 'recommend'])->name('hotels.recommend');
    Route::get('/hotels/{hotel}',                     [HotelController::class, 'show'])->name('hotels.show');
    Route::get('/hotels/{hotel}/book',                [HotelController::class, 'book'])->name('hotels.book');
    Route::post('/hotels/{hotel}/book',               [HotelController::class, 'storeBooking'])->name('hotels.store');
    Route::get('/hotel-bookings/{booking}/payment',   [HotelController::class, 'payment'])->name('hotels.payment');
    Route::post('/hotel-bookings/{booking}/razorpay/order',  [HotelController::class, 'createRazorpayOrder'])->name('hotels.razorpay.order');
    Route::post('/hotel-bookings/{booking}/razorpay/verify', [HotelController::class, 'verifyPayment'])->name('hotels.razorpay.verify');
    Route::get('/hotel-bookings/{booking}/confirmation',     [HotelController::class, 'confirmation'])->name('hotels.confirmation');

    // Itineraries
    Route::get('/itineraries', [ItineraryController::class, 'index'])->name('itineraries.index');
    Route::get('/itineraries/create', [ItineraryController::class, 'create'])->name('itineraries.create');
    Route::post('/itineraries/generate', [ItineraryController::class, 'generate'])->name('itineraries.generate');
    Route::get('/itineraries/{itinerary}', [ItineraryController::class, 'show'])->name('itineraries.show');
    Route::get('/itineraries/{itinerary}/pdf', [ItineraryController::class, 'downloadPdf'])->name('itineraries.pdf');
    Route::get('/itineraries/{itinerary}/edit', [ItineraryController::class, 'edit'])->name('itineraries.edit');
    Route::put('/itineraries/{itinerary}', [ItineraryController::class, 'update'])->name('itineraries.update');
    Route::delete('/itineraries/{itinerary}', [ItineraryController::class, 'destroy'])->name('itineraries.destroy');
    Route::post('/itineraries/{itinerary}/replan', [ItineraryController::class, 'replan'])->name('itineraries.replan');
    Route::post('/itineraries/{itinerary}/unlock', [ItineraryController::class, 'unlockOrder'])->name('itineraries.unlock-order');
    Route::post('/itineraries/{itinerary}/verify', [ItineraryController::class, 'verifyPayment'])->name('itineraries.verify-payment');

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'dashboard'])->name('expenses.dashboard');
    Route::get('/itineraries/{itinerary}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/itineraries/{itinerary}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/itineraries/{itinerary}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');

    // AI Chatbot
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
});

// ── Guide Routes ──────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('guide')->name('guide.')->group(function () {
    Route::get('/dashboard', [GuideDashboardController::class, 'index'])->name('dashboard');
    Route::get('/assigned-bookings', [GuideDashboardController::class, 'assignedBookings'])->name('assigned-bookings');
    Route::get('/bookings/{booking}/manifest-pdf', [GuideDashboardController::class, 'downloadManifestPdf'])->name('manifest-pdf');
});


// ── Admin Routes ──────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super_admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/toggle-status', [AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');

    // Bookings
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/refund', [AdminDashboardController::class, 'processRefund'])->name('bookings.refund');
    Route::post('/bookings/{booking}/assign-guide', [AdminDashboardController::class, 'assignGuide'])->name('bookings.assign_guide');
    Route::post('/bookings/{booking}/notify-guide', [AdminDashboardController::class, 'notifyGuide'])->name('bookings.notify_guide');

    // Destinations
    Route::get('/destinations', [AdminDashboardController::class, 'destinations'])->name('destinations');
    Route::post('/destinations', [AdminDashboardController::class, 'storeDestination'])->name('destinations.store');
    Route::put('/destinations/{destination}', [AdminDashboardController::class, 'updateDestination'])->name('destinations.update');
    Route::patch('/destinations/{destination}/pricing', [AdminDashboardController::class, 'updatePricing'])->name('destinations.pricing');
    Route::patch('/destinations/{destination}/featured', [AdminDashboardController::class, 'toggleFeatured'])->name('destinations.toggleFeatured');

    // Packages
    Route::get('/packages', [AdminDashboardController::class, 'packages'])->name('packages');
    Route::post('/packages', [AdminDashboardController::class, 'storePackage'])->name('packages.store');
    Route::put('/packages/{package}', [AdminDashboardController::class, 'updatePackage'])->name('packages.update');
    Route::patch('/packages/{package}/toggle', [AdminDashboardController::class, 'togglePackageStatus'])->name('packages.toggle');

    // Tickets
    Route::get('/tickets', [AdminDashboardController::class, 'tickets'])->name('tickets');
    Route::post('/tickets/{ticket}/respond', [AdminDashboardController::class, 'respondTicket'])->name('tickets.respond');
    Route::get('/tickets/{ticket}/reply', [AdminDashboardController::class, 'replyTicket'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/send-reply', [AdminDashboardController::class, 'sendTicketReply'])->name('tickets.send_reply');

    // Reviews
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{review}/flag', [AdminDashboardController::class, 'flagReview'])->name('reviews.flag');

    // Guide Approvals
    Route::get('/guides', [AdminDashboardController::class, 'guideRequests'])->name('guides');
    Route::patch('/guides/{user}/approve', [AdminDashboardController::class, 'approveGuide'])->name('guides.approve');
    Route::patch('/guides/{user}/reject', [AdminDashboardController::class, 'rejectGuide'])->name('guides.reject');

    // Team Avatars Management
    Route::get('/team-avatars', [AdminDashboardController::class, 'teamAvatars'])->name('team_avatars');
    Route::post('/team-avatars', [AdminDashboardController::class, 'uploadTeamAvatar'])->name('team_avatars.upload');
});
