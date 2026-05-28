@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Business Intelligence Dashboard')

@section('content')

{{-- ── KPI ROW ── --}}
<div class="kpi-grid">
    <div class="kpi-card orange">
        <div class="kpi-top">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-icon orange"><i class="fas fa-indian-rupee-sign"></i></div>
        </div>
        <div class="kpi-value">₹{{ number_format($stats['total_revenue'] / 1000, 1) }}K</div>
        <div class="kpi-sub">
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> This month: ₹{{ number_format($stats['monthly_revenue']) }}</span>
        </div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-top">
            <div class="kpi-label">Total Users</div>
            <div class="kpi-icon blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="kpi-value">{{ number_format($stats['total_users']) }}</div>
        <div class="kpi-sub">
            <span class="kpi-trend up"><i class="fas fa-arrow-up"></i> +{{ $stats['new_users_month'] }} this month</span>
        </div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-top">
            <div class="kpi-label">Total Bookings</div>
            <div class="kpi-icon green"><i class="fas fa-ticket"></i></div>
        </div>
        <div class="kpi-value">{{ number_format($stats['total_bookings']) }}</div>
        <div class="kpi-sub">
            <span class="kpi-trend up"><i class="fas fa-check"></i> {{ $stats['confirmed_bookings'] }} confirmed</span>
        </div>
    </div>
    <div class="kpi-card cyan">
        <div class="kpi-top">
            <div class="kpi-label">Destinations</div>
            <div class="kpi-icon cyan"><i class="fas fa-globe"></i></div>
        </div>
        <div class="kpi-value">{{ $stats['destinations'] }}</div>
        <div class="kpi-sub">
            <span class="kpi-trend up"><i class="fas fa-suitcase"></i> {{ $stats['packages'] }} active packages</span>
        </div>
    </div>
</div>

