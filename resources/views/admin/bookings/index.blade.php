@extends('layouts.admin')
@section('title','Admin — Bookings')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900"><i class="fas fa-ticket" style="color:var(--primary)"></i> Booking Management</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
    <form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ref or user name..." style="max-width:260px">
        <select name="status" class="form-control" style="max-width:160px">
            <option value="">All statuses</option>
            @foreach(['pending','confirmed','cancelled','completed'] as $s)
            <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
    <div class="card" style="overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead><tr style="background:var(--surface2)">
                @foreach(['Ref','User','Package','Amount','Status','Payment','Date','Actions'] as $h)
                <th style="padding:.85rem 1rem;text-align:left;font-size:.82rem;color:var(--muted);font-weight:600;border-bottom:1px solid var(--border)">{{ $h }}</th>
                @endforeach
            </tr></thead>
            <tbody>
            @foreach($bookings as $b)
            <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:.75rem 1rem;font-weight:600;font-size:.88rem">{{ $b->booking_reference }}</td>
                <td style="padding:.75rem 1rem;font-size:.85rem">{{ $b->user?->name }}<div style="font-size:.75rem;color:var(--muted)">{{ $b->user?->email }}</div></td>
                <td style="padding:.75rem 1rem;font-size:.85rem">{{ Str::limit($b->package?->title ?? $b->hotel?->name ?? 'N/A',30) }}</td>
                <td style="padding:.75rem 1rem;font-weight:700;color:var(--secondary)">₹{{ number_format($b->total_amount) }}</td>
                <td style="padding:.75rem 1rem"><span class="badge-pill {{ $b->booking_status=='confirmed'?'badge-success':($b->booking_status=='cancelled'?'badge-danger':'badge-warning') }}" style="font-size:.72rem">{{ ucfirst($b->booking_status) }}</span></td>
                <td style="padding:.75rem 1rem"><span class="badge-pill {{ $b->payment_status=='paid'?'badge-success':($b->payment_status=='refunded'?'badge-primary':'badge-warning') }}" style="font-size:.72rem">{{ ucfirst($b->payment_status) }}</span></td>
                <td style="padding:.75rem 1rem;font-size:.8rem;color:var(--muted)">{{ $b->created_at->format('M d, Y') }}</td>
                <td style="padding:.75rem 1rem">
                    <div style="display:flex;gap:.5rem;align-items:center">
                        <button type="button" class="btn btn-sm btn-outline" style="font-size:.75rem;padding:.2rem .5rem" onclick="openAssignModal('{{ $b->id }}', '{{ $b->booking_reference }}', '{{ $b->guide_id }}', `{{ addslashes($b->package_details_shared ?? '') }}`)">
                            <i class="fas fa-tasks"></i> Manage Booking
                        </button>
                        @if($b->guide_id)
                        <form method="POST" action="{{ route('admin.bookings.notify_guide', $b) }}">@csrf
                            <button type="submit" class="btn btn-sm" style="font-size:.75rem;padding:.2rem .5rem;background:#f59e0b;color:#fff;border:none;border-radius:6px;cursor:pointer" title="Send core briefing details directly to guide">
                                <i class="fas fa-paper-plane"></i> Send to Guide
                            </button>
                        </form>
                        @endif
                        @if($b->payment_status==='paid')
                        <form method="POST" action="{{ route('admin.bookings.refund',$b) }}">@csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Process refund for {{ $b->booking_reference }}?')" style="font-size:.75rem;padding:.2rem .5rem"><i class="fas fa-rotate-left"></i> Refund</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.25rem">{{ $bookings->links() }}</div>
</div>

<!-- Modal for Assigning Guide & Sharing Details -->
<div id="assignModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center">
    <div style="background:var(--surface);width:100%;max-width:500px;border-radius:16px;padding:2rem;position:relative;border:1px solid rgba(255,255,255,0.1)">
        <button type="button" onclick="closeAssignModal()" style="position:absolute;top:1rem;right:1rem;background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.2rem"><i class="fas fa-times"></i></button>
        <h2 style="font-family:'Playfair Display',serif;font-weight:800;margin-bottom:1.5rem;font-size:1.4rem">Manage Booking: <span id="modal-booking-ref" style="color:var(--primary)"></span></h2>
        
        <form id="assignForm" method="POST" action="">
            @csrf
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label" style="font-size:.85rem;color:var(--muted)">Assign Local Guide</label>
                <select name="guide_id" id="modal-guide-id" class="form-control" required onchange="toggleModalNotifyBtn(this.value)">
                    <option value="">-- Select a Guide --</option>
                    @foreach($guides as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom:1.5rem">
                <label class="form-label" style="font-size:.85rem;color:var(--muted)">Package Details to Share with User</label>
                <textarea name="package_details_shared" id="modal-package-details" class="form-control" rows="4" placeholder="Enter itinerary details, flight info, or welcome notes here..." required></textarea>
                <div style="font-size:.75rem;color:var(--muted);margin-top:.4rem"><i class="fas fa-info-circle"></i> This information will be displayed proudly on the user's dashboard.</div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;flex-wrap:wrap">
                <button type="button" class="btn btn-outline" onclick="closeAssignModal()">Cancel</button>
                <button type="button" class="btn btn-warning" id="modal-notify-guide-btn" onclick="submitNotifyGuideForm()" style="display:none;background:#f59e0b;color:#fff;border:none"><i class="fas fa-paper-plane"></i> Send Briefing to Guide</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save & Notify User</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentBookingId = null;

function openAssignModal(bookingId, ref, guideId, packageDetails) {
    currentBookingId = bookingId;
    document.getElementById('modal-booking-ref').textContent = ref;
    document.getElementById('modal-guide-id').value = guideId || '';
    document.getElementById('modal-package-details').value = packageDetails || '';
    
    // Build the action URL dynamically (assuming URL structure: /admin/bookings/{booking}/assign-guide)
    const form = document.getElementById('assignForm');
    form.action = `/admin/bookings/${bookingId}/assign-guide`;
    
    toggleModalNotifyBtn(guideId);
    
    const modal = document.getElementById('assignModal');
    modal.style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
    currentBookingId = null;
}

function toggleModalNotifyBtn(value) {
    const btn = document.getElementById('modal-notify-guide-btn');
    if (value) {
        btn.style.display = 'inline-flex';
    } else {
        btn.style.display = 'none';
    }
}

function submitNotifyGuideForm() {
    if (!currentBookingId) return;
    const form = document.getElementById('assignForm');
    
    // Change action to notify-guide
    form.action = `/admin/bookings/${currentBookingId}/notify-guide`;
    
    // Submit the form which includes the selected guide_id and package details to save them first
    form.submit();
}
</script>
@endsection
