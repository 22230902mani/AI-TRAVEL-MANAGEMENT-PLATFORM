@extends('layouts.admin')
@section('title','Admin — Support Tickets')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900"><i class="fas fa-headset" style="color:var(--gold)"></i> Support Tickets</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
    <form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.5rem">
        <select name="status" class="form-control" style="max-width:160px">
            <option value="">All statuses</option>
            @foreach(['open','in_progress','resolved','closed'] as $s)
            <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
    @foreach($tickets as $ticket)
    <div class="card" style="padding:1.5rem;margin-bottom:1rem">
        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div style="flex:1">
                <div style="display:flex;gap:.75rem;align-items:center;margin-bottom:.4rem;flex-wrap:wrap">
                    <span style="font-weight:700">#{{ $ticket->id }} — {{ $ticket->subject }}</span>
                    <span class="badge-pill {{ $ticket->status=='open'?'badge-danger':($ticket->status=='resolved'?'badge-success':'badge-warning') }}" style="font-size:.72rem">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    <span class="badge-pill badge-primary" style="font-size:.72rem">{{ ucfirst($ticket->priority) }}</span>
                </div>
                <div style="font-size:.82rem;color:var(--muted)">From: {{ $ticket->name }} ({{ $ticket->email }}) • {{ $ticket->created_at->diffForHumans() }}</div>
                <p style="color:var(--muted);font-size:.85rem;margin-top:.5rem">{{ Str::limit($ticket->message,120) }}</p>
                @if($ticket->admin_response)
                <div style="margin-top:.75rem;padding:.75rem;background:rgba(0,212,170,.08);border-radius:8px;font-size:.85rem">
                    <strong style="color:var(--secondary)">Admin Response:</strong> {{ $ticket->admin_response }}
                </div>
                @endif
                <div style="margin-top:1rem;">
                    <a href="{{ route('admin.tickets.reply', $ticket) }}" class="btn btn-sm btn-orange" style="padding:0.35rem 0.85rem;"><i class="fas fa-reply"></i> Auto Write Mail</a>
                </div>
            </div>
            <div>
                <form method="POST" action="{{ route('admin.tickets.respond',$ticket) }}" style="min-width:280px">
                    @csrf
                    <textarea name="admin_response" class="form-control" rows="2" placeholder="Write response..." style="margin-bottom:.5rem">{{ $ticket->admin_response }}</textarea>
                    <select name="status" class="form-control" style="margin-bottom:.5rem">
                        @foreach(['in_progress','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ $ticket->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">Send Response</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    <div style="margin-top:1.25rem">{{ $tickets->links() }}</div>
</div>
@endsection