{{-- ── SECONDARY KPIs ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:1.5rem">
    @foreach([
        [$stats['cancelled_bookings'], 'Cancellations',  'fa-ban',       'danger',  '#c62828'],
        [$stats['open_tickets'],       'Open Tickets',   'fa-headset',   'warning', '#e65100'],
        [$stats['flagged_reviews'],    'Flagged Reviews','fa-flag',      'danger',  '#c62828'],
        [$stats['packages'],           'Active Packages','fa-suitcase', 'info',    '#0288d1'],
    ] as [$val, $label, $icon, $type, $color])
    <div class="card flex items-center gap-2" style="padding:1rem 1.25rem">
        <div style="width:38px;height:38px;border-radius:10px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:.9rem"></i>
        </div>
        <div>
            <div style="font-size:1.3rem;font-weight:800;color:var(--text)">{{ $val }}</div>
            <div style="font-size:.72rem;color:var(--muted);font-weight:600">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── CHARTS ROW ── --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.5rem">
    {{-- Revenue Chart --}}
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div class="card-title" style="margin-bottom: 0;">
                <i class="fas fa-chart-line" id="revenueIcon" style="color:#ff6f00; transition: all 0.3s ease;"></i> Revenue — Last 6 Months
            </div>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <div class="chart-switcher" style="display: flex; background: rgba(255,255,255,0.03); border-radius: 8px; padding: 3px; border: 1px solid var(--border)">
                    <button onclick="switchRevenueChart('line')" id="btn-chart-line" class="btn-chart-switch active" style="background: rgba(255, 111, 0, 0.2); color: #ff6f00; border: 1px solid rgba(255, 111, 0, 0.4); padding: 4px 12px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-chart-area"></i> Spline Area
                    </button>
                    <button onclick="switchRevenueChart('bar')" id="btn-chart-bar" class="btn-chart-switch" style="background: transparent; color: var(--muted); border: 1px solid transparent; padding: 4px 12px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-chart-bar"></i> Neon Columns
                    </button>
                </div>
                <span class="badge info" style="display: flex; align-items: center; gap: 4px; padding: 4px 10px; font-weight: 700; font-size: 0.7rem; background: rgba(2,136,209,.12); color: #0288d1; border-radius: 50px;">
                    <span style="width: 6px; height: 6px; background-color: #0288d1; border-radius: 50%; display: inline-block; animation: pulse 1.8s infinite;"></span> Live
                </span>
            </div>
        </div>
        <div class="card-body" style="flex: 1; min-height: 250px; position: relative;">
            <canvas id="revenueChart" style="width: 100%; height: 100%;"></canvas>
        </div>
    </div>

    {{-- Bookings by Type --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-chart-pie" style="color:#0288d1"></i> Booking Distribution
            </div>
        </div>
        <div class="card-body" style="display: flex; align-items: center; justify-content: center; min-height: 250px; position: relative;">
            <div style="width: 100%; max-width: 220px; margin: 0 auto;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── BOTTOM ROW: Tables ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem">

    {{-- Recent Bookings --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-ticket" style="color:#ff6f00"></i> Recent Bookings</div>
            <a href="{{ route('admin.bookings') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div style="overflow:hidden;border-radius:0 0 var(--radius) var(--radius)">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentBookings->take(6) as $b)
                <tr>
                    <td style="font-weight:600;font-size:.8rem">{{ $b->booking_reference }}</td>
                    <td>
                        <div style="font-size:.82rem;font-weight:500">{{ $b->user?->name }}</div>
                        <div style="font-size:.72rem;color:var(--muted)">{{ $b->created_at->format('M d') }}</div>
                    </td>
                    <td style="font-weight:700;color:#0288d1">₹{{ number_format($b->total_amount) }}</td>
                    <td>
                        <span class="badge {{ $b->booking_status=='confirmed'?'success':($b->booking_status=='cancelled'?'danger':'warning') }}">
                            {{ ucfirst($b->booking_status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:2rem">No bookings yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- User Growth + Cancellation Risk --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-bar" style="color:#0d2b6b"></i> User Growth</div>
            </div>
            <div class="card-body" style="padding:1rem 1.5rem">
                <canvas id="userChart" height="90"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-triangle-exclamation" style="color:#e65100"></i> Cancellation Risk</div>
            </div>
            <div style="padding:.75rem 1.5rem 1rem">
                @forelse($highRiskUsers as $risk)
                <div class="flex items-center justify-between" style="padding:.5rem 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-size:.82rem;font-weight:600">{{ $risk->user?->name ?? 'Unknown' }}</div>
                        <div style="font-size:.72rem;color:var(--muted)">{{ $risk->user?->email }}</div>
                    </div>
                    <span class="badge danger">{{ $risk->cancel_count }} cancels</span>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.82rem;padding:.75rem 0">✅ No high-risk users detected.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── TOP DESTINATIONS + QUICK ACTIONS ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

    {{-- Top Destinations --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-trophy" style="color:#f9a825"></i> Top Destinations</div>
            <a href="{{ route('admin.destinations') }}" class="btn btn-ghost btn-sm">Manage</a>
        </div>
        <div style="padding:0 1.5rem 1rem">
            @foreach($topDestinations as $i => $dest)
            <div class="flex items-center gap-2" style="padding:.75rem 0;border-bottom:1px solid var(--border)">
                <div style="width:30px;height:30px;border-radius:8px;font-weight:800;font-size:.78rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                    background:{{ ['rgba(249,168,37,.2)','rgba(140,140,140,.15)','rgba(180,100,40,.15)'][$i] ?? 'var(--bg)' }};
                    color:{{ ['#f9a825','#757575','#8d4925'][$i] ?? 'var(--muted)' }}">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1">
                    <div style="font-size:.875rem;font-weight:600">{{ $dest->name }}</div>
                    <div style="font-size:.72rem;color:var(--muted)">{{ $dest->country }}</div>
                </div>
                <span class="badge success">{{ $dest->booking_count }} bookings</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-bolt" style="color:#ff6f00"></i> Quick Actions</div>
        </div>
        <div style="padding:1.25rem 1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:.85rem">
            @foreach([
                [route('admin.users'),        'fa-users',         'Manage Users',    '#0d2b6b'],
                [route('admin.bookings'),      'fa-ticket',        'Bookings',        '#0288d1'],
                [route('admin.packages'),      'fa-suitcase',      'Add Package',     '#ff6f00'],
                [route('admin.destinations'),  'fa-globe',         'Destinations',    '#2e7d32'],
                [route('admin.tickets'),       'fa-headset',       'Support Tickets', '#e65100'],
                [route('admin.reviews'),       'fa-flag',          'Mod Reviews',     '#c62828'],
                [route('admin.team_avatars'),  'fa-user-cog',      'Team Avatars',    '#9c27b0'],
            ] as [$url, $icon, $label, $color])
            <a href="{{ $url }}" style="display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;
                border-radius:10px;border:1px solid var(--border);text-decoration:none;
                background:var(--bg);transition:all .2s;color:var(--text);font-size:.82rem;font-weight:600"
               onmouseover="this.style.background='{{ $color }}12';this.style.borderColor='{{ $color }}'"
               onmouseout="this.style.background='var(--bg)';this.style.borderColor='var(--border)'">
                <div style="width:34px;height:34px;border-radius:9px;background:{{ $color }}18;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:.85rem"></i>
                </div>
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse {
    0% { transform: scale(0.92); opacity: 0.6; }
    50% { transform: scale(1.18); opacity: 1; }
    100% { transform: scale(0.92); opacity: 0.6; }
}
.btn-chart-switch {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-chart-switch:hover:not(.active) {
    background: rgba(255, 255, 255, 0.05) !important;
    color: var(--text) !important;
}
.btn-chart-switch.active:hover {
    background: rgba(255, 111, 0, 0.25) !important;
    border-color: rgba(255, 111, 0, 0.5) !important;
}
</style>
@endpush

@push('scripts')
<script>
// --- Revenue Chart Component ---
let revenueChartInstance = null;
const revData = @json($revenueChart);

function buildRevenueChart(type = 'line') {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    
    if (revenueChartInstance) {
        revenueChartInstance.destroy();
    }
    
    const ctx = canvas.getContext('2d');
    
    // Switch icon class based on type
    const revIcon = document.getElementById('revenueIcon');
    if (revIcon) {
        if (type === 'line') {
            revIcon.className = 'fas fa-chart-line';
            revIcon.style.color = '#ff6f00';
        } else {
            revIcon.className = 'fas fa-chart-bar';
            revIcon.style.color = '#ffca28';
        }
    }

    let datasetOpts = {};
    
    if (type === 'line') {
        // Gradient fill for area
        const fillGrad = ctx.createLinearGradient(0, 0, 0, 260);
        fillGrad.addColorStop(0, 'rgba(255, 111, 0, 0.28)');
        fillGrad.addColorStop(0.5, 'rgba(255, 111, 0, 0.08)');
        fillGrad.addColorStop(1, 'rgba(255, 111, 0, 0.0)');

        // Stroke gradient
        const strokeGrad = ctx.createLinearGradient(0, 0, canvas.width || 400, 0);
        strokeGrad.addColorStop(0, '#ffca28');
        strokeGrad.addColorStop(0.5, '#ff8f00');
        strokeGrad.addColorStop(1, '#ff6f00');

        datasetOpts = {
            borderColor: strokeGrad,
            backgroundColor: fillGrad,
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointBackgroundColor: '#ff6f00',
            pointBorderColor: '#fff',
            pointBorderWidth: 1.5,
            pointRadius: 4,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#ff6f00',
            pointHoverBorderWidth: 3,
        };
    } else {
        // Bar vertical gradient
        const barGrad = ctx.createLinearGradient(0, 0, 0, 240);
        barGrad.addColorStop(0, '#ff9f3b');
        barGrad.addColorStop(0.5, '#ff6f00');
        barGrad.addColorStop(1, 'rgba(255, 111, 0, 0.12)');

        datasetOpts = {
            backgroundColor: barGrad,
            borderColor: '#ff6f00',
            borderWidth: 1.5,
            borderRadius: 8,
            hoverBackgroundColor: '#ffca28',
            hoverBorderColor: '#fff',
        };
    }

    revenueChartInstance = new Chart(canvas, {
        type: type,
        data: {
            labels: revData.map(r => {
                const parts = r.month.split('-');
                const d = new Date(parts[0], parts[1] - 1);
                return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: revData.map(r => r.total),
                ...datasetOpts
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(13, 27, 42, 0.95)',
                    titleColor: '#fff',
                    titleFont: { weight: 'bold', family: "'Inter', sans-serif" },
                    bodyColor: '#ff6f00',
                    bodyFont: { weight: 'bold', size: 13, family: "'Inter', sans-serif" },
                    borderColor: 'rgba(255, 111, 0, 0.35)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return ' ' + new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10.5, family: "'Inter', sans-serif" },
                        callback: v => '₹' + (v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v)
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10, family: "'Inter', sans-serif" }
                    }
                }
            }
        }
    });
}

function switchRevenueChart(type) {
    buildRevenueChart(type);
    
    const btnLine = document.getElementById('btn-chart-line');
    const btnBar = document.getElementById('btn-chart-bar');
    
    if (!btnLine || !btnBar) return;
    
    if (type === 'line') {
        btnLine.style.background = 'rgba(255, 111, 0, 0.2)';
        btnLine.style.color = '#ff6f00';
        btnLine.style.borderColor = 'rgba(255, 111, 0, 0.4)';
        
        btnBar.style.background = 'transparent';
        btnBar.style.color = 'var(--muted)';
        btnBar.style.borderColor = 'transparent';
    } else {
        btnBar.style.background = 'rgba(255, 111, 0, 0.2)';
        btnBar.style.color = '#ff6f00';
        btnBar.style.borderColor = 'rgba(255, 111, 0, 0.4)';
        
        btnLine.style.background = 'transparent';
        btnLine.style.color = 'var(--muted)';
        btnLine.style.borderColor = 'transparent';
    }
}

// Initial build
buildRevenueChart('line');

// --- Booking distribution Doughnut Chart ---
const typeData = @json($bookingsByType);
const typeCanvas = document.getElementById('typeChart');
if (typeCanvas) {
    const totalBookingsCount = typeData.reduce((sum, item) => sum + item.count, 0);
    new Chart(typeCanvas, {
        type: 'doughnut',
        data: {
            labels: typeData.map(t => {
                const str = t.booking_type || 'Other';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }),
            datasets: [{
                data: typeData.map(t => t.count),
                backgroundColor: ['#ff6f00', '#0288d1', '#2e7d32', '#f9a825', '#ab47bc', '#c62828'],
                borderColor: '#050508',
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        boxWidth: 10,
                        boxHeight: 10,
                        padding: 12,
                        font: { size: 11, family: "'Inter', sans-serif", weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(13, 27, 42, 0.95)',
                    titleColor: '#fff',
                    titleFont: { weight: 'bold', family: "'Inter', sans-serif" },
                    bodyColor: '#f8fafc',
                    bodyFont: { family: "'Inter', sans-serif" },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed;
                            const percent = totalBookingsCount > 0 ? ((val / totalBookingsCount) * 100).toFixed(1) : 0;
                            return ` ${val} bookings (${percent}%)`;
                        }
                    }
                }
            }
        }
    });
}

// --- User Growth Column Chart ---
const ugData = @json($userGrowth);
const userCanvas = document.getElementById('userChart');
if (userCanvas) {
    const userCtx = userCanvas.getContext('2d');
    const userGrad = userCtx.createLinearGradient(0, 0, 0, 100);
    userGrad.addColorStop(0, '#0288d1');
    userGrad.addColorStop(1, 'rgba(2, 136, 209, 0.15)');

    new Chart(userCanvas, {
        type: 'bar',
        data: {
            labels: ugData.map(u => {
                const parts = u.month.split('-');
                const d = new Date(parts[0], parts[1] - 1);
                return d.toLocaleDateString('en-US', { month: 'short' });
            }),
            datasets: [{
                label: 'New Users',
                data: ugData.map(u => u.count),
                backgroundColor: userGrad,
                borderColor: '#0288d1',
                borderWidth: 1.5,
                borderRadius: 5,
                hoverBackgroundColor: '#00f2fe'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(13, 27, 42, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#0288d1',
                    bodyFont: { weight: 'bold', family: "'Inter', sans-serif" },
                    padding: 8,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9.5, family: "'Inter', sans-serif" },
                        precision: 0
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: "'Inter', sans-serif" }
                    }
                }
            }
        }
    });
}
</script>
@endpush
@endsection
