@extends('layouts.admin')
@section('title','Admin — Guide Requests')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.75rem">
        <div>
            <h1 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0"><i class="fas fa-user-tie" style="color:#14b8a6;margin-right:.6rem"></i> Guide Requests</h1>
            <p style="color:rgba(255,255,255,0.5);font-size:.85rem;margin-top:.3rem">Review and manage guide applications</p>
        </div>
        <div style="display:flex;gap:.75rem">
            <span style="background:rgba(245,158,11,0.15);color:#f59e0b;padding:.4rem 1rem;border-radius:50px;font-size:.8rem;font-weight:700">
                <i class="fas fa-clock"></i> {{ $pending->count() }} Pending
            </span>
            <span style="background:rgba(20,184,166,0.15);color:#14b8a6;padding:.4rem 1rem;border-radius:50px;font-size:.8rem;font-weight:700">
                <i class="fas fa-check"></i> {{ $approved->count() }} Approved
            </span>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(20,184,166,0.1);border:1px solid rgba(20,184,166,0.3);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#14b8a6;font-weight:600">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- PENDING REQUESTS --}}
    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:18px;margin-bottom:2rem;overflow:hidden">
        <div style="background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(245,158,11,0.04));padding:1.1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.07);display:flex;align-items:center;gap:.75rem">
            <i class="fas fa-hourglass-half" style="color:#f59e0b"></i>
            <span style="font-weight:700;color:#fff;font-size:1rem">Pending Approval</span>
            <span style="background:rgba(245,158,11,0.2);color:#f59e0b;padding:.15rem .65rem;border-radius:50px;font-size:.75rem;font-weight:700">{{ $pending->count() }}</span>
        </div>
        @forelse($pending as $g)
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
            <div style="display:flex;align-items:center;gap:1rem;flex:1;min-width:220px">
                <img src="{{ $g->avatar_url }}" alt="avatar" style="width:48px;height:48px;border-radius:50%;border:2px solid rgba(245,158,11,0.4)">
                <div>
                    <div style="font-weight:700;color:#fff;font-size:.95rem">{{ $g->name }}</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,0.5)">{{ $g->email }}</div>
                    @if($g->guide_specialty)
                    <div style="font-size:.75rem;color:#f59e0b;margin-top:.2rem"><i class="fas fa-compass"></i> {{ $g->guide_specialty }}</div>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:.5rem;flex-shrink:0;font-size:.78rem;color:rgba(255,255,255,0.5)">
                @if($g->guide_phone)<span><i class="fas fa-phone"></i> {{ $g->guide_phone }}</span>@endif
                @if($g->guide_experience)<span style="margin-left:.75rem"><i class="fas fa-star"></i> {{ $g->guide_experience }} yrs</span>@endif
                <span style="margin-left:.75rem"><i class="fas fa-calendar"></i> {{ $g->created_at->diffForHumans() }}</span>
            </div>
            <div style="display:flex;gap:.6rem;flex-shrink:0">
                <form method="POST" action="{{ route('admin.guides.approve', $g) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:linear-gradient(135deg,#059669,#10b981);color:#fff;border:none;border-radius:10px;padding:.5rem 1.1rem;font-size:.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.4rem;transition:.3s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.guides.reject', $g) }}">
                    @csrf @method('PATCH')
                    <button type="submit" onclick="return confirm('Reject guide application for {{ $g->name }}?')" style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:.5rem 1.1rem;font-size:.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.4rem;transition:.3s" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:3rem;color:rgba(255,255,255,0.35)">
            <i class="fas fa-inbox" style="font-size:2.5rem;margin-bottom:1rem;display:block;opacity:.4"></i>
            No pending guide requests at the moment.
        </div>
        @endforelse
    </div>

    {{-- APPROVED GUIDES --}}
    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:18px;margin-bottom:2rem;overflow:hidden">
        <div style="background:linear-gradient(135deg,rgba(20,184,166,0.12),rgba(20,184,166,0.04));padding:1.1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.07);display:flex;align-items:center;gap:.75rem">
            <i class="fas fa-circle-check" style="color:#14b8a6"></i>
            <span style="font-weight:700;color:#fff;font-size:1rem">Approved Guides</span>
            <span style="background:rgba(20,184,166,0.2);color:#14b8a6;padding:.15rem .65rem;border-radius:50px;font-size:.75rem;font-weight:700">{{ $approved->count() }}</span>
        </div>
        @forelse($approved as $g)
        <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div style="display:flex;align-items:center;gap:1rem;flex:1">
                <img src="{{ $g->avatar_url }}" alt="avatar" style="width:44px;height:44px;border-radius:50%;border:2px solid rgba(20,184,166,0.4)">
                <div>
                    <div style="font-weight:600;color:#fff;font-size:.9rem">{{ $g->name }}</div>
                    <div style="font-size:.77rem;color:rgba(255,255,255,0.5)">{{ $g->email }}</div>
                </div>
            </div>
            <div style="display:flex;gap:.5rem;font-size:.78rem;color:rgba(255,255,255,0.5)">
                @if($g->guide_specialty)<span><i class="fas fa-compass" style="color:#14b8a6"></i> {{ $g->guide_specialty }}</span>@endif
            </div>
            <span style="background:rgba(20,184,166,0.1);color:#14b8a6;padding:.25rem .85rem;border-radius:50px;font-size:.72rem;font-weight:700">✓ Active</span>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:rgba(255,255,255,0.35);font-size:.85rem">No approved guides yet.</div>
        @endforelse
    </div>

    {{-- REJECTED GUIDES --}}
    @if($rejected->count() > 0)
    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:18px;overflow:hidden">
        <div style="background:linear-gradient(135deg,rgba(239,68,68,0.1),rgba(239,68,68,0.04));padding:1.1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.07);display:flex;align-items:center;gap:.75rem">
            <i class="fas fa-ban" style="color:#ef4444"></i>
            <span style="font-weight:700;color:#fff;font-size:1rem">Rejected</span>
        </div>
        @foreach($rejected as $g)
        <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:space-between;gap:1rem;opacity:.6">
            <div style="display:flex;align-items:center;gap:1rem">
                <img src="{{ $g->avatar_url }}" alt="avatar" style="width:40px;height:40px;border-radius:50%;border:2px solid rgba(239,68,68,0.4)">
                <div>
                    <div style="font-weight:600;color:#fff;font-size:.88rem">{{ $g->name }}</div>
                    <div style="font-size:.76rem;color:rgba(255,255,255,0.5)">{{ $g->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.guides.approve', $g) }}">
                @csrf @method('PATCH')
                <button type="submit" style="background:rgba(20,184,166,0.1);color:#14b8a6;border:1px solid rgba(20,184,166,0.3);border-radius:10px;padding:.4rem .9rem;font-size:.78rem;font-weight:700;cursor:pointer">
                    <i class="fas fa-undo"></i> Re-approve
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
