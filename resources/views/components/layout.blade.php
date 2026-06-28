<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'BookMe' }} — BookMe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ─── Design Tokens ──────────────────────────────────────── */
        :root {
            --bg:           #0d1117;
            --panel-1:      #12171f;
            --panel-2:      #161b24;
            --panel-3:      #1c2230;
            --border:       rgba(255,255,255,0.07);
            --border-soft:  rgba(255,255,255,0.07);
            --border-hover: rgba(255,255,255,0.14);
            --text-primary: #e8eaf0;
            --text-secondary:#9aa3b5;
            --text-tertiary: #60697c;
            --pink:         #f478c0;
            --pink-dim:     #c45899;
            --purple:       #a78bfa;
            --purple-dim:   #7c5cbf;
            --green:        #34d399;
            --amber:        #fbbf24;
            --red:          #f87171;
            --radius:       10px;
            --radius-lg:    16px;
            --nav-h:        60px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ─── Snow ───────────────────────────────────────────────── */
        #snow-canvas {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0;
        }
        .bg-glow {
            position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden;
        }
        .bg-glow::before {
            content: ''; position: absolute; top: -20%; left: -10%;
            width: 55vw; height: 55vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(164,120,255,0.08) 0%, transparent 70%);
        }
        .bg-glow::after {
            content: ''; position: absolute; bottom: -15%; right: -8%;
            width: 45vw; height: 45vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(244,120,192,0.07) 0%, transparent 70%);
        }

        /* ─── Nav ────────────────────────────────────────────────── */
        .bm-nav {
            position: sticky; top: 0; z-index: 100;
            height: var(--nav-h);
            background: rgba(13,17,23,0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px; gap: 20px;
        }
        .bm-nav-logo {
            display: flex; align-items: center; gap: 9px;
            text-decoration: none; flex-shrink: 0;
        }
        .bm-nav-logo-mark {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-radius: 7px; display: flex; align-items: center; justify-content: center;
        }
        .bm-nav-logo-text { font-size: 16px; font-weight: 700; color: var(--text-primary); }
        .bm-nav-logo-text span { color: var(--pink); }

        .bm-nav-links {
            display: flex; align-items: center; gap: 2px; margin-left: 8px;
        }
        .bm-nav-link {
            font-size: 13px; font-weight: 500; color: var(--text-secondary);
            text-decoration: none; padding: 6px 12px; border-radius: 7px;
            transition: background .15s, color .15s;
        }
        .bm-nav-link:hover, .bm-nav-link.active {
            background: var(--panel-2); color: var(--text-primary);
        }
        .bm-nav-link.active { color: var(--purple); }

        .bm-nav-spacer { flex: 1; }

        .bm-nav-user {
            display: flex; align-items: center; gap: 10px; position: relative;
        }
        .bm-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, var(--purple-dim), var(--pink-dim));
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff; cursor: pointer;
            flex-shrink: 0;
        }
        .bm-nav-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            background: var(--panel-2); border: 1px solid var(--border-hover);
            border-radius: var(--radius-lg); padding: 6px; min-width: 200px;
            display: none; z-index: 200; box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        }
        .bm-nav-dropdown.open { display: block; }
        .bm-nav-dropdown a, .bm-nav-dropdown button {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 9px 12px;
            font-size: 13px; color: var(--text-secondary);
            text-decoration: none; background: none; border: none;
            border-radius: 8px; cursor: pointer; font-family: inherit;
            transition: background .14s, color .14s;
        }
        .bm-nav-dropdown a:hover, .bm-nav-dropdown button:hover {
            background: var(--panel-3); color: var(--text-primary);
        }
        .bm-nav-dropdown .divider {
            height: 1px; background: var(--border); margin: 4px 0;
        }

        /* ─── Page shell ─────────────────────────────────────────── */
        .bm-page {
            position: relative; z-index: 1;
            max-width: 1120px; margin: 0 auto;
            padding: 28px 24px 60px;
        }

        /* ─── Buttons ────────────────────────────────────────────── */
        .bm-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 38px; padding: 0 18px;
            border: none; border-radius: var(--radius);
            font-size: 13.5px; font-weight: 600; font-family: inherit;
            cursor: pointer; text-decoration: none;
            transition: opacity .14s, box-shadow .18s;
        }
        .bm-btn.primary {
            background: linear-gradient(135deg, var(--purple-dim), var(--pink-dim));
            color: #fff;
        }
        .bm-btn.primary:hover { opacity: .88; box-shadow: 0 4px 16px rgba(167,139,250,.25); }
        .bm-btn.secondary {
            background: var(--panel-2); border: 1px solid var(--border-hover);
            color: var(--text-secondary);
        }
        .bm-btn.secondary:hover { color: var(--text-primary); }
        .bm-btn.full { width: 100%; }
        .bm-btn.sm { height: 32px; padding: 0 13px; font-size: 12.5px; }

        /* ─── Tab pills ──────────────────────────────────────────── */
        .bm-tab {
            display: inline-flex; align-items: center; height: 32px; padding: 0 14px;
            border-radius: 999px; font-size: 12.5px; font-weight: 500;
            color: var(--text-secondary); background: var(--panel-2);
            border: 1px solid var(--border); text-decoration: none;
            transition: all .15s;
        }
        .bm-tab:hover { border-color: var(--border-hover); color: var(--text-primary); }
        .bm-tab.active {
            background: rgba(167,139,250,.12); border-color: var(--purple-dim);
            color: var(--purple);
        }

        /* ─── Cards / form card ──────────────────────────────────── */
        .bm-card {
            background: var(--panel-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); overflow: hidden;
        }
        .bm-form-card {
            background: var(--panel-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 22px;
        }

        /* ─── Stat card ──────────────────────────────────────────── */
        .bm-stat-card {
            background: var(--panel-2); border: 1px solid var(--border);
            border-radius: var(--radius-lg);
        }

        /* ─── List rows ──────────────────────────────────────────── */
        .bm-list-row {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 12px 16px; border-bottom: 1px solid var(--border);
            text-decoration: none; color: inherit; transition: background .14s;
        }
        .bm-list-row:last-child { border-bottom: none; }
        .bm-list-row:hover { background: var(--panel-3); }
        .bm-row-info .name { font-size: 14px; font-weight: 500; color: var(--text-primary); }
        .bm-row-info .meta { font-size: 12.5px; color: var(--text-tertiary); margin-top: 2px; }

        /* ─── Badges ─────────────────────────────────────────────── */
        .bm-row-badge {
            font-size: 11.5px; font-weight: 600; padding: 4px 10px;
            border-radius: 999px; flex-shrink: 0;
        }
        .badge-green  { background: rgba(52,211,153,.12); color: #34d399; }
        .badge-amber  { background: rgba(251,191,36,.10);  color: #fbbf24; }
        .badge-red    { background: rgba(248,113,113,.10); color: #f87171; }
        .badge-gray   { background: var(--panel-3); color: var(--text-tertiary); }
        .badge-purple { background: rgba(167,139,250,.12); color: var(--purple); }

        /* ─── Empty state ────────────────────────────────────────── */
        .bm-empty {
            text-align: center; color: var(--text-tertiary); font-size: 13.5px;
            padding: 60px 20px;
        }
        .bm-empty .icon { margin-bottom: 14px; }

        /* ─── Input ──────────────────────────────────────────────── */
        .bm-input {
            width: 100%; height: 40px;
            background: rgba(255,255,255,.04); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 0 13px;
            color: var(--text-primary); font-size: 13.5px; font-family: inherit;
            outline: none; transition: border-color .18s, box-shadow .18s;
        }
        .bm-input::placeholder { color: var(--text-tertiary); }
        .bm-input:focus {
            border-color: var(--purple-dim);
            box-shadow: 0 0 0 3px rgba(167,139,250,.12);
        }
        select.bm-input { cursor: pointer; }
        textarea.bm-input { height: auto; padding: 10px 13px; resize: vertical; }

        /* ─── Footer ─────────────────────────────────────────────── */
        .bm-footer {
            position: relative; z-index: 1;
            border-top: 1px solid var(--border);
            padding: 20px 24px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 12px; color: var(--text-tertiary); flex-wrap: wrap; gap: 8px;
        }
        .bm-footer a { color: var(--text-tertiary); text-decoration: none; }
        .bm-footer a:hover { color: var(--text-secondary); }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    <canvas id="snow-canvas"></canvas>

    {{-- Navigation --}}
    <nav class="bm-nav">
        <a href="{{ url('/') }}" class="bm-nav-logo">
            <div class="bm-nav-logo-mark">
                <svg width="14" height="14" viewBox="0 0 18 18" fill="none">
                    <path d="M9 2L11.5 7H16.5L12.5 10.5L14 15.5L9 12.5L4 15.5L5.5 10.5L1.5 7H6.5L9 2Z" fill="white" opacity="0.9"/>
                </svg>
            </div>
            <span class="bm-nav-logo-text">Book<span>Me</span></span>
        </a>



        <div class="bm-nav-spacer"></div>

        @auth
            <div class="bm-nav-user" id="nav-user">
                <div class="bm-avatar" onclick="document.getElementById('nav-dd').classList.toggle('open')" title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="bm-nav-dropdown" id="nav-dd">
                    <div style="padding:8px 12px 6px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ auth()->user()->name }}</div>
                        <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:2px;">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="divider"></div>
                    <a href="{{ route('dashboard') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        My Bookings
                    </a>
                    <a href="{{ route('profile.edit') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('bookings.calendar') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Calendar
                    </a>
                    <a href="{{ route('bookings.history') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
                        History
                    </a>
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="bm-btn secondary sm">Log in</a>
            <a href="{{ route('register') }}" class="bm-btn primary sm">Sign up</a>
        @endauth
    </nav>

    <main class="bm-page">
        {{ $slot }}
    </main>

    <footer class="bm-footer">
        <span>© {{ date('Y') }} BookMe. All rights reserved.</span>
        <div style="display:flex;gap:14px;">
            <a href="{{ route('listings.index', ['type' => 'dorm']) }}">Dorms</a>
            <a href="{{ route('listings.index', ['type' => 'house']) }}">Houses</a>
            <a href="{{ route('listings.index', ['type' => 'hotel']) }}">Hotels</a>
            <a href="{{ route('listings.index', ['type' => 'restaurant']) }}">Restaurants</a>
        </div>
    </footer>

    <script>
    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const dd = document.getElementById('nav-dd');
        if (dd && !document.getElementById('nav-user').contains(e.target)) {
            dd.classList.remove('open');
        }
    });

    // Snow animation
    (function() {
        const canvas = document.getElementById('snow-canvas');
        const ctx = canvas.getContext('2d');
        let W, H, flakes = [];
        const N = 120;

        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        function mk() {
            return { x: Math.random()*W, y: Math.random()*H-H,
              r: Math.random()*2.6+0.5, s: Math.random()*0.65+0.22,
              d: (Math.random()-.5)*0.3, a: Math.random()*0.45+0.18,
              w: Math.random()*Math.PI*2, ws: (Math.random()*0.012+0.004)*(Math.random()<.5?1:-1) };
        }
        window.addEventListener('resize', resize);
        resize();
        flakes = Array.from({length:N}, mk);
        flakes.forEach(f => { f.y = Math.random()*H; });

        (function tick() {
            ctx.clearRect(0,0,W,H);
            for (const f of flakes) {
                f.w+=f.ws; f.x+=f.d+Math.sin(f.w)*.35; f.y+=f.s;
                if (f.y>H+5) { f.y=-6; f.x=Math.random()*W; }
                if (f.x<-5) f.x=W+5; if (f.x>W+5) f.x=-5;
                ctx.beginPath(); ctx.arc(f.x,f.y,f.r,0,Math.PI*2);
                ctx.fillStyle=`rgba(220,235,255,${f.a})`; ctx.fill();
            }
            requestAnimationFrame(tick);
        })();
    })();
    </script>
</body>
</html>