@extends('layouts.app')
@section('title','About TravelMate')
@section('content')
<section class="section">
    <div class="section-inner" style="max-width:900px;margin:0 auto">
        <div class="section-header">
            <div class="section-tag">🚀 About Us</div>
            <h1 class="section-title">About TravelMate</h1>
            <p class="section-sub">An enterprise-grade, AI-powered travel ecosystem built for the next generation of explorers.</p>
        </div>
        <div class="grid-2" style="gap:2rem;margin-bottom:3rem">
            <div class="card" style="padding:2rem">
                <h2 style="font-weight:700;margin-bottom:1rem">🎓 Final Year Project</h2>
                <p style="color:var(--muted);line-height:1.8">TravelMate is an enterprise-level microservice-ready travel platform built on the MVC architectural pattern using Laravel. It demonstrates advanced computer science concepts including:</p>
                <ul style="margin-top:1rem;display:flex;flex-direction:column;gap:.5rem;list-style:none">
                    @foreach(['Genetic algorithm for itinerary optimization','LSTM-inspired price prediction','Blockchain-inspired tamper-evident reviews','RAG chatbot with vector DB simulation','Event-sourced booking history (CQRS-lite)','Gradient-boosted regression for budget prediction','Spatie RBAC with 5 role levels','Real-time WebSocket collaboration (architecture)'] as $feat)
                    <li style="font-size:.88rem;color:var(--muted)"><i class="fas fa-check" style="color:var(--secondary);width:16px"></i> {{ $feat }}</li>
                    @endforeach
                </ul>
            </div>
            <div>
                @foreach([['🏗️','MVC Architecture','Laravel 12 • Eloquent ORM • Service Layer • Repository Pattern • Event Sourcing'],['🤖','AI & ML Modules','Collaborative Filtering • Genetic Algorithm • Gradient Boosting • LSTM Simulation • RAG Chatbot'],['🛡️','Security','RBAC • CSRF/XSS • JWT-ready • PKI-signed QR tickets • Hash-chained reviews'],['⚡','Performance','Redis caching architecture • Queue workers • Horizon-ready • CDN-optimized assets']] as [$emoji,$title,$desc])
                <div class="card" style="padding:1.25rem;margin-bottom:1rem;display:flex;gap:1rem">
                    <div style="font-size:1.5rem;flex-shrink:0">{{ $emoji }}</div>
                    <div><div style="font-weight:700;margin-bottom:.25rem">{{ $title }}</div>
                        <div style="font-size:.83rem;color:var(--muted)">{{ $desc }}</div></div>
                </div>
                @endforeach
            </div>
        </div>
        <div style="text-align:center">
            <a href="{{ route('register') }}" class="btn btn-primary" style="padding:.85rem 2rem;font-size:1rem"><i class="fas fa-rocket"></i> Get Started Free</a>
        </div>
    </div>
</section>
@endsection
