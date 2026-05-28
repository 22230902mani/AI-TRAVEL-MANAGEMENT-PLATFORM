@extends('layouts.admin')
@section('title', 'Admin — Team Avatars Control')
@section('content')

<div style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    
    {{-- Back navigation --}}
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.dashboard') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.88rem; font-weight: 600; padding: 0.5rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.07)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.color='rgba(255,255,255,0.6)';">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    {{-- Title block --}}
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #fff; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-user-cog" style="color: #9c27b0;"></i> Team Avatars Control Panel
        </h1>
        <p style="color: rgba(255,255,255,0.5); font-size: 0.88rem; margin-top: 0.4rem;">
            Upload and update custom profile pictures for TravelMate developers featured on the public Contact Us page.
        </p>
    </div>

    {{-- Success and error feedback --}}
    @if(session('success'))
    <div style="background: rgba(156, 39, 176, 0.1); border: 1px solid rgba(156, 39, 176, 0.35); border-radius: 14px; padding: 1.1rem 1.4rem; margin-bottom: 1.5rem; color: #d05ce3; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 15px rgba(156,39,176,0.15);">
        <i class="fas fa-check-circle" style="font-size: 1.1rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 14px; padding: 1.1rem 1.4rem; margin-bottom: 1.5rem; color: #ef4444; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-exclamation-circle" style="font-size: 1.1rem;"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 14px; padding: 1.1rem 1.4rem; margin-bottom: 1.5rem; color: #ef4444;">
        <div style="font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-times-circle"></i> Submission Errors:
        </div>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; line-height: 1.5;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main upload form card --}}
    <div style="background: linear-gradient(145deg, rgba(30,32,60,0.6) 0%, rgba(17,24,39,0.9) 100%); border: 1px solid rgba(156, 39, 176, 0.2); border-top: 1px solid rgba(156, 39, 176, 0.45); border-radius: 24px; padding: 2.5rem; box-shadow: 0 15px 40px rgba(0,0,0,0.5); position: relative; overflow: hidden;">
        
        {{-- Custom aesthetic accent light --}}
        <div style="position: absolute; bottom: -80px; right: -80px; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, rgba(156, 39, 176, 0.12) 0%, transparent 70%); pointer-events: none;"></div>

        <form method="POST" action="{{ route('admin.team_avatars.upload') }}" enctype="multipart/form-data">
            @csrf

            {{-- Member Selector --}}
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.72rem; color: rgba(255,255,255,0.6); letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 800;">
                    Select Team Member
                </label>
                <div style="position: relative;">
                    <select name="member" id="member-select" onchange="updateSelectedTheme()" required style="width: 100%; background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; color: #fff; padding: 1.1rem 1.4rem; font-size: 0.95rem; font-weight: 600; outline: none; appearance: none; transition: 0.3s; cursor: pointer;">
                        <option value="manikanta" selected>Manikanta — Lead Full Stack Developer</option>
                        <option value="devaraj">Devaraj — UI / UX Designer</option>
                        <option value="sai">Sai — AI & Backend Engineer</option>
                    </select>
                    <div style="position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: rgba(255,255,255,0.4);">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            {{-- Image Preview & Upload Zone --}}
            <div style="margin-bottom: 2.5rem;">
                <label style="display: block; font-size: 0.72rem; color: rgba(255,255,255,0.6); letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 800;">
                    Upload Profile Picture
                </label>
                
                {{-- Drag Zone Container --}}
                <div id="dropzone" style="border: 2px dashed rgba(156, 39, 176, 0.3); background: rgba(0,0,0,0.2); border-radius: 18px; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer; transition: 0.3s; position: relative;"
                     onclick="document.getElementById('file-input').click()"
                     onmouseover="this.style.borderColor='#9c27b0'; this.style.background='rgba(156,39,176,0.03)';"
                     onmouseout="this.style.borderColor='rgba(156, 39, 176, 0.3)'; this.style.background='rgba(0,0,0,0.2)';">
                    
                    <input type="file" name="photo" id="file-input" accept="image/*" onchange="previewImage(event)" style="display: none;" required>

                    {{-- Default instruction state --}}
                    <div id="instruction-state">
                        <div id="avatar-circle-icon" style="width: 72px; height: 72px; border-radius: 50%; background: rgba(156, 39, 176, 0.08); border: 2px solid rgba(156, 39, 176, 0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; font-size: 1.75rem; color: #9c27b0; transition: 0.3s;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <p style="color: #fff; font-weight: 700; font-size: 0.95rem; margin: 0 0 0.4rem 0;">Drag and drop an image here or click to browse</p>
                        <p style="color: rgba(255,255,255,0.4); font-size: 0.78rem; margin: 0;">Supports PNG, JPEG, JPG, or WEBP (Max 2MB)</p>
                    </div>

                    {{-- Image preview state (hidden initially) --}}
                    <div id="preview-state" style="display: none; align-items: center; justify-content: center; flex-direction: column;">
                        <img id="image-preview" src="#" alt="Preview" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #9c27b0; box-shadow: 0 0 20px rgba(156, 39, 176, 0.4); margin-bottom: 1.25rem; display: block; transition: 0.3s;">
                        <div style="background: rgba(255,255,255,0.06); padding: 0.4rem 1rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.1); display: inline-flex; align-items: center; gap: 0.5rem;">
                            <span id="file-name-text" style="color: #fff; font-size: 0.78rem; font-weight: 600;">filename.png</span>
                            <span onclick="resetPreview(event)" style="color: #ef4444; font-size: 0.78rem; cursor: pointer; font-weight: 700; margin-left: 0.5rem; display: inline-block;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                <i class="fas fa-trash-alt"></i> Remove
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Action Button --}}
            <button type="submit" id="submit-btn" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 1.1rem 2rem; background: linear-gradient(135deg, #9c27b0, #7b1fa2); color: #fff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.1em; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 24px rgba(156, 39, 176, 0.3); text-transform: uppercase;">
                <i class="fas fa-sync-alt"></i> Upload and Synchronize Photo
            </button>
        </form>
    </div>
</div>

<script>
    // Member colors to update key elements dynamically based on choice
    const memberConfig = {
        manikanta: { color: '#6c63ff', name: 'Manikanta' },
        devaraj: { color: '#14b8a6', name: 'Devaraj' },
        sai: { color: '#f59e0b', name: 'Sai' }
    };

    function updateSelectedTheme() {
        const select = document.getElementById('member-select');
        const config = memberConfig[select.value];
        if (!config) return;

        // Update elements
        const previewBorder = document.getElementById('image-preview');
        const iconCircle = document.getElementById('avatar-circle-icon');
        const submitBtn = document.getElementById('submit-btn');
        const dropzone = document.getElementById('dropzone');

        // Apply new accent colors smoothly
        if (previewBorder) {
            previewBorder.style.borderColor = config.color;
            previewBorder.style.boxShadow = `0 0 20px ${config.color}60`;
        }
        if (iconCircle) {
            iconCircle.style.color = config.color;
            iconCircle.style.borderColor = `${config.color}50`;
            iconCircle.style.background = `${config.color}10`;
        }
        if (submitBtn) {
            submitBtn.style.background = `linear-gradient(135deg, ${config.color}, ${adjustColorBrightness(config.color, -20)})`;
            submitBtn.style.boxShadow = `0 8px 24px ${config.color}40`;
        }
    }

    // Helper function to darken color for button gradients
    function adjustColorBrightness(hex, percent) {
        let num = parseInt(hex.replace("#",""), 16),
            amt = Math.round(2.55 * percent),
            R = (num >> 16) + amt,
            G = (num >> 8 & 0x00FF) + amt,
            B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R<255?R<0?0:R:255)*0x10000 + (G<255?G<0?0:G:255)*0x100 + (B<255?B<0?0:B:255)).toString(16).slice(1);
    }

    // Client-side instant image previewing
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Render file name
            const nameText = document.getElementById('file-name-text');
            if (nameText) {
                nameText.innerText = file.name.length > 22 ? file.name.substring(0, 19) + '...' : file.name;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('image-preview');
                if (previewImg) {
                    previewImg.src = e.target.result;
                }

                // Switch states
                document.getElementById('instruction-state').style.display = 'none';
                document.getElementById('preview-state').style.display = 'flex';
                
                // Trigger styling sync
                updateSelectedTheme();
            }
            reader.readAsDataURL(file);
        }
    }

    // Resetting the file upload input preview
    function resetPreview(event) {
        event.stopPropagation(); // Avoid triggering dropzone select file trigger
        
        const fileInput = document.getElementById('file-input');
        if (fileInput) {
            fileInput.value = '';
        }

        // Revert UI view states
        document.getElementById('instruction-state').style.display = 'block';
        document.getElementById('preview-state').style.display = 'none';
    }

    // Setup initial theme on load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedTheme();
    });
</script>

@endsection
