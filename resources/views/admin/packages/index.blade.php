@extends('layouts.admin')
@section('title','Admin — Packages')
@section('page-title','Package Management')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">

    {{-- Page Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem">
        <div>
            <div class="section-tag">⚡ Admin Panel</div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;color:var(--text)">
                <i class="fas fa-suitcase" style="color:#ff6f00"></i> Package Management
            </h1>
            <p style="color:var(--muted);font-size:.88rem;margin-top:.25rem">Create and manage all travel packages</p>
        </div>
        <div style="display:flex;gap:.75rem">
            <button onclick="toggleForm()" class="btn btn-primary" id="toggleBtn">
                <i class="fas fa-plus"></i> Create New Package
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- ── CREATE PACKAGE FORM (hidden by default) ── --}}
    <div id="createForm" style="display:none;margin-bottom:2.5rem">
        <div class="card" style="padding:2.5rem;border-left:5px solid #ff6f00;box-shadow:0 15px 35px rgba(0,0,0,0.1)">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
                <h2 style="font-weight:800;color:var(--text);margin:0;display:flex;align-items:center;gap:.75rem">
                    <i class="fas fa-plus-circle" style="color:#ff6f00"></i> Create New Package
                </h2>
                <div style="font-size:.82rem;color:var(--muted)">Fields marked with * are required</div>
            </div>

            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:2rem;border-radius:12px">
                <i class="fas fa-triangle-exclamation"></i>
                <div style="margin-left:.75rem">
                    <strong style="display:block;margin-bottom:.25rem">Please fix the following errors:</strong>
                    <ul style="margin:0;padding-left:1rem;font-size:.85rem">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.packages.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- SECTION 1: CORE INFORMATION --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle icon-orange"></i> Core Information
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
                        <div class="form-group">
                            <label class="form-label">Package Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Bali Adventure 7 Days" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Destination *</label>
                            <select name="destination_id" class="form-control" required>
                                <option value="">— Select —</option>
                                @foreach(\App\Models\Destination::orderBy('name')->get() as $dest)
                                <option value="{{ $dest->id }}" {{ old('destination_id')==$dest->id?'selected':'' }}>{{ $dest->name }}, {{ $dest->country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div class="form-group">
                            <label class="form-label">Package Type *</label>
                            <select name="package_type" class="form-control" required>
                                <option value="">— Choose Type —</option>
                                @foreach(['adventure','cultural','beach','mountain','city','wildlife','cruise','honeymoon','family','budget','luxury'] as $t)
                                <option value="{{ $t }}" {{ old('package_type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Package Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: PRICING & LOGISTICS --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-coins icon-blue"></i> Pricing & Logistics
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:1.25rem">
                        <div class="form-group">
                            <label class="form-label">Selling Price (₹) *</label>
                            <input type="number" name="price_per_person" class="form-control" placeholder="0" value="{{ old('price_per_person') }}" min="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Original Price (₹)</label>
                            <input type="number" name="original_price" class="form-control" placeholder="0" value="{{ old('original_price') }}" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duration (Days) *</label>
                            <input type="number" name="duration_days" class="form-control" placeholder="1" value="{{ old('duration_days') }}" min="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty_level" class="form-control">
                                <option value="easy">Easy</option>
                                <option value="moderate" selected>Moderate</option>
                                <option value="challenging">Challenging</option>
                                <option value="extreme">Extreme</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-top:1rem">
                        <div class="form-group">
                            <label class="form-label">Available Slots</label>
                            <input type="number" name="availability_count" class="form-control" placeholder="50" value="{{ old('availability_count',50) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Max Group Size</label>
                            <input type="number" name="max_group_size" class="form-control" placeholder="20" value="{{ old('max_group_size',20) }}">
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: CONTENT & ITINERARY --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-file-lines icon-green"></i> Content & Itinerary
                    </div>
                    <div class="form-group">
                        <label class="form-label">Package Overview *</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Write a compelling overview..." required>{{ old('description') }}</textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-top:1.5rem">
                        <div class="form-group">
                            <label class="form-label">What's Included <small>(One per line)</small></label>
                            <textarea name="inclusions" class="form-control" rows="6" placeholder="Hotel Stay&#10;Meals&#10;Local Guide">{{ old('inclusions') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">What's Excluded <small>(One per line)</small></label>
                            <textarea name="exclusions" class="form-control" rows="6" placeholder="Flight Tickets&#10;Personal Expenses">{{ old('exclusions') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Key Highlights <small>(One per line)</small></label>
                            <textarea name="highlights" class="form-control" rows="6" placeholder="Visit Eiffel Tower&#10;River Cruise">{{ old('highlights') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:1.5rem">
                        <label class="form-label">Cancellation Policy</label>
                        <textarea name="cancellation_policy" class="form-control" rows="2" placeholder="Free cancellation up to 7 days...">{{ old('cancellation_policy') }}</textarea>
                    </div>
                </div>

                {{-- SECTION 4: PROMOTIONAL DEALS (The New Feature) --}}
                <div class="form-section form-section-promo">
                    <div class="form-section-title" style="color:#ff6f00;border-bottom-color:rgba(255,111,0,.2)">
                        <i class="fas fa-bullhorn icon-orange"></i> Promotional Offers (Marketplace Badges)
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:1.25rem">
                        <div class="form-group">
                            <label class="form-label">🏷️ Badge Label</label>
                            <input type="text" name="offer_badge" class="form-control" placeholder="e.g. HOT DEAL" value="{{ old('offer_badge') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">📢 Promo Description</label>
                            <input type="text" name="offer_text" class="form-control" placeholder="e.g. Save ₹2,000 this weekend!" value="{{ old('offer_text') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">⏳ Expiry Date</label>
                            <input type="datetime-local" name="offer_expires_at" class="form-control" value="{{ old('offer_expires_at') }}">
                        </div>
                    </div>
                    <div style="margin-top:1rem">
                        <div class="form-group">
                            <label class="form-label">Direct Discount (%)</label>
                            <input type="number" name="discount_percent" class="form-control" style="max-width:200px" placeholder="0" value="{{ old('discount_percent',0) }}" min="0" max="99" step="0.1">
                        </div>
                    </div>
                </div>

                {{-- SETTINGS & SUBMIT --}}
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:2rem;border-top:1px solid var(--border)">
                    <div style="display:flex;gap:2rem">
                        <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured')?'checked':'' }} style="width:20px;height:20px;accent-color:#ff6f00">
                            <span><i class="fas fa-star" style="color:#f9a825"></i> Featured</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active',true)?'checked':'' }} style="width:20px;height:20px;accent-color:#2e7d32">
                            <span><i class="fas fa-toggle-on" style="color:#2e7d32"></i> Publish Immediately</span>
                        </label>
                    </div>
                    <div style="display:flex;gap:1rem">
                        <button type="button" onclick="toggleForm()" class="btn btn-outline" style="padding:.75rem 2rem">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" style="padding:.75rem 3rem;font-size:1rem;background:#ff6f00">
                            <i class="fas fa-cloud-upload-alt"></i> Create & Launch Package
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── PACKAGES TABLE ── --}}
    <div class="card" style="overflow:hidden">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-list" style="color:#ff6f00"></i> All Packages
                <span class="badge-pill badge-primary" style="margin-left:.5rem">
                    {{ $packages->total() }}
                </span>
            </h2>
            <a href="{{ route('packages.index') }}" class="btn btn-outline btn-sm" target="_blank">
                <i class="fas fa-eye"></i> Public View
            </a>
        </div>
        <div style="overflow-x:auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Package</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($packages as $i => $pkg)
                <tr>
                    <td style="color:var(--muted);font-size:.8rem">{{ $packages->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.85rem">
                            @if($pkg->image && file_exists(public_path('storage/'.$pkg->image)))
                                <img src="{{ asset('storage/'.$pkg->image) }}" alt=""
                                     style="width:48px;height:36px;object-fit:cover;border-radius:8px;border:1px solid var(--border);flex-shrink:0">
                            @else
                                <div style="width:48px;height:36px;border-radius:8px;background:rgba(255,111,0,.1);border:1px solid rgba(255,111,0,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fas fa-image" style="color:#ff6f00;font-size:.7rem"></i>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;font-size:.88rem;color:var(--text)">{{ Str::limit($pkg->title, 28) }}</div>
                                @if($pkg->is_featured)
                                    <span style="font-size:.65rem;color:#f9a825"><i class="fas fa-star"></i> Featured</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.85rem;color:var(--muted)">
                        {{ $pkg->destination?->name ?? '—' }}
                    </td>
                    <td style="font-size:.85rem">
                        {{ $pkg->duration_days }} days
                    </td>
                    <td>
                        <div style="font-weight:700;color:#0288d1">₹{{ number_format($pkg->price_per_person) }}</div>
                        @if($pkg->discount_percent > 0)
                            <span style="font-size:.7rem;color:#ef5350;font-weight:600">-{{ $pkg->discount_percent }}% off</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-pill badge-primary">{{ ucfirst($pkg->package_type) }}</span>
                    </td>
                    <td>
                        <span class="badge-pill {{ $pkg->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('packages.show', $pkg) }}" class="btn btn-outline btn-sm btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.packages.toggle', $pkg) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-icon" title="{{ $pkg->is_active ? 'Deactivate' : 'Activate' }}"
                                    style="background:{{ $pkg->is_active ? 'rgba(220,38,38,.12)' : 'rgba(0,200,83,.12)' }};color:{{ $pkg->is_active ? '#ef5350' : '#00c853' }};border:1px solid {{ $pkg->is_active ? 'rgba(220,38,38,.2)' : 'rgba(0,200,83,.2)' }}">
                                    <i class="fas {{ $pkg->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:3rem;text-align:center;color:var(--muted)">
                        <i class="fas fa-suitcase" style="font-size:2rem;margin-bottom:.75rem;display:block;opacity:.3"></i>
                        No packages yet. Click <strong>Create New Package</strong> to add one.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1.25rem 1.5rem;border-top:1px solid var(--border)">
            {{ $packages->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleForm() {
    const form = document.getElementById('createForm');
    const btn  = document.getElementById('toggleBtn');
    const open = form.style.display === 'none';
    form.style.display = open ? 'block' : 'none';
    btn.innerHTML = open
        ? '<i class="fas fa-times"></i> Cancel'
        : '<i class="fas fa-plus"></i> Create New Package';
    if (open) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Auto-open form if there are validation errors
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => toggleForm());
@endif
</script>
@endpush
@endsection
