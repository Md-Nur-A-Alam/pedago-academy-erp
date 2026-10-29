<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Pedago Academy ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
<div class="erp-shell" id="erp-shell">

    {{-- SIDEBAR --}}
    <aside class="erp-sidebar" id="erp-sidebar">
        <div class="erp-sidebar-logo">
            <img src="{{ asset('build/assets/Logo/squrare on blue bg.png') }}" alt="Pedago Academy"
                 style="height:40px;width:auto;flex-shrink:0;filter:brightness(10);">
            <div class="erp-sidebar-logo-text">
                <div style="font-size:14px;font-weight:800;letter-spacing:0.02em;line-height:1.1;">PEDAGO</div>
                <div style="font-size:10px;font-weight:500;letter-spacing:0.18em;opacity:0.7;margin-top:1px;">ACADEMY ERP</div>
            </div>
        </div>
        <nav class="erp-sidebar-nav">
            <div class="erp-nav-section-label">Main</div>
            <a href="{{ url('/dashboard') }}" class="erp-nav-item {{ request()->is('dashboard') ? 'active' : '' }}" title="Dashboard">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="nav-label">Dashboard</span>
            </a>
            <div class="erp-nav-section-label">Sales &amp; Leads</div>
            <a href="{{ url('/leads') }}" class="erp-nav-item {{ request()->is('leads*') ? 'active' : '' }}" title="Lead Management">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">Lead Management</span>
                <span class="erp-nav-badge">12</span>
            </a>
            <a href="{{ url('/leads/distribution') }}" class="erp-nav-item {{ request()->is('leads/distribution*') ? 'active' : '' }}" title="Lead Distribution">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span class="nav-label">Lead Distribution</span>
            </a>
            <a href="{{ url('/follow-ups') }}" class="erp-nav-item {{ request()->is('follow-ups*') ? 'active' : '' }}" title="Follow-Ups">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="nav-label">Follow-Ups</span>
                <span class="erp-nav-badge">5</span>
            </a>
            <div class="erp-nav-section-label">Enrollment</div>
            <a href="{{ url('/enrollments') }}" class="erp-nav-item {{ request()->is('enrollments*') ? 'active' : '' }}" title="Enrollments">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="nav-label">Enrollments</span>
            </a>
            <a href="{{ url('/courses') }}" class="erp-nav-item {{ request()->is('courses*') ? 'active' : '' }}" title="Courses">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="nav-label">Courses</span>
            </a>
            <div class="erp-nav-section-label">Finance</div>
            <a href="{{ url('/payments') }}" class="erp-nav-item {{ request()->is('payments*') ? 'active' : '' }}" title="Payments">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="nav-label">Payments</span>
            </a>
            <a href="{{ url('/installments') }}" class="erp-nav-item {{ request()->is('installments*') ? 'active' : '' }}" title="Installments">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="nav-label">Installments</span>
            </a>
            <div class="erp-nav-section-label">Administration</div>
            <a href="{{ url('/teams') }}" class="erp-nav-item {{ request()->is('teams*') ? 'active' : '' }}" title="Teams">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="nav-label">Teams</span>
            </a>
            <a href="{{ url('/users') }}" class="erp-nav-item {{ request()->is('users*') ? 'active' : '' }}" title="Users">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="nav-label">Users</span>
            </a>
            <a href="{{ url('/reports') }}" class="erp-nav-item {{ request()->is('reports*') ? 'active' : '' }}" title="Reports">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="nav-label">Reports</span>
            </a>
            <a href="{{ url('/settings') }}" class="erp-nav-item {{ request()->is('settings*') ? 'active' : '' }}" title="Settings">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">Settings</span>
            </a>
        </nav>
        <div class="erp-sidebar-footer">
            <div class="erp-user-avatar" title="{{ auth()->user()?->name ?? 'User' }}">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
            </div>
            <div class="erp-user-info">
                <div class="erp-user-name">{{ auth()->user()?->name ?? 'Guest' }}</div>
                <div class="erp-user-role">Administrator</div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="erp-main" id="erp-main">
        <header class="erp-topbar">
            <button class="erp-topbar-toggle" id="sidebar-toggle" aria-label="Toggle Sidebar">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="erp-topbar-breadcrumb">
                <span>Pedago Academy</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:0.4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="current">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="erp-topbar-search">
                <svg class="search-icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="erp-global-search" placeholder="Search leads, students, courses&#8230;" autocomplete="off">
            </div>
            <div class="erp-topbar-actions">
                <button class="erp-topbar-btn" id="btn-notifications" title="Notifications">
                    <div class="erp-topbar-notif-dot"></div>
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <button class="erp-topbar-btn" id="btn-engine-toggle" title="Lead Distribution Engine"
                        style="width:auto;padding:0 12px;gap:7px;font-size:12px;font-weight:600;color:#1E3A8A;border-color:rgba(30,58,138,0.2);background:rgba(30,58,138,0.05);">
                    <span id="engine-dot" style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#059669;flex-shrink:0;"></span>
                    <span id="engine-label">Engine ON</span>
                </button>
                <button class="erp-topbar-btn" title="Profile"
                        style="border-radius:50%;background:linear-gradient(135deg,#1E3A8A,#172554);color:#fff;font-weight:700;font-size:13px;border:none;">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                </button>
            </div>
        </header>
        <main class="erp-page-content">
            @yield('content')
        </main>
    </div>

</div>
@stack('scripts')
<script>
(function(){
    var sidebar=document.getElementById('erp-sidebar'),main=document.getElementById('erp-main'),toggle=document.getElementById('sidebar-toggle');
    function applyState(c){sidebar.classList.toggle('collapsed',c);main.classList.toggle('sidebar-collapsed',c);}
    applyState(localStorage.getItem('pedago_sidebar_collapsed')==='1');
    toggle.addEventListener('click',function(){var c=!sidebar.classList.contains('collapsed');applyState(c);localStorage.setItem('pedago_sidebar_collapsed',c?'1':'0');});
    var eBtn=document.getElementById('btn-engine-toggle'),eDot=document.getElementById('engine-dot'),eLabel=document.getElementById('engine-label'),eOn=localStorage.getItem('pedago_engine_on')!=='0';
    function applyEngine(on){eDot.style.background=on?'#059669':'#dc2626';eLabel.textContent=on?'Engine ON':'Engine OFF';}
    applyEngine(eOn);
    eBtn.addEventListener('click',function(){eOn=!eOn;localStorage.setItem('pedago_engine_on',eOn?'1':'0');applyEngine(eOn);});
})();
</script>
</body>
</html>
