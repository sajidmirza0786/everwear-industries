@extends('users.master')

@section('seo')
    <title>Sign In · Everwear Industries</title>
    <meta name="description" content="Sign in to your Everwear Industries account to track orders, manage wishlists, and access exclusive member benefits.">
    <meta name="keywords" content="Everwear Industries login, sign in, account access, member login">
@endsection

@section('content')

<section data-testid="login-section">
    <div class="auth-wrap">

        {{-- Left Art Panel --}}
        <div class="auth-art" style="background-image:url({{ url('users/assets/images/about.jpg') }})">
            <div class="auth-art-quote">
                "A trophy is a story the hand can hold."
                <br>
                <small style="font-family:var(--font-body);font-style:normal;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;opacity:0.7;">
                    — Thiru K., Founder
                </small>
            </div>
        </div>

        {{-- Right Form Panel --}}
        <div class="auth-form">
            <div style="max-width:400px;margin:0 auto;width:100%;">

                <span class="section-eyebrow">Welcome back</span>
                <h1>Sign in</h1>
                <p class="text-soft mb-4">
                    New to Everwear?
                    <a href="{{ route('register') }}" style="color:var(--accent-2);text-decoration:underline;">
                        Create an account
                    </a>
                </p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div style="background:var(--surface);border-left:3px solid var(--accent);padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--ink);">
                        <i class="bi bi-check-circle" style="color:var(--accent);margin-right:8px;"></i>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Errors --}}
                @if ($errors->any())
                    <div style="background:#fef2f2;border-left:3px solid var(--danger);padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--danger);">
                        <i class="bi bi-exclamation-circle" style="margin-right:8px;"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate
                      data-testid="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="mb-2" for="email">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="you@example.com"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               required autofocus
                               data-testid="login-email">
                        @error('email')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="mb-2" for="password">Password</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="••••••••"
                                   autocomplete="current-password"
                                   required
                                   style="padding-right:42px;"
                                   data-testid="login-password">
                            <button type="button" id="togglePw"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--soft);cursor:pointer;padding:0;font-size:15px;"
                                    tabindex="-1" aria-label="Show password">
                                <i class="bi bi-eye" id="togglePwIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="d-flex justify-content-between mb-4" style="font-size:13px;">
                        <label style="text-transform:none;letter-spacing:0;color:var(--ink);font-weight:400;display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="checkbox" name="remember" id="remember"
                                   style="accent-color:var(--accent);"
                                   {{ old('remember') ? 'checked' : '' }}>
                            Remember me
                        </label>
                        {{-- @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color:var(--accent-2);">
                                Forgot password?
                            </a>
                        @endif --}}
                    </div>

                    {{-- Submit --}}
                    <button class="btn btn-dark w-100" type="submit"
                            id="submitBtn" data-testid="login-submit">
                        <span class="btn-spinner" id="btnSpinner"
                              style="display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:8px;flex-shrink:0;"></span>
                        <span id="btnText">Sign in</span>
                        <i class="bi bi-arrow-right ms-2" id="btnArrow"></i>
                    </button>

                </form>

                {{-- Secure note --}}
                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:20px;font-size:11px;color:var(--soft-2);">
                    <i class="bi bi-shield-lock" style="color:var(--accent);font-size:13px;"></i>
                    Secured · Your data is encrypted
                </div>

            </div>
        </div>

    </div>
</section>

{{-- Secure overlay --}}
<div id="secureOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(20,16,12,0.55);backdrop-filter:blur(4px);z-index:999;align-items:center;justify-content:center;">
    <div style="background:var(--bg);padding:2rem 2.5rem;text-align:center;min-width:220px;border:1px solid var(--line);">
        <div style="width:44px;height:44px;border:3px solid var(--line);border-top-color:var(--accent);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 1rem;"></div>
        <p style="font-size:0.88rem;color:var(--ink);font-weight:500;margin:0 0 4px;font-family:var(--font-display);">
            Verifying credentials
        </p>
        <span style="font-size:0.75rem;color:var(--soft);">Establishing secure session...</span>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
    // Password toggle
    document.getElementById('togglePw').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('togglePwIcon');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    // Secure submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const email    = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        if (!email || !password) return;

        const btn     = document.getElementById('submitBtn');
        const spinner = document.getElementById('btnSpinner');
        const text    = document.getElementById('btnText');
        const arrow   = document.getElementById('btnArrow');
        const overlay = document.getElementById('secureOverlay');

        btn.disabled          = true;
        spinner.style.display = 'inline-block';
        text.textContent      = 'Signing in...';
        arrow.style.display   = 'none';

        setTimeout(() => {
            overlay.style.display = 'flex';
        }, 300);
    });
</script>

@endsection