@extends('layouts.app')
@section('title','Notifications')
@section('content')
<section class="section">
    <div class="section-inner" style="max-width:700px;margin:0 auto">
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:2rem">🔔 Notifications</h1>
        @forelse($notifications as $notif)
        <div class="card" style="padding:1.25rem;margin-bottom:.75rem;border-left:3px solid {{ $notif->is_read?'var(--border)':'var(--primary)' }}">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
                <div><div style="font-weight:600{{ !$notif->is_read?';color:var(--text)':'' }}">{{ $notif->title }}</div>
                    <div style="color:var(--muted);font-size:.85rem;margin-top:.2rem">{{ $notif->message }}</div></div>
                <div style="font-size:.75rem;color:var(--muted);flex-shrink:0">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:4rem;color:var(--muted)">
            <i class="fas fa-bell" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:.3"></i>
            No notifications yet.
        </div>
        @endforelse
        {{ $notifications->links() }}
    </div>
</section>
@endsection
