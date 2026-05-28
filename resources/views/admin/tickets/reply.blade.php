@extends('layouts.admin')
@section('title', 'Admin — Reply to Ticket #' . $ticket->id)
@section('page-title', 'Secure Transmission — Auto Mail Writer')

@section('content')
<div style="max-width:800px; margin:0 auto; padding:2rem 0;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <div>
            <div class="section-tag">📨 Auto Mail Writer</div>
            <h1 style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:900;">Reply to Ticket #{{ $ticket->id }}</h1>
        </div>
        <a href="{{ route('admin.tickets') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Tickets</a>
    </div>

    <div class="card" style="padding:2.5rem;">
        <div style="padding:1rem; background:rgba(0,242,254,0.05); border:1px solid rgba(0,242,254,0.2); border-radius:12px; margin-bottom:2rem;">
            <div style="font-size:0.75rem; color:#00f2fe; font-weight:800; text-transform:uppercase; margin-bottom:0.25rem;"><i class="fas fa-shield-halved"></i> SECURE MAIL NODE</div>
            <div style="font-size:0.9rem; color:#fff;">Automatic mailing protocol initiated. This dispatch will be routed directly to the client's communication node.</div>
        </div>

        <form method="POST" action="{{ route('admin.tickets.send_reply', $ticket) }}">
            @csrf
            
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label"><i class="fas fa-user" style="color:var(--primary)"></i> Client Name</label>
                <input type="text" class="form-control" value="{{ $ticket->name }}" readonly style="background:rgba(255,255,255,0.02); color:var(--muted);">
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label"><i class="fas fa-envelope" style="color:var(--cyan)"></i> Recipient Email Node</label>
                <input type="email" name="recipient_email" class="form-control" value="{{ $ticket->email }}" readonly style="background:rgba(255,255,255,0.02); color:var(--muted);">
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label"><i class="fas fa-heading" style="color:var(--gold)"></i> Subject</label>
                <input type="text" name="subject" class="form-control" value="Re: {{ $ticket->subject }}" required>
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label"><i class="fas fa-pen-to-square" style="color:var(--primary)"></i> Description / Mail Content</label>
                <textarea name="admin_response" class="form-control" rows="10" required>Dear {{ $ticket->name }},

Thank you for reaching out to TravelMate regarding "{{ $ticket->subject }}".

We have received your inquiry. [Please type your detailed resolution description here...]

Best regards,
TravelMate Administration Team</textarea>
            </div>

            <div class="form-group" style="margin-bottom:2rem;">
                <label class="form-label"><i class="fas fa-tasks" style="color:var(--green)"></i> Update Status</label>
                <select name="status" class="form-control">
                    <option value="resolved" selected>Resolved (Close Ticket)</option>
                    <option value="in_progress">In Progress</option>
                </select>
            </div>

            <div style="text-align:right;">
                <button type="submit" class="btn btn-primary" style="padding:0.85rem 2.5rem; font-size:1rem;"><i class="fas fa-paper-plane"></i> Dispatch Mail Transmission</button>
            </div>
        </form>
    </div>
</div>
@endsection
