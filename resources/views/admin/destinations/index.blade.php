@extends('layouts.admin')
@section('title','India State Pricing')
@section('page-title','🇮🇳 India State Destination Pricing')
@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1.5rem">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Region Filter Tabs --}}
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem">
    @foreach(['All','North','South','East','West','Central','Northeast','Islands'] as $region)
    <a href="{{ request()->fullUrlWithQuery(['region' => $region == 'All' ? '' : $region]) }}"
       style="padding:.45rem 1.1rem;border-radius:50px;font-size:.78rem;font-weight:700;text-decoration:none;transition:all .2s;
              background:{{ (request('region', '') == ($region == 'All' ? '' : $region)) ? '#ff6f00' : 'var(--card-bg)' }};
              color:{{ (request('region', '') == ($region == 'All' ? '' : $region)) ? '#fff' : 'var(--muted)' }};
              border:1px solid {{ (request('region', '') == ($region == 'All' ? '' : $region)) ? '#ff6f00' : 'var(--border)' }}">
        {{ $region }}
    </a>
    @endforeach
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
    @foreach([
        ['37', 'Total States/UTs', 'fa-map', '#0d2b6b'],
        [$destinations->where('base_price_economy', '>', 0)->count(), 'Prices Set', 'fa-indian-rupee-sign', '#2e7d32'],
        ['₹'.number_format($destinations->avg('base_price_standard')), 'Avg Standard Price', 'fa-chart-bar', '#ff6f00'],
        [$destinations->where('is_featured', true)->count(), 'Featured States', 'fa-star', '#f9a825'],
    ] as [$val, $label, $icon, $color])
    <div class="kpi-card" style="padding:1.25rem">
        <div class="kpi-top">
            <div class="kpi-label">{{ $label }}</div>
            <div class="kpi-icon" style="background:{{ $color }}18;color:{{ $color }}">
                <i class="fas {{ $icon }}"></i>
            </div>
        </div>
        <div class="kpi-value" style="font-size:1.6rem">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- India State Pricing Table --}}
