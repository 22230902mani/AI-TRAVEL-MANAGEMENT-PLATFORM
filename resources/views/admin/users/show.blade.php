@extends('layouts.admin')
@section('title', 'Admin — User Profile: ' . $user->name)
@section('page-title', 'User Intelligence Profile')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    
    {{-- Back navigation --}}
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('admin.users') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Back to Users List
        </a>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge {{ ($user->is_active ?? true) ? 'success' : 'danger' }}" style="padding: 0.4rem 1rem; font-size: 0.8rem;">
                <i class="fas {{ ($user->is_active ?? true) ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                {{ ($user->is_active ?? true) ? 'Active Profile' : 'Suspended' }}
            </span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 1.5rem; align-items: start;">
        
        {{-- LEFT COLUMN: Profile overview & metadata --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            {{-- User card --}}
            <div class="card" style="padding: 2rem 1.5rem; text-align: center;">
                <div style="position: relative; width: 110px; height: 110px; margin: 0 auto 1.25rem;">
                    <div style="position: absolute; inset: -4px; border-radius: 50%; background: linear-gradient(135deg, #ff6f00, #0288d1); padding: 4px; mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none; z-index: 1;"></div>
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--bg); position: relative; z-index: 2;">
                </div>

                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; color: var(--text); line-height: 1.2; margin-bottom: 0.35rem;">
                    {{ $user->name }}
                </h2>
                <div style="font-size: 0.82rem; color: var(--muted); word-break: break-all; margin-bottom: 1rem;">
                    {{ $user->email }}
                </div>

                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.35rem; margin-bottom: 1.5rem;">
                    @forelse($user->getRoleNames() as $role)
                        <span class="badge primary" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ str_replace('_', ' ', $role) }}
                        </span>
                    @empty
                        <span class="badge info" style="font-size: 0.7rem; text-transform: uppercase;">Traveler</span>
                    @endforelse
                </div>

                <hr style="border: 0; height: 1px; background: var(--border); margin: 1.25rem 0;">

                {{-- Loyalty Details --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem; text-align: left;">
                    <div style="background: rgba(255,255,255,0.02); padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid var(--border);">
                        <div style="font-size: 0.68rem; color: var(--muted); font-weight: 600; text-transform: uppercase;">Tier</div>
                        <div style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 800; color: {{ $user->profile?->loyalty_badge_color ?? '#ff6f00' }}; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                            <i class="fas fa-crown" style="font-size: 0.85rem;"></i>
                            {{ $user->profile?->loyalty_level_name ?? 'Bronze' }}
                        </div>
                    </div>
                    <div style="background: rgba(255,255,255,0.02); padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid var(--border);">
                        <div style="font-size: 0.68rem; color: var(--muted); font-weight: 600; text-transform: uppercase;">Trips</div>
                        <div style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 800; color: var(--cyan); display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                            <i class="fas fa-route" style="font-size: 0.85rem;"></i>
                            {{ $user->profile?->total_trips ?? $user->bookings->count() }}
                        </div>
                    </div>
                </div>

                {{-- Toggle Status Button --}}
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="btn {{ ($user->is_active ?? true) ? 'btn-ghost' : 'btn-orange' }}" style="width: 100%; justify-content: center; font-size: 0.8rem; padding: 0.6rem 1rem; border-color: ($user->is_active ?? true) ? 'rgba(198,40,40,0.3)' : 'rgba(255,111,0,0.3)'; color: ($user->is_active ?? true) ? '#ef5350' : '#fff';">
                        <i class="fas {{ ($user->is_active ?? true) ? 'fa-ban' : 'fa-check-circle' }}" style="margin-right: 0.35rem;"></i>
                        {{ ($user->is_active ?? true) ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            </div>

            {{-- Personal metadata card --}}
            <div class="card" style="padding: 1.5rem;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 0.9rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-address-card" style="color: var(--primary);"></i> Personal Particulars
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Phone Number</div>
                        <div style="font-size: 0.88rem; font-weight: 500; color: var(--text);">
                            <i class="fas fa-phone" style="font-size: 0.75rem; color: var(--primary); margin-right: 0.35rem;"></i>
                            {{ $user->profile?->phone ?? 'Not Available' }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Nationality</div>
                        <div style="font-size: 0.88rem; font-weight: 500; color: var(--text);">
                            <i class="fas fa-flag" style="font-size: 0.75rem; color: var(--cyan); margin-right: 0.35rem;"></i>
                            {{ $user->profile?->nationality ?? 'Not Specified' }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Date of Birth / Gender</div>
                        <div style="font-size: 0.88rem; font-weight: 500; color: var(--text);">
                            <i class="fas fa-calendar" style="font-size: 0.75rem; color: var(--gold); margin-right: 0.35rem;"></i>
                            {{ $user->profile?->date_of_birth ? $user->profile->date_of_birth->format('M d, Y') : 'Unknown' }}
                            <span style="color: var(--muted); font-size: 0.8rem; margin-left: 0.25rem;">({{ ucfirst($user->profile?->gender ?? 'Unspecified') }})</span>
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Preferences</div>
                        <div style="font-size: 0.82rem; font-weight: 500; color: var(--text); display: flex; gap: 0.75rem;">
                            <span><i class="fas fa-language" style="color: var(--muted); margin-right: 0.25rem;"></i> {{ strtoupper($user->profile?->preferred_language ?? 'EN') }}</span>
                            <span><i class="fas fa-money-bill-wave" style="color: var(--muted); margin-right: 0.25rem;"></i> {{ $user->profile?->preferred_currency ?? 'INR' }}</span>
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Passport Info</div>
                        <div style="font-size: 0.82rem; font-weight: 500; color: var(--text);">
                            <i class="fas fa-passport" style="color: var(--muted); margin-right: 0.35rem;"></i>
                            @if($user->profile?->passport_number)
                                {{ substr($user->profile->passport_number, 0, 3) }}***** (Expires {{ $user->profile->passport_expiry ? $user->profile->passport_expiry->format('M Y') : 'N/A' }})
                            @else
                                No Passport Filed
                            @endif
                        </div>
                    </div>

                    @if($user->last_login_at)
                    <hr style="border: 0; height: 1px; background: var(--border); margin: 0.5rem 0;">
                    <div>
                        <div style="font-size: 0.72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.2rem;">Security & Session</div>
                        <div style="font-size: 0.78rem; color: var(--muted);">
                            Last login: {{ $user->last_login_at->diffForHumans() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Heavy stats, logs, travel vectors and tables --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            {{-- Bio & Travel Vectors --}}
            <div class="card" style="padding: 1.75rem;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 800; color: var(--text); margin-bottom: 1rem;">
                    Travel Intelligence Profile & Interests
                </h3>
                
                <p style="font-size: 0.88rem; line-height: 1.6; color: var(--muted); margin-bottom: 1.5rem; background: rgba(255,255,255,0.01); padding: 1rem; border-radius: 8px; border: 1px solid var(--border); border-left: 3px solid var(--primary);">
                    {{ $user->profile?->bio ?? 'No biographical profile statement provided.' }}
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <h4 style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.65rem;">
                            Travel Interests
                        </h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                            @php $interests = is_array($user->profile?->travel_interests) ? $user->profile->travel_interests : []; @endphp
                            @forelse($interests as $interest)
                                <span style="font-size: 0.72rem; font-weight: 600; padding: 4px 10px; background: rgba(2,136,209,0.08); border: 1px solid rgba(2,136,209,0.2); color: #0288d1; border-radius: 6px;">
                                    {{ $interest }}
                                </span>
                            @empty
                                <span style="font-size: 0.78rem; color: var(--muted); font-style: italic;">No specific interests listed.</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h4 style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.65rem;">
                            Special / Accessibility Needs
                        </h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                            @php $needs = is_array($user->profile?->accessibility_needs) ? $user->profile->accessibility_needs : []; @endphp
                            @forelse($needs as $need)
                                <span style="font-size: 0.72rem; font-weight: 600; padding: 4px 10px; background: rgba(198,40,40,0.08); border: 1px solid rgba(198,40,40,0.2); color: #ef5350; border-radius: 6px;">
                                    {{ $need }}
                                </span>
                            @empty
                                <span style="font-size: 0.78rem; color: var(--muted); font-style: italic; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-circle-check" style="color: var(--green); font-size: 0.8rem;"></i> None declared (Standard traveler)
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Behavioral Vector --}}
                @if(is_array($user->profile?->behavioral_vector) && count($user->profile->behavioral_vector) > 0)
                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                    <h4 style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.75rem;">
                        Behavioral Analytics Vector
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                        @foreach($user->profile->behavioral_vector as $vKey => $vVal)
                        <div style="background: rgba(255,255,255,0.015); border: 1px solid var(--border); padding: 0.6rem 0.85rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.65rem; color: var(--muted); text-transform: uppercase; font-weight: 600;">{{ str_replace('_', ' ', $vKey) }}</div>
                            <div style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-top: 2px;">
                                {{ is_numeric($vVal) ? number_format($vVal, 1) : $vVal }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Bookings History --}}
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="card-title">
                        <i class="fas fa-ticket" style="color: var(--primary);"></i> Bookings History ({{ $user->bookings->count() }})
                    </div>
                </div>
                
                <div style="overflow-x: auto; width: 100%;">
                    <table class="admin-table" style="width: 100%; min-width: 600px;">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Journey / Package</th>
                                <th>Guests</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Booking Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->bookings as $b)
                            <tr>
                                <td style="font-weight: 700; font-size: 0.82rem; color: var(--text);">
                                    {{ $b->booking_reference }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem;">{{ $b->package?->title ?? 'Hotel/Custom Booking' }}</div>
                                    <div style="font-size: 0.72rem; color: var(--muted);">
                                        <i class="fas fa-calendar-day" style="font-size: 0.7rem;"></i>
                                        {{ $b->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td style="font-size: 0.8rem;">
                                    {{ $b->adults }} A, {{ $b->children }} C
                                </td>
                                <td style="font-weight: 700; color: var(--cyan); font-size: 0.85rem;">
                                    ₹{{ number_format($b->total_amount) }}
                                </td>
                                <td>
                                    <span class="badge {{ $b->payment_status === 'paid' ? 'success' : ($b->payment_status === 'refunded' ? 'info' : 'warning') }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($b->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $b->booking_status === 'confirmed' ? 'success' : ($b->booking_status === 'cancelled' ? 'danger' : 'warning') }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($b->booking_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--muted); padding: 3rem;">
                                    <i class="fas fa-box-open" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                    No bookings logged for this profile yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Reviews Left --}}
            <div class="card" style="padding: 1.75rem;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 800; color: var(--text); margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fas fa-star" style="color: var(--gold);"></i> User Feedback & Reviews ({{ $user->reviews->count() }})</span>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($user->reviews as $r)
                    <div style="background: rgba(255,255,255,0.015); border: 1px solid var(--border); padding: 1.25rem; border-radius: 10px; display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="font-weight: 600; font-size: 0.85rem; color: var(--text);">
                                {{ $r->destination?->name ?? ($r->package?->title ?? 'Travel Service') }}
                            </div>
                            <div style="display: flex; gap: 2px; color: var(--gold); font-size: 0.8rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $r->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p style="font-size: 0.82rem; color: var(--muted); line-height: 1.5; font-style: italic; margin: 0.25rem 0;">
                            "{{ $r->comment }}"
                        </p>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem; border-top: 1px dashed var(--border); padding-top: 0.5rem; font-size: 0.72rem; color: var(--muted);">
                            <span>Reviewed on: {{ $r->created_at->format('M d, Y') }}</span>
                            @if($r->is_flagged)
                                <span class="badge danger" style="padding: 2px 6px; font-size: 0.65rem;">
                                    <i class="fas fa-flag"></i> FLAGGED
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: var(--muted); padding: 2rem 0; font-style: italic; font-size: 0.88rem;">
                        No review feedback submitted by this user.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
