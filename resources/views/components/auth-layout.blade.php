<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'BookMe' }} — BookMe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ─── Tokens ─────────────────────────────────────────────── */
        :root {
            --bm-bg:          #0d1117;
            --bm-panel:       #161b24;
            --bm-border:      rgba(255,255,255,0.08);
            --bm-border-hover:rgba(255,255,255,0.16);
            --bm-text:        #e8eaf0;
            --bm-muted:       #7c8494;
            --bm-placeholder: #4a5060;
            --bm-pink:        #f478c0;
            --bm-pink-dim:    #c45899;
            --bm-purple:      #a78bfa;
            --bm-purple-dim:  #7c5cbf;
            --bm-input-bg:    rgba(255,255,255,0.04);
            --bm-input-hover: rgba(255,255,255,0.07);
            --bm-radius:      10px;
            --bm-radius-lg:   16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bm-bg);
            color: var(--bm-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* ─── Snow Canvas ────────────────────────────────────────── */
        #snow-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* ─── Background glow blobs ──────────────────────────────── */
        .bm-bg-glow {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bm-bg-glow::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(164,120,255,0.10) 0%, transparent 70%);
            border-radius: 50%;
        }
        .bm-bg-glow::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(244,120,192,0.09) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* ─── Wrapper ────────────────────────────────────────────── */
        .bm-auth-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px 16px 40px;
        }

        /* ─── Logo ───────────────────────────────────────────────── */
        .bm-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            margin-bottom: 28px;
            text-decoration: none;
        }
        .bm-logo-mark {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--bm-purple), var(--bm-pink));
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bm-logo-mark svg { display: block; }
        .bm-logo-text {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.4px;
            color: var(--bm-text);
        }
        .bm-logo-text span { color: var(--bm-pink); }

        /* ─── Card ───────────────────────────────────────────────── */
        .bm-card {
            background: var(--bm-panel);
            border: 1px solid var(--bm-border);
            border-radius: var(--bm-radius-lg);
            padding: 32px 32px 28px;
            backdrop-filter: blur(12px);
        }

        /* ─── Headings ───────────────────────────────────────────── */
        .bm-card h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.4px;
            color: var(--bm-text);
            margin-bottom: 6px;
        }
        .bm-card .sub {
            font-size: 13.5px;
            color: var(--bm-muted);
            line-height: 1.55;
            margin-bottom: 24px;
        }

        /* ─── Form Fields ────────────────────────────────────────── */
        .bm-field {
            margin-bottom: 16px;
        }
        .bm-field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #b0b8cc;
            margin-bottom: 6px;
        }
        .bm-field label .hint {
            font-weight: 400;
            color: var(--bm-placeholder);
        }
        .bm-field input[type="text"],
        .bm-field input[type="email"],
        .bm-field input[type="password"],
        .bm-field input[type="tel"] {
            width: 100%;
            height: 40px;
            background: var(--bm-input-bg);
            border: 1px solid var(--bm-border);
            border-radius: var(--bm-radius);
            padding: 0 13px;
            color: var(--bm-text);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
        }
        .bm-field input::placeholder { color: var(--bm-placeholder); }
        .bm-field input:hover {
            background: var(--bm-input-hover);
            border-color: var(--bm-border-hover);
        }
        .bm-field input:focus {
            background: var(--bm-input-hover);
            border-color: var(--bm-purple-dim);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.12);
        }

        /* Error states */
        .bm-field input.has-error { border-color: #f87171; }
        .bm-field .error {
            font-size: 12px;
            color: #f87171;
            margin-top: 5px;
        }

        /* ─── Role Picker ────────────────────────────────────────── */
        .bm-role-pick {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .bm-role-pick label {
            position: relative;
            margin: 0;
            cursor: pointer;
        }
        .bm-role-pick label input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0; height: 0;
        }
        .bm-role-pick label span {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            border: 1px solid var(--bm-border);
            border-radius: var(--bm-radius);
            background: var(--bm-input-bg);
            font-size: 13px;
            font-weight: 500;
            color: var(--bm-muted);
            transition: all 0.17s;
        }
        .bm-role-pick label input:checked + span {
            border-color: var(--bm-purple-dim);
            background: rgba(167,139,250,0.10);
            color: var(--bm-purple);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.10);
        }
        .bm-role-pick label:hover span {
            border-color: var(--bm-border-hover);
            color: var(--bm-text);
        }

        /* ─── Checkbox row ───────────────────────────────────────── */
        .bm-check-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .bm-check-row input[type="checkbox"] {
            width: 15px !important;
            height: 15px !important;
            accent-color: var(--bm-purple);
            cursor: pointer;
        }
        .bm-check-row label {
            font-size: 13px;
            font-weight: 400;
            color: var(--bm-muted);
            margin: 0;
            cursor: pointer;
        }

        /* ─── Forgot link ────────────────────────────────────────── */
        .bm-forgot {
            font-size: 12px;
            color: var(--bm-pink);
            text-decoration: none;
            font-weight: 600;
        }
        .bm-forgot:hover { text-decoration: underline; }

        /* ─── Divider ────────────────────────────────────────────── */
        .bm-divider {
            height: 1px;
            background: var(--bm-border);
            margin: 20px 0;
        }

        /* ─── Buttons ────────────────────────────────────────────── */
        .bm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            padding: 0 18px;
            border: none;
            border-radius: var(--bm-radius);
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.14s, opacity 0.14s, box-shadow 0.18s;
        }
        .bm-btn:active { transform: scale(0.98); }
        .bm-btn.full { width: 100%; }

        .bm-btn.primary {
            background: linear-gradient(135deg, var(--bm-purple-dim), var(--bm-pink-dim));
            color: #fff;
        }
        .bm-btn.primary:hover {
            box-shadow: 0 4px 18px rgba(167,139,250,0.30);
            opacity: 0.93;
        }

        .bm-btn.secondary {
            background: var(--bm-input-bg);
            border: 1px solid var(--bm-border);
            color: var(--bm-muted);
        }
        .bm-btn.secondary:hover {
            border-color: var(--bm-border-hover);
            color: var(--bm-text);
        }

        /* ─── Switch line ────────────────────────────────────────── */
        .switch {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: var(--bm-muted);
        }
        .switch a {
            color: var(--bm-pink);
            font-weight: 600;
            text-decoration: none;
        }
        .switch a:hover { text-decoration: underline; }

        /* ─── Alert ──────────────────────────────────────────────── */
        .bm-alert.success {
            background: rgba(52,199,89,0.10);
            border: 1px solid rgba(52,199,89,0.25);
            border-radius: var(--bm-radius);
            padding: 10px 13px;
            font-size: 13px;
            color: #4ade80;
        }

        /* ─── Icon circle (verify email) ─────────────────────────── */
        .bm-auth-icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        /* ─── Responsive ─────────────────────────────────────────── */
        @media (max-width: 460px) {
            .bm-card { padding: 24px 20px 22px; border-radius: 14px; }
        }
    </style>
