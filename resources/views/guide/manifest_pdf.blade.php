<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TravelMate Service Manifest - {{ $booking->booking_reference }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5; font-size: 11px; margin: 0; padding: 0; }
        .container { padding: 15px; }
        
        /* Premium Header Styling */
        .header-table { width: 100%; border-bottom: 3px solid #14b8a6; padding-bottom: 12px; margin-bottom: 20px; }
        .logo-text { font-size: 24px; font-weight: 800; color: #0d1f3c; letter-spacing: -0.5px; }
        .logo-accent { color: #14b8a6; }
        .manifest-title { text-align: right; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; }
        .manifest-ref { text-align: right; font-size: 18px; font-weight: 900; color: #14b8a6; margin-top: 2px; }

        /* Double-Column Details Card */
        .card-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .card-cell { width: 50%; vertical-align: top; padding: 0 10px 0 0; }
        .card-cell:last-child { padding: 0 0 0 10px; }
        
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 15px; min-height: 140px; }
        .box-title { font-size: 10px; font-weight: 800; color: #0d1f3c; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 8px; }
        
        .row { margin-bottom: 6px; }
        .label { font-weight: bold; color: #64748b; display: inline-block; width: 100px; }
        .value { color: #1e293b; font-weight: 600; }
        
        /* Status Badges */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 50px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-info { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

        /* Itinerary Planner Days */
        .section-title { font-size: 13px; font-weight: 800; color: #0d1f3c; margin: 25px 0 12px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .day-card { border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; overflow: hidden; page-break-inside: avoid; background: #ffffff; }
        .day-header { background: #0d1f3c; color: #ffffff; padding: 8px 12px; font-weight: 700; font-size: 11px; }
        .day-header-cost { float: right; color: #14b8a6; font-weight: bold; }
        
        .slot { border-bottom: 1px solid #f1f5f9; padding: 8px 12px; }
        .slot:last-child { border-bottom: none; }
        .slot-time { color: #f59e0b; font-weight: bold; float: left; width: 85px; font-size: 10px; }
        .slot-details { margin-left: 95px; }
        .slot-title { font-weight: 700; font-size: 11px; color: #1e293b; }
        .slot-notes { color: #64748b; font-size: 10px; margin-top: 2px; font-style: italic; }
        .slot-cost { color: #10b981; font-size: 9px; margin-top: 2px; font-weight: bold; }

        /* Dispatch Guidelines Box */
        .briefing-box { background: rgba(20, 184, 166, 0.05); border-left: 4px solid #14b8a6; border-radius: 8px; padding: 12px 15px; margin-bottom: 20px; border-top: 1px solid rgba(20, 184, 166, 0.1); border-bottom: 1px solid rgba(20, 184, 166, 0.1); border-right: 1px solid rgba(20, 184, 166, 0.1); }
        .briefing-title { font-size: 10px; font-weight: 800; color: #14b8a6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .briefing-text { color: #334155; font-size: 10px; margin: 0; line-height: 1.5; }

        /* Premium Extra Footer content */
        .footer-banner { background: #0d1f3c; color: #94a3b8; border-radius: 8px; padding: 15px; margin-top: 30px; text-align: center; page-break-inside: avoid; }
        .footer-logo { font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 4px; }
        .footer-logo span { color: #14b8a6; }
        .footer-text { font-size: 9px; margin: 0; line-height: 1.4; color: #94a3b8; }
        
        .page-break { page-break-after: always; }
        .clear { clear: both; }
    </style>
</head>
<body>

<div class="container">
    
    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td>
                <div class="logo-text">Travel<span class="logo-accent">Mate</span></div>
                <div style="font-size: 9px; color: #64748b; font-weight: 600; margin-top: 2px;">AI TRAVEL PLANNER & GUIDE NETWORK</div>
            </td>
            <td>
                <div class="manifest-title">Service Manifest & Receipt</div>
                <div class="manifest-ref">{{ $booking->booking_reference }}</div>
            </td>
        </tr>
    </table>

    <!-- DISPATCH BRIEFING NOTICE -->
    <div class="briefing-box">
        <div class="briefing-title"><i class="fas fa-exclamation-triangle"></i> Official Dispatch Instructions</div>
        <p class="briefing-text">
            <strong>Dispatched Guide Briefing:</strong> The assigned local guide / travel manager is officially authorized to coordinate services. Guide must prioritize traveler safety, verify daily itinerary slots, and update any expense variations via the TravelMate Guide Portal.
        </p>
    </div>

    <!-- DOUBLE-COLUMN INFO BOXES -->
    <table class="card-table">
        <tr>
            <!-- Left Box: Booking Receipt Details -->
            <td class="card-cell">
                <div class="box">
                    <div class="box-title">Official Receipt Details</div>
                    <div class="row">
                        <span class="label">Reference:</span>
                        <span class="value">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Journey Type:</span>
                        <span class="value">{{ $booking->booking_type === 'itinerary' ? 'Custom AI Planner' : 'Pre-set Package' }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Amount Paid:</span>
                        <span class="value" style="color: #10b981;">₹{{ number_format($booking->total_amount) }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Payment Status:</span>
                        <span class="badge badge-success">{{ $booking->payment_status }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Booking Status:</span>
                        <span class="badge badge-info">{{ $booking->booking_status }}</span>
                    </div>
                </div>
            </td>
            
            <!-- Right Box: Dispatched Guide & traveler details -->
            <td class="card-cell">
                <div class="box">
                    <div class="box-title">Traveler & Dispatch Manifest</div>
                    <div class="row">
                        <span class="label">Traveler Name:</span>
                        <span class="value">{{ $booking->user?->name ?? 'Anonymous' }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Traveler Email:</span>
                        <span class="value" style="font-size: 10px;">{{ $booking->user?->email ?? 'N/A' }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Traveler Phone:</span>
                        <span class="value">{{ $booking->user?->profile?->phone ?? 'Not Provided' }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Dispatched Guide:</span>
                        <span class="value">{{ $booking->guide?->name ?? 'Unassigned' }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Guide Contact:</span>
                        <span class="value" style="font-size: 10px;">{{ $booking->guide?->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- SERVICE SPECIFICATIONS -->
    <div class="box" style="min-height: auto; margin-bottom: 20px;">
        <div class="box-title">Service Manifest Specifications</div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 33%;">
                    <div style="font-weight: bold; color: #64748b;">Journey Schedule</div>
                    <div style="font-size: 11px; font-weight: 700; margin-top: 2px;">
                        {{ $booking->check_in ? $booking->check_in->format('M d, Y') : 'N/A' }} to {{ $booking->check_out ? $booking->check_out->format('M d, Y') : 'N/A' }}
                    </div>
                </td>
                <td style="width: 33%;">
                    <div style="font-weight: bold; color: #64748b;">Travel Party Summary</div>
                    <div style="font-size: 11px; font-weight: 700; margin-top: 2px;">
                        {{ $booking->passenger_summary }}
                    </div>
                </td>
                <td style="width: 33%;">
                    <div style="font-weight: bold; color: #64748b;">Special Traveler Requests</div>
                    <div style="font-size: 10px; font-weight: 600; color: #f59e0b; margin-top: 2px;">
                        "{{ $booking->special_requests ?? 'No specific requests filed.' }}"
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- DAILY ITINERARY PLAN OR PACKAGE SCHEME -->
    @if($booking->booking_type === 'itinerary' && $booking->itinerary)
        <div class="section-title">AI Generative Itinerary Plan</div>
        @if(is_array($booking->itinerary->days) && count($booking->itinerary->days) > 0)
            @foreach($booking->itinerary->days as $day)
                <div class="day-card">
                    <div class="day-header">
                        {{ $day['label'] ?? ('Day ' . $day['day']) }}
                        @if(isset($day['location'])) • Location: {{ $day['location'] }} @endif
                        @if(isset($day['day_cost']))
                            <span class="day-header-cost">Est. Cost: ₹{{ number_format($day['day_cost']) }}</span>
                        @endif
                    </div>
                    @if(isset($day['weather_tip']))
                        <div style="background: #fffbeb; color: #b45309; padding: 6px 12px; font-size: 10px; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
                            🌤️ Weather Guideline: {{ $day['weather_tip'] }}
                        </div>
                    @endif
                    
                    @if(isset($day['slots']) && is_array($day['slots']))
                        @foreach($day['slots'] as $slot)
                            <div class="slot">
                                <div class="slot-time">{{ $slot['time'] ?? 'N/A' }}</div>
                                <div class="slot-details">
                                    <div class="slot-title">{{ $slot['activity'] ?? 'Planned Activity' }}</div>
                                    @if(isset($slot['notes']) && $slot['notes'])
                                        <div class="slot-notes">{{ $slot['notes'] }}</div>
                                    @endif
                                    @if(isset($slot['est_cost']) && $slot['est_cost'] > 0)
                                        <div class="slot-cost">Estimated slot cost: ₹{{ number_format($slot['est_cost']) }}</div>
                                    @endif
                                </div>
                                <div class="clear"></div>
                            </div>
                        @endforeach
                    @else
                        <div style="padding: 10px 12px; color: #64748b;">No activities generated for this day.</div>
                    @endif
                </div>
            @endforeach
        @else
            <div style="padding: 15px; border: 1px solid #cbd5e1; border-radius: 8px; color: #64748b;">No day by day activities generated.</div>
        @endif
    @elseif($booking->booking_type === 'package' && $booking->package)
        <div class="section-title">Tour Package Details</div>
        <div class="day-card" style="padding: 15px;">
            <h3 style="margin: 0 0 10px 0; color: #0d1f3c; font-size: 13px;">{{ $booking->package->title }}</h3>
            <p style="margin: 0 0 15px 0; font-size: 11px; line-height: 1.6; color: #475569;">{{ $booking->package->description }}</p>
            
            <div style="font-weight: bold; font-size: 10px; text-transform: uppercase; color: #0d1f3c; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">Package Highlights</div>
            <ul style="margin: 0 0 15px 0; padding-left: 15px;">
                @foreach($booking->package->highlights ?? [] as $highlight)
                    <li style="margin-bottom: 4px;">{{ $highlight }}</li>
                @endforeach
            </ul>

            <div style="font-weight: bold; font-size: 10px; text-transform: uppercase; color: #0d1f3c; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">Included Perks</div>
            <ul style="margin: 0; padding-left: 15px;">
                @foreach($booking->package->inclusions ?? [] as $inc)
                    <li style="margin-bottom: 4px;">{{ $inc }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div style="padding: 15px; border: 1px solid #cbd5e1; border-radius: 8px; color: #64748b;">Custom individual guide request details. No structured package plan.</div>
    @endif

    <!-- PREMIUM PLATFORM FOOTER BANNER -->
    <div class="footer-banner">
        <div class="footer-logo">Travel<span>Mate</span> AI Portal</div>
        <p class="footer-text" style="color: #cbd5e1; font-weight: bold; margin-bottom: 6px;">Secure, Automated & Premium Traveling Solutions</p>
        <p class="footer-text">
            This document is generated dynamically by the TravelMate Platform to record official guide assignment and traveler manifests. TravelMate integrates real-time itinerary planners, secure financial ledger checkouts, local dispatch managers, and emergency support channels.
        </p>
        <p class="footer-text" style="margin-top: 8px; color: #14b8a6; font-weight: bold;">
            Support Coordination Desk: support@travelmate.com | +91 1800 345 6789 | Secure Manifest ID: TM-GEN-{{ strtoupper(substr(md5($booking->id), 0, 10)) }}
        </p>
    </div>

</div>

</body>
</html>
