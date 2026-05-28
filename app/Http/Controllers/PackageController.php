<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Destination;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::active()->with('destination');

        if ($request->filled('destination')) {
            $query->whereHas('destination', fn($q) =>
                $q->where('name', 'like', '%' . $request->destination . '%')
            );
        }
        if ($request->filled('type')) {
            $query->where('package_type', $request->type);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_person', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_person', '<=', $request->max_price);
        }
        if ($request->filled('duration')) {
            $query->where('duration_days', '<=', $request->duration);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('title','like',"%$s%")
                  ->orWhere('description','like',"%$s%")
            );
        }

        match ($request->sort ?? 'default') {
            'price_asc'  => $query->orderBy('price_per_person'),
            'price_desc' => $query->orderByDesc('price_per_person'),
            'popular'    => $query->orderByDesc('id'),
            'discount'   => $query->orderByDesc('discount_percent'),
            default      => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        $packages    = $query->paginate(9)->withQueryString();
        $destinations = Destination::active()->orderBy('name')->get();

        return view('packages.index', compact('packages', 'destinations'));
    }

    public function show(Package $package)
    {
        $package->load('destination', 'reviews.user');

        $priceHistory = $this->simulatePriceHistory($package);
        $relatedPackages = Package::active()
            ->where('destination_id', $package->destination_id)
            ->where('id', '!=', $package->id)
            ->limit(3)->get();

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('package_id', $package->id)->exists()
            : false;

        return view('packages.show', compact(
            'package', 'priceHistory', 'relatedPackages', 'isWishlisted'
        ));
    }

    /**
     * Simulate LSTM price history (7-day trend) for "best time to book" feature.
     */
    private function simulatePriceHistory(Package $package): array
    {
        $history = [];
        $base    = $package->price_per_person;
        for ($i = 30; $i >= 0; $i--) {
            $fluctuation = $base * (rand(-8, 12) / 100);
            $history[]   = [
                'date'  => now()->subDays($i)->format('M d'),
                'price' => round($base + $fluctuation, 2),
            ];
        }
        return $history;
    }
}
