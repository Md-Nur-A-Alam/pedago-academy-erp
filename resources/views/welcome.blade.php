<!DOCTYPE html>
<html lang="{{ str_replace(''_'', ''-'', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedago Academy ERP — Sign In</title>
    <meta name="description" content="Pedago Academy Enterprise Resource Planning System. Manage leads, enrollments, payments and teams from a single intelligent platform.">

    @if (file_exists(public_path(''build/manifest.json'')) || file_exists(public_path(''hot'')))
        @vite([''resources/css/app.css'', ''resources/js/app.js''])
    @endif
</head>
<body style="margin:0;padding:0;font-family:Inter,system-ui,sans-serif;">

<div class="erp-auth-page">

    {{-- ═══ LEFT: Branding Panel ═══ --}}
    <div class="erp-auth-left" style="position:relative;">

        {{-- Logo --}}
        <div style="margin-bottom:48px;">
            <img src="{{ asset(''build/assets/Logo/horizontal on white bg.png'') }}"
                 alt="Pedago Academy"
                 style="height:52px;width:auto;filter:brightness(0) invert(1);">
        </div>

        {{-- Headline --}}
        <h1 style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:clamp(28px,3vw,42px);font-weight:800;color:#fff;line-height:1.15;margin:0 0 18px;letter-spacing:-0.01em;">
            Your Sales<br>
            <span style="color:#EA580C;">Supercharged.</span>
        </h1>
        <p style="font-size:15px;color:rgba(255,255,255,0.58);max-width:400px;line-height:1.8;margin:0 0 52px;">
            The Pedago Academy ERP — a single intelligent platform for managing leads, enrollments, payments and teams.
        </p>

        {{-- Feature list --}}
        <div style="display:flex;flex-direction:column;gap:18px;">
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;border:1px solid rgba(255,255,255,0.15);">🎯</div>
                <div>
                    <div style="font-weight:700;color:#fff;font-size:14px;font-family:''Plus Jakarta Sans'',sans-serif;">Smart Lead Distribution</div>
                    <div style="font-size:12.5px;color:rgba(255,255,255,0.48);margin-top:2px;">Auto-batch leads to sales executives with live engine control</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;border:1px solid rgba(255,255,255,0.15);">📋</div>
                <div>
                    <div style="font-weight:700;color:#fff;font-size:14px;font-family:''Plus Jakarta Sans'',sans-serif;">End-to-End Enrollment</div>
                    <div style="font-size:12.5px;color:rgba(255,255,255,0.48);margin-top:2px;">From first contact to enrolled student — no gaps in the pipeline</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;border:1px solid rgba(255,255,255,0.15);">💳</div>
                <div>
                    <div style="font-weight:700;color:#fff;font-size:14px;font-family:''Plus Jakarta Sans'',sans-serif;">Payment & Installment Tracking</div>
                    <div style="font-size:12.5px;color:rgba(255,255,255,0.48);margin-top:2px;">Finance team verification and real-time payment status</div>
                </div>
            </div>
        </div>

        {{-- Stats row --}}
        <div style="display:flex;gap:32px;margin-top:56px;">
            <div>
                <div style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:28px;font-weight:800;color:#fff;line-height:1;">2.4k+</div>
                <div style="font-size:11.5px;color:rgba(255,255,255,0.45);margin-top:3px;text-transform:uppercase;letter-spacing:0.06em;">Active Leads</div>
            </div>
            <div style="width:1px;background:rgba(255,255,255,0.12);"></div>
            <div>
                <div style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:28px;font-weight:800;color:#fff;line-height:1;">98%</div>
                <div style="font-size:11.5px;color:rgba(255,255,255,0.45);margin-top:3px;text-transform:uppercase;letter-spacing:0.06em;">Distribution Rate</div>
            </div>
            <div style="width:1px;background:rgba(255,255,255,0.12);"></div>
            <div>
                <div style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:28px;font-weight:800;color:#fff;line-height:1;">15+</div>
                <div style="font-size:11.5px;color:rgba(255,255,255,0.45);margin-top:3px;text-transform:uppercase;letter-spacing:0.06em;">Sales Executives</div>
            </div>
        </div>

        {{-- Decorative elements --}}
        <div style="position:absolute;bottom:40px;left:0;width:300px;height:300px;border-radius:50%;background:rgba(234,88,12,0.07);filter:blur(60px);pointer-events:none;"></div>
        <div style="position:absolute;top:60px;right:0;width:260px;height:260px;border-radius:50%;background:rgba(99,102,241,0.1);filter:blur(70px);pointer-events:none;"></div>
    </div>

    {{-- ═══ RIGHT: Auth Panel ═══ --}}
    <div class="erp-auth-right">

        {{-- Square logo --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:36px;">
            <img src="{{ asset(''build/assets/Logo/squrare on blue bg.png'') }}"
                 alt="Pedago"
                 style="height:44px;width:auto;">
            <div>
                <div style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:16px;font-weight:800;color:#1E3A8A;letter-spacing:0.01em;">PEDAGO</div>
                <div style="font-size:10.5px;color:#64748B;letter-spacing:0.14em;text-transform:uppercase;margin-top:1px;">Academy ERP</div>
            </div>
        </div>

        {{-- Welcome text --}}
        <h2 style="font-family:''Plus Jakarta Sans'',sans-serif;font-size:26px;font-weight:800;color:#0f172a;margin:0 0 6px;letter-spacing:-0.01em;">Welcome back</h2>
        <p style="font-size:14px;color:#64748B;margin:0 0 32px;">Sign in to your Pedago Academy account</p>

        {{-- Login form --}}
        @if (Route::has(''login''))
        <form method="POST" action="{{ route(''login'') }}" id="login-form">
            @csrf
            <div style="margin-bottom:18px;">
                <label class="erp-label" for="email">Email Address</label>
                <input class="erp-input" type="email" id="email" name="email"
                       value="{{ old(''email'') }}"
                       placeholder="you@pedagoacademy.com"
                       required autofocus autocomplete="username">
                @error(''email'')
                    <div style="color:#dc2626;font-size:12px;margin-top:5px;">{{ $message }}</div>
                @enderror
            </div>
            <div style="margin-bottom:10px;">
                <label class="erp-label" for="password">Password</label>
                <div style="position:relative;">
                    <input class="erp-input" type="password" id="password" name="password"
                           placeholder="Enter your password"
                           required autocomplete="current-password"
                           style="padding-right:44px;">
                    <button type="button" id="toggle-password"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;align-items:center;"
                            onclick="var i=document.getElementById(''password'');i.type=i.type===''password''?''text'':''password'';">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error(''password'')
                    <div style="color:#dc2626;font-size:12px;margin-top:5px;">{{ $message }}</div>
                @enderror
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#475569;font-weight:500;">
                    <input type="checkbox" name="remember" id="remember_me"
                           style="width:16px;height:16px;border-radius:4px;accent-color:#1E3A8A;">
                    Remember me
                </label>
                @if (Route::has(''password.request''))
                    <a href="{{ route(''password.request'') }}"
                       style="font-size:13px;color:#1E3A8A;font-weight:600;text-decoration:none;">
                        Forgot password?
                    </a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:15px;border-radius:11px;" id="signin-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign In to ERP
            </button>
        </form>
        @else
        <div style="text-align:center;padding:32px;background:#f8fafc;border-radius:12px;border:1px dashed #e2e8f0;">
            <div style="font-size:32px;margin-bottom:12px;">🚀</div>
            <div style="font-weight:600;color:#1e293b;margin-bottom:6px;">ERP System Ready</div>
            <div style="font-size:13px;color:#64748B;">Authentication routes are not yet configured.</div>
        </div>
        @endif

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;margin:28px 0;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:12px;color:#94a3b8;font-weight:500;">PEDAGO ACADEMY ERP</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>

        {{-- Module quick links --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            @foreach([
                [''🎯'', ''Leads'', ''leads''],
                [''📋'', ''Enrollment'', ''enrollments''],
                [''💳'', ''Finance'', ''payments''],
                [''📊'', ''Reports'', ''reports''],
            ] as $m)
            <div style="display:flex;align-items:center;gap:10px;padding:12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:default;transition:border-color 0.15s;">
                <span style="font-size:16px;">{{ $m[0] }}</span>
                <span style="font-size:13px;font-weight:600;color:#334155;">{{ $m[1] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div style="margin-top:32px;text-align:center;font-size:12px;color:#94a3b8;">
            &copy; {{ date(''Y'') }} Pedago Academy. All rights reserved.<br>
            <span style="color:#cbd5e1;">v1.0 &bull; Laravel {{ app()->version() }}</span>
        </div>

    </div>

</div>

<style>
@import url(''https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap'');
.erp-auth-page{min-height:100vh;display:flex;background:linear-gradient(150deg,#0f172a 0%,#1E3A8A 55%,#172554 100%);position:relative;overflow:hidden;}
.erp-auth-page::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 70% 40%,rgba(99,102,241,0.15) 0%,transparent 70%),radial-gradient(ellipse 50% 50% at 20% 80%,rgba(234,88,12,0.1) 0%,transparent 60%);pointer-events:none;}
.erp-auth-left{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 64px;position:relative;z-index:1;}
.erp-auth-right{width:480px;min-height:100vh;background:#fff;display:flex;flex-direction:column;justify-content:center;padding:52px 48px;position:relative;z-index:1;box-shadow:-20px 0 60px rgba(0,0,0,0.2);}
.erp-label{display:block;font-size:12.5px;font-weight:600;color:#334155;margin-bottom:6px;letter-spacing:0.01em;}
.erp-input{width:100%;height:42px;padding:0 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:Inter,system-ui,sans-serif;color:#1e293b;background:#fff;outline:none;transition:all 0.15s ease;box-sizing:border-box;}
.erp-input:focus{border-color:#1E3A8A;box-shadow:0 0 0 3px rgba(30,58,138,0.1);}
.erp-input::placeholder{color:#94a3b8;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;font-family:Inter,sans-serif;cursor:pointer;border:none;transition:all 0.15s ease;text-decoration:none;line-height:1;}
.btn-primary{background:#1E3A8A;color:#fff;}
.btn-primary:hover{background:#172554;box-shadow:0 4px 16px rgba(30,58,138,0.4);}
@media(max-width:900px){.erp-auth-left{display:none;}.erp-auth-right{width:100%;}}
</style>

</body>
</html>
