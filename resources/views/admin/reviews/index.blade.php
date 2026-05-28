@extends('layouts.admin')
@section('title','Admin — Reviews')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900"><i class="fas fa-flag" style="color:var(--accent)"></i> Review Moderation</h1>
        <div style="display:flex;gap:.75rem">
            <a href="{{ route('admin.reviews').'?flagged=1' }}" class="btn {{ request('flagged')?'btn-primary':'btn-outline' }} btn-sm">Flagged Only</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>
    
    <div class="card" style="overflow-x:auto;">
        <table style="width:100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background:var(--surface2); border-bottom:1px solid var(--border);">
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">User</th>
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">Rating</th>
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">Type</th>
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">Review</th>
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">Status</th>
                    <th style="padding:1rem; color:var(--muted); font-weight:600; font-size:.85rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr style="border-bottom:1px solid var(--border); {{ $review->is_flagged ? 'background:rgba(219, 39, 119, 0.05);' : '' }}">
                    <td style="padding:1rem;">
                        <div style="font-weight:600; font-size:.9rem;">{{ $review->user?->name ?? 'Unknown User' }}</div>
                        <div style="font-size:.75rem; color:var(--muted);">{{ $review->created_at->format('M d, Y') }}</div>
                    </td>
                    <td style="padding:1rem;">
                        <div class="stars" style="font-size:.85rem; color:var(--gold);">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star" style="color: {{ $i <= $review->rating ? 'var(--gold)' : 'rgba(255,255,255,0.2)' }}"></i>
                            @endfor
                        </div>
                    </td>
                    <td style="padding:1rem;">
                        <span class="badge-pill badge-primary" style="font-size:.72rem;">{{ ucfirst($review->reviewable_type) }}</span>
                    </td>
                    <td style="padding:1rem; max-width:300px;">
                        <p style="color:var(--text); font-size:.85rem; margin-bottom:.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $review->body }}</p>
                    </td>
                    <td style="padding:1rem;">
                        @if($review->is_flagged)
                            <span class="badge-pill badge-danger" style="font-size:.72rem;"><i class="fas fa-flag"></i> Flagged</span>
                        @endif
                        @if($review->is_verified)
                            <span class="badge-pill badge-success" style="font-size:.72rem;"><i class="fas fa-check"></i> Verified</span>
                        @endif
                    </td>
                    <td style="padding:1rem;">
                        <form method="POST" action="{{ route('admin.reviews.flag',$review) }}">
                            @csrf
                            <button type="submit" class="btn {{ $review->is_flagged ? 'btn-outline' : 'btn-danger' }} btn-sm" style="padding:.25rem .75rem;">
                                <i class="fas fa-flag"></i> {{ $review->is_flagged ? 'Unflag' : 'Flag' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:2rem; text-align:center; color:var(--muted);">
                        No reviews found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem">{{ $reviews->links() }}</div>
</div>
@endsection