<div class="card" style="overflow:hidden">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-map-marked-alt" style="color:#ff6f00"></i>
            State & Capital Destination Pricing
        </div>
        <div style="display:flex;gap:.5rem">
            <span style="font-size:.78rem;color:var(--muted);background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:.3rem .75rem">
                {{ $destinations->count() }} destinations
            </span>
        </div>
    </div>

    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="min-width:140px">State / UT</th>
                    <th>Capital City</th>
                    <th>Region</th>
                    <th>Transport</th>
                    <th>Days</th>
                    <th style="color:#2e7d32">Economy (₹)</th>
                    <th style="color:#0288d1">Standard (₹)</th>
                    <th style="color:#f9a825">Luxury (₹)</th>
                    <th>Top Attractions</th>
                    <th>Featured</th>
                    <th style="min-width:100px">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($destinations as $dest)
            <tr id="row-{{ $dest->id }}">
                <td>
                    <div style="font-weight:700;font-size:.875rem">
                        {{ $dest->state ?? $dest->name }}
                    </div>
                    <div style="font-size:.7rem;color:var(--muted);font-weight:600;letter-spacing:.05em">
                        {{ $dest->state_code }}
                    </div>
                </td>
                <td style="font-weight:600">{{ $dest->city }}</td>
                <td>
                    @php $regionColors = ['North'=>'#0d2b6b','South'=>'#2e7d32','East'=>'#0288d1','West'=>'#e65100','Central'=>'#6a1b9a','Northeast'=>'#00838f','Islands'=>'#c62828']; @endphp
                    <span class="badge" style="background:{{ ($regionColors[$dest->region] ?? '#666') }}18;color:{{ $regionColors[$dest->region] ?? '#666' }}">
                        {{ $dest->region ?? '—' }}
                    </span>
                </td>
                <td>
                    <span style="font-size:.78rem">
                        @if($dest->transport_mode == 'flight') ✈️ Flight
                        @elseif($dest->transport_mode == 'train') 🚂 Train
                        @else 🚌 Bus @endif
                    </span>
                </td>
                <td style="text-align:center;font-weight:700">{{ $dest->duration_days_suggested }}d</td>

                {{-- INLINE EDITABLE PRICES --}}
                <td>
                    <div class="price-display" id="eco-display-{{ $dest->id }}"
                         onclick="editPrice({{ $dest->id }}, 'economy')"
                         style="font-weight:700;color:#2e7d32;cursor:pointer;padding:.2rem .5rem;border-radius:6px;border:1px dashed transparent"
                         title="Click to edit">
                        ₹{{ number_format($dest->base_price_economy) }}
                    </div>
                </td>
                <td>
                    <div class="price-display" id="std-display-{{ $dest->id }}"
                         onclick="editPrice({{ $dest->id }}, 'standard')"
                         style="font-weight:700;color:#0288d1;cursor:pointer;padding:.2rem .5rem;border-radius:6px;border:1px dashed transparent"
                         title="Click to edit">
                        ₹{{ number_format($dest->base_price_standard) }}
                    </div>
                </td>
                <td>
                    <div class="price-display" id="lux-display-{{ $dest->id }}"
                         onclick="editPrice({{ $dest->id }}, 'luxury')"
                         style="font-weight:700;color:#f9a825;cursor:pointer;padding:.2rem .5rem;border-radius:6px;border:1px dashed transparent"
                         title="Click to edit">
                        ₹{{ number_format($dest->base_price_luxury) }}
                    </div>
                </td>

                <td style="max-width:200px;font-size:.75rem;color:var(--muted)">
                    {{ Str::limit($dest->what_to_see, 60) }}
                </td>
                <td style="text-align:center">
                    <form method="POST" action="{{ route('admin.destinations.toggleFeatured', $dest) }}" style="display:inline">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:none;border:none;cursor:pointer;font-size:1.1rem" title="Toggle featured">
                            {{ $dest->is_featured ? '⭐' : '☆' }}
                        </button>
                    </form>
                </td>
                <td>
                    <button onclick="openEditModal({{ $dest->id }}, '{{ addslashes($dest->state ?? $dest->name) }}', {{ $dest->base_price_economy }}, {{ $dest->base_price_standard }}, {{ $dest->base_price_luxury }}, {{ $dest->duration_days_suggested }}, '{{ $dest->transport_mode }}')"
                            class="btn btn-ghost btn-sm">
                        <i class="fas fa-pencil"></i> Edit
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align:center;padding:3rem;color:var(--muted)">
                    No destinations found. Run <code>php artisan db:seed --class=IndiaStatesSeeder</code>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border)">
        {{ $destinations->links() }}
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:20px;padding:2rem;width:560px;max-width:95vw;box-shadow:0 24px 60px rgba(0,0,0,0.2);animation:slideUp .3s ease">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--text)">
                <i class="fas fa-map-marker-alt" style="color:#ff6f00"></i>
                Edit Pricing — <span id="modal-state-name"></span>
            </h3>
            <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--muted)">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editPriceForm" method="POST">
            @csrf @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.25rem">
                <div>
                    <label style="font-size:.72rem;font-weight:700;color:#2e7d32;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem">
                        🟢 Economy (₹)
                    </label>
                    <input type="number" name="base_price_economy" id="modal-economy"
                           style="width:100%;padding:.65rem .9rem;border:2px solid rgba(46,125,50,.3);border-radius:10px;font-size:.9rem;font-weight:700;color:#2e7d32;outline:none"
                           onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='rgba(46,125,50,.3)'"
                           min="0" step="1" required>
                </div>
                <div>
                    <label style="font-size:.72rem;font-weight:700;color:#0288d1;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem">
                        🔵 Standard (₹)
                    </label>
                    <input type="number" name="base_price_standard" id="modal-standard"
                           style="width:100%;padding:.65rem .9rem;border:2px solid rgba(2,136,209,.3);border-radius:10px;font-size:.9rem;font-weight:700;color:#0288d1;outline:none"
                           onfocus="this.style.borderColor='#0288d1'" onblur="this.style.borderColor='rgba(2,136,209,.3)'"
                           min="0" step="1" required>
                </div>
                <div>
                    <label style="font-size:.72rem;font-weight:700;color:#e65100;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem">
                        🟡 Luxury (₹)
                    </label>
                    <input type="number" name="base_price_luxury" id="modal-luxury"
                           style="width:100%;padding:.65rem .9rem;border:2px solid rgba(249,168,37,.3);border-radius:10px;font-size:.9rem;font-weight:700;color:#e65100;outline:none"
                           onfocus="this.style.borderColor='#f9a825'" onblur="this.style.borderColor='rgba(249,168,37,.3)'"
                           min="0" step="1" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
                <div>
                    <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem">
                        Duration (Days)
                    </label>
                    <input type="number" name="duration_days_suggested" id="modal-days"
                           style="width:100%;padding:.65rem .9rem;border:1px solid var(--border);border-radius:10px;font-size:.9rem;font-weight:600;outline:none"
                           min="1" max="30" required>
                </div>
                <div>
                    <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.4rem">
                        Transport Mode
                    </label>
                    <select name="transport_mode" id="modal-transport"
                            style="width:100%;padding:.65rem .9rem;border:1px solid var(--border);border-radius:10px;font-size:.9rem;font-weight:600;outline:none;background:#fff">
                        <option value="flight">✈️ Flight</option>
                        <option value="train">🚂 Train</option>
                        <option value="bus">🚌 Bus</option>
                    </select>
                </div>
            </div>

            <div style="background:rgba(255,111,0,.06);border:1px solid rgba(255,111,0,.2);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.5rem;font-size:.78rem;color:#e65100">
                <i class="fas fa-info-circle"></i>
                These prices will be shown to users when they select this destination. They can choose Economy, Standard, or Luxury tier.
            </div>

            <div style="display:flex;gap:.75rem;justify-content:flex-end">
                <button type="button" onclick="closeModal()" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-orange">
                    <i class="fas fa-save"></i> Save Pricing
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
.price-display:hover {
    border-color:currentColor !important;
    background:rgba(0,0,0,.03);
}
</style>

@push('scripts')
<script>
function openEditModal(id, state, economy, standard, luxury, days, transport) {
    document.getElementById('modal-state-name').textContent = state;
    document.getElementById('modal-economy').value  = economy;
    document.getElementById('modal-standard').value = standard;
    document.getElementById('modal-luxury').value   = luxury;
    document.getElementById('modal-days').value     = days;
    document.getElementById('modal-transport').value = transport;
    document.getElementById('editPriceForm').action  = `/admin/destinations/${id}/pricing`;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
@endsection
