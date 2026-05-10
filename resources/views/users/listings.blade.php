@extends('users.master')

@section('seo')
    @if (request()->has('search'))
        <title>Search results for "{{ request('search') }}" | On Jewel</title>
    @else
        <title>{{ $category->name }} | On Jewel</title>
        <meta name="keywords" content="{{ $category->keywords ?? '' }}">
        <meta name="description" content="{{ $category->description ?? '' }}">
    @endif
@endsection

@section('content')

    {{-- ── Breadcrumb ── --}}
    <div class="pd-breadcrumb">
        <div class="container-fluid px-3 px-xl-5">
            <nav class="pd-crumbs" aria-label="breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                @if (!request()->has('search') && isset($category) && $category->parent)
                    <span>/</span>
                    <a href="{{ route('listing', $category->parent) }}">{{ $category->parent->name }}</a>
                @endif
                <span>/</span>
                <span class="pd-crumb-active">
                    {{ request()->has('search') ? 'Search' : $category->name ?? 'Products' }}
                </span>
            </nav>
        </div>
    </div>

    {{-- ── Category Description ── --}}
    @if (!request()->has('search') && isset($category) && $category->description)
        <div style="background:var(--surface); padding:18px 0; border-bottom:1px solid var(--line);">
            <div class="container-fluid px-3 px-xl-5">
                <p class="mb-0" style="color:var(--soft); font-size:13px;">{{ $category->description }}</p>
            </div>
        </div>
    @endif

    {{-- ── Mobile Category Drawer Overlay ── --}}
    <div id="catOverlay" class="lst-overlay" aria-hidden="true"></div>

    {{-- ── Mobile Category Drawer ── --}}
    <div id="catDrawer" class="lst-drawer" role="dialog" aria-label="Categories">
        <div class="lst-drawer-head">
            <span class="section-eyebrow mb-0" style="font-size:10px;">Categories</span>
            <button id="catDrawerClose" class="lst-drawer-close" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="lst-drawer-list">
            @foreach (ucategories() as $cat)
                @php $isActive = isset($category) && $category->id === $cat->id; @endphp
                <li>
                    <a href="{{ route('listing', $cat) }}" class="lst-drawer-link {{ $isActive ? 'active' : '' }}">
                        <span>{{ $cat->name }}</span>
                        @if ($isActive)
                            <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- ── Main Shop Body ── --}}
    <div class="lst-body">
        <div class="container-fluid px-3 px-xl-5">
            <div class="row g-0">

                {{-- ── Desktop Sidebar ── --}}
                <div class="col-lg-2 d-none d-lg-block">
                    <div class="lst-sidebar">
                        <p class="section-eyebrow mb-3">Categories</p>
                        <ul class="list-unstyled mb-0">
                            @foreach (ucategories() as $cat)
                                @php $isActive = isset($category) && $category->id === $cat->id; @endphp
                                <li style="border-bottom:1px solid var(--line);">
                                    <a href="{{ route('listing', $cat) }}"
                                        class="lst-sidebar-link {{ $isActive ? 'active' : '' }}">
                                        <span>{{ $cat->name }}</span>
                                        @if ($isActive)
                                            <i class="bi bi-chevron-right" style="font-size:9px;"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- ── Products Area ── --}}
                <div class="col-lg-10">
                    <div class="ps-lg-4">

                        {{-- ── Toolbar ── --}}
                        <div class="lst-toolbar">
                            <p class="lst-count mb-0">
                                <strong>{{ $products->count() }}</strong>
                                item{{ $products->count() !== 1 ? 's' : '' }} found
                            </p>
                            {{-- Mobile categories button --}}
                            <button id="catOpen" type="button" class="btn btn-ghost btn-sm d-lg-none lst-cat-btn">
                                <i class="bi bi-grid" style="font-size:13px;"></i>
                                <span>Categories</span>
                                <i class="bi bi-chevron-down" style="font-size:10px; opacity:0.6;"></i>
                            </button>
                        </div>

                        {{-- ── Empty State ── --}}
                        @if ($products->isEmpty())
                            <div class="lst-empty">
                                <i class="bi bi-gem lst-empty-icon"></i>
                                <h5 class="lst-empty-title">No products found</h5>
                                <p class="lst-empty-text">
                                    @if (request()->has('search'))
                                        Try a different search term or browse our categories.
                                    @else
                                        This collection is currently empty. Check back soon.
                                    @endif
                                </p>
                            </div>
                        @else
                            {{-- ── Product Grid ── --}}
                            <div class="lst-grid">
                                @foreach ($products as $product)
                                    <div class="lst-card">

                                        {{-- Image --}}
                                        <a href="{{ route('listing', $product) }}" class="lst-card-img">
                                            <img src="{{ url(\Storage::url($product->image ?? '')) }}"
                                                alt="{{ $product->name }}" loading="lazy">
                                            @if ($product->mrp > $product->selling)
                                                @php $disc = round((($product->mrp - $product->selling) / $product->mrp) * 100); @endphp
                                                @if ($disc > 0)
                                                    <span class="lst-badge">{{ $disc }}% off</span>
                                                @endif
                                            @endif
                                            @if ($product->stock <= 0)
                                                <span class="lst-badge lst-badge-oos">Out of Stock</span>
                                            @endif
                                        </a>

                                        {{-- Info --}}
                                        <div class="lst-card-body">
                                            <a href="{{ route('listing', $product) }}" class="lst-card-name">
                                                {{ $product->name }}
                                            </a>

                                            @if ($product->size || $product->color)
                                                <div class="lst-card-attrs">
                                                    @if ($product->size)
                                                        <span>{{ $product->size }}</span>
                                                    @endif
                                                    @if ($product->size && $product->color)
                                                        <span class="lst-sep">·</span>
                                                    @endif
                                                    @if ($product->color)
                                                        <span>{{ $product->color }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="lst-card-price">
                                                <span class="lst-price">₹{{ number_format($product->selling) }}</span>
                                                @if ($product->mrp > $product->selling)
                                                    <span class="lst-price-old">₹{{ number_format($product->mrp) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="lst-card-foot">
                                            <a href="{{ route('listing', $product) }}" class="lst-action-btn">
                                                <i class="bi bi-eye"></i>
                                                <span>View</span>
                                            </a>

                                            @if ($product->stock <= 0 && !$product->attributes()->where('status', 'enable')->exists())
                                                {{-- Fully out of stock, no variants --}}
                                                <span class="lst-action-oos">
                                                    <i class="bi bi-slash-circle"></i>
                                                    <span>Out of Stock</span>
                                                </span>
                                            @elseif($product->attributes()->where('status', 'enable')->exists())
                                                {{-- Has variants — must go to product page to select --}}
                                                <a href="{{ route('listing', $product) }}"
                                                    class="lst-action-btn lst-action-cart">
                                                    <i class="bi bi-tag"></i>
                                                    <span>Select</span>
                                                </a>
                                            @else
                                                {{-- No variants, in stock — direct add to cart --}}
                                                <form action="{{ route('cart.add') }}" method="POST"
                                                    class="lst-action-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="lst-action-btn lst-action-cart">
                                                        <i class="bi bi-bag"></i>
                                                        <span>Add to Cart</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- ── Pagination ── --}}
                        @if (method_exists($products, 'links') && $products->lastPage() > 1)
                            <div class="d-flex justify-content: center mt-5">
                                {{ $products->links() }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* ── Breadcrumb ── */
        .pd-breadcrumb {
            background: var(--bg-2);
            border-bottom: 1px solid var(--line);
            padding: 9px 0;
        }

        .pd-crumbs {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            letter-spacing: 0.05em;
            flex-wrap: wrap;
            margin: 0;
        }

        .pd-crumbs a {
            color: var(--soft);
        }

        .pd-crumbs a:hover {
            color: var(--accent-2);
        }

        .pd-crumbs>span {
            color: var(--soft-2);
        }

        .pd-crumb-active {
            color: var(--ink);
            font-weight: 500;
        }

        /* ── Body ── */
        .lst-body {
            background: var(--bg);
            padding: 28px 0 72px;
        }

        /* ── Desktop Sidebar ── */
        .lst-sidebar {
            position: sticky;
            top: 90px;
            padding-right: 24px;
            border-right: 1px solid var(--line);
        }

        .lst-sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 10px;
            font-size: 13px;
            font-weight: 400;
            color: var(--ink);
            border-radius: var(--radius-sm);
            transition: all 0.2s;
        }

        .lst-sidebar-link:hover {
            background: var(--surface);
            color: var(--accent-2);
        }

        .lst-sidebar-link.active {
            font-weight: 600;
            color: var(--accent-2);
            background: var(--accent-soft);
        }

        /* ── Toolbar ── */
        .lst-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--line);
            flex-wrap: wrap;
            gap: 10px;
        }

        .lst-count {
            font-size: 12px;
            color: var(--soft);
        }

        .lst-count strong {
            color: var(--ink);
            font-weight: 600;
        }

        .lst-cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.04em;
            padding: 8px 14px;
        }

        /* ── Product Grid ── */
        .lst-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            /* ← breathing room on mobile */
        }

        @media (min-width: 576px) {
            .lst-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }

        @media (min-width: 768px) {
            .lst-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 18px;
            }
        }

        @media (min-width: 1200px) {
            .lst-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
        }

        /* ── Product Card ── */
        .lst-card {
            background: var(--bg);
            border: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.25s, transform 0.25s;
            overflow: hidden;
        }

        .lst-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
        }

        /* Card Image */
        .lst-card-img {
            display: block;
            position: relative;
            overflow: hidden;
            background: var(--surface);
            aspect-ratio: 3 / 4;
            /* consistent portrait ratio */
            flex-shrink: 0;
        }

        .lst-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.55s cubic-bezier(0.2, 0.7, 0.2, 1);
        }

        .lst-card:hover .lst-card-img img {
            transform: scale(1.05);
        }

        /* Badges */
        .lst-badge {
            position: absolute;
            bottom: 8px;
            left: 8px;
            z-index: 2;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            background: var(--ink);
            color: var(--bg);
            padding: 3px 8px;
        }

        .lst-badge-oos {
            background: #a8412c;
            bottom: auto;
            top: 8px;
            left: 8px;
        }

        /* Card Body */
        .lst-card-body {
            padding: 10px 12px 8px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .lst-card-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }

        .lst-card-name:hover {
            color: var(--accent-2);
        }

        .lst-card-attrs {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--soft-2);
        }

        .lst-sep {
            color: var(--line-strong);
        }

        .lst-card-price {
            display: flex;
            align-items: baseline;
            gap: 7px;
            flex-wrap: wrap;
            margin-top: auto;
            padding-top: 4px;
        }

        .lst-price {
            font-size: 14px;
            font-weight: 600;
            color: var(--accent-2);
        }

        .lst-price-old {
            font-size: 11px;
            color: var(--soft-2);
            text-decoration: line-through;
        }

        /* Card Footer Actions */
        .lst-card-foot {
            display: flex;
            border-top: 1px solid var(--line);
            margin-top: auto;
        }

        .lst-action-form {
            flex: 1;
            display: flex;
        }

        .lst-action-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 4px;
            font-size: 11.5px;
            font-weight: 500;
            letter-spacing: 0.03em;
            color: var(--soft);
            border: none;
            background: transparent;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
            border-right: 1px solid var(--line);
            text-decoration: none;
            white-space: nowrap;
        }

        .lst-action-btn:last-child,
        .lst-action-form:last-child .lst-action-btn {
            border-right: none;
        }

        .lst-action-btn:hover {
            color: var(--ink);
            background: var(--surface);
        }

        .lst-action-btn i {
            font-size: 12px;
        }

        .lst-action-oos {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 4px;
            font-size: 11px;
            color: var(--soft-2);
            cursor: not-allowed;
        }

        .lst-action-cart:hover {
            color: var(--accent-2);
            background: rgba(176, 141, 87, 0.07);
        }

        /* ── Empty State ── */
        .lst-empty {
            padding: 72px 24px;
            text-align: center;
            border: 1px solid var(--line);
            background: var(--surface);
        }

        .lst-empty-icon {
            font-size: 1.8rem;
            color: var(--line-strong);
            display: block;
            margin-bottom: 12px;
        }

        .lst-empty-title {
            font-family: var(--font-display);
            font-weight: 500;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .lst-empty-text {
            color: var(--soft);
            font-size: 13px;
            margin: 0;
        }

        /* ── Mobile Category Drawer ── */
        .lst-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(26, 20, 16, 0.45);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            animation: fadeIn 0.25s ease;
        }

        .lst-overlay.open {
            display: block;
        }

        .lst-drawer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 201;
            background: var(--bg);
            border-top: 2px solid var(--accent);
            border-radius: 14px 14px 0 0;
            max-height: 70vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.32s cubic-bezier(0.25, 0.8, 0.25, 1);
            padding-bottom: max(16px, env(safe-area-inset-bottom));
        }

        .lst-drawer.open {
            transform: translateY(0);
        }

        .lst-drawer-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px 12px;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 1;
        }

        .lst-drawer-close {
            width: 34px;
            height: 34px;
            border: 1px solid var(--line);
            background: var(--surface);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            color: var(--ink);
            transition: background 0.2s;
        }

        .lst-drawer-close:hover {
            background: var(--ink);
            color: var(--bg);
        }

        .lst-drawer-list {
            list-style: none;
            margin: 0;
            padding: 8px 0;
        }

        .lst-drawer-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            font-size: 14px;
            font-weight: 400;
            color: var(--ink);
            border-bottom: 1px solid var(--line);
            transition: background 0.15s, color 0.15s;
        }

        .lst-drawer-link:hover {
            background: var(--surface);
        }

        .lst-drawer-link.active {
            font-weight: 600;
            color: var(--accent-2);
            background: var(--accent-soft);
        }

        .lst-drawer-list li:last-child .lst-drawer-link {
            border-bottom: none;
        }

        /* Drag handle visual */
        .lst-drawer::before {
            content: '';
            display: block;
            width: 36px;
            height: 4px;
            background: var(--line-strong);
            border-radius: 999px;
            margin: 10px auto 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* ── Mobile card tweaks ── */
        @media (max-width: 575.98px) {
            .lst-card-body {
                padding: 8px 10px 6px;
            }

            .lst-card-name {
                font-size: 12.5px;
            }

            .lst-price {
                font-size: 13px;
            }

            .lst-action-btn {
                font-size: 11px;
                padding: 8px 2px;
                gap: 3px;
            }

            .lst-action-btn i {
                font-size: 11px;
            }

            .lst-action-btn span {
                display: none;
            }

            /* icon-only on very small screens */
            .lst-action-oos span {
                display: none;
            }

            .lst-action-oos i {
                display: inline;
            }
        }

        /* Show label again from 380px */
        @media (min-width: 380px) {
            .lst-action-btn span {
                display: inline;
            }

            .lst-action-oos span {
                display: inline;
            }
        }
    </style>

@endsection

@section('scripts')
    <script>
        (function() {
            var openBtn = document.getElementById('catOpen');
            var drawer = document.getElementById('catDrawer');
            var overlay = document.getElementById('catOverlay');
            var closeBtn = document.getElementById('catDrawerClose');

            if (!openBtn || !drawer || !overlay) return;

            function openDrawer() {
                drawer.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
                drawer.setAttribute('aria-hidden', 'false');
            }

            function closeDrawer() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
                drawer.setAttribute('aria-hidden', 'true');
            }

            openBtn.addEventListener('click', openDrawer);
            closeBtn.addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);

            // Swipe down to close
            var startY = 0;
            drawer.addEventListener('touchstart', function(e) {
                startY = e.changedTouches[0].clientY;
            }, {
                passive: true
            });
            drawer.addEventListener('touchend', function(e) {
                var dy = e.changedTouches[0].clientY - startY;
                if (dy > 60) closeDrawer();
            }, {
                passive: true
            });

            // ESC to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDrawer();
            });
        }());
    </script>
@endsection
