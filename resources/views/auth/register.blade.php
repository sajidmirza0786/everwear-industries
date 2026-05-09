@extends('users.master')

@section('seo')
    <title>Create Account · Everwear Industries</title>
    <meta name="description" content="Create your Everwear Industries account to track orders, manage wishlists, and access exclusive member benefits.">
    <meta name="keywords" content="Everwear Industries register, create account, sign up, new account">
@endsection

@section('content')

<section data-testid="register-section">
    <div class="auth-wrap">

        {{-- Left Art Panel --}}
        <div class="auth-art" style="background-image:url({{ url('users/assets/images/about.jpg') }})">
            <div class="auth-art-quote">
                "Every award begins with a vision. Let us craft yours."
                <br>
                <small style="font-family:var(--font-body);font-style:normal;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;opacity:0.7;">
                    — Everwear Industries
                </small>
            </div>
        </div>

        {{-- Right Form Panel --}}
        <div class="auth-form">
            <div style="max-width:400px;margin:0 auto;width:100%;">

                <span class="section-eyebrow">Get started</span>
                <h1>Create account</h1>
                <p class="text-soft mb-4">
                    Already have an account?
                    <a href="{{ route('login') }}" style="color:var(--accent-2);text-decoration:underline;">
                        Sign in
                    </a>
                </p>

                {{-- Errors --}}
                @if ($errors->any())
                    <div style="background:#fef2f2;border-left:3px solid var(--danger);padding:12px 16px;margin-bottom:20px;font-size:14px;color:var(--danger);">
                        <i class="bi bi-exclamation-circle" style="margin-right:8px;"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate
                      data-testid="register-form">
                    @csrf

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label class="mb-2" for="name">Full Name</label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control"
                               placeholder="John Doe"
                               value="{{ old('name') }}"
                               autocomplete="name"
                               required autofocus
                               data-testid="register-name">
                        @error('name')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Mobile --}}
                    <div class="mb-3">
                        <label class="mb-2" for="mobile">Mobile Number</label>
                        <input type="tel"
                               id="mobile"
                               name="mobile"
                               class="form-control"
                               placeholder="10-digit mobile number"
                               value="{{ old('mobile') }}"
                               autocomplete="tel"
                               maxlength="10"
                               required
                               data-testid="register-mobile">
                        @error('mobile')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="mb-2" for="email">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="you@example.com"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               required
                               data-testid="register-email">
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
                                   placeholder="At least 8 characters"
                                   autocomplete="new-password"
                                   required
                                   style="padding-right:42px;"
                                   data-testid="register-password">
                            <button type="button" id="togglePw1"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--soft);cursor:pointer;padding:0;font-size:15px;"
                                    tabindex="-1">
                                <i class="bi bi-eye" id="togglePw1Icon"></i>
                            </button>
                        </div>

                        {{-- Password strength bar --}}
                        <div id="pwStrength" style="display:none;margin-top:8px;">
                            <div style="height:3px;border-radius:4px;background:var(--line);overflow:hidden;margin-bottom:4px;">
                                <div id="pwStrengthFill"
                                     style="height:100%;border-radius:4px;transition:width 0.3s,background 0.3s;width:0%;"></div>
                            </div>
                            <span id="pwStrengthLabel" style="font-size:11px;color:var(--soft);"></span>
                        </div>

                        @error('password')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="mb-2" for="password_confirmation">Confirm Password</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Re-enter your password"
                                   autocomplete="new-password"
                                   required
                                   style="padding-right:42px;"
                                   data-testid="register-password-confirm">
                            <button type="button" id="togglePw2"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--soft);cursor:pointer;padding:0;font-size:15px;"
                                    tabindex="-1">
                                <i class="bi bi-eye" id="togglePw2Icon"></i>
                            </button>
                        </div>
                        <div id="pwMatch" style="display:none;font-size:12px;margin-top:5px;"></div>
                        @error('password_confirmation')
                            <span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Terms --}}
                    <div class="mb-4" style="display:flex;align-items:flex-start;gap:8px;">
                        <input type="checkbox" name="terms" id="terms"
                               style="accent-color:var(--accent);margin-top:3px;flex-shrink:0;"
                               required>
                        <label for="terms"
                               style="text-transform:none;letter-spacing:0;color:var(--soft);font-weight:400;font-size:13px;cursor:pointer;line-height:1.5;">
                            I agree to the
                            <a href="#" style="color:var(--accent-2);">Terms of Service</a>
                            and
                            <a href="#" style="color:var(--accent-2);">Privacy Policy</a>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button class="btn btn-dark w-100" type="submit"
                            id="submitBtn" data-testid="register-submit">
                        <span id="btnSpinner"
                              style="display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:8px;flex-shrink:0;"></span>
                        <span id="btnText">Create account</span>
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
            Creating your account
        </p>
        <span style="font-size:0.75rem;color:var(--soft);">Setting up your workspace...</span>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
    // Password toggles
    function makeToggle(btnId, iconId, inputId) {
        document.getElementById(btnId).addEventListener('click', function () {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
    makeToggle('togglePw1', 'togglePw1Icon', 'password');
    makeToggle('togglePw2', 'togglePw2Icon', 'password_confirmation');

    // Password strength meter
    const pwInput    = document.getElementById('password');
    const pwStrength = document.getElementById('pwStrength');
    const pwFill     = document.getElementById('pwStrengthFill');
    const pwLabel    = document.getElementById('pwStrengthLabel');

    pwInput.addEventListener('input', function () {
        const val = pwInput.value;
        if (!val) { pwStrength.style.display = 'none'; return; }
        pwStrength.style.display = 'block';

        let score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { pct:'20%', color:'#a8412c', text:'Too weak'    },
            { pct:'40%', color:'#b08d57', text:'Weak'        },
            { pct:'65%', color:'#d4ac6e', text:'Fair'        },
            { pct:'85%', color:'#5a7a3a', text:'Strong'      },
            { pct:'100%',color:'#3a5c20', text:'Very strong' },
        ];
        const lvl = levels[Math.min(score, 4)];
        pwFill.style.width      = lvl.pct;
        pwFill.style.background = lvl.color;
        pwLabel.textContent     = lvl.text;
        pwLabel.style.color     = lvl.color;

        checkMatch();
    });

    // Password match
    const pwConfirm = document.getElementById('password_confirmation');
    const pwMatch   = document.getElementById('pwMatch');

    function checkMatch() {
        const val = pwConfirm.value;
        if (!val) { pwMatch.style.display = 'none'; return; }
        pwMatch.style.display = 'block';
        const match = val === pwInput.value;
        pwMatch.innerHTML = match
            ? '<i class="bi bi-check-circle-fill" style="color:var(--success);"></i> <span style="color:var(--success);">Passwords match</span>'
            : '<i class="bi bi-x-circle-fill" style="color:var(--danger);"></i> <span style="color:var(--danger);">Passwords do not match</span>';
        pwConfirm.style.borderColor = match ? 'var(--success)' : 'var(--danger)';
    }

    pwConfirm.addEventListener('input', checkMatch);

    // Mobile — numbers only
    document.getElementById('mobile').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    // Secure submit
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const name     = document.getElementById('name').value.trim();
        const mobile   = document.getElementById('mobile').value.trim();
        const email    = document.getElementById('email').value.trim();
        const password = pwInput.value;
        const confirm  = pwConfirm.value;
        const terms    = document.getElementById('terms').checked;

        if (!name || !mobile || !email || !password || !confirm || !terms) return;
        if (password !== confirm) return;

        const btn     = document.getElementById('submitBtn');
        const spinner = document.getElementById('btnSpinner');
        const text    = document.getElementById('btnText');
        const arrow   = document.getElementById('btnArrow');
        const overlay = document.getElementById('secureOverlay');

        btn.disabled          = true;
        spinner.style.display = 'inline-block';
        text.textContent      = 'Creating account...';
        arrow.style.display   = 'none';

        setTimeout(() => {
            overlay.style.display = 'flex';
        }, 300);
    });
</script>

@endsection