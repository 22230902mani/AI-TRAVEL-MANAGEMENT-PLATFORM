@extends('layouts.app')
@section('title','Expenses — '.$itinerary->title)
@section('content')
<section class="section">
    <div class="section-inner">
        <div style="margin-bottom:1rem"><a href="{{ route('itineraries.show',$itinerary) }}" style="color:var(--muted);font-size:.85rem"><i class="fas fa-arrow-left"></i> Back to Itinerary</a></div>
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;margin-bottom:.5rem">💸 Expense Tracker</h1>
        <p style="color:var(--muted);margin-bottom:2rem">{{ $itinerary->title }}</p>
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start">
            <div>
                {{-- Add Expense --}}
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <h3 style="font-weight:700;margin-bottom:1rem">Log Expense</h3>
                    <form method="POST" action="{{ route('expenses.store',$itinerary) }}" enctype="multipart/form-data">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                            <div class="form-group"><label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    @foreach(['accommodation','food','transport','activities','shopping','health','other'] as $cat)
                                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                                    @endforeach
                                </select></div>
                            <div class="form-group"><label class="form-label">Amount (INR)</label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required></div>
                            <div class="form-group"><label class="form-label">Date</label>
                                <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            <div class="form-group"><label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    @foreach(['cash','card','upi','wallet'] as $m)
                                    <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                                    @endforeach
                                </select></div>
                        </div>
                        <div class="form-group"><label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="What did you spend on?"></div>
                        <div class="form-group"><label class="form-label">Receipt Photo</label>
                            <input type="file" name="receipt_image" class="form-control" accept="image/*"></div>
                        <button type="submit" class="btn btn-primary">Log Expense</button>
                    </form>
                </div>
                {{-- List --}}
                @foreach($expenses as $exp)
                <div class="card" style="padding:1rem;margin-bottom:.6rem;display:flex;justify-content:space-between;align-items:center">
                    <div><div style="font-weight:600;font-size:.9rem">{{ ucfirst($exp->category) }}
                        @if($exp->description)<span style="font-weight:400;color:var(--muted)"> — {{ $exp->description }}</span>@endif</div>
                        <div style="font-size:.78rem;color:var(--muted)">{{ $exp->expense_date->format('M d, Y') }} • {{ ucfirst($exp->payment_method??'cash') }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <span style="font-weight:800;color:var(--secondary)">₹{{ number_format($exp->amount,2) }}</span>
                        <form method="POST" action="{{ route('expenses.destroy',[$itinerary,$exp]) }}">@csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:.25rem .5rem" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
                {{ $expenses->links() }}
            </div>
            {{-- Budget Widget --}}
            <div class="card" style="padding:1.5rem;position:sticky;top:90px;background:linear-gradient(135deg,rgba(0,212,170,.08),rgba(108,99,255,.08))">
                <h3 style="font-weight:700;margin-bottom:1.25rem">Budget Summary</h3>
                @foreach([['Budget','₹'.number_format($dashboard['budget']),'var(--primary)'],['Spent','₹'.number_format($dashboard['total_spent']),'var(--accent)'],['Remaining','₹'.number_format($dashboard['remaining']),'var(--secondary)'],['Predicted Total','₹'.number_format($dashboard['predicted_total']),'var(--gold)']] as [$l,$v,$c])
                <div style="display:flex;justify-content:space-between;margin-bottom:.6rem;font-size:.9rem">
                    <span style="color:var(--muted)">{{ $l }}</span><span style="font-weight:700;color:{{ $c }}">{{ $v }}</span>
                </div>
                @endforeach
                <div style="height:6px;background:var(--surface);border-radius:6px;margin-top:.75rem">
                    <div style="width:{{ min(100,$dashboard['percent_used']) }}%;height:100%;background:{{ $dashboard['percent_used']>80?'var(--accent)':'var(--secondary)' }};border-radius:6px"></div>
                </div>
                <div style="font-size:.78rem;color:var(--muted);margin-top:.4rem;text-align:center">{{ $dashboard['percent_used'] }}% of budget used</div>
                @if(!empty($dashboard['anomalies']))
                <div style="margin-top:1rem;display:flex;flex-direction:column;gap:.5rem">
                    @foreach($dashboard['anomalies'] as $anomaly)
                    <div style="padding:.75rem;background:{{ $anomaly['severity'] === 'high' ? 'rgba(255,75,75,.08)' : 'rgba(249,168,37,.08)' }};
                                border-left:4px solid {{ $anomaly['severity'] === 'high' ? 'var(--accent)' : '#f9a825' }};
                                border-radius:8px;font-size:.8rem;color:{{ $anomaly['severity'] === 'high' ? 'var(--accent)' : '#e65100' }};
                                font-weight:600;display:flex;align-items:flex-start;gap:.5rem">
                        <i class="fas fa-triangle-exclamation" style="margin-top:.15rem;flex-shrink:0"></i>
                        <span>{{ $anomaly['message'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
