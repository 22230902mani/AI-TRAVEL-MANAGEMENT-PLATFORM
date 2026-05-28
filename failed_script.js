
const localPlaces = [];

function editDistance(s1, s2) {
    s1 = s1.toLowerCase();
    s2 = s2.toLowerCase();
    let costs = [];
    for (let i = 0; i <= s1.length; i++) {
        let lastValue = i;
        for (let j = 0; j <= s2.length; j++) {
            if (i == 0) costs[j] = j;
            else {
                if (j > 0) {
                    let newValue = costs[j - 1];
                    if (s1.charAt(i - 1) != s2.charAt(j - 1)) {
                        newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                    }
                    costs[j - 1] = lastValue;
                    lastValue = newValue;
                }
            }
        }
        if (i > 0) costs[s2.length] = lastValue;
    }
    return costs[s2.length];
}

function getSimilarity(s1, s2) {
    let longer = s1;
    let shorter = s2;
    if (s1.length < s2.length) {
        longer = s2;
        shorter = s1;
    }
    let longerLength = longer.length;
    if (longerLength === 0) return 1.0;
    return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
}

function detectLiveOrigin() {
    const input = document.getElementById('origin-input');
    input.value = "Detecting live GPS node...";
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async pos => {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`);
                const data = await res.json();
                const city = data.address.city || data.address.town || data.address.village || data.address.county || "Phagwara";
                const state = data.address.state || "Punjab";
                input.value = `${city}, ${state}`;
            } catch(e) {
                input.value = "Phagwara, Punjab";
            }
        }, err => {
            input.value = "Phagwara, Punjab";
        });
    } else {
        input.value = "Phagwara, Punjab";
    }
}

document.getElementById('itinerary-form').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('generate-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;

    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());
    data['interests[]'] = [...form.querySelectorAll('[name="interests[]"]:checked')].map(i=>i.value);

    try {
        const res = await fetch('http://localhost:8100/itineraries/generate', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':''},
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) { renderResult(json.plan, json.itinerary); }
        else { alert('Generation failed. Please try again.'); }
    } catch(err) { alert('Error: ' + err.message); }
    finally { btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Generate AI Itinerary'; btn.disabled = false; }
});

// --- Origin Autocomplete (Local Database + Free Nominatim API) ---
const originInput = document.getElementById('origin-input');
const autocompleteBox = document.getElementById('origin-autocomplete');
let timeout = null;

originInput.addEventListener('focus', function() {
    this.closest('.form-group').style.zIndex = '9999';
});
originInput.addEventListener('blur', function() {
    setTimeout(() => { this.closest('.form-group').style.zIndex = ''; }, 200);
});

originInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        autocompleteBox.style.display = 'none';
        return;
    }

    // Show searching state
    autocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:var(--muted);font-size:.85rem"><i class="fas fa-spinner fa-spin"></i> Searching cities...</div>';
    autocompleteBox.style.display = 'block';

    timeout = setTimeout(async () => {
        try {
            const queryLower = query.toLowerCase();
            
            // 1. Search local places
            let localMatches = [];
            if (typeof localPlaces !== 'undefined' && Array.isArray(localPlaces)) {
                localMatches = localPlaces.filter(p => {
                    const name = (p.name || '').toLowerCase();
                    const city = (p.city || '').toLowerCase();
                    const state = (p.state || '').toLowerCase();
                    
                    if (name.includes(queryLower) || city.includes(queryLower) || state.includes(queryLower)) {
                        return true;
                    }
                    
                    if (getSimilarity(name, queryLower) > 0.75 || getSimilarity(city, queryLower) > 0.75) {
                        return true;
                    }
                    
                    return false;
                }).map(p => ({
                    city: p.city || p.name,
                    state: p.state || 'India',
                    isLocal: true
                }));
            }

            // 2. Fetch from backend proxy
            let apiMatches = [];
            try {
                const response = await fetch(`/api/city-search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                apiMatches = data.map(place => {
                    const city = place.address.city || place.address.town || place.address.village || place.name;
                    const state = place.address.state || 'India';
                    return { city, state, isLocal: false };
                });
            } catch (apiErr) {
                console.error("Nominatim API failed, relying on local search", apiErr);
            }

            // Combine both lists, keeping local matches at the top and removing duplicates
            const combined = [...localMatches];
            apiMatches.forEach(apiItem => {
                const exists = combined.some(localItem => 
                    localItem.city.toLowerCase() === apiItem.city.toLowerCase()
                );
                if (!exists) {
                    combined.push(apiItem);
                }
            });

            if (combined.length === 0) {
                autocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:var(--muted);font-size:.85rem">No cities found.</div>';
                return;
            }

            autocompleteBox.innerHTML = '';
            combined.forEach(place => {
                const item = document.createElement('div');
                item.style.cssText = 'padding:.75rem 1rem;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);transition:background .2s;display:flex;align-items:center;justify-content:space-between;gap:.5rem';
                
                const badgeHtml = place.isLocal 
                    ? `<span style="font-size:0.7rem;background:rgba(108,99,255,0.15);color:#00f0ff;padding:2px 6px;border-radius:4px;font-weight:700">✨ Recommended</span>`
                    : '';

                item.innerHTML = `
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <i class="fas fa-location-dot" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i> 
                        <div>
                            <div style="font-weight:700;font-size:.9rem;color:#ffffff">${place.city}</div>
                            <div style="font-size:.75rem;color:rgba(255,255,255,0.5)">${place.state}</div>
                        </div>
                    </div>
                    ${badgeHtml}
                `;
                
                item.onmouseenter = () => item.style.background = 'rgba(255, 255, 255, 0.08)';
                item.onmouseleave = () => item.style.background = 'transparent';
                
                item.onclick = () => {
                    originInput.value = `${place.city}, ${place.state}`;
                    autocompleteBox.style.display = 'none';
                };
                
                autocompleteBox.appendChild(item);
            });
        } catch (error) {
            autocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:#e74c3c;font-size:.85rem">Error loading cities.</div>';
        }
    }, 500);
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!originInput.contains(e.target) && !autocompleteBox.contains(e.target)) {
        autocompleteBox.style.display = 'none';
    }
    if (!document.getElementById('dest-input').contains(e.target) && !document.getElementById('dest-autocomplete').contains(e.target)) {
        document.getElementById('dest-autocomplete').style.display = 'none';
    }
});

