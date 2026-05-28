@extends('layouts.app')
@section('title','AI Travel Assistant')
@section('content')
<div style="height:calc(100vh - 70px);display:flex;flex-direction:column">
    <div style="background:linear-gradient(135deg,#0f0f2e,#0a1628);padding:1.5rem 2rem;border-bottom:1px solid var(--border)">
        <div style="max-width:900px;margin:0 auto;display:flex;align-items:center;gap:1rem">
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-size:1.3rem">🤖</div>
            <div>
                <h1 style="font-size:1.25rem;font-weight:800">TravelMate AI Assistant</h1>
                <div style="font-size:.82rem;color:var(--muted)">RAG-powered · 20+ languages · Real-time travel knowledge</div>
            </div>
            <div style="margin-left:auto"><span class="badge-pill badge-success" style="font-size:.75rem">● Online</span></div>
        </div>
    </div>

    <div style="flex:1;overflow-y:auto;padding:1.5rem" id="chat-body">
        <div style="max-width:900px;margin:0 auto;display:flex;flex-direction:column;gap:1rem">
            {{-- History --}}
            @forelse($history as $msg)
            <div style="display:flex;{{ $msg->role==='user'?'justify-content:flex-end':'' }}">
                @if($msg->role==='assistant')
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;margin-right:.75rem;flex-shrink:0;font-size:.9rem;margin-top:4px">🤖</div>
                @endif
                <div style="max-width:75%;padding:.75rem 1rem;border-radius:{{ $msg->role==='user'?'16px 16px 4px 16px':'16px 16px 16px 4px' }};background:{{ $msg->role==='user'?'var(--primary)':'var(--surface)' }};border:1px solid var(--border);font-size:.9rem;line-height:1.6">
                    {!! nl2br(preg_replace([
                        '/\!\[(.*?)\]\((.*?)\)/',
                        '/\*\*(.*?)\*\*/'
                    ], [
                        '<img src="$2" alt="$1" style="max-width:100%;height:auto;border-radius:12px;margin:8px 0;display:block;box-shadow:0 4px 12px rgba(0,0,0,0.15)">',
                        '<strong>$1</strong>'
                    ], e($msg->message))) !!}
                    <div style="font-size:.72rem;opacity:.6;margin-top:.25rem;text-align:right">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:2rem;color:var(--muted)">
                <div style="font-size:3rem;margin-bottom:.75rem">🤖</div>
                <div style="font-weight:600;margin-bottom:.25rem">Hi! I'm TravelMate AI</div>
                <div style="font-size:.88rem">Ask me about destinations, packages, visas, weather, or let me build your itinerary!</div>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;justify-content:center;margin-top:1.25rem">
                    @foreach(['🗺️ Top beach destinations','📦 Budget packages under ₹500','🛂 Visa for Japan','🗓️ Plan 7-day Bali trip','💰 Budget tips for solo travel'] as $s)
                    <button onclick="quickSend(this)" class="chat-suggestion">{{ $s }}</button>
                    @endforeach
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <div style="border-top:1px solid var(--border);padding:1rem 1.5rem;background:var(--surface)">
        <div style="max-width:900px;margin:0 auto;display:flex;gap:.75rem;align-items:flex-end">
            <textarea id="main-chat-input" class="form-control" rows="1" placeholder="Ask anything about travel..." style="resize:none;max-height:120px;overflow-y:auto"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMainChat()}"
                oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
            <button onclick="sendMainChat()" class="btn btn-primary" style="padding:.75rem 1.25rem;flex-shrink:0"><i class="fas fa-paper-plane"></i> Send</button>
        </div>
        <div style="max-width:900px;margin:.5rem auto 0;font-size:.75rem;color:var(--muted)">
            Shift+Enter for new line · Powered by RAG with live travel knowledge base
        </div>
    </div>
</div>

<style>
.chat-suggestion{padding:.35rem .85rem;background:rgba(108,99,255,.15);color:var(--primary);border:1px solid rgba(108,99,255,.3);border-radius:50px;font-size:.8rem;cursor:pointer;transition:.15s;}
.chat-suggestion:hover{background:var(--primary);color:#fff;}
</style>
<script>
let session = null;
const body = document.getElementById('chat-body');
const inner = body.firstElementChild;
body.scrollTop = body.scrollHeight;

function quickSend(btn) { document.getElementById('main-chat-input').value=btn.textContent.trim(); sendMainChat(); }

async function sendMainChat() {
    const input = document.getElementById('main-chat-input');
    const msg = input.value.trim(); if (!msg) return;
    appendMsg(msg, 'user'); input.value=''; input.style.height='auto';
    const typing = appendMsg('Thinking...', 'bot', false, true);
    try {
        const res = await fetch('{{ route("chatbot.send") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({message:msg, session_id:session})
        });
        const data = await res.json();
        session = data.session_id;
        typing.remove();
        appendMsg(data.response.text, 'bot', true);
        if (data.response.suggestions?.length) {
            const sug = document.createElement('div');
            sug.style.cssText='display:flex;flex-wrap:wrap;gap:.5rem;max-width:75%;margin-top:-.5rem';
            data.response.suggestions.forEach(s=>{
                const b=document.createElement('button'); b.className='chat-suggestion'; b.textContent=s;
                b.onclick=()=>quickSend(b); sug.appendChild(b);
            });
            inner.appendChild(sug);
        }
    } catch(e) { typing.textContent='Error: '+e.message; }
    body.scrollTop=body.scrollHeight;
}

function appendMsg(text, role, markdown=false, isTyping=false) {
    const wrap=document.createElement('div');
    wrap.style.cssText=`display:flex;${role==='user'?'justify-content:flex-end':''}`;
    if(role==='bot'){const av=document.createElement('div');av.style.cssText='width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;margin-right:.75rem;flex-shrink:0;font-size:.9rem;margin-top:4px';av.textContent='🤖';wrap.appendChild(av);}
    const bubble=document.createElement('div');
    bubble.style.cssText=`max-width:75%;padding:.75rem 1rem;border-radius:${role==='user'?'16px 16px 4px 16px':'16px 16px 16px 4px'};background:${role==='user'?'var(--primary)':'var(--surface)'};border:1px solid var(--border);font-size:.9rem;line-height:1.6${isTyping?';color:var(--muted);font-style:italic':''}`;
    bubble.innerHTML=markdown?text.replace(/\!\[(.*?)\]\((.*?)\)/g,'<img src="$2" alt="$1" style="max-width:100%;height:auto;border-radius:12px;margin:8px 0;display:block;box-shadow:0 4px 12px rgba(0,0,0,0.15)">').replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>'):text;
    wrap.appendChild(bubble); inner.appendChild(wrap);
    body.scrollTop=body.scrollHeight;
    return wrap;
}
</script>
@endsection
