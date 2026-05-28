@extends('layouts.app')
@section('title', 'Secure Payment — TravelMate')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

.pay-page { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0a0b0f 0%, #0f0f2e 50%, #0a1628 100%); min-height: 100vh; padding: 3rem 1rem; }
.pay-wrapper { max-width: 960px; margin: 0 auto; display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start; }
.pay-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
.pay-title { font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: .3rem; }
.pay-subtitle { color: #b0c4de; font-size: .9rem; margin-bottom: 2rem; }
.section-label { font-size: .75rem; font-weight: 700; color: #6c63ff; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem; }

/* Razorpay brand button */
.rzp-btn {
    width: 100%; padding: 1rem; border: none; border-radius: 14px; cursor: pointer;
    font-family: 'Poppins', sans-serif; font-size: 1.05rem; font-weight: 700;
    background: linear-gradient(135deg, #072654 0%, #1a6fdf 100%);
    color: #fff; display: flex; align-items: center; justify-content: center; gap: .75rem;
    transition: all .3s ease; position: relative; overflow: hidden;
    box-shadow: 0 10px 30px rgba(26,111,223,0.35);
}
.rzp-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(26,111,223,0.5); }
.rzp-btn:active { transform: translateY(0); }
.rzp-btn img { height: 22px; filter: brightness(0) invert(1); }
.rzp-btn .shimmer { position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent); transform: translateX(-100%); animation: shimmer 2s infinite; }
@keyframes shimmer { 100% { transform: translateX(100%); } }

/* Order summary card */
.summary-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 2rem; position: sticky; top: 90px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: .6rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: .88rem; }
.summary-row:last-child { border: none; }
.summary-label { color: #b0c4de; }
.summary-value { color: #fff; font-weight: 600; }
.summary-total { font-size: 1.4rem; font-weight: 800; color: #00d4aa; }

/* Security badges */
.badges { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem; }
.badge-item { display: flex; align-items: center; gap: .4rem; font-size: .75rem; color: #b0c4de; }
.badge-item i { color: #00d4aa; }

/* Payment methods display */
.methods-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-bottom: 2rem; }
.method-item { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: .75rem .5rem; text-align: center; font-size: .75rem; color: #b0c4de; }
.method-item i { display: block; font-size: 1.4rem; margin-bottom: .3rem; color: #6c63ff; }

/* Loading spinner */
.spinner { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Success overlay */
.success-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; }
.success-box { background: linear-gradient(135deg,#0a1628,#0f1f3d); border:1px solid rgba(0,212,170,.3); border-radius:24px; padding:3rem; text-align:center; max-width:400px; animation: popIn .4s ease; }
@keyframes popIn { from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }
.success-icon { width:80px; height:80px; border-radius:50%; background:rgba(0,212,170,.15); border:2px solid #00d4aa; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:2.5rem; color:#00d4aa; }

@media(max-width:768px) { .pay-wrapper { grid-template-columns: 1fr; } .summary-card { position:static; } }
</style>

<div class="pay-page">
<div class="pay-wrapper">

    {{-- LEFT: Payment Panel --}}
    <div class="pay-card">
        <div class="pay-title">🔐 Secure Checkout</div>
        <div class="pay-subtitle">Booking ref: <strong style="color:#ffca28">{{ $booking->booking_reference }}</strong></div>

        {{-- Accepted Methods --}}
        <div class="section-label">Accepted Payment Methods</div>
        <div class="methods-grid">
            <div class="method-item"><i class="fas fa-credit-card"></i> Cards</div>
            <div class="method-item"><i class="fas fa-mobile-alt"></i> UPI</div>
            <div class="method-item"><i class="fas fa-university"></i> Net Banking</div>
            <div class="method-item"><i class="fas fa-wallet"></i> Wallets</div>
        </div>

        {{-- Amount display --}}
        <div style="background:rgba(108,99,255,0.1);border:1px solid rgba(108,99,255,0.3);border-radius:16px;padding:1.5rem;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center;">
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
        <button class="rzp-btn" id="pay-btn" onclick="initiateRazorpayPayment()">
            <span class="shimmer"></span>
            <span class="spinner" id="btn-spinner"></span>
            <i class="fas fa-lock" id="btn-icon"></i>
            <span id="btn-text">Pay ₹{{ number_format($booking->total_amount) }} Securely</span>
        </button>

        {{-- Security Badges --}}
        <div class="badges">
            <div class="badge-item"><i class="fas fa-shield-alt"></i> 256-bit SSL Encrypted</div>
            <div class="badge-item"><i class="fas fa-check-circle"></i> RBI Compliant</div>
            <div class="badge-item"><i class="fas fa-undo"></i> Instant Refund Support</div>
            <div class="badge-item"><i class="fas fa-lock"></i> PCI DSS Secured</div>
        </div>

        {{-- Test Credentials hint --}}
        <div style="margin-top:1.5rem;background:rgba(255,202,40,.08);border:1px solid rgba(255,202,40,.25);border-radius:12px;padding:1.2rem;font-size:.82rem;color:#ffca28;">
            <div style="font-weight:700;margin-bottom:.75rem"><i class="fas fa-flask"></i> Test Mode Credentials</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div style="background:rgba(0,0,0,.3);border-radius:8px;padding:.75rem;">
                    <div style="color:#b0c4de;margin-bottom:.4rem;font-size:.75rem;font-weight:600">📱 UPI (Easiest)</div>
                    <code style="color:#00d4aa;font-size:.85rem;">success@razorpay</code>
                </div>
                <div style="background:rgba(0,0,0,.3);border-radius:8px;padding:.75rem;">
                    <div style="color:#b0c4de;margin-bottom:.4rem;font-size:.75rem;font-weight:600">💳 Card (Domestic Test Card)</div>
                    <code style="color:#00d4aa;font-size:.78rem;">4111 1111 1111 1111</code><br>
                    <span style="color:#b0c4de;font-size:.75rem;">Exp: 12/26 · CVV: 123</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Order Summary --}}
    <div class="summary-card">
        <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fas fa-receipt" style="color:#6c63ff"></i> Order Summary
        </div>

        @if($booking->package)
        <div style="background:rgba(255,255,255,0.04);border-radius:12px;padding:1rem;margin-bottom:1.5rem;">
            <div style="font-weight:700;color:#fff;margin-bottom:.3rem">{{ $booking->package->title }}</div>
            @if($booking->package->destination)
            <div style="font-size:.8rem;color:#b0c4de"><i class="fas fa-map-marker-alt" style="color:#6c63ff"></i> {{ $booking->package->destination->name }}, {{ $booking->package->destination->country }}</div>
            @endif
        </div>
        @endif

        <div class="summary-row">
            <span class="summary-label">Booking Ref</span>
            <span class="summary-value" style="font-size:.82rem;color:#ffca28">{{ $booking->booking_reference }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Travelers</span>
            <span class="summary-value">{{ $booking->adults }} Adult{{ $booking->adults > 1 ? 's':'' }}{{ $booking->children ? ', '.$booking->children.' Child' : '' }}</span>
        </div>
        @if($booking->check_in)
        <div class="summary-row">
            <span class="summary-label">Check-in</span>
            <span class="summary-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</span>
        </div>
        @endif
        <div class="summary-row" style="margin-top:.5rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.1)">
            <span class="summary-label" style="font-size:1rem;font-weight:700;color:#fff">Total</span>
            <span class="summary-total">₹{{ number_format($booking->total_amount) }}</span>
        </div>

        <div style="margin-top:1.5rem;font-size:.78rem;color:#b0c4de;line-height:1.6">
            <i class="fas fa-info-circle" style="color:#6c63ff"></i>
            By proceeding, you agree to our <a href="#" style="color:#6c63ff">Terms of Service</a> and <a href="#" style="color:#6c63ff">Cancellation Policy</a>.
        </div>
    </div>
</div>
</div>

{{-- Success Overlay --}}
<div class="success-overlay" id="success-overlay">
    <div class="success-box">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h2 style="color:#fff;font-size:1.5rem;font-weight:800;margin-bottom:.5rem">Payment Successful!</h2>
        <p style="color:#b0c4de;margin-bottom:1.5rem">Your trip has been confirmed. Redirecting...</p>
        <div style="display:flex;justify-content:center"><div class="spinner" style="display:block"></div></div>
    </div>
</div>

{{-- Razorpay Checkout.js --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const BOOKING_ID    = '{{ $booking->id }}';
const ORDER_URL     = '{{ route("bookings.razorpay.order", $booking) }}';
const VERIFY_URL    = '{{ route("bookings.razorpay.verify", $booking) }}';
const CSRF_TOKEN    = '{{ csrf_token() }}';

async function initiateRazorpayPayment() {
    const btn     = document.getElementById('pay-btn');
    const spinner = document.getElementById('btn-spinner');
    const icon    = document.getElementById('btn-icon');
    const text    = document.getElementById('btn-text');

    // Show loading state
    btn.disabled = true;
    spinner.style.display = 'block';
    icon.style.display = 'none';
    text.textContent = 'Creating order...';

    try {
        // Step 1: Create Razorpay order on server
        const res  = await fetch(ORDER_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
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

        // Reset button
        btn.disabled = false;
        spinner.style.display = 'none';
        icon.style.display = 'inline';
        text.textContent = 'Pay ₹' + (data.amount / 100).toLocaleString('en-IN') + ' Securely';

        // Step 2: Open Razorpay checkout popup
        const options = {
            key:         data.key_id,
            amount:      data.amount,
            currency:    data.currency,
            name:        'TravelMate',
            description: data.description,
            order_id:    data.order_id,
            image:       'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=100&q=80',
            prefill: {
                name:  data.name,
                email: data.email,
                contact: '9999999999', // Prefill mock contact to bypass saved-card verification
            },
            notes: { booking_ref: data.booking_ref },
            theme: { color: '#6c63ff' },
            modal: {
                ondismiss: function() {
                    btn.disabled = false;
                    text.textContent = 'Pay ₹' + (data.amount / 100).toLocaleString('en-IN') + ' Securely';
                }
            },
            handler: async function(response) {
                // Step 3: Verify payment signature on server
                document.getElementById('success-overlay').style.display = 'flex';

                try {
                    const verifyRes = await fetch(VERIFY_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id:   response.razorpay_order_id,
                            razorpay_signature:  response.razorpay_signature,
                        }),
                    });
                    
                    if (!verifyRes.ok) {
                        const verifyErr = await verifyRes.text();
                        let verifyMsg = 'Payment verification failed.';
                        try {
                            const errJson = JSON.parse(verifyErr);
                            verifyMsg = errJson.message || errJson.error || verifyMsg;
                        } catch(e) {
                            verifyMsg = verifyErr.substring(0, 100) || verifyMsg;
                        }
                        throw new Error(verifyMsg);
                    }
                    
                    const result = await verifyRes.json();

                    if (result.success) {
                        window.location.href = result.redirect_url;
                    } else {
                        document.getElementById('success-overlay').style.display = 'none';
                        alert('❌ ' + (result.message || 'Payment verification failed.'));
                    }
                } catch(errVerify) {
                    document.getElementById('success-overlay').style.display = 'none';
                    alert('❌ Verification Error: ' + errVerify.message);
                }
            },
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function(response) {
            document.getElementById('success-overlay').style.display = 'none';
            alert('❌ Payment failed: ' + response.error.description);
        });
        rzp.open();

    } catch (err) {
        btn.disabled = false;
        spinner.style.display = 'none';
        icon.style.display = 'inline';
        text.textContent = 'Pay Securely';
        alert('❌ Could not initiate payment: ' + err.message);
    }
}
</script>
@endsection
