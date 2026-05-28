@extends('layouts.app')
@section('title', 'Hotel Payment — TravelMate')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Poppins',sans-serif}
.pay-page{background:linear-gradient(135deg,#0a0b0f 0%,#0f0f2e 50%,#0a1628 100%);min-height:100vh;padding:3rem 1rem 6rem}
.pay-wrapper{max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start}
.pay-card{background:rgba(255,255,255,.05);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.1);border-radius:24px;padding:2.5rem;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.rzp-btn{width:100%;padding:1rem;border:none;border-radius:14px;cursor:pointer;font-family:'Poppins',sans-serif;font-size:1.05rem;font-weight:700;background:linear-gradient(135deg,#072654 0%,#1a6fdf 100%);color:#fff;display:flex;align-items:center;justify-content:center;gap:.75rem;transition:all .3s;box-shadow:0 10px 30px rgba(26,111,223,.35)}
.rzp-btn:hover{transform:translateY(-2px);box-shadow:0 15px 40px rgba(26,111,223,.5)}
.spinner{display:none;width:20px;height:20px;border:3px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.success-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center}
.success-box{background:linear-gradient(135deg,#0a1628,#0f1f3d);border:1px solid rgba(0,212,170,.3);border-radius:24px;padding:3rem;text-align:center;max-width:400px;animation:popIn .4s ease}
@keyframes popIn{from{transform:scale(.8);opacity:0}to{transform:scale(1);opacity:1}}
@media(max-width:700px){.pay-wrapper{grid-template-columns:1fr}}
</style>

<div class="pay-page">
<div class="pay-wrapper">

    {{-- Payment Card --}}
    <div class="pay-card">
        <div style="font-size:1.8rem;font-weight:900;color:#fff;margin-bottom:.3rem">🏨 Hotel Payment</div>
        <div style="color:#b0c4de;margin-bottom:2rem">Ref: <strong style="color:#ffca28">{{ $booking->booking_reference }}</strong></div>

        {{-- Hotel Info --}}
        @if($booking->hotel)
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:1.25rem;margin-bottom:2rem;display:flex;gap:1rem;align-items:center">
            <img src="{{ $booking->hotel->image_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80' }}" style="width:70px;height:70px;border-radius:10px;object-fit:cover">
            <div>
                <div style="color:#fff;font-weight:700">{{ $booking->hotel->name }}</div>
                <div style="color:#b0c4de;font-size:.82rem">
                    <i class="fas fa-map-marker-alt" style="color:#6c63ff"></i> {{ $booking->hotel->address ?? 'Hotel location' }}
                </div>
                <div style="font-size:.8rem;color:#b0c4de;margin-top:.3rem">
                    {{ $booking->traveler_details['room_type'] ?? 'Room' }} ·
                    {{ $booking->traveler_details['nights'] ?? 1 }} night(s)
                </div>
            </div>
        </div>
        @endif

        {{-- Payment Methods --}}
        <div style="font-size:.75rem;font-weight:700;color:#6c63ff;text-transform:uppercase;letter-spacing:2px;margin-bottom:1rem">Accepted Methods</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:2rem">
            @foreach([['fas fa-credit-card','Cards'],['fas fa-mobile-alt','UPI'],['fas fa-university','Net Banking'],['fas fa-wallet','Wallets']] as [$icon,$label])
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:.75rem .5rem;text-align:center;font-size:.75rem;color:#b0c4de">
                <i class="{{ $icon }}" style="display:block;font-size:1.4rem;margin-bottom:.3rem;color:#6c63ff"></i> {{ $label }}
            </div>
            @endforeach
        </div>

        {{-- Amount --}}
        <div style="background:rgba(108,99,255,.1);border:1px solid rgba(108,99,255,.3);border-radius:16px;padding:1.5rem;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center">
            <div>
                <div style="font-size:.8rem;color:#b0c4de;margin-bottom:.3rem">Total Amount Due</div>
                <div style="font-size:2rem;font-weight:900;color:#fff">₹{{ number_format($booking->total_amount) }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:.75rem;color:#b0c4de">Powered by</div>
                <div style="font-size:1.1rem;font-weight:700;color:#6c63ff">Razorpay</div>
            </div>
        </div>

        {{-- Pay Button --}}
        <button class="rzp-btn" id="pay-btn" onclick="initiatePayment()">
            <span class="spinner" id="btn-spinner"></span>
            <i class="fas fa-lock" id="btn-icon"></i>
            <span id="btn-text">Pay ₹{{ number_format($booking->total_amount) }} for Hotel</span>
        </button>

        {{-- Test Credentials --}}
        <div style="margin-top:1.5rem;background:rgba(255,202,40,.08);border:1px solid rgba(255,202,40,.25);border-radius:12px;padding:1.2rem;font-size:.82rem;color:#ffca28">
            <div style="font-weight:700;margin-bottom:.75rem"><i class="fas fa-flask"></i> Test Credentials</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                <div style="background:rgba(0,0,0,.3);border-radius:8px;padding:.75rem">
                    <div style="color:#b0c4de;margin-bottom:.4rem;font-size:.75rem;font-weight:600">📱 UPI</div>
                    <code style="color:#00d4aa">success@razorpay</code>
                </div>
                <div style="background:rgba(0,0,0,.3);border-radius:8px;padding:.75rem">
                    <div style="color:#b0c4de;margin-bottom:.4rem;font-size:.75rem;font-weight:600">💳 Card (Domestic Test Card)</div>
                    <code style="color:#00d4aa;font-size:.78rem">4111 1111 1111 1111</code><br>
                    <span style="color:#b0c4de;font-size:.72rem">Exp: 12/26 · CVV: 123</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:2rem;position:sticky;top:90px">
        <h3 style="color:#fff;font-weight:800;margin-bottom:1.5rem;font-size:1.1rem"><i class="fas fa-receipt" style="color:#6c63ff"></i> Booking Summary</h3>
        <div style="font-size:.88rem">
            @foreach([['Ref','booking_reference'],['Check-in','check_in'],['Check-out','check_out'],['Guests','adults'],['Room',"traveler_details.room_type"],['Nights',"traveler_details.nights"]] as [$label,$key])
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid rgba(255,255,255,.05)">
                <span style="color:#b0c4de">{{ $label }}</span>
                <span style="color:#fff;font-weight:600">
                    @if($key==='check_in' || $key==='check_out')
                        {{ $booking->{explode('.',$key)[0]} ? \Carbon\Carbon::parse($booking->{explode('.',$key)[0]})->format('d M Y') : 'N/A' }}
                    @elseif(str_contains($key,'.'))
                        @php [$a,$b] = explode('.',$key); @endphp
                        {{ $booking->$a[$b] ?? 'N/A' }}
                    @else
                        {{ $booking->$key ?? 'N/A' }}
                    @endif
                </span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding-top:1rem;margin-top:.5rem;border-top:1px solid rgba(255,255,255,.1)">
                <span style="color:#fff;font-weight:700;font-size:1rem">Total</span>
                <span style="color:#00d4aa;font-weight:900;font-size:1.3rem">₹{{ number_format($booking->total_amount) }}</span>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Success Overlay --}}
<div class="success-overlay" id="success-overlay">
    <div class="success-box">
        <div style="width:80px;height:80px;border-radius:50%;background:rgba(0,212,170,.15);border:2px solid #00d4aa;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2.5rem">✅</div>
        <h2 style="color:#fff;font-size:1.5rem;font-weight:800;margin-bottom:.5rem">Hotel Booked!</h2>
        <p style="color:#b0c4de;margin-bottom:1.5rem">Your room is confirmed. Redirecting...</p>
        <div style="display:flex;justify-content:center"><div class="spinner" style="display:block"></div></div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function initiatePayment() {
    const btn = document.getElementById('pay-btn');
    const spinner = document.getElementById('btn-spinner');
    const icon = document.getElementById('btn-icon');
    const text = document.getElementById('btn-text');

    btn.disabled = true;
    spinner.style.display = 'block';
    icon.style.display = 'none';
    text.textContent = 'Creating order...';

    try {
        const res  = await fetch('{{ route("hotels.razorpay.order", $booking) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        
        if (!res.ok) {
            const errText = await res.text();
            let msg = 'Failed to create order.';
            try {
                const errJson = JSON.parse(errText);
                msg = errJson.error || errJson.message || msg;
            } catch(e) {
                if (errText.includes('Page Expired')) msg = 'Session expired. Please refresh the page.';
                else msg = errText.substring(0, 100) || msg;
            }
            throw new Error(msg);
        }
        
        const data = await res.json();

        if (!data.order_id) throw new Error(data.error || 'Failed to create order.');

        btn.disabled = false;
        spinner.style.display = 'none';
        icon.style.display = 'inline';
        text.textContent = 'Pay ₹' + (data.amount / 100).toLocaleString('en-IN') + ' for Hotel';

        const rzp = new Razorpay({
            key: data.key_id, amount: data.amount, currency: data.currency,
            name: 'TravelMate Hotels', description: data.description,
            order_id: data.order_id,
            prefill: { 
                name: data.name, 
                email: data.email,
                contact: '9999999999' // Prefill mock contact to bypass saved-card verification
            },
            theme: { color: '#6c63ff' },
            handler: async function(response) {
                document.getElementById('success-overlay').style.display = 'flex';
                
                try {
                    const verify = await fetch('{{ route("hotels.razorpay.verify", $booking) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id:   response.razorpay_order_id,
                            razorpay_signature:  response.razorpay_signature,
                        }),
                    });
                    
                    if (!verify.ok) {
                        const verifyErr = await verify.text();
                        let verifyMsg = 'Payment verification failed.';
                        try {
                            const errJson = JSON.parse(verifyErr);
                            verifyMsg = errJson.message || errJson.error || verifyMsg;
                        } catch(e) {
                            verifyMsg = verifyErr.substring(0, 100) || verifyMsg;
                        }
                        throw new Error(verifyMsg);
                    }
                    
                    const result = await verify.json();
                    if (result.success) window.location.href = result.redirect_url;
                    else { document.getElementById('success-overlay').style.display = 'none'; alert('❌ ' + result.message); }
                } catch(errVerify) {
                    document.getElementById('success-overlay').style.display = 'none';
                    alert('❌ Verification Error: ' + errVerify.message);
                }
            },
        });
        rzp.open();
    } catch(e) {
        btn.disabled = false;
        spinner.style.display = 'none';
        icon.style.display = 'inline';
        text.textContent = 'Pay for Hotel';
        alert('❌ ' + e.message);
    }
}
</script>
@endsection
