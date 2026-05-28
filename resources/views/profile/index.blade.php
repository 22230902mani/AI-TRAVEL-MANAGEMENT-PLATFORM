@extends('layouts.app')
@section('title','My Profile')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap');
.profile-wrap { min-height:100vh; background:linear-gradient(135deg,#06090f 0%,#0a0f1e 50%,#060d1a 100%); padding:6rem 2rem 3rem; }
.profile-inner { max-width:1100px; margin:0 auto; display:grid; grid-template-columns:340px 1fr; gap:2rem; }

/* LEFT PANEL */
.profile-left { background:rgba(255,255,255,0.03); border:1px solid rgba(108,99,255,0.25); border-radius:24px; overflow:hidden; position:relative; }
.profile-left::before { content:''; position:absolute; top:-80px; left:50%; transform:translateX(-50%); width:300px; height:300px; background:radial-gradient(circle, rgba(108,99,255,0.15) 0%, transparent 70%); pointer-events:none; }
.neural-bar { padding:.5rem 1rem; background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(108,99,255,0.2); display:flex; align-items:center; gap:.5rem; font-size:.7rem; color:#6c63ff; font-weight:700; letter-spacing:.15em; }
.neural-dot { width:6px; height:6px; border-radius:50%; background:#00d4aa; animation:pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.3} }
.avatar-section { padding:2.5rem 1.5rem 1.5rem; text-align:center; }
.avatar-ring { position:relative; display:inline-block; margin-bottom:1.5rem; }
.avatar-ring img, .avatar-ring .avatar-initials {
    width:110px; height:110px; border-radius:20px;
    border:2px solid rgba(108,99,255,0.6);
    box-shadow:0 0 30px rgba(108,99,255,0.3), inset 0 0 20px rgba(0,0,0,0.5);
    object-fit:cover;
}
.avatar-ring .avatar-initials {
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,#1a1040,#0d0b2e);
    font-size:2.5rem; font-weight:900; color:#fff;
    font-family:'Orbitron',monospace;
}
.verified-badge { position:absolute; bottom:-8px; right:-8px; width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#facc15,#eab308); color:#000; display:flex; align-items:center; justify-content:center; font-size:.8rem; border:2px solid #06090f; }
.profile-name { font-family:'Orbitron',monospace; font-size:1.6rem; font-weight:900; color:#fff; letter-spacing:.08em; margin-bottom:.3rem; text-transform:uppercase; }
.profile-designation { font-size:.72rem; color:#6c63ff; letter-spacing:.2em; text-transform:uppercase; margin-bottom:1.25rem; }
.profile-badges { display:flex; justify-content:center; gap:.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.p-badge { padding:.25rem .7rem; border-radius:4px; font-size:.65rem; font-weight:800; letter-spacing:.15em; text-transform:uppercase; }
.badge-class { background:rgba(108,99,255,0.15); color:#8f8aff; border:1px solid rgba(108,99,255,0.3); }
.badge-verify { background:rgba(0,212,170,0.15); color:#00d4aa; border:1px solid rgba(0,212,170,0.3); }
.badge-secure { background:rgba(255,202,40,0.15); color:#ffca28; border:1px solid rgba(255,202,40,0.3); }

.stat-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; padding:0 1.5rem 1.5rem; }
.stat-box { border-radius:12px; padding:1.2rem 1rem; text-align:center; box-shadow:0 8px 24px rgba(0,0,0,0.2); border:1px solid; transition:.3s; }
.stat-box:hover { transform:translateY(-2px); }
.stat-box.blue { background:linear-gradient(135deg,rgba(30,58,138,0.5),rgba(29,78,216,0.1)); border-color:rgba(59,130,246,0.3); }
.stat-box.purple { background:linear-gradient(135deg,rgba(91,33,182,0.5),rgba(124,58,237,0.1)); border-color:rgba(167,139,250,0.3); }
.stat-box.pink { background:linear-gradient(135deg,rgba(157,23,77,0.5),rgba(219,39,119,0.1)); border-color:rgba(251,113,133,0.3); }
.stat-box.red { background:linear-gradient(135deg,rgba(127,29,29,0.5),rgba(185,28,28,0.1)); border-color:rgba(252,165,165,0.3); }
.stat-box .stat-icon { font-size:1.4rem; margin-bottom:.5rem; }
.stat-box .stat-val { font-family:'Orbitron',monospace; font-size:1.25rem; font-weight:900; color:#fff; }
.stat-box .stat-lbl { font-size:.65rem; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:.1em; margin-top:.2rem; font-weight:600; }

.terminate-btn { display:flex; align-items:center; justify-content:center; gap:.5rem; margin:0 1.5rem 1.5rem; padding:.8rem; border:1px solid rgba(220,38,38,0.4); border-radius:10px; color:#fca5a5; font-size:.8rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; cursor:pointer; background:linear-gradient(135deg,rgba(153,27,27,0.3),rgba(220,38,38,0.1)); transition:.3s; text-decoration:none; box-shadow:0 4px 15px rgba(220,38,38,0.1); }
.terminate-btn:hover { background:linear-gradient(135deg,rgba(153,27,27,0.5),rgba(220,38,38,0.2)); border-color:#ef4444; color:#fff; box-shadow:0 8px 25px rgba(220,38,38,0.3); transform:translateY(-2px); }

.admin-back-btn { display:flex; align-items:center; justify-content:center; gap:.5rem; margin:0 1.5rem 1rem; padding:.8rem; border:1px solid rgba(255,111,0,0.4); border-radius:10px; color:#ff9800; font-size:.8rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; cursor:pointer; background:linear-gradient(135deg,rgba(255,111,0,0.25),rgba(255,111,0,0.05)); transition:.3s; text-decoration:none; box-shadow:0 4px 15px rgba(255,111,0,0.15); }
.admin-back-btn:hover { background:linear-gradient(135deg,rgba(255,111,0,0.45),rgba(255,111,0,0.15)); border-color:#ff6f00; color:#fff; box-shadow:0 8px 25px rgba(255,111,0,0.35); transform:translateY(-2px); }

/* RIGHT PANEL */
.profile-right { display:flex; flex-direction:column; gap:1.5rem; }
.tab-bar { display:flex; gap:.5rem; }
.tab-btn { padding:.55rem 1.4rem; border-radius:8px; font-size:.78rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; border:1px solid rgba(255,255,255,0.1); background:transparent; color:rgba(255,255,255,0.45); transition:.25s; }
.tab-btn.active { background:linear-gradient(135deg,#00d4aa,#3b82f6); color:#fff; border-color:transparent; box-shadow:0 4px 20px rgba(0,212,170,0.4); text-shadow:none; }
.tab-btn:hover:not(.active) { border-color:rgba(108,99,255,0.4); color:#8f8aff; }

.bio-card { background:linear-gradient(145deg, rgba(30,32,60,0.5) 0%, rgba(17,24,39,0.8) 100%); border:1px solid rgba(108,99,255,0.2); border-top:1px solid rgba(108,99,255,0.4); border-radius:20px; padding:2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
.bio-title { display:flex; align-items:center; gap:.75rem; font-family:'Orbitron',monospace; font-size:.85rem; font-weight:700; color:#8f8aff; letter-spacing:.15em; text-transform:uppercase; margin-bottom:1.75rem; }
.bio-title span { color:#8f8aff; }
.bio-title i { color:#8f8aff; }
.bio-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.bio-field label { display:block; font-size:.65rem; color:rgba(255,255,255,0.4); letter-spacing:.2em; text-transform:uppercase; margin-bottom:.5rem; }
.bio-field input, .bio-field textarea, .bio-field select {
    width:100%; background:rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.08);
    border-radius:8px; color:#fff; padding:.7rem 1rem; font-size:.88rem;
    font-family:'Inter',sans-serif; outline:none; transition:.25s;
}
.bio-field input:focus, .bio-field textarea:focus { border-color:#6c63ff; box-shadow:0 0 0 2px rgba(108,99,255,0.2); background:rgba(108,99,255,0.05); }
.bio-field.full { grid-column:1/-1; }
.bio-field textarea { resize:vertical; min-height:80px; }

.interests-wrap { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.25rem; }
.interest-chip { padding:.35rem .85rem; border-radius:50px; font-size:.75rem; cursor:pointer; border:1px solid rgba(108,99,255,0.25); color:rgba(255,255,255,0.5); background:rgba(108,99,255,0.05); transition:.2s; font-weight:600; }
.interest-chip:has(input:checked) { background:rgba(108,99,255,0.25); border-color:#6c63ff; color:#8f8aff; box-shadow:0 0 10px rgba(108,99,255,0.2); }
.interest-chip input { display:none; }

.save-btn { display:inline-flex; align-items:center; gap:.6rem; padding:.75rem 2rem; background:linear-gradient(135deg,#00d4aa,#3b82f6); color:#fff; border:none; border-radius:10px; font-size:.88rem; font-weight:800; letter-spacing:.08em; cursor:pointer; transition:.3s; margin-top:.5rem; box-shadow:0 4px 15px rgba(0,212,170,0.3); }
.save-btn:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(0,212,170,0.5); filter:brightness(1.1); }

/* Stats bottom row */
.metrics-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
.metric-card { border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:.6rem; transition:.3s; border:1px solid; }
.metric-card.purple { background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(37,99,235,0.05)); border-color:rgba(124,58,237,0.3); }
.metric-card.purple:hover { box-shadow:0 8px 24px rgba(124,58,237,0.2); transform:translateY(-3px); background:linear-gradient(135deg,rgba(124,58,237,0.25),rgba(37,99,235,0.1)); }
.metric-card.emerald { background:linear-gradient(135deg,rgba(0,200,83,0.15),rgba(0,200,83,0.05)); border-color:rgba(0,200,83,0.3); }
.metric-card.emerald:hover { box-shadow:0 8px 24px rgba(0,200,83,0.2); transform:translateY(-3px); background:linear-gradient(135deg,rgba(0,200,83,0.25),rgba(0,200,83,0.1)); }
.metric-card.gold { background:linear-gradient(135deg,rgba(251,191,36,0.15),rgba(251,191,36,0.05)); border-color:rgba(251,191,36,0.3); }
.metric-card.gold:hover { box-shadow:0 8px 24px rgba(251,191,36,0.2); transform:translateY(-3px); background:linear-gradient(135deg,rgba(251,191,36,0.25),rgba(251,191,36,0.1)); }

.metric-icon { font-size:1.6rem; }
.metric-label { font-size:.75rem; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:.15em; font-weight:800; }
.metric-val { font-family:'Orbitron',monospace; font-size:1.2rem; color:#fff; font-weight:900; }
.metric-sub { font-size:.7rem; color:rgba(255,255,255,0.5); font-weight:500; }

@media(max-width:768px) { .profile-inner { grid-template-columns:1fr; } .bio-grid { grid-template-columns:1fr; } .metrics-row { grid-template-columns:1fr 1fr; } }
</style>

<div class="profile-wrap">
    <div class="profile-inner">

        {{-- LEFT PANEL --}}
        <div class="profile-left">
            <div class="neural-bar">
                <div class="neural-dot"></div>
                NEURAL LINK : SECURE
            </div>
            <div class="avatar-section">
                <div class="avatar-ring">
                    @if(($profile && $profile->avatar) || ($user->avatar && !str_starts_with($user->avatar, 'https://ui-avatars')))
                        <img src="{{ $user->avatar_url }}" alt="Avatar">
                    @else
                        <div class="avatar-initials">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    @endif
                    <div class="verified-badge">✓</div>
                </div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-designation">
                    ⊕ DESIGNATION : 
                    @if($user->isAdmin())
                        ADMINISTRATOR
                    @elseif($user->hasRole('guide'))
                        LOCAL TRAVEL GUIDE
                    @else
                        {{ strtoupper($user->loyalty_level_name) }}
                    @endif
                </div>
                <div class="profile-badges">
                    <span class="p-badge badge-class">CLASS A</span>
                    <span class="p-badge badge-verify">VERIFIED</span>
                    <span class="p-badge badge-secure">SECURE</span>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-box blue">
                    <div class="stat-icon">🗺️</div>
                    <div class="stat-val">{{ $stats['total_itineraries'] ?? 0 }}</div>
                    <div class="stat-lbl">Itineraries</div>
                </div>
                <div class="stat-box purple">
                    <div class="stat-icon">🎫</div>
                    <div class="stat-val">{{ $stats['total_bookings'] ?? 0 }}</div>
                    <div class="stat-lbl">Bookings</div>
                </div>
                <div class="stat-box pink">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-val">{{ number_format($profile->total_points ?? 0) }}</div>
                    <div class="stat-lbl">Points</div>
                </div>
                <div class="stat-box red">
                    <div class="stat-icon">💎</div>
                    <div class="stat-val">{{ $user->loyalty_level_name }}</div>
                    <div class="stat-lbl">Tier</div>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="admin-back-btn">
                <i class="fas fa-shield-halved"></i> ADMIN DASHBOARD
            </a>
            @endif

            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="terminate-btn">
                <i class="fas fa-right-from-bracket"></i> TERMINATE SESSION
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="profile-right">

            {{-- Tab bar --}}
            <div class="tab-bar">
                <button class="tab-btn active" onclick="switchTab('identity',this)">⊕ Identity</button>
                <button class="tab-btn" onclick="switchTab('photo',this)">📷 Photo</button>
                <button class="tab-btn" onclick="switchTab('interests',this)">🌍 Interests</button>
            </div>

            @if(session('success'))
                <div style="padding:.8rem 1.2rem;background:rgba(0,212,170,0.12);border:1px solid rgba(0,212,170,0.3);border-radius:10px;color:#00d4aa;font-size:.88rem;font-weight:600;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf

                {{-- IDENTITY TAB --}}
                <div id="tab-identity" class="bio-card">
                    <div class="bio-title"><i class="fas fa-fingerprint"></i> <span>IDENTIFICATION BIO-DATA</span></div>
                    <div class="bio-grid">
                        <div class="bio-field">
                            <label>⊛ Legal Alias</label>
                            <input type="text" name="name" value="{{ old('name',$user->name) }}" required>
                        </div>
                        <div class="bio-field">
                            <label>✉ Communication Node</label>
                            <input type="email" value="{{ $user->email }}" disabled style="opacity:.5;cursor:not-allowed;">
                        </div>
                        <div class="bio-field">
                            <label>📡 Signal Frequency (Phone)</label>
                            <input type="text" name="phone" value="{{ old('phone',$profile->phone) }}" placeholder="+91 00000 00000">
                        </div>
                        <div class="bio-field">
                            <label>🌐 Origin Node (Nationality)</label>
                            <input type="text" name="nationality" value="{{ old('nationality',$profile->nationality) }}" placeholder="e.g. Indian">
                        </div>
                        <div class="bio-field full">
                            <label>📝 Bio Protocol</label>
                            <textarea name="bio" rows="3" placeholder="Tell us about yourself...">{{ old('bio',$profile->bio) }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="save-btn"><i class="fas fa-satellite-dish"></i> SYNC CHANGES</button>
                </div>

                {{-- PHOTO TAB --}}
                <div id="tab-photo" class="bio-card" style="display:none;">
                    <div class="bio-title"><i class="fas fa-camera"></i> AVATAR UPLINK</div>
                    <div style="text-align:center;padding:1rem 0;">
                        <div class="avatar-ring" style="display:inline-block;margin-bottom:1.5rem;">
                            @if(($profile && $profile->avatar) || ($user->avatar && !str_starts_with($user->avatar, 'https://ui-avatars')))
                                <img src="{{ $user->avatar_url }}" alt="Avatar">
                            @else
                                <div class="avatar-initials">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            @endif
                        </div>
                        <div class="bio-field" style="max-width:400px;margin:0 auto;">
                            <label>Upload New Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*">
                        </div>
                        <button type="submit" class="save-btn" style="margin-top:1.5rem;"><i class="fas fa-upload"></i> UPLOAD AVATAR</button>
                    </div>
                </div>

                {{-- INTERESTS TAB --}}
                <div id="tab-interests" class="bio-card" style="display:none;">
                    <div class="bio-title"><i class="fas fa-globe"></i> <span>TRAVEL INTEREST MATRIX</span></div>
                    <div class="interests-wrap">
                        @foreach(['adventure','culinary','heritage','ecotourism','relaxation','urban','beaches','mountains','wildlife','luxury'] as $interest)
                        <label class="interest-chip">
                            <input type="checkbox" name="travel_interests[]" value="{{ $interest }}" {{ in_array($interest,$profile->travel_interests??[])?'checked':'' }}>
                            {{ ucfirst($interest) }}
                        </label>
                        @endforeach
                    </div>
                    <button type="submit" class="save-btn" style="margin-top:1.5rem;"><i class="fas fa-satellite-dish"></i> SYNC INTERESTS</button>
                </div>

            </form>

            {{-- Metrics Row --}}
            <div class="metrics-row">
                <div class="metric-card purple">
                    <div class="metric-icon">🧠</div>
                    <div class="metric-label">AI Engine</div>
                    <div class="metric-val">ACTIVE</div>
                    <div class="metric-sub">Gemini 1.5 Pro</div>
                </div>
                <div class="metric-card emerald">
                    <div class="metric-icon">🌐</div>
                    <div class="metric-label">Member Since</div>
                    <div class="metric-val">{{ $user->created_at->format('M Y') }}</div>
                    <div class="metric-sub">{{ $user->created_at->diffForHumans() }}</div>
                </div>
                <div class="metric-card gold">
                    <div class="metric-icon">⚡</div>
                    <div class="metric-label">Loyalty Status</div>
                    <div class="metric-val" style="color:#fde68a;">{{ strtoupper($user->loyalty_level_name) }}</div>
                    <div class="metric-sub">{{ number_format($profile->total_points ?? 0) }} pts earned</div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('[id^="tab-"]').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    btn.classList.add('active');
}

// Instant Avatar Preview
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.querySelector('input[name="avatar"]');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                let reader = new FileReader();
                reader.onload = function(ev) {
                    // Update all avatar images on the page
                    document.querySelectorAll('.avatar-ring img, .avatar-sm, .topbar-avatar').forEach(img => {
                        img.src = ev.target.result;
                    });
                    
                    // Replace initials with image if no image existed before
                    document.querySelectorAll('.avatar-initials').forEach(div => {
                        let img = document.createElement('img');
                        img.src = ev.target.result;
                        img.style.width = '110px';
                        img.style.height = '110px';
                        img.style.borderRadius = '20px';
                        img.style.objectFit = 'cover';
                        img.style.border = '2px solid rgba(108,99,255,0.6)';
                        img.style.boxShadow = '0 0 30px rgba(108,99,255,0.3)';
                        div.replaceWith(img);
                    });
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
});
</script>
@endsection