</head>
<body>
    <div class="bm-bg-glow"></div>
    <canvas id="snow-canvas"></canvas>

    <div class="bm-auth-wrap">
        <a href="{{ url('/') }}" class="bm-logo">
            <div class="bm-logo-mark">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M9 2L11.5 7H16.5L12.5 10.5L14 15.5L9 12.5L4 15.5L5.5 10.5L1.5 7H6.5L9 2Z" fill="white" opacity="0.9"/>
                </svg>
            </div>
            <span class="bm-logo-text">Book<span>Me</span></span>
        </a>

        <div class="bm-card">
            {{ $slot }}
        </div>
    </div>

    <script>
    (function () {
        const canvas = document.getElementById('snow-canvas');
        const ctx    = canvas.getContext('2d');
        let W, H, flakes = [];

        const FLAKE_COUNT = 130;

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }

        function randomFlake() {
            return {
                x:     Math.random() * W,
                y:     Math.random() * H - H,
                r:     Math.random() * 2.8 + 0.6,
                speed: Math.random() * 0.7 + 0.25,
                drift: (Math.random() - 0.5) * 0.35,
                alpha: Math.random() * 0.55 + 0.25,
                wobble: Math.random() * Math.PI * 2,
                wobbleSpeed: (Math.random() * 0.015 + 0.005) * (Math.random() < 0.5 ? 1 : -1),
            };
        }

        function init() {
            resize();
            flakes = Array.from({ length: FLAKE_COUNT }, randomFlake);
            flakes.forEach(f => { f.y = Math.random() * H; });
        }

        function draw() {
            ctx.clearRect(0, 0, W, H);
            for (const f of flakes) {
                ctx.beginPath();
                ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(230,240,255,${f.alpha})`;
                ctx.fill();
            }
        }

        function update() {
            for (const f of flakes) {
                f.wobble += f.wobbleSpeed;
                f.x += f.drift + Math.sin(f.wobble) * 0.4;
                f.y += f.speed;
                if (f.y > H + 5) {
                    f.y = -6;
                    f.x = Math.random() * W;
                }
                if (f.x < -5)  f.x = W + 5;
                if (f.x > W+5) f.x = -5;
            }
        }

        function loop() {
            update();
            draw();
            requestAnimationFrame(loop);
        }

        window.addEventListener('resize', () => { resize(); });
        init();
        loop();
    })();
    </script>
</body>
</html>