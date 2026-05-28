@extends('layouts.app')
@section('title', 'Transactions & Bookings — TravelMate')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Poppins',sans-serif}
.tx-page {
    background: linear-gradient(135deg, #0a0b0f 0%, #0f0f2e 50%, #0a1628 100%);
    min-height: 100vh;
    padding: 3rem 1.5rem 6rem;
    color: #fff;
}
.inner {
    max-width: 1200px;
    margin: 0 auto;
}
.glass-card {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
}
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
}
.stat-val {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
}
.stat-label {
    font-size: 0.8rem;
    color: #b0c4de;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}
.tx-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
.tx-table th {
    text-align: left;
    padding: 1rem;
    font-size: 0.82rem;
    color: #8f9cae;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.tx-table td {
    padding: 1.1rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 0.9rem;
    color: #e2e8f0;
}
.tx-table tr:hover {
    background: rgba(255, 255, 255, 0.02);
}
.badge-pill {
    padding: 0.3rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.badge-paid { background: rgba(0, 212, 170, 0.15); color: #00d4aa; }
.badge-pending { background: rgba(255, 202, 40, 0.15); color: #ffca28; }
.badge-cancelled { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

.btn-action {
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.btn-action.view { background: rgba(108, 99, 255, 0.15); color: #a29cf4; border: 1px solid rgba(108,99,255,0.3); }
.btn-action.view:hover { background: #6c63ff; color: #fff; }

@media(max-width: 900px) {
    .stat-grid { grid-template-columns: 1fr 1fr; }
    .charts-grid { grid-template-columns: 1fr; }
}
</style>

<div class="tx-page">
<div class="inner">

    {{-- Title --}}
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; background: linear-gradient(135deg, #fff, #b0c4de); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                💳 Transaction & Booking History
            </h1>
            <p style="color: #b0c4de; margin-top: 0.25rem;">Monitor your travel payments, invoices, package reservations, and hotel check-ins.</p>
        </div>
        <a href="{{ route('home') }}" class="btn-action view" style="font-size: 0.9rem; padding: 0.7rem 1.2rem; border-radius: 12px;">
            <i class="fas fa-wand-magic-sparkles"></i> AI Plan Dashboard
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="stat-grid">
        <div class="stat-card" style="border-color: rgba(0, 212, 170, 0.2); background: rgba(0, 212, 170, 0.02);">
            <div class="stat-val" style="color: #00d4aa;">₹{{ number_format($stats['total_spent']) }}</div>
            <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card" style="border-color: rgba(108, 99, 255, 0.2); background: rgba(108, 99, 255, 0.02);">
            <div class="stat-val" style="color: #a29cf4;">{{ $stats['total_bookings'] }}</div>
            <div class="stat-label">Bookings Count</div>
        </div>
        <div class="stat-card" style="border-color: rgba(255, 202, 40, 0.2); background: rgba(255, 202, 40, 0.02);">
            <div class="stat-val" style="color: #ffca28;">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">Confirmed Stays</div>
        </div>
        <div class="stat-card" style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.02);">
            <div class="stat-val" style="color: #ef4444;">{{ $stats['cancelled'] }}</div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="charts-grid">
        {{-- Trend Chart --}}
        <div class="glass-card">
            <h3 style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem;">📊 Monthly Spend Trend</h3>
            <div style="height: 250px; position: relative;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        {{-- Category Donut --}}
        <div class="glass-card">
            <h3 style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem;">🍕 Category Share</h3>
            <div style="height: 250px; position: relative;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="glass-card">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem;">📁 All Reservations</h3>
        
        <div style="overflow-x: auto;">
            <table class="tx-table">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Type</th>
                        <th>Destination / Hotel</th>
                        <th>Travel Dates</th>
                        <th>Total Paid</th>
                        <th>Payment Status</th>
                        <th>Booking Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #a29cf4;">{{ $b->booking_reference }}</td>
                        <td>
                            @if($b->booking_type === 'package')
                                <span style="color: #ffca28; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-suitcase"></i> PACKAGE</span>
                            @elseif($b->booking_type === 'itinerary')
                                <span style="color: #ff7b00; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-map-marked-alt"></i> ITINERARY</span>
                            @else
                                <span style="color: #00d4aa; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-hotel"></i> HOTEL</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #fff;">
                                @if($b->booking_type === 'package')
                                    {{ $b->package?->title ?? 'Curated Trip' }}
                                @elseif($b->booking_type === 'itinerary')
                                    {{ $b->itinerary?->title ?? 'Premium AI Itinerary' }}
                                @else
                                    {{ $b->hotel?->name ?? 'Premium Stay' }}
                                @endif
                            </div>
                            <div style="font-size: 0.78rem; color: #b0c4de;">
                                @if($b->booking_type === 'package')
                                    📍 {{ $b->package?->destination?->name ?? 'Global' }}
                                @elseif($b->booking_type === 'itinerary')
                                    📍 {{ $b->itinerary?->destination?->name ?? 'Custom Destination' }}
                                @else
                                    📍 {{ $b->hotel?->address ?? 'Destination' }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($b->booking_type === 'itinerary')
                                @if($b->itinerary?->start_date)
                                    <span style="font-size: 0.82rem; color: #b0c4de;">
                                        {{ \Carbon\Carbon::parse($b->itinerary->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($b->itinerary->end_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span style="color: #64748b;">N/A</span>
                                @endif
                            @else
                                @if($b->check_in)
                                    <span style="font-size: 0.82rem; color: #b0c4de;">
                                        {{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }} - {{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}
                                    </span>
                                @else
                                    <span style="color: #64748b;">N/A</span>
                                @endif
                            @endif
                        </td>
                        <td style="font-weight: 700; color: #00d4aa;">₹{{ number_format($b->total_amount) }}</td>
                        <td>
                            @if($b->payment_status === 'paid')
                                <span class="badge-pill badge-paid"><i class="fas fa-check-circle"></i> Paid</span>
                            @elseif($b->payment_status === 'pending')
                                <span class="badge-pill badge-pending"><i class="fas fa-clock"></i> Pending</span>
                            @else
                                <span class="badge-pill badge-cancelled"><i class="fas fa-times-circle"></i> Failed</span>
                            @endif
                        </td>
                        <td>
                            @if($b->booking_status === 'confirmed')
                                <span class="badge-pill badge-paid"><i class="fas fa-check"></i> Confirmed</span>
                            @elseif($b->booking_status === 'pending')
                                <span class="badge-pill badge-pending"><i class="fas fa-clock"></i> Pending</span>
                            @else
                                <span class="badge-pill badge-cancelled"><i class="fas fa-ban"></i> Cancelled</span>
                            @endif
                        </td>
                        <td>
                            @if($b->booking_type === 'package')
                                <a href="{{ route('bookings.confirmation', $b) }}" class="btn-action view">
                                    <i class="fas fa-file-invoice"></i> View Ticket
                                </a>
                            @elseif($b->booking_type === 'itinerary')
                                <a href="{{ route('itineraries.show', $b->itinerary_id) }}" class="btn-action view" style="background: rgba(255, 123, 0, 0.15); color: #ffa726; border-color: rgba(255, 123, 0, 0.3);">
                                    <i class="fas fa-map"></i> View Plan
                                </a>
                            @else
                                <a href="{{ route('hotels.confirmation', $b) }}" class="btn-action view">
                                    <i class="fas fa-key"></i> Room Pass
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #8f9cae; padding: 3rem;">
                            <i class="fas fa-receipt" style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.4;"></i>
                            <p>No transaction history found. Let's plan and book your first trip!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Trend Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($monthlySpend)->pluck('label')) !!},
            datasets: [{
                label: 'Spending (INR)',
                data: {!! json_encode(collect($monthlySpend)->pluck('amount')) !!},
                borderColor: '#6c63ff',
                backgroundColor: 'rgba(108, 99, 255, 0.12)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#00d4aa',
                pointBorderColor: '#fff',
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#b0c4de' } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#b0c4de' } }
            }
        }
    });

    // Donut Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Packages', 'Hotels', 'Others'],
            datasets: [{
                data: [{{ $byType['package'] }}, {{ $byType['hotel'] }}, {{ $byType['other'] }}],
                backgroundColor: ['#ffca28', '#00d4aa', '#6c63ff'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#b0c4de', boxWidth: 12, font: { family: 'Poppins' } }
                }
            }
        }
    });
});
</script>
@endsection
