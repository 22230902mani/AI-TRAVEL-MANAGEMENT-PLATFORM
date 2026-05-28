<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account – TravelMate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --neon-pink: #ff00cc;
            --neon-blue: #00f0ff;
            --neon-purple: #7000ff;
            --dark-bg: #030014;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: var(--dark-bg);
            overflow-y: auto;
            overflow-x: hidden;
            color: #fff;
            padding: 2rem 1rem;
        }

        /* LIQUID BACKGROUND */
        .liquid-bg { position: fixed; inset: 0; z-index: -1; background: var(--dark-bg); overflow: hidden; }
        .liquid-blob {
            position: absolute; 
            border-radius: 50%; 
            filter: blur(120px); 
            animation: liquidFloat 25s infinite alternate ease-in-out;
            opacity: 0.6;
        }
        .blob-1 { background: var(--neon-pink); width: 70vw; height: 70vw; top: -10%; right: -20%; animation-delay: 0s; }
        .blob-2 { background: var(--neon-blue); width: 60vw; height: 60vw; bottom: -20%; left: -10%; animation-delay: -5s; }
        .blob-3 { background: var(--neon-purple); width: 80vw; height: 80vw; top: 30%; right: 20%; animation-delay: -10s; mix-blend-mode: screen; }

        @keyframes liquidFloat {
            0%   { transform: translate(0, 0) scale(1) rotate(0deg); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            50%  { transform: translate(-5vw, -10vh) scale(1.1) rotate(-45deg); border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            100% { transform: translate(5vw, 5vh) scale(0.9) rotate(-90deg); border-radius: 50% 50% 40% 60% / 40% 60% 50% 60%; }
        }

        /* GLASS CARD */
        .glass-container {
            display: flex;
            width: 1100px;
            max-width: 95vw;
            min-height: 550px;
            background: rgba(10, 10, 25, 0.25);
            backdrop-filter: blur(50px);
            -webkit-backdrop-filter: blur(50px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.8), inset 0 1px 0 rgba(255,255,255,0.2);
            overflow: visible;
            animation: cardEntrance 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
            transform-style: preserve-3d;
            will-change: transform;
            margin: auto;
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.9) translateY(60px) rotateX(10deg); }
            to   { opacity: 1; transform: scale(1) translateY(0) rotateX(0deg); }
        }

        /* Staggered Animations */
        .stagger-in {
            opacity: 0;
            animation: fadeUpItem 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeUpItem {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stagger-1 { animation-delay: 0.2s; }
        .stagger-2 { animation-delay: 0.3s; }
        .stagger-3 { animation-delay: 0.4s; }
        .stagger-4 { animation-delay: 0.5s; }
        .stagger-5 { animation-delay: 0.6s; }
        .stagger-6 { animation-delay: 0.7s; }

        /* LEFT FORM PANEL */
        .form-panel {
            flex: 1;
            padding: 40px 50px;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            border-right: 1px solid rgba(255,255,255,0.05);
            overflow-y: auto;
            border-radius: 40px 0 0 40px;
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--neon-pink), var(--neon-blue));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 15px rgba(255,0,204,0.4);
        }

        .form-header { margin-bottom: 20px; }
        .form-header h2 { font-family: 'Outfit'; font-size: 28px; font-weight: 800; margin-bottom: 6px; }
        .form-header p { color: rgba(255,255,255,0.5); font-size: 13px; }

        .input-group { margin-bottom: 14px; position: relative; }
        .input-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 6px;
            transition: 0.3s;
        }
        .input-wrapper {
            position: relative;
            background: rgba(255,255,255,0.03);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.08);
            transition: 0.3s;
            overflow: hidden;
        }
        .input-wrapper:focus-within {
            background: rgba(255,255,255,0.06);
            border-color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.15);
        }
        .input-wrapper:focus-within + label { color: var(--neon-blue); }
        .form-input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 12px 16px;
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            outline: none;
        }
        .form-input::placeholder { color: rgba(255,255,255,0.2); }
        .error-msg { font-size: 11px; color: #ff3366; margin-top: 6px; }

        /* Password strength bar */
        .strength-bar { display: flex; gap: 4px; margin-top: 8px; }
        .strength-segment { flex: 1; height: 3px; border-radius: 2px; background: rgba(255,255,255,0.1); transition: background 0.3s; }
        .strength-label { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }

        .btn-submit {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(45deg, var(--neon-blue), var(--neon-purple));
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 240, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: 5px;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; inset: 0; background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple));
            opacity: 0; transition: 0.4s; z-index: 0;
        }
        .btn-submit span { position: relative; z-index: 1; }
        .btn-submit:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 40px rgba(255, 0, 204, 0.5); }
        .btn-submit:hover::after { opacity: 1; }

        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 12px; margin-top: 12px;
            background: transparent; border: 1px solid rgba(255,255,255,0.15); border-radius: 14px;
            color: #fff; font-family: 'Space Grotesk'; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: 0.3s; text-decoration: none;
        }
        .btn-google:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.3); transform: translateY(-2px); }

        .login-wrap { text-align: center; margin-top: 20px; font-size: 13px; color: rgba(255,255,255,0.5); }
        .login-wrap a { color: var(--neon-pink); text-decoration: none; font-weight: 700; margin-left: 5px; transition: 0.3s; }
        .login-wrap a:hover { text-shadow: 0 0 15px rgba(255,0,204,0.6); }

        /* Eye Icon */
        .toggle-pwd { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; color: rgba(255,255,255,0.3); cursor: pointer; font-size: 18px; outline: none; transition: 0.3s; }
        .toggle-pwd:hover { color: #fff; }

        /* RIGHT VISUAL PANEL */
        .visual-panel {
            flex: 1.2;
            padding: 50px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(225deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%);
        }
        
        .visual-content h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 52px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 15px;
            background: linear-gradient(to right, #fff, rgba(255,255,255,0.5));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 2;
        }
        .visual-content p {
            font-size: 17px;
            color: rgba(255,255,255,0.6);
            max-width: 400px;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        /* Floating Holographic ID Card */
        .holo-id {
            position: absolute;
            right: -30px;
            left: auto;
            bottom: 40px;
            top: auto;
            transform: rotate(-10deg);
            width: 250px;
            height: 380px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            box-shadow: 20px 20px 50px rgba(0,0,0,0.5), inset 0 0 20px rgba(255,0,204,0.2);
            padding: 30px;
            display: flex; flex-direction: column;
            animation: idFloat 6s infinite alternate ease-in-out;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }
        .holo-id::before {
            content:''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.3) 50%, transparent 60%);
            background-size: 300% 300%;
            animation: holoShimmer 4s infinite linear;
            mix-blend-mode: overlay;
        }
        @keyframes idFloat {
            0% { transform: translateY(0) rotate(-10deg) translateX(0); }
            100% { transform: translateY(-15px) rotate(-5deg) translateX(-10px); box-shadow: 30px 30px 60px rgba(0,0,0,0.6), inset 0 0 30px rgba(0,240,255,0.3); }
        }
        .id-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
            border: 2px dashed rgba(255,255,255,0.3);
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; color: rgba(255,255,255,0.5);
        }
        .id-line {
            height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-bottom: 12px;
        }
        .id-line.short { width: 60%; margin: 0 auto 30px; }
        .id-chip {
            width: 40px; height: 30px; border-radius: 6px;
            background: linear-gradient(135deg, #ffd700, #b8860b);
            margin-bottom: auto;
        }
        .id-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto;}
        .id-barcode {
            width: 100%; height: 30px;
            background-image: repeating-linear-gradient(to right, #fff 0, #fff 2px, transparent 2px, transparent 6px, #fff 6px, #fff 10px, transparent 10px, transparent 12px);
            opacity: 0.3;
        }

        .feature-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 40px; position: relative; z-index: 2;
        }
        .feature-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .feature-icon {
            width: 36px; height: 36px; border-radius: 10px; background: rgba(0,240,255,0.15); color: var(--neon-blue);
            display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 12px;
        }
        .feature-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .feature-desc { font-size: 12px; color: rgba(255,255,255,0.4); }

        @media (max-width: 900px) {
            .visual-panel { display: none; }
            .glass-container { width: 90vw; border-radius: 30px; }
            .form-panel { padding: 40px 30px; border-right: none; }
        }
    </style>
