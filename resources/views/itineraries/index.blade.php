@extends('layouts.app')
@section('title','My Itineraries')
@section('content')
<div style="background:linear-gradient(135deg,#0f0f2e,#0a1628);padding:4rem 2rem 2rem">
    <div style="max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
        <div><h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900">My Itineraries</h1>
            <p style="color:var(--muted)">{{ $itineraries->total() }} itineraries created</p></div>
        <a href="{{ route('itineraries.create') }}" class="btn btn-primary"><i class="fas fa-wand-magic-sparkles"></i> Generate New</a>
    </div>
</div>
<section class="section" style="padding-top:2rem">
    <div class="section-inner">
        @forelse($itineraries as $itin)
        <div class="card" style="padding:1.5rem;margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem">
                <div style="flex:1">
                    <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin-bottom:.4rem">
                        <h3 style="font-weight:700">{{ $itin->title }}</h3>
                        <span class="badge-pill {{ $itin->status==='active'?'badge-success':($itin->status==='completed'?'badge-primary':'badge-warning') }}" style="font-size:.75rem">{{ ucfirst($itin->status) }}</span>
                        @if($itin->is_public)<span class="badge-pill badge-primary" style="font-size:.72rem"><i class="fas fa-globe"></i> Public</span>@endif
                    </div>
                    <div style="color:var(--muted);font-size:.85rem">
                        📍 {{ $itin->destination?->name ?? 'Custom' }}
                        &nbsp;•&nbsp; 📅 {{ $itin->start_date?->format('M d') }} – {{ $itin->end_date?->format('M d, Y') }}
                        &nbsp;•&nbsp; ⏳ {{ $itin->duration_days }} days
                    </div>
                    @if($itin->budget)
                    <div style="margin-top:.75rem;max-width:300px">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--muted);margin-bottom:.3rem">
                            <span>Budget</span><span>₹{{ number_format($itin->spent) }} / ₹{{ number_format($itin->budget) }}</span>
                        </div>
                        <div style="height:5px;background:var(--surface2);border-radius:5px">
                            <div style="width:{{ min(100,$itin->budget_used_percent) }}%;height:100%;background:{{ $itin->budget_used_percent>80?'var(--accent)':'var(--secondary)' }};border-radius:5px"></div>
                        </div>
                    </div>
                    @endif
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                    <a href="{{ route('itineraries.show',$itin) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                    @if($itin->is_paid)
                    <a href="{{ route('expenses.index',$itin) }}" class="btn btn-outline btn-sm"><i class="fas fa-wallet"></i> Expenses</a>
                    @endif
                    <form method="POST" action="{{ route('itineraries.destroy',$itin) }}" onsubmit="return confirm('Delete this itinerary?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:4rem;color:var(--muted)">
            <i class="fas fa-map" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:.3"></i>
            No itineraries yet. <a href="{{ route('itineraries.create') }}" style="color:var(--primary)">Generate one with AI →</a>
        </div>
        @endforelse
        <div style="margin-top:1.5rem">{{ $itineraries->links() }}</div>
    </div>
</section>
@endsection
