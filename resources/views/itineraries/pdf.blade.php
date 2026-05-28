<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $itinerary->title }}</title>
    @php
        $currency = $itinerary->preferences['currency'] ?? ['symbol' => 'Rs. ', 'code' => 'INR', 'rate' => 1];
        $formatCurrency = function($amount) use ($currency) {
            if ($currency['code'] === 'INR') return 'Rs. ' . number_format($amount);
            $foreign = $amount / $currency['rate'];
            $fAmt = $foreign > 100 ? number_format(round($foreign)) : number_format($foreign, 2);
            return ($currency['symbol'] === '₹' ? 'Rs. ' : $currency['symbol']) . $fAmt . ' (Rs. ' . number_format($amount) . ')';
        };
    @endphp
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #ff6f00; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #0d2b6b; margin: 0 0 10px; font-size: 24px; }
        .header p { margin: 0; color: #666; font-size: 14px; }
        .meta-box { background: #f8fafc; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .meta-box table { width: 100%; }
        .meta-box td { padding: 5px; }
        .meta-label { font-weight: bold; color: #0d2b6b; width: 30%; }
        .day-card { border: 1px solid #e2e8f0; border-radius: 5px; margin-bottom: 20px; overflow: hidden; page-break-inside: avoid; }
        .day-header { background: #0d2b6b; color: #fff; padding: 10px 15px; font-weight: bold; font-size: 14px; }
        .slot { border-bottom: 1px solid #e2e8f0; padding: 10px 15px; }
        .slot:last-child { border-bottom: none; }
        .slot-time { color: #ff6f00; font-weight: bold; float: left; width: 100px; }
        .slot-details { margin-left: 110px; }
        .slot-title { font-weight: bold; font-size: 13px; color: #1e293b; }
        .slot-notes { color: #64748b; font-size: 11px; margin-top: 3px; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $itinerary->title }}</h1>
    <p>TravelMate AI Generated Itinerary &bull; {{ $itinerary->days ? count($itinerary->days) : $itinerary->duration_days }} Days</p>
</div>

<div class="meta-box">
    <table>
        <tr>
            <td class="meta-label">Origin:</td>
            <td>{{ $itinerary->preferences['origin'] ?? 'N/A' }}</td>
            <td class="meta-label">Destination:</td>
            <td>{{ $itinerary->destination->name }}, {{ $itinerary->destination->country }}</td>
        </tr>
        <tr>
            <td class="meta-label">Start Date:</td>
            <td>{{ \Carbon\Carbon::parse($itinerary->start_date)->format('F j, Y') }}</td>
            <td class="meta-label">End Date:</td>
            <td>{{ \Carbon\Carbon::parse($itinerary->end_date)->format('F j, Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Estimated Budget:</td>
            <td>{{ $formatCurrency($itinerary->budget) }}</td>
            <td class="meta-label">Group Type:</td>
            <td>{{ ucfirst($itinerary->preferences['group_type'] ?? 'Solo') }}</td>
        </tr>
    </table>
</div>

@php $fin = $itinerary->preferences['financials'] ?? null; @endphp
@if($fin)
    <h2 style="color: #0d2b6b; border-bottom: 1px solid #eee; padding-bottom: 5px;">AI Financial Breakdown</h2>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #e2e8f0;">
        <thead>
            <tr style="background: #0d2b6b; color: #fff;">
                <th style="padding: 8px 12px; text-align: left; font-size: 11px;">Category</th>
                <th style="padding: 8px 12px; text-align: right; font-size: 11px;">Estimated Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach([
                ['Flight (Round Trip)', $fin['travel_flight'] ?? 0],
                ['Train (Round Trip)', $fin['travel_train'] ?? 0],
                ['Room Rents', $fin['room_cost'] ?? 0],
                ['Activities', $fin['activity_cost'] ?? 0],
                ['Food & Dining', $fin['food_cost'] ?? 0]
            ] as [$title, $amount])
                @if($amount > 0)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 8px 12px; font-size: 11px;">{{ $title }}</td>
                    <td style="padding: 8px 12px; text-align: right; font-size: 11px; font-weight: bold;">{{ $formatCurrency($amount) }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    @if(!empty($fin['recommended_train']) || !empty($fin['recommended_flight']))
    <table style="width: 100%; margin-bottom: 30px; border-collapse: separate; border-spacing: 15px 0;">
        <tr>
            @if(!empty($fin['recommended_train']))
            <td style="width: 50%; background: #1565c0; color: #fff; padding: 12px; border-radius: 5px; text-align: center;">
                <div style="font-size: 9px; text-transform: uppercase; font-weight: bold; margin-bottom: 3px;">🚂 Train Journey Plan</div>
                <div style="font-size: 16px; font-weight: bold;">{{ $formatCurrency($fin['recommended_train']) }}</div>
                <div style="font-size: 8px; opacity: 0.8; margin-top: 2px;">Recommended total with buffer.</div>
            </td>
            @endif
            @if(!empty($fin['recommended_flight']))
            <td style="width: 50%; background: #f39c12; color: #fff; padding: 12px; border-radius: 5px; text-align: center;">
                <div style="font-size: 9px; text-transform: uppercase; font-weight: bold; margin-bottom: 3px;">✈️ Flight Journey Plan</div>
                <div style="font-size: 16px; font-weight: bold;">{{ $formatCurrency($fin['recommended_flight']) }}</div>
                <div style="font-size: 8px; opacity: 0.8; margin-top: 2px;">Recommended total with buffer.</div>
            </td>
            @endif
        </tr>
    </table>
    @endif
@endif

<h2 style="color: #0d2b6b; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px;">Day by Day Plan</h2>

@if(is_array($itinerary->days) && count($itinerary->days) > 0)
    @foreach($itinerary->days as $day)
        <div class="day-card">
            <div class="day-header">
                {{ $day['label'] ?? ('Day ' . $day['day']) }} 
                @if(isset($day['day_cost'])) | Est. Cost: {{ $formatCurrency($day['day_cost']) }} @endif
            </div>
            
            @if(isset($day['weather_tip']))
            <div style="padding: 10px 15px; background: #fffbeb; color: #b45309; font-size: 11px; border-bottom: 1px solid #e2e8f0;">
                <strong>Weather Tip:</strong> {{ $day['weather_tip'] }}
            </div>
            @endif

            @if(isset($day['slots']) && is_array($day['slots']))
                @foreach($day['slots'] as $slot)
                    <div class="slot">
                        <div class="slot-time">{{ $slot['time'] ?? '' }}</div>
                        <div class="slot-details">
                            <div class="slot-title">{{ $slot['activity'] ?? '' }}</div>
                            @if(isset($slot['notes']) && $slot['notes'])
                                <div class="slot-notes">{{ $slot['notes'] }}</div>
                            @endif
                            @if(isset($slot['est_cost']) && $slot['est_cost'] > 0)
                                <div style="color: #2e7d32; font-size: 10px; margin-top: 3px; font-weight: bold;">Cost: {{ $formatCurrency($slot['est_cost']) }}</div>
                            @endif
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                @endforeach
            @else
                <div class="slot">No planned activities for this day.</div>
            @endif
        </div>
    @endforeach
@else
    <p>No itinerary days generated.</p>
@endif

<div class="footer">
    Generated by TravelMate AI on {{ now()->format('Y-m-d H:i') }}
</div>

</body>
</html>