// --- Destination Autocomplete (Local Database + Free Nominatim API) ---
const destInput = document.getElementById('dest-input');
const destAutocompleteBox = document.getElementById('dest-autocomplete');
let destTimeout = null;

destInput.addEventListener('focus', function() {
    this.closest('.form-group').style.zIndex = '9999';
});
destInput.addEventListener('blur', function() {
    setTimeout(() => { this.closest('.form-group').style.zIndex = ''; }, 200);
});

destInput.addEventListener('input', function() {
    clearTimeout(destTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        destAutocompleteBox.style.display = 'none';
        return;
    }

    destAutocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:var(--muted);font-size:.85rem"><i class="fas fa-spinner fa-spin"></i> Searching destinations...</div>';
    destAutocompleteBox.style.display = 'block';

    destTimeout = setTimeout(async () => {
        try {
            const queryLower = query.toLowerCase();
            
            // 1. Search local places
            let localMatches = [];
            if (typeof localPlaces !== 'undefined' && Array.isArray(localPlaces)) {
                localMatches = localPlaces.filter(p => {
                    const name = (p.name || '').toLowerCase();
                    const city = (p.city || '').toLowerCase();
                    const country = (p.country || '').toLowerCase();
                    
                    if (name.includes(queryLower) || city.includes(queryLower) || country.includes(queryLower)) {
                        return true;
                    }
                    
                    if (getSimilarity(name, queryLower) > 0.75 || getSimilarity(city, queryLower) > 0.75) {
                        return true;
                    }
                    
                    return false;
                }).map(p => ({
                    city: p.city || p.name,
                    country: p.country || 'India',
                    isLocal: true
                }));
            }

            // 2. Fetch from backend proxy
            let apiMatches = [];
            try {
                const response = await fetch(`/api/city-search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                apiMatches = data.map(place => {
                    const city = place.address.city || place.address.town || place.address.village || place.name;
                    const country = place.address.country || '';
                    return { city, country, isLocal: false };
                });
            } catch (apiErr) {
                console.error("Nominatim API failed, relying on local search", apiErr);
            }

            // Combine both lists, keeping local matches at the top and removing duplicates
            const combined = [...localMatches];
            apiMatches.forEach(apiItem => {
                const exists = combined.some(localItem => 
                    localItem.city.toLowerCase() === apiItem.city.toLowerCase()
                );
                if (!exists) {
                    combined.push(apiItem);
                }
            });

            if (combined.length === 0) {
                destAutocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:var(--muted);font-size:.85rem">No locations found.</div>';
                return;
            }

            destAutocompleteBox.innerHTML = '';
            combined.forEach(place => {
                const item = document.createElement('div');
                item.style.cssText = 'padding:.75rem 1rem;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);transition:background .2s;display:flex;align-items:center;justify-content:space-between;gap:.5rem';
                
                const badgeHtml = place.isLocal 
                    ? `<span style="font-size:0.7rem;background:rgba(108,99,255,0.15);color:#00f0ff;padding:2px 6px;border-radius:4px;font-weight:700">✨ Recommended</span>`
                    : '';

                item.innerHTML = `
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <i class="fas fa-globe" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i> 
                        <div>
                            <div style="font-weight:700;font-size:.9rem;color:#ffffff">${place.city}</div>
                            <div style="font-size:.75rem;color:rgba(255,255,255,0.5)">${place.country}</div>
                        </div>
                    </div>
                    ${badgeHtml}
                `;
                
                item.onmouseenter = () => item.style.background = 'rgba(255, 255, 255, 0.08)';
                item.onmouseleave = () => item.style.background = 'transparent';
                
                item.onclick = () => {
                    destInput.value = `${place.city}, ${place.country}`;
                    destAutocompleteBox.style.display = 'none';
                };
                
                destAutocompleteBox.appendChild(item);
            });
        } catch (error) {
            destAutocompleteBox.innerHTML = '<div style="padding:.75rem 1rem;color:#e74c3c;font-size:.85rem">Error loading locations.</div>';
        }
    }, 500);
});

function renderResult(plan, itinerary) {
    const formatCurrency = (amount, c) => {
        if (!c || c.code === 'INR') return `₹${amount.toLocaleString()}`;
        const foreign = amount / c.rate;
        const fAmt = foreign > 100 ? Math.round(foreign).toLocaleString() : foreign.toFixed(2);
        return `${c.symbol}${fAmt} <span style="font-size:0.8em;opacity:0.8">(₹${amount.toLocaleString()})</span>`;
    };

    document.getElementById('res-title').textContent = '📍 ' + plan.origin + ' ➔ ' + plan.destination + ' (' + plan.total_days + ' Days)';
    document.getElementById('res-meta').textContent = `${plan.start_date} to ${plan.end_date} • Algorithm: ${plan.algorithm}`;
    const badges = document.getElementById('res-badges');
    const fit = plan.budget_fit === 'within_budget';
    badges.innerHTML = `
        <span class="badge-pill ${fit?'badge-success':'badge-danger'}">${fit?'✅ Within Budget':'⚠️ Over Budget'}</span>
        <span class="badge-pill badge-warning">AI Score: ${plan.optimization_score}%</span>
        <span class="badge-pill badge-primary">Est. ${formatCurrency(plan.total_estimated_cost, plan.currency)}</span>
    `;

    // Financial Breakdown Section (Blurred for free users)
    const fin = plan.financial_summary;
    let finHtml = '';
    if (fin) {
        finHtml = `
            <div style="background:rgba(108,99,255,.05);border:1px solid rgba(108,99,255,.2);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
                <h3 style="font-size:1.1rem;font-weight:800;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem">
                    <i class="fas fa-wallet" style="color:var(--primary)"></i> Financial Breakdown <span style="font-size:0.75rem;color:#ffaa00;background:rgba(255,170,0,0.1);padding:2px 8px;border-radius:50px;margin-left:0.5rem;"><i class="fas fa-lock"></i> Premium</span>
                </h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;margin-bottom:1rem;filter:blur(5px);pointer-events:none;user-select:none;">
                    <div style="background:rgba(255,255,255,0.04);padding:1rem;border-radius:8px;border:1px solid var(--border)">
                        <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700">✈️ Flight (Round Trip)</div>
                        <div style="font-size:1.1rem;font-weight:800;color:var(--text)">${formatCurrency(fin.travel_flight, plan.currency)}</div>
                        <div style="font-size:.7rem;color:var(--muted)">Est. airfare from ${plan.origin.split(',')[0]}</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.04);padding:1rem;border-radius:8px;border:1px solid var(--border)">
                        <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700">🚆 Train (Round Trip)</div>
                        <div style="font-size:1.1rem;font-weight:800;color:var(--text)">${formatCurrency(fin.travel_train, plan.currency)}</div>
                        <div style="font-size:.7rem;color:var(--muted)">Est. rail fare (used for total)</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.04);padding:1rem;border-radius:8px;border:1px solid var(--border)">
                        <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700">🏨 Room Rents</div>
                        <div style="font-size:1.1rem;font-weight:800;color:var(--text)">${formatCurrency(fin.room_cost, plan.currency)}</div>
                        <div style="font-size:.7rem;color:var(--muted)">For ${plan.total_days - 1} nights</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.04);padding:1rem;border-radius:8px;border:1px solid var(--border)">
                        <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700">📸 Activities</div>
                        <div style="font-size:1.1rem;font-weight:800;color:var(--text)">${formatCurrency(fin.activity_cost, plan.currency)}</div>
                        <div style="font-size:.7rem;color:var(--muted)">Sightseeing & traditions</div>
                    </div>
                    ${fin.food_cost > 0 ? `
                    <div style="background:rgba(255,255,255,0.04);padding:1rem;border-radius:8px;border:1px solid var(--border)">
                        <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700">🍔 Food & Dining</div>
                        <div style="font-size:1.1rem;font-weight:800;color:var(--text)">${formatCurrency(fin.food_cost, plan.currency)}</div>
                        <div style="font-size:.7rem;color:var(--muted)">For ${plan.total_days} days</div>
                    </div>` : ''}
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:1rem;filter:blur(5px);pointer-events:none;user-select:none;">
                    ${fin.recommended_train ? `
                    <div style="background:rgba(255,255,255,0.04);padding:1rem 1.25rem;border-radius:8px;border-left:4px solid var(--secondary);display:flex;flex-direction:column;justify-content:center;border-top:1px solid var(--border);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
                        <div style="font-size:.85rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:.35rem"><i class="fas fa-train" style="color:var(--secondary)"></i> Train Journey Plan</div>
                        <div style="font-size:.75rem;color:var(--muted);margin-bottom:.5rem">Recommended total including buffer.</div>
                        <div style="font-size:1.2rem;font-weight:900;color:var(--secondary)">${formatCurrency(fin.recommended_train, plan.currency)}</div>
                    </div>` : ''}
                    ${fin.recommended_flight ? `
                    <div style="background:rgba(255,255,255,0.04);padding:1rem 1.25rem;border-radius:8px;border-left:4px solid var(--gold);display:flex;flex-direction:column;justify-content:center;border-top:1px solid var(--border);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
                        <div style="font-size:.85rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:.35rem"><i class="fas fa-plane" style="color:var(--gold)"></i> Flight Journey Plan</div>
                        <div style="font-size:.75rem;color:var(--muted);margin-bottom:.5rem">Recommended total including buffer.</div>
                        <div style="font-size:1.2rem;font-weight:900;color:var(--gold)">${formatCurrency(fin.recommended_flight, plan.currency)}</div>
                    </div>` : ''}
                </div>
    const container = document.getElementById('days-container');
    
    // Enforce paywall: Day 1 is visible, Day 2 and onwards are blurred/locked if NOT paid
    const daysHtml = plan.days.map((day, i) => {
        if (i === 0 || itinerary.is_paid) {
            return `
                <div class="day-card">
                    <div class="day-header" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'">
                        <span>${day.label}</span>
                        <span style="color:var(--secondary)">Est. ${formatCurrency(day.day_cost, plan.currency)} ↕</span>
                    </div>
                    <div class="day-slots">
                        <div style="margin-bottom:.75rem;font-size:.82rem;color:var(--gold)">${day.weather_tip}</div>
                        ${day.slots.map(s => `
                        <div class="slot-row">
                            <div class="slot-time">${s.time}</div>
                            <div>
                                <div style="font-weight:600">${s.activity}</div>
                                <div style="color:var(--muted);font-size:.8rem">${s.notes}</div>
                                <div style="font-size:.78rem;color:var(--secondary);margin-top:.2rem">Est. ${formatCurrency(s.est_cost, plan.currency)}</div>
                            </div>
                        </div>`).join('')}
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="day-card" style="border-radius:12px; overflow:hidden; background:rgba(255,255,255,0.01); border:1px dashed rgba(255,255,255,0.1); opacity:0.35; filter:blur(2px); pointer-events:none; user-select:none; margin-bottom:1rem; transition:all 0.3s; position:relative; cursor:pointer;" onclick="window.location.href='/itineraries/'+itinerary.id">
                    <div class="day-header" style="padding:.75rem 1.25rem; display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.1);">
                        <div style="display:flex; align-items:center; gap:1rem;">
                            <span style="font-weight:800; font-size:1rem; color:rgba(255,255,255,0.4);">${day.label} — Premium Only</span>
                        </div>
                        <div>
                            <i class="fas fa-lock" style="color:#ff7b00; font-size:1.1rem;"></i>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');

    // Beautiful Paywall Promo card at the bottom of the container
    let paywallPromoHtml = '';
    if (!itinerary.is_paid) {
        paywallPromoHtml = `
            <div class="card" style="margin-top:2rem; padding:2rem 1.5rem; border-radius:16px; background:linear-gradient(135deg, #0e1e38 0%, #050e21 100%); border:1px solid rgba(255,111,0,0.25); text-align:center; position:relative; overflow:hidden; box-shadow:0 15px 30px rgba(0,0,0,0.4); margin-bottom:1.5rem;">
                <div style="position:relative; z-index:1;">
                    <div style="width:45px; height:45px; margin:0 auto 1rem; background:rgba(255,111,0,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,111,0,0.2);">
                        <i class="fas fa-lock" style="color:#ff6f00; font-size:1.2rem;"></i>
                    </div>
                    <h4 style="font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:800; color:#fff; margin-bottom:0.5rem;">🔒 Premium Plan Locked (Day 2 to ${plan.total_days})</h4>
                    <p style="color:#b0c4de; max-width:600px; margin:0 auto 1rem; font-size:0.9rem; line-height:1.6;">
                        Unlock the complete daily schedule, timing slots, custom travel warnings, and full financial breakdowns. Pay a small one-time secure fee to unlock full access.
                    </p>
                </div>
            </div>
        `;
    }

    container.innerHTML = finHtml + daysHtml + paywallPromoHtml;

    // Direct the user to the itinerary detail page to trigger payment OR view it fully
    document.getElementById('view-btn').className = 'btn btn-primary';
    document.getElementById('view-btn').style.cssText = 'padding:1.1rem 3rem; font-size:1.1rem; border-radius:50px; background:linear-gradient(135deg, #ffca28, #ff6f00); box-shadow:0 8px 25px rgba(255,111,0,0.4); border:none; display:inline-flex; align-items:center; gap:0.5rem; color:#fff; font-weight:800; cursor:pointer;';
    
    if (itinerary.is_paid) {
        document.getElementById('view-btn').innerHTML = '<i class="fas fa-eye"></i> View Full Saved Itinerary';
        document.getElementById('download-btn').style.display = 'inline-flex';
    } else {
        document.getElementById('view-btn').innerHTML = '<i class="fas fa-unlock"></i> View & Unlock Full Itinerary (₹99)';
        document.getElementById('download-btn').style.display = 'none';
        
        // Add overlay blur for financials if not paid
        const overlay = document.createElement('div');
        overlay.style.cssText = "position:absolute;inset:0;background:rgba(6,7,19,0.3);backdrop-filter:blur(0.5px);display:flex;align-items:center;justify-content:center;z-index:2;cursor:pointer;";
        overlay.onclick = () => window.location.href = '/itineraries/' + itinerary.id;
        overlay.innerHTML = `<div style="background:#151538;border:1px solid rgba(255,111,0,0.3);box-shadow:0 10px 25px rgba(0,0,0,0.5);border-radius:50px;padding:0.75rem 1.5rem;display:flex;align-items:center;gap:0.5rem;color:#fff;font-weight:700;font-size:0.9rem;"><i class="fas fa-lock" style="color:#ffaa00;"></i> Pay ₹99 to Unlock AI Breakdown</div>`;
        const walletIcon = container.querySelector('.fa-wallet');
        if (walletIcon) {
            const finDiv = walletIcon.closest('div').parentElement;
            if (finDiv) finDiv.appendChild(overlay);
        }
    }
    
    document.getElementById('view-btn').href = itinerary.id ? `/itineraries/${itinerary.id}` : '#';

    document.getElementById('result-container').style.display = 'block';
    document.getElementById('result-container').scrollIntoView({behavior:'smooth'});
}
