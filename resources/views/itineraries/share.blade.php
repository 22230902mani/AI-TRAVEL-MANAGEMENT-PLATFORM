@extends('layouts.app')
@section('title',$itinerary->title.' — Shared Itinerary')
@section('content')
<section class="section">
    <div class="section-inner" style="max-width:900px;margin:0 auto">
        <div style="text-align:center;margin-bottom:2rem">
            <div class="section-tag">🌍 Shared Itinerary</div>
            <h1 style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;margin:.5rem 0">{{ $itinerary->title }}</h1>
            <p style="color:var(--muted)">Shared by <strong>{{ $itinerary->user->name }}</strong> · {{ $itinerary->destination?->name }} · {{ $itinerary->duration_days }} days</p>
        </div>
        @if($itinerary->days)
        @foreach($itinerary->days as $day)
        <div class="card" style="margin-bottom:1rem;overflow:hidden">
            <div style="padding:1rem 1.5rem;background:rgba(108,99,255,.1);border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
                <span style="font-weight:700">{{ $day['label'] }}</span>
                <span style="color:var(--secondary);font-size:.85rem">Est. ₹{{ number_format($day['day_cost'] ?? 0) }}</span>
            </div>
            <div style="padding:1.25rem">
                @foreach($day['slots'] as $slot)
                <div style="display:flex;gap:1rem;padding:.65rem 0;border-bottom:1px solid var(--border);font-size:.88rem">
                    <div style="min-width:130px;color:var(--muted);font-size:.8rem">{{ $slot['time'] }}</div>
                    <div>
                        <div style="font-weight:600">{{ $slot['activity'] }}</div>
                        <div style="color:var(--muted);font-size:.8rem">{{ $slot['notes'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
        <div style="text-align:center;margin-top:2rem">
            <a href="{{ route('register') }}" class="btn btn-primary" style="padding:.85rem 2rem"><i class="fas fa-rocket"></i> Create Your Own Itinerary</a>
        </div>
    </div>
</section>
@endsection
