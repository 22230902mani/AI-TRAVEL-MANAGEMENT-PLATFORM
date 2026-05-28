@extends('layouts.app')
@section('title','My Wishlist')
@section('content')
<section class="section">
    <div class="section-inner">
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:2rem">❤️ My Wishlist</h1>
        @forelse($wishlist as $item)
        <div class="card" style="padding:1.25rem;margin-bottom:.75rem;display:flex;align-items:center;gap:1rem">
            @if($item->destination)<img src="{{ $item->destination->image_url ?? asset('images/placeholder.png') }}" style="width:80px;height:55px;object-fit:cover;border-radius:8px;flex-shrink:0">@endif
            @if($item->package?->destination)<img src="{{ $item->package->destination->image_url ?? asset('images/placeholder.png') }}" style="width:80px;height:55px;object-fit:cover;border-radius:8px;flex-shrink:0">@endif
            <div style="flex:1">
                <div style="font-weight:700">{{ $item->destination?->name ?? $item->package?->title }}</div>
                <div style="font-size:.82rem;color:var(--muted)">{{ $item->destination?->country ?? $item->package?->destination?->name }}</div>
            </div>
            @if($item->destination)<a href="{{ route('destinations.show',$item->destination) }}" class="btn btn-outline btn-sm">View</a>@endif
            @if($item->package)<a href="{{ route('packages.show',$item->package) }}" class="btn btn-primary btn-sm">View Package</a>@endif
        </div>
        @empty
        <div style="text-align:center;padding:4rem;color:var(--muted)">
            <i class="fas fa-heart" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:.3"></i>
            Your wishlist is empty. <a href="{{ route('destinations.index') }}" style="color:var(--primary)">Browse destinations →</a>
        </div>
        @endforelse
        {{ $wishlist->links() }}
    </div>
</section>
@endsection
