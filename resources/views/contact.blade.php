@extends('layouts.app')
@section('title','Contact Us')
@section('content')
<style>
.contact-wrap { min-height:80vh; position:relative; padding:5rem 2rem; overflow:hidden; }
.contact-wrap::before { content:''; position:absolute; top:-100px; right:-100px; width:600px; height:600px; background:radial-gradient(circle, rgba(108,99,255,0.1) 0%, transparent 70%); pointer-events:none; }
.contact-wrap::after { content:''; position:absolute; bottom:-100px; left:-100px; width:600px; height:600px; background:radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%); pointer-events:none; }

.contact-header { text-align:center; margin-bottom:4rem; position:relative; z-index:2; }
.c-tag { display:inline-block; padding:.4rem 1.25rem; background:rgba(108,99,255,0.1); border:1px solid rgba(108,99,255,0.3); border-radius:50px; color:#8f8aff; font-size:.75rem; font-weight:800; letter-spacing:.15em; text-transform:uppercase; margin-bottom:1.5rem; box-shadow:0 0 15px rgba(108,99,255,0.2); }
.c-title { font-family:'Orbitron',monospace; font-size:3rem; font-weight:900; color:#fff; text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem; }
.c-sub { font-size:1.1rem; color:rgba(255,255,255,0.5); max-width:600px; margin:0 auto; }

.grid-main { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1.2fr 1fr; gap:3rem; position:relative; z-index:2; }
@media(max-width:900px){ .grid-main { grid-template-columns:1fr; } }

.contact-card { background:linear-gradient(145deg, rgba(30,32,60,0.6) 0%, rgba(17,24,39,0.9) 100%); border:1px solid rgba(108,99,255,0.2); border-top:1px solid rgba(108,99,255,0.4); border-radius:24px; padding:3rem; box-shadow: 0 15px 40px rgba(0,0,0,0.5); }
.card-title { font-family:'Orbitron',monospace; font-size:1.4rem; font-weight:800; color:#fff; letter-spacing:.05em; margin-bottom:2rem; display:flex; align-items:center; gap:.75rem; }
.card-title i { color:#6c63ff; }

.c-field label { display:block; font-size:.7rem; color:rgba(255,255,255,0.6); letter-spacing:.15em; text-transform:uppercase; margin-bottom:.5rem; font-weight:700; }
.c-field input, .c-field textarea { width:100%; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#fff; padding:1rem 1.25rem; font-size:.9rem; font-family:'Inter',sans-serif; outline:none; transition:.3s; margin-bottom:1.5rem; }
.c-field input:focus, .c-field textarea:focus { border-color:#6c63ff; box-shadow:0 0 0 3px rgba(108,99,255,0.2); background:rgba(108,99,255,0.05); }

.c-btn { width:100%; display:inline-flex; align-items:center; justify-content:center; gap:.75rem; padding:1.1rem 2rem; background:linear-gradient(135deg,#6c63ff,#4f46e5); color:#fff; border:none; border-radius:12px; font-size:.9rem; font-weight:800; letter-spacing:.1em; cursor:pointer; transition:.3s; box-shadow:0 8px 24px rgba(108,99,255,0.4); text-transform:uppercase; }
.c-btn:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(108,99,255,0.6); filter:brightness(1.1); }

.info-cards { display:flex; flex-direction:column; gap:1.25rem; }
.info-card { background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:20px; padding:1.75rem; display:flex; gap:1.25rem; align-items:flex-start; transition:.3s; position:relative; overflow:hidden; }
.info-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:var(--ic-color); opacity:0.3; transition:.3s; }
.info-card:hover { background:rgba(255,255,255,0.04); transform:translateY(-3px); box-shadow:0 15px 30px rgba(0,0,0,0.4); border-color:rgba(255,255,255,0.1); }
.info-card:hover::before { opacity:1; }

.ic-box { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:var(--ic-color); flex-shrink:0; box-shadow:inset 0 0 15px rgba(0,0,0,0.5); }
.ic-title { font-family:'Orbitron',monospace; font-weight:800; font-size:1.05rem; color:#fff; margin-bottom:.4rem; letter-spacing:.05em; text-transform:uppercase; }
.ic-desc { font-size:.85rem; color:rgba(255,255,255,0.5); line-height:1.6; white-space:pre-line; }

/* ===== TEAM SECTION ===== */
.team-section { max-width:1200px; margin:0 auto 4.5rem auto; position:relative; z-index:2; }
.team-header { text-align:center; margin-bottom:2.5rem; }
.team-header h2 { font-family:'Orbitron',monospace; font-size:2rem; font-weight:900; color:#fff; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.5rem; }
.team-header p { color:rgba(255,255,255,0.5); font-size:.95rem; }
.team-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:1.75rem; }
@media(max-width:768px){ .team-grid { grid-template-columns:1fr; } }

.team-card {
    background:linear-gradient(145deg, rgba(30,32,60,0.7) 0%, rgba(17,24,39,0.9) 100%);
    border:1px solid rgba(255,255,255,0.07);
    border-radius:24px;
    padding:2rem 1.5rem 1.75rem;
    text-align:center;
    transition:all .35s ease;
    position:relative;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,0.35);
}
.team-card::before {
    content:'';
    position:absolute;
    top:0; left:0; right:0;
    height:3px;
    background:var(--member-color);
    border-radius:24px 24px 0 0;
}
.team-card::after {
    content:'';
    position:absolute;
    bottom:-60px; right:-60px;
    width:150px; height:150px;
    border-radius:50%;
    background:radial-gradient(circle, var(--member-color) 0%, transparent 70%);
    opacity:.07;
    pointer-events:none;
}
.team-card:hover {
    transform:translateY(-10px);
    box-shadow:0 24px 55px rgba(0,0,0,0.55);
    border-color:rgba(255,255,255,0.14);
}

.member-avatar {
    width:96px; height:96px;
    border-radius:50%;
    margin:0 auto 1.1rem auto;
    display:block;
    border:3px solid var(--member-color);
    box-shadow:0 0 0 5px rgba(255,255,255,0.05), 0 8px 24px rgba(0,0,0,0.4);
    object-fit:cover;
    transition:transform .3s;
}
.team-card:hover .member-avatar { transform:scale(1.07); }

.member-name { font-size:1.1rem; font-weight:800; color:#fff; margin-bottom:.2rem; }
.member-role {
    display:inline-block;
    font-size:.72rem; font-weight:700;
    color:var(--member-color);
    letter-spacing:.1em;
    text-transform:uppercase;
    background:rgba(255,255,255,0.05);
    padding:.25rem .75rem;
    border-radius:50px;
    margin-bottom:1rem;
}
.member-bio { font-size:.82rem; color:rgba(255,255,255,0.45); line-height:1.65; margin-bottom:1.5rem; min-height:52px; }

.social-links { display:flex; justify-content:center; gap:.6rem; }
.social-btn {
    width:38px; height:38px;
    border-radius:10px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:.9rem;
    text-decoration:none;
    transition:all .3s;
    border:1px solid rgba(255,255,255,0.09);
    background:rgba(255,255,255,0.03);
    color:rgba(255,255,255,0.55);
}
.social-btn:hover { transform:translateY(-4px) scale(1.1); color:#fff; }
.social-btn.linkedin:hover  { background:#0a66c2; border-color:#0a66c2; box-shadow:0 6px 18px rgba(10,102,194,0.55); }
.social-btn.instagram:hover { background:linear-gradient(135deg,#833ab4,#fd1d1d,#fcb045); border-color:#fd1d1d; box-shadow:0 6px 18px rgba(253,29,29,0.45); }
.social-btn.twitter:hover   { background:#1d9bf0; border-color:#1d9bf0; box-shadow:0 6px 18px rgba(29,155,240,0.55); }
.social-btn.github:hover    { background:#24292e; border-color:#24292e; box-shadow:0 6px 18px rgba(36,41,46,0.55); }
</style>

<div class="contact-wrap">

    {{-- ✅ MEET OUR TEAM SECTION --}}
    <div class="team-section">
        <div class="team-header">
            <div class="c-tag"><i class="fas fa-users"></i> Our Team</div>
            <h2>Meet The Builders</h2>
            <p>The passionate minds behind TravelMate — crafting smarter journeys, one line at a time.</p>
        </div>
        <div class="team-grid">

            {{-- ===== MEMBER 1 — Manikanta ===== --}}
            <div class="team-card" style="--member-color:#6c63ff">
                <img class="member-avatar"
                     src="{{ file_exists(public_path('images/manikanta.png')) ? asset('images/manikanta.png') . '?t=' . filemtime(public_path('images/manikanta.png')) : 'https://ui-avatars.com/api/?name=Manikanta&background=6c63ff&color=fff&size=200&bold=true' }}"
                     alt="Manikanta">
                <div class="member-name">Manikanta</div>
                <div class="member-role">Lead Full Stack Developer</div>
                <div class="member-bio">Architect of TravelMate. Passionate about engineering smarter generative itineraries and cohesive backend environments.</div>
                <div class="social-links">
                    <a href="https://github.com/22230902mani" target="_blank" class="social-btn github" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/manikanta-lukka/" target="_blank" class="social-btn linkedin"  title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://www.instagram.com/mani_kanta_lukka_18/" target="_blank" class="social-btn instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/LukkaMani36851" target="_blank" class="social-btn twitter"   title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            {{-- ===== MEMBER 2 — Devaraj ===== --}}
            <div class="team-card" style="--member-color:#14b8a6">
                <img class="member-avatar"
                     src="{{ file_exists(public_path('images/devaraj.png')) ? asset('images/devaraj.png') . '?t=' . filemtime(public_path('images/devaraj.png')) : 'https://ui-avatars.com/api/?name=Devaraj&background=14b8a6&color=fff&size=200&bold=true' }}"
                     alt="Devaraj">
                <div class="member-name">Devaraj</div>
                <div class="member-role">UI / UX Designer</div>
                <div class="member-bio">Crafts beautiful visual systems, highly engaging micro-interactions, and premium responsive web layouts.</div>
                <div class="social-links">
                    <a href="#" target="_blank" class="social-btn github" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="#" target="_blank" class="social-btn linkedin"  title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" target="_blank" class="social-btn instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank" class="social-btn twitter"   title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            {{-- ===== MEMBER 3 — Sai ===== --}}
            <div class="team-card" style="--member-color:#f59e0b">
                <img class="member-avatar"
                     src="{{ file_exists(public_path('images/sai.png')) ? asset('images/sai.png') . '?t=' . filemtime(public_path('images/sai.png')) : 'https://ui-avatars.com/api/?name=Sai&background=f59e0b&color=fff&size=200&bold=true' }}"
                     alt="Sai">
                <div class="member-name">Sai</div>
                <div class="member-role">AI & Backend Engineer</div>
                <div class="member-bio">Specialist in native database optimization, custom payment triggers, and high-performance system APIs.</div>
                <div class="social-links">
                    <a href="https://github.com/saivenkatakrishna13" target="_blank" class="social-btn github" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/saivenkatakrishna130?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app" target="_blank" class="social-btn linkedin"  title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" target="_blank" class="social-btn instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank" class="social-btn twitter"   title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

        </div>
    </div>
    {{-- END TEAM SECTION --}}

    <div class="contact-header">
        <div class="c-tag"><i class="fas fa-satellite-dish"></i> TRANSMISSION LINK</div>
        <h1 class="c-title">Contact TravelMate</h1>
        <p class="c-sub">Our neural network is online 24/7. Average human-agent response time is currently under 2 hours.</p>
    </div>

    <div class="grid-main">
        {{-- LEFT FORM --}}
        <div class="contact-card">
            <div class="card-title"><i class="fas fa-envelope-open-text"></i> SECURE MESSAGE</div>
            @if(session('success'))<div class="alert alert-success" style="margin-bottom:1.5rem">{{ session('success') }}</div>@endif
            
            <form method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                    <div class="c-field"><label>Legal Alias (Name)</label>
                        <input type="text" name="name" value="{{ old('name',auth()->user()?->name) }}" required></div>
                    <div class="c-field"><label>Comm Node (Email)</label>
                        <input type="email" name="email" value="{{ old('email',auth()->user()?->email) }}" required></div>
                </div>
                <div class="c-field"><label>Transmission Subject</label>
                    <input type="text" name="subject" placeholder="What is the nature of your inquiry?" required></div>
                <div class="c-field"><label>Encrypted Payload (Message)</label>
                    <textarea name="message" rows="5" placeholder="Detail your requirements..." required style="resize:vertical"></textarea></div>
                
                @auth
                <button type="submit" class="c-btn">
                    <i class="fas fa-paper-plane"></i> INITIATE TRANSMISSION
                </button>
                @else
                <div style="background: rgba(255, 111, 0, 0.08); border: 1px solid rgba(255, 111, 0, 0.3); border-radius: 16px; padding: 1.5rem; text-align: center; margin-top: 1rem;">
                    <div style="font-family: 'Orbitron', monospace; font-size: 1.1rem; font-weight: 800; color: #ffca28; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="fas fa-lock"></i> AUTHENTICATION REQUIRED
                    </div>
                    <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-bottom: 1.25rem;">
                        Secure transmission requires an active account. Please sign up or log in to contact our support team.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('register') }}" class="c-btn" style="width: auto; background: linear-gradient(135deg, #ffca28, #ff6f00); color: #000; font-weight: 900; text-decoration: none; padding: 0.8rem 2rem;">
                            <i class="fas fa-user-plus"></i> Sign Up Free
                        </a>
                        <a href="{{ route('login') }}" class="c-btn" style="width: auto; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2); text-decoration: none; padding: 0.8rem 2rem;">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </a>
                    </div>
                </div>
                @endauth
            </form>
        </div>

        {{-- RIGHT INFO --}}
        <div class="info-cards">
            @foreach([
                ['fa-clock','#6c63ff','Support Matrix','24/7 online neural support.\nLive human chat 9AM–9PM (IST).'],
                ['fa-envelope','#3b82f6','Direct Comm','support@travelmate.com\nPriority routing enabled.'],
                ['fa-headset','#facc15','Voice Uplink','1-800-TRAVEL-AI\nToll-free international line.'],
                ['fa-shield-halved','#ec4899','Emergency SOS','In-app SOS module routes your geolocation directly to local embassy services.']
            ] as [$icon,$color,$title,$desc])
            <div class="info-card" style="--ic-color:{{ $color }}">
                <div class="ic-box"><i class="fas {{ $icon }}"></i></div>
                <div>
                    <div class="ic-title" style="color:{{ $color }}">{{ $title }}</div>
                    <div class="ic-desc">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
