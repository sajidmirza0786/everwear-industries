@extends('users.master')

@section('seo')
    <title>Contact Us · Everwear Industries</title>
    <meta name="description"
        content="Get in touch with Everwear Industries for custom award designs, bulk orders, corporate gifting, and general inquiries. Our team responds within 24 hours, Monday to Saturday.">
    <meta name="keywords"
        content="contact Everwear Industries, get in touch, custom award inquiry, bulk order inquiry, corporate gifting contact, customer support, Everwear contact details">
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="border-bottom py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.78rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('welcome') }}" class="text-decoration-none text-muted">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">
                        Contact Us
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Info Strip --}}
    <div class="info-strip">
        <div class="container px-0">
            <div class="row g-0">
                <div class="col-md-4 info-strip-item">
                    <i class="bi bi-clock"></i>
                    Mon – Sat &nbsp;|&nbsp; 11:00 AM – 7:00 PM
                </div>
                <div class="col-md-4 info-strip-item">
                    <i class="bi bi-reply-fill"></i>
                    Response Within 24 Hours
                </div>
                <div class="col-md-4 info-strip-item">
                    <i class="bi bi-whatsapp"></i>
                    WhatsApp Support Available
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="section">
        <div class="container">
            <div class="row g-5">

                {{-- Left — Contact Form --}}
                <div class="col-lg-7">
                    <div style="background:var(--bg);border:1px solid var(--line);padding:40px;">

                        <span class="section-eyebrow">Send a Message</span>
                        <h2 style="font-size:2.2rem;margin-bottom:8px;">Get In Touch</h2>
                        <p style="color:var(--soft);font-size:14px;margin-bottom:32px;">
                            Fill out the form below and our team will respond as soon as possible.
                        </p>

                        {{-- Validation error summary --}}
                        @if ($errors->any())
                            <div
                                style="background:var(--surface);border-left:3px solid #c0392b;padding:14px 18px;margin-bottom:24px;font-size:14px;color:var(--ink);">
                                <div style="font-weight:600;margin-bottom:8px;color:#c0392b;">
                                    <i class="bi bi-exclamation-triangle" style="margin-right:6px;"></i>
                                    Please fix the following errors:
                                </div>
                                <ul style="margin:0;padding-left:18px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div
                                style="background:var(--surface);border-left:3px solid var(--accent);padding:14px 18px;margin-bottom:24px;font-size:14px;color:var(--ink);">
                                <i class="bi bi-check-circle" style="color:var(--accent);margin-right:8px;"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div
                                style="background:var(--surface);border-left:3px solid var(--accent);padding:14px 18px;margin-bottom:24px;font-size:14px;color:var(--ink);">
                                <i class="bi bi-check-circle" style="color:var(--accent);margin-right:8px;"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('storeEnquiry') }}">
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control mt-1"
                                        placeholder="John Doe" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span style="font-size:12px;color:var(--danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="mobile">Mobile Number</label>
                                    <input type="tel" id="mobile" name="mobile" class="form-control mt-1"
                                        placeholder="+91 00000 00000" value="{{ old('mobile') }}">
                                    @error('mobile')
                                        <span style="font-size:12px;color:var(--danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control mt-1"
                                    placeholder="you@example.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <span style="font-size:12px;color:var(--danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="subject">Subject</label>
                                <input type="subject" id="subject" name="subject" class="form-control mt-1"
                                    placeholder="subject" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <span style="font-size:12px;color:var(--danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" class="form-control mt-1"
                                    placeholder="Tell us how we can help you…" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span style="font-size:12px;color:var(--danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100"
                                style="letter-spacing:0.12em;text-transform:uppercase;font-size:13px;">
                                Send Message &nbsp;<i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right — Contact Details --}}
                <div class="col-lg-5 d-flex flex-column gap-4">

                    {{-- We're Here to Help --}}
                    <div style="background:var(--ink);color:var(--bg);padding:36px;">
                        <span class="section-eyebrow" style="color:var(--accent-soft);">Everwear Industries</span>
                        <h3 style="color:var(--bg);font-size:1.9rem;margin-bottom:14px;">
                            We're Here <em style="color:var(--accent-soft);">To Help</em>
                        </h3>
                        <p style="color:rgba(241,234,217,0.72);font-size:14px;line-height:1.7;margin:0;">
                            Whether you need bespoke award designs, curated recommendations, or
                            assistance with large-scale corporate requirements — our specialists
                            are here to serve with precision and discretion.
                        </p>
                    </div>

                    {{-- Contact Details --}}
                    <div style="background:var(--bg);border:1px solid var(--line);padding:32px;">
                        <span class="section-eyebrow">Contact Details</span>

                        <div style="display:flex;flex-direction:column;gap:20px;margin-top:16px;">

                            <div style="display:flex;align-items:flex-start;gap:14px;">
                                <span
                                    style="width:40px;height:40px;background:var(--surface);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-envelope" style="color:var(--accent);font-size:16px;"></i>
                                </span>
                                <div>
                                    <div
                                        style="font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--soft);margin-bottom:4px;">
                                        Email Address</div>
                                    <a href="mailto:{{ settings()->email }}"
                                        style="font-size:14px;color:var(--ink);font-weight:500;">
                                        {{ settings()->email }}
                                    </a>
                                    <br>
                                    <a href="mailto:{{ settings()->alt_email }}"
                                        style="font-size:14px;color:var(--ink);font-weight:500;">
                                        {{ settings()->alt_email ?? '' }}
                                    </a>
                                    <br>
                                    <a href="mailto:order@everwearindustries.com"
                                        style="font-size:14px;color:var(--ink);font-weight:500;">
                                        order@everwearindustries.com
                                    </a>
                                </div>
                            </div>

                            <div style="display:flex;align-items:flex-start;gap:14px;">
                                <span
                                    style="width:40px;height:40px;background:var(--surface);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-telephone" style="color:var(--accent);font-size:16px;"></i>
                                </span>
                                <div>
                                    <div
                                        style="font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--soft);margin-bottom:4px;">
                                        Phone / WhatsApp</div>
                                    <a href="tel:{{ settings()->mobile }}" style="font-size:14px;color:var(--ink);font-weight:500;">
                                        {{ settings()->mobile }} 
                                    </a>
                                    <br>
                                    <a href="tel:{{ settings()->alt_mobile?? '' }}" style="font-size:14px;color:var(--ink);font-weight:500;">
                                        {{ settings()->alt_mobile?? '' }} 
                                    </a>
                                </div>
                            </div>

                            <div style="display:flex;align-items:flex-start;gap:14px;">
                                <span
                                    style="width:40px;height:40px;background:var(--surface);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-geo-alt" style="color:var(--accent);font-size:16px;"></i>
                                </span>
                                <div>
                                    <div
                                        style="font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--soft);margin-bottom:4px;">
                                        Office Address</div>
                                    <span style="font-size:14px;color:var(--ink);font-weight:500;line-height:1.5;">
                                        {{ settings()->address }}
                                    </span>
                                </div>
                            </div>

                            <div style="display:flex;align-items:flex-start;gap:14px;">
                                <span
                                    style="width:40px;height:40px;background:var(--surface);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-clock" style="color:var(--accent);font-size:16px;"></i>
                                </span>
                                <div>
                                    <div
                                        style="font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--soft);margin-bottom:4px;">
                                        Business Hours</div>
                                    <span style="font-size:14px;color:var(--ink);font-weight:500;">
                                        Monday – Saturday, 11:00 AM – 6:00 PM
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <style>
        /* ============================================================
                       PAGE HERO FIX — force light text on dark bg
                    ============================================================ */
        .page-hero {
            background: var(--ink);
            color: var(--bg);
            padding: 80px 0 60px;
            border-bottom: 1px solid var(--line);
        }

        .page-hero h1 {
            color: var(--bg);
            font-size: clamp(2.4rem, 5vw, 4rem);
            margin-bottom: 8px;
        }

        .page-hero .crumbs {
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--soft-2);
            margin-bottom: 12px;
        }

        .page-hero .crumbs a {
            color: rgba(241, 234, 217, 0.6);
        }

        .page-hero .crumbs a:hover {
            color: var(--accent);
        }

        .page-hero .crumbs .separator {
            color: rgba(241, 234, 217, 0.3);
            margin: 0 8px;
        }

        .page-hero .section-eyebrow {
            color: var(--accent-soft);
        }

        /* ============================================================
                       INFO STRIP (Mon–Sat / Response / WhatsApp)
                    ============================================================ */
        .info-strip {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .info-strip-item {
            padding: 20px 24px;
            text-align: center;
            font-size: 13px;
            letter-spacing: 0.06em;
            color: var(--ink);
            border-right: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .info-strip-item:last-child {
            border-right: none;
        }

        .info-strip-item i {
            color: var(--accent);
            font-size: 16px;
        }
    </style>
@endsection
