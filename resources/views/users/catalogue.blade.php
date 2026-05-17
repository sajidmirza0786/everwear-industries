@extends('users.master')

@section('seo')
    <title>Download Catalogue | Everwear Industries</title>
    <meta name="description"
        content="Download the Everwear Industries product catalogue. Browse our complete range of trophies, awards, and corporate mementos with full product details and pricing.">
    <meta name="keywords"
        content="Everwear Industries catalogue, download catalogue, trophy catalogue, awards catalogue, corporate gifts catalogue">
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="border-bottom-soft py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.78rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('welcome') }}" class="text-decoration-none" style="color:var(--soft)">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color:var(--ink);font-weight:500" aria-current="page">
                        Catalogue
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Hero Banner --}}
    <div style="background:var(--ink);padding:64px 0 48px;position:relative;overflow:hidden;">
        <div
            style="position:absolute;inset:0;background:radial-gradient(circle at 80% 50%, rgba(176,141,87,0.18), transparent 55%);pointer-events:none;">
        </div>
        <div class="container" style="position:relative;z-index:2;">
            <div style="max-width:600px;">
                <span class="section-eyebrow" style="color:var(--accent-soft);">Resources</span>
                <h1 style="color:#fff;font-size:clamp(2rem,4vw,3.2rem);margin:8px 0 16px;">
                    Product <em style="color:var(--accent-soft);font-style:italic;">Catalogue</em>
                </h1>
                <p style="color:rgba(255,255,255,0.72);font-size:15px;line-height:1.75;max-width:480px;margin:0;">
                    Explore our complete range of trophies, awards, and corporate mementos.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="section-tight">
        <div class="container">
            <div style="display:flex;justify-content:center;padding:48px 0;">
                <div class="catalogue-card">

                    {{-- Icon + Title --}}
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                        <div
                            style="width:50px;height:50px;border-radius:10px;background:var(--bg-2);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-file-earmark-pdf" style="font-size:22px;color:var(--soft);"></i>
                        </div>
                        <div>
                            <div style="font-size:16px;font-weight:600;color:var(--ink);line-height:1.2;">Everwear
                                Industries</div>
                            <div style="font-size:12px;color:var(--soft);letter-spacing:0.04em;margin-top:2px;">Product
                                Catalogue &middot; PDF</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p style="font-size:13px;color:var(--soft);line-height:1.7;margin:0 0 24px;">
                        Browse our complete range of trophies, awards, and corporate mementos. View online or save a copy
                        for your team.
                    </p>

                    {{-- Divider --}}
                    <div style="border-top:1px solid var(--line);margin-bottom:20px;"></div>

                    {{-- Actions --}}
                    <div style="display:flex;flex-direction:column;gap:10px;">

                        {{-- View --}}
                        <a href="{{ url('users/final-catalogue-1.pdf') }}" target="_blank" class="cat-btn cat-btn-primary">
                            <i class="bi bi-eye"></i>
                            View catalogue
                        </a>

                        {{-- Download --}}
                        <a href="{{ url('users/final-catalogue-1.pdf') }}" download class="cat-btn cat-btn-secondary">
                            <i class="bi bi-download"></i>
                            Download PDF
                        </a>

                        {{-- Copy Link --}}
                        <button id="copyLinkBtn"
                            onclick="
                            navigator.clipboard.writeText('{{ url('users/final-catalogue-1.pdf') }}').then(function() {
                                var btn = document.getElementById('copyLinkBtn');
                                btn.classList.add('cat-btn-copied');
                                btn.innerHTML = '<i class=\'bi bi-check2\'></i> Link copied!';
                                setTimeout(function() {
                                    btn.classList.remove('cat-btn-copied');
                                    btn.innerHTML = '<i class=\'bi bi-link-45deg\'></i> Copy link';
                                }, 2200);
                            });
                        "
                            class="cat-btn cat-btn-ghost">
                            <i class="bi bi-link-45deg"></i>
                            Copy link
                        </button>

                    </div>

                    {{-- Footer note --}}
                    <p style="font-size:11px;color:var(--soft-2);text-align:center;margin:20px 0 0;letter-spacing:0.03em;">
                        <i class="bi bi-info-circle"></i>
                        If the PDF doesn't open, try the Download option above.
                    </p>

                </div>
                <div class="catalogue-card">

                    {{-- Icon + Title --}}
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                        <div
                            style="width:50px;height:50px;border-radius:10px;background:var(--bg-2);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-file-earmark-pdf" style="font-size:22px;color:var(--soft);"></i>
                        </div>
                        <div>
                            <div style="font-size:16px;font-weight:600;color:var(--ink);line-height:1.2;">Everwear
                                Industries</div>
                            <div style="font-size:12px;color:var(--soft);letter-spacing:0.04em;margin-top:2px;">Product
                                Catalogue &middot; PDF</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p style="font-size:13px;color:var(--soft);line-height:1.7;margin:0 0 24px;">
                        Browse our complete range of trophies, awards, and corporate mementos. View online or save a copy
                        for your team.
                    </p>

                    {{-- Divider --}}
                    <div style="border-top:1px solid var(--line);margin-bottom:20px;"></div>

                    {{-- Actions --}}
                    <div style="display:flex;flex-direction:column;gap:10px;">

                        {{-- View --}}
                        <a href="{{ url('users/product-catalogue.pdf') }}" target="_blank" class="cat-btn cat-btn-primary">
                            <i class="bi bi-eye"></i>
                            View catalogue
                        </a>

                        {{-- Download --}}
                        <a href="{{ url('users/product-catalogue.pdf') }}" download class="cat-btn cat-btn-secondary">
                            <i class="bi bi-download"></i>
                            Download PDF
                        </a>

                        {{-- Copy Link --}}
                        <button id="copyLinkBtn"
                            onclick="
                            navigator.clipboard.writeText('{{ url('users/product-catalogue.pdf') }}').then(function() {
                                var btn = document.getElementById('copyLinkBtn');
                                btn.classList.add('cat-btn-copied');
                                btn.innerHTML = '<i class=\'bi bi-check2\'></i> Link copied!';
                                setTimeout(function() {
                                    btn.classList.remove('cat-btn-copied');
                                    btn.innerHTML = '<i class=\'bi bi-link-45deg\'></i> Copy link';
                                }, 2200);
                            });
                        "
                            class="cat-btn cat-btn-ghost">
                            <i class="bi bi-link-45deg"></i>
                            Copy link
                        </button>

                    </div>

                    {{-- Footer note --}}
                    <p style="font-size:11px;color:var(--soft-2);text-align:center;margin:20px 0 0;letter-spacing:0.03em;">
                        <i class="bi bi-info-circle"></i>
                        If the PDF doesn't open, try the Download option above.
                    </p>

                </div>
            </div>
        </div>
    </section>

    <style>
        .catalogue-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 28px 28px 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
        }

        .cat-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-decoration: none;
            border-radius: 8px;
            transition: opacity 0.15s, background 0.15s, color 0.15s;
            cursor: pointer;
            box-sizing: border-box;
            border: none;
        }

        .cat-btn-primary {
            background: var(--ink);
            color: var(--bg);
            border: 1px solid var(--ink);
        }

        .cat-btn-primary:hover {
            opacity: 0.85;
            color: var(--bg);
            text-decoration: none;
        }

        .cat-btn-secondary {
            background: transparent;
            color: var(--ink);
            border: 1px solid var(--line-strong);
        }

        .cat-btn-secondary:hover {
            background: var(--bg-2);
            color: var(--ink);
            text-decoration: none;
        }

        .cat-btn-ghost {
            background: transparent;
            color: var(--soft);
            border: 1px solid var(--line);
        }

        .cat-btn-ghost:hover {
            background: var(--bg-2);
            color: var(--ink);
        }

        .cat-btn-copied {
            background: var(--bg-2) !important;
            color: var(--ink) !important;
            border-color: var(--line-strong) !important;
        }

        @media (max-width: 480px) {
            .catalogue-card {
                padding: 22px 18px 18px;
                border-radius: 10px;
            }
        }
    </style>
@endsection