</head>
<body>

    <div class="liquid-bg">
        <div class="liquid-blob blob-1"></div>
        <div class="liquid-blob blob-2"></div>
        <div class="liquid-blob blob-3"></div>
    </div>

    <div class="glass-container">
        
        <!-- LEFT FORM PANEL -->
        <div class="form-panel">
            <div class="brand-logo stagger-in stagger-1">
                <div class="brand-icon"><i class="fas fa-planet-ringed" style="font-family: 'Font Awesome 6 Free';"></i></div>
                TravelMate
            </div>

            <div class="form-header stagger-in stagger-1">
                <h2>Build Neural Profile</h2>
                <p>Register to unlock the complete travel ecosystem.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="role" id="role-input" value="user">

                {{-- ROLE SELECTOR --}}
                <div class="input-group stagger-in stagger-2" style="margin-bottom:18px">
                    <label>Account Type</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px">
                        <button type="button" id="role-user-btn" onclick="selectRole('user')" style="padding:11px;border-radius:12px;border:1.5px solid var(--neon-blue);background:rgba(0,240,255,0.12);color:#fff;font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;cursor:pointer;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px">
                            <i class="fas fa-user-astronaut"></i> Traveler
                        </button>
                        <button type="button" id="role-guide-btn" onclick="selectRole('guide')" style="padding:11px;border-radius:12px;border:1.5px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.03);color:rgba(255,255,255,0.5);font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;cursor:pointer;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px">
                            <i class="fas fa-user-tie"></i> Guide
                        </button>
                    </div>
                </div>

                <div class="input-group stagger-in stagger-2">
                    <label for="name">Designation (Full Name)</label>
                    <div class="input-wrapper">
                        <input id="name" type="text" name="name" class="form-input" placeholder="Traveler Alpha" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group stagger-in stagger-3">
                    <label for="email">Neural ID (Email Address)</label>
                    <div class="input-wrapper">
                        <input id="email" type="email" name="email" class="form-input" placeholder="alpha@domain.com" value="{{ old('email') }}" required autocomplete="username">
                    </div>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group stagger-in stagger-4">
                    <label for="password">Security Key (Password)</label>
                    <div class="input-wrapper">
                        <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="new-password" oninput="checkStrength(this.value)" style="padding-right: 50px;">
                        <button type="button" id="togglePassword" class="toggle-pwd" title="Toggle Visibility">
                            <i class="far fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div class="strength-bar">
                        <div class="strength-segment" id="s1"></div>
                        <div class="strength-segment" id="s2"></div>
                        <div class="strength-segment" id="s3"></div>
                        <div class="strength-segment" id="s4"></div>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group stagger-in stagger-5">
                    <label for="password_confirmation">Confirm Security Key</label>
                    <div class="input-wrapper">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required autocomplete="new-password" style="padding-right: 50px;">
                        <button type="button" id="togglePasswordConfirm" class="toggle-pwd" title="Toggle Visibility">
                            <i class="far fa-eye" id="eyeIconConfirm"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- GUIDE EXTRA FIELDS (hidden by default) --}}
                <div id="guide-fields" style="display:none;animation:fadeUpItem .4s ease both">
                    <div style="background:rgba(255,215,0,0.07);border:1px solid rgba(255,215,0,0.2);border-radius:12px;padding:12px 16px;margin-bottom:14px">
                        <div style="font-size:11px;color:#ffd700;font-weight:700;letter-spacing:1px;text-transform:uppercase"><i class="fas fa-shield-alt"></i> Guide Secret Key Required</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.45);margin-top:4px">Contact our admin to get your secret key before applying.</div>
                    </div>
                    <div class="input-group">
                        <label>Secret Key</label>
                        <div class="input-wrapper">
                            <input id="guide_secret_key" type="password" name="guide_secret_key" class="form-input" placeholder="••••••••" autocomplete="off">
                        </div>
                        @error('guide_secret_key')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div class="input-group">
                            <label>Specialty</label>
                            <div class="input-wrapper">
                                <input type="text" name="guide_specialty" class="form-input" placeholder="e.g. Heritage & Culture" value="{{ old('guide_specialty') }}">
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Experience (yrs)</label>
                            <div class="input-wrapper">
                                <input type="number" name="guide_experience" class="form-input" placeholder="e.g. 5" min="0" max="50" value="{{ old('guide_experience') }}">
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <div class="input-wrapper">
                            <input type="tel" name="guide_phone" class="form-input" placeholder="+91 98765 43210" value="{{ old('guide_phone') }}">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit stagger-in stagger-6" id="submit-btn">
                    <span id="submit-label">Generate Profile</span>
                </button>
            </form>


            <div class="login-wrap stagger-in stagger-6">
                Already registered? <a href="{{ route('login') }}">Access Network</a>
            </div>
        </div>

        <!-- RIGHT VISUAL PANEL -->
        <div class="visual-panel stagger-in stagger-1">
            <div class="holo-id">
                <div class="id-avatar"><i class="fas fa-user-astronaut"></i></div>
                <div class="id-line short"></div>
                <div class="id-line"></div>
                <div class="id-line"></div>
                <div class="id-chip"></div>
                <div class="id-footer">
                    <div class="id-barcode"></div>
                </div>
            </div>

            <div class="visual-content stagger-in stagger-3">
                <h1>Join the<br>Evolution.</h1>
                <p>Gain absolute control over your travel metrics. Our AI engines synthesize billions of data points to deliver flawless travel experiences.</p>
                
                <div class="feature-grid stagger-in stagger-4">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-robot"></i></div>
                        <div class="feature-title">AI Engine</div>
                        <div class="feature-desc">Dynamic travel generation</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon" style="background: rgba(255,0,204,0.15); color: var(--neon-pink);"><i class="fas fa-shield-alt"></i></div>
                        <div class="feature-title">Secure</div>
                        <div class="feature-desc">Encrypted cloud profiles</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // 3D Parallax Tilt Effect
        setTimeout(() => {
            const card = document.querySelector('.glass-container');
            const bgBlobs = document.querySelector('.liquid-bg');
            
            card.style.transition = 'transform 0.1s ease-out';
            
            document.addEventListener('mousemove', (e) => {
                if(window.innerWidth > 900) {
                    let xAxis = (window.innerWidth / 2 - e.pageX) / 50;
                    let yAxis = (window.innerHeight / 2 - e.pageY) / 50;
                    card.style.transform = `perspective(1200px) rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
                    
                    // Subtle background pan
                    bgBlobs.style.transform = `translate(${xAxis * -2}px, ${yAxis * -2}px)`;
                }
            });

            document.addEventListener('mouseleave', () => {
                card.style.transform = `perspective(1200px) rotateY(0deg) rotateX(0deg)`;
                bgBlobs.style.transform = `translate(0px, 0px)`;
            });
        }, 1200); // Wait for entrance animation to finish

        // Password strength checker
        function checkStrength(val) {
            const segs = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3'), document.getElementById('s4')];
            const label = document.getElementById('strength-label');

            let score = 0;
            if (val.length >= 8)  score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ff3366','#f59e0b','#00f0ff','#00ff66'];
            const labels = ['', 'Weak','Fair','Good','Secure'];

            segs.forEach((s, i) => {
                s.style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.1)';
                s.style.boxShadow = i < score ? `0 0 10px ${colors[score - 1]}` : 'none';
            });

            label.textContent = val.length > 0 ? labels[score] : '';
            label.style.color = score > 0 ? colors[score - 1] : 'transparent';
        }

        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                    togglePassword.style.color = 'rgba(255,255,255,0.3)';
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                    togglePassword.style.color = 'var(--neon-blue)';
                }
            });
        }

        // Confirm Password visibility toggle
        const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
        const passwordConfirm = document.querySelector('#password_confirmation');
        const eyeIconConfirm = document.querySelector('#eyeIconConfirm');

        if (togglePasswordConfirm) {
            togglePasswordConfirm.addEventListener('click', function () {
                const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirm.setAttribute('type', type);
                
                if (type === 'password') {
                    eyeIconConfirm.classList.remove('fa-eye-slash');
                    eyeIconConfirm.classList.add('fa-eye');
                    togglePasswordConfirm.style.color = 'rgba(255,255,255,0.3)';
                } else {
                    eyeIconConfirm.classList.remove('fa-eye');
                    eyeIconConfirm.classList.add('fa-eye-slash');
                    togglePasswordConfirm.style.color = 'var(--neon-blue)';
                }
            });
        }

        // Role toggle
        function selectRole(role) {
            document.getElementById('role-input').value = role;
            const userBtn  = document.getElementById('role-user-btn');
            const guideBtn = document.getElementById('role-guide-btn');
            const guideFields = document.getElementById('guide-fields');
            const submitLabel = document.getElementById('submit-label');

            if (role === 'guide') {
                guideBtn.style.borderColor  = 'var(--neon-pink)';
                guideBtn.style.background   = 'rgba(255,0,204,0.12)';
                guideBtn.style.color        = '#fff';
                userBtn.style.borderColor   = 'rgba(255,255,255,0.15)';
                userBtn.style.background    = 'rgba(255,255,255,0.03)';
                userBtn.style.color         = 'rgba(255,255,255,0.5)';
                guideFields.style.display   = 'block';
                submitLabel.textContent     = 'Submit Guide Application';
            } else {
                userBtn.style.borderColor   = 'var(--neon-blue)';
                userBtn.style.background    = 'rgba(0,240,255,0.12)';
                userBtn.style.color         = '#fff';
                guideBtn.style.borderColor  = 'rgba(255,255,255,0.15)';
                guideBtn.style.background   = 'rgba(255,255,255,0.03)';
                guideBtn.style.color        = 'rgba(255,255,255,0.5)';
                guideFields.style.display   = 'none';
                submitLabel.textContent     = 'Generate Profile';
            }
        }

        // Re-open guide fields if there was a validation error with guide role
        @if(old('role') === 'guide')
        selectRole('guide');
        @endif
    </script>
</body>
</html>
