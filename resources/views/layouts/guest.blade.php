<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — Pedago Academy ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body style="margin:0;padding:0;">

<div class="erp-auth-page">

    {{-- Left branding panel --}}
    <div class="erp-auth-left">

        {{-- Logo --}}
        <div style="margin-bottom:52px;">
            <img src="{{ asset('build/assets/Logo/horizontal on white bg.png') }}"
                 alt="Pedago Academy"
                 style="height:56px;width:auto;filter:brightness(0) invert(1);">
        </div>

        {{-- Headline --}}
        <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:36px;font-weight:800;color:#fff;line-height:1.15;margin:0 0 16px;">
            Your Sales<br>
            <span style="color:#EA580C;">Supercharged.</span>
        </h1>
        <p style="font-size:16px;color:rgba(255,255,255,0.6);max-width:400px;line-height:1.7;margin:0 0 48px;">
            The Pedago Academy ERP — managing leads, enrollments, payments and teams from a single intelligent platform.
        </p>

        {{-- Feature pills --}}
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach([
                ['🎯', 'Smart Lead Distribution', 'Auto-assign leads with batch intelligence'],
                ['📋', 'End-to-End Enrollment', 'From lead to enrolled student, no gaps'],
                ['💳', 'Payment & Installment Tracking', 'Accounts verified in real-time'],
            ] as $f)
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">{{ $f[0] }}</div>
                <div>
                    <div style="font-weight:600;color:#fff;font-size:14px;">{{ $f[1] }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:1px;">{{ $f[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Decorative orbs --}}
        <div style="position:absolute;bottom:60px;left:64px;width:180px;height:180px;border-radius:50%;background:rgba(234,88,12,0.08);filter:blur(40px);pointer-events:none;"></div>
        <div style="position:absolute;top:80px;right:60px;width:220px;height:220px;border-radius:50%;background:rgba(99,102,241,0.1);filter:blur(60px);pointer-events:none;"></div>

    </div>

    {{-- Right form panel --}}
    <div class="erp-auth-right">
        @yield('auth-content')
    </div>

</div>

@stack('scripts')
</body>
</html>
