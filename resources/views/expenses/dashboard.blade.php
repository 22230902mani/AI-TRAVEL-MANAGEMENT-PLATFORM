@extends('layouts.app')
@section('title','Expense Dashboard')
@section('content')
<section class="section">
    <div class="section-inner">
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:2rem">💰 Expense Dashboard</h1>
        <div class="grid-4" style="margin-bottom:2rem">
            @foreach([['Total Budget','₹'.number_format($totalBudget),'#6c63ff','fa-piggy-bank'],['Total Spent','₹'.number_format($totalSpent),'#ff6b6b','fa-receipt'],['Remaining','₹'.number_format(max(0,$totalBudget-$totalSpent)),'#00d4aa','fa-wallet'],['Itineraries',$itineraries->count(),'#ffd700','fa-map']] as [$l,$v,$c,$i])
            <div class="card" style="padding:1.5rem;display:flex;align-items:center;gap:1rem">
                <div style="width:46px;height:46px;border-radius:12px;background:{{ $c }}22;display:flex;align-items:center;justify-content:center"><i class="fas {{ $i }}" style="color:{{ $c }}"></i></div>
                <div><div style="font-size:1.3rem;font-weight:800">{{ $v }}</div><div style="font-size:.82rem;color:var(--muted)">{{ $l }}</div></div>
            </div>
            @endforeach
        </div>
        <div class="grid-2">
            @foreach($itineraries as $itin)
            @if($itin->expenses->count())
            <div class="card" style="padding:1.5rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                    <div><div style="font-weight:700">{{ $itin->title }}</div><div style="font-size:.8rem;color:var(--muted)">{{ $itin->destination?->name }}</div></div>
                    <a href="{{ route('expenses.index',$itin) }}" class="btn btn-outline btn-sm">Details</a>
                </div>
                @foreach($itin->expenses->groupBy('category') as $cat=>$items)
                <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:.4rem">
                    <span style="color:var(--muted)">{{ ucfirst($cat) }}</span><span>₹{{ number_format($items->sum('amount'),2) }}</span>
                </div>
                @endforeach
                <div style="border-top:1px solid var(--border);padding-top:.75rem;margin-top:.5rem;font-weight:700;color:var(--secondary)">Total: ₹{{ number_format($itin->expenses->sum('amount'),2) }}</div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endsection
