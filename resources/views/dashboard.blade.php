@extends('users.master')

@section('seo')
    <title>My Account — Everwear Industries</title>
    <meta name="description" content="Manage your orders, address, and account settings on Everwear Industries.">
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
                    Account
                </li>
            </ol>
        </nav>
    </div>
</div>

<section class="section" data-testid="dashboard-section">
    <div class="container">
        <div class="dash-grid">

            {{-- Sidebar --}}
            <aside class="dash-side" data-testid="dash-side">
                <a href="#orders" class="active"><i class="bi bi-box-seam"></i> Orders</a>
                <a href="#address"><i class="bi bi-geo-alt"></i> Address book</a>
                <a href="#settings"><i class="bi bi-gear"></i> Settings</a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Sign out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </aside>

            <div>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Orders --}}
                <div class="dash-card mb-4" id="orders" data-testid="dash-orders">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 style="margin:0;">Recent orders</h4>
                        <a href="{{ route('welcome') }}" class="btn btn-ghost btn-sm">Shop again</a>
                    </div>

                    @if($orders->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam" style="font-size:32px; color:var(--soft-2);"></i>
                            <p class="text-soft mt-2 mb-0">No orders yet. Start shopping to see your orders here.</p>
                        </div>
                    @else
                        <div style="border-top:1px solid var(--line);">
                            @foreach($orders as $index => $order)
                                <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom-soft' : '' }}">
                                    <div>
                                        <div style="font-weight:500;">#EWI-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <small class="text-soft">
                                            {{ $order->items_count ?? $order->items->count() }} item{{ ($order->items_count ?? $order->items->count()) !== 1 ? 's' : '' }}
                                            &middot;
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-weight:600;">&#8377; {{ number_format($order->total, 0) }}</div>
                                        <small style="color:
                                            @if($order->status === 'completed') var(--success)
                                            @elseif($order->status === 'cancelled') var(--danger, #dc3545)
                                            @elseif($order->status === 'in-transit') var(--accent-2)
                                            @else var(--soft-2)
                                            @endif
                                        ;">
                                            @switch($order->status)
                                                @case('completed') Delivered @break
                                                @case('cancelled') Cancelled @break
                                                @case('in-transit') In transit @break
                                                @default Pending
                                            @endswitch
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($orders->hasPages())
                            <div class="mt-3">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Default Address --}}
                <div class="dash-card mb-4" id="address" data-testid="dash-address">
                    <h4 style="margin-bottom:18px;">Default address</h4>

                    @php
                        $user = auth()->user();
                        $hasAddress = $user->address || $user->city || $user->state;
                    @endphp

                    @if($hasAddress)
                        <p class="text-soft" style="margin:0;">
                            {{ $user->name }}<br>
                            @if($user->address) {{ $user->address }}<br> @endif
                            @if($user->locality) {{ $user->locality }},<br> @endif
                            @if($user->city) {{ $user->city }} @endif
                            @if($user->zipcode) {{ $user->zipcode }}, @endif
                            @if($user->state) {{ $user->state }}, India @endif
                            <br>
                            @if($user->mobile) {{ $user->country_code ?? '+91' }} {{ $user->mobile }} @endif
                        </p>
                    @else
                        <p class="text-soft" style="margin:0;">No address saved yet.</p>
                    @endif

                    {{-- <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm mt-3">Edit</a> --}}
                </div>

                {{-- Account Settings --}}
                <div class="dash-card" id="settings" data-testid="dash-settings">
                    <h4 style="margin-bottom:18px;">Account settings</h4>
                    <div style="border-top:1px solid var(--line);">
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom-soft">
                            <div>
                                <div style="font-weight:500;">Name</div>
                                <small class="text-soft">{{ auth()->user()->name }}</small>
                            </div>
                            {{-- <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm">Edit</a> --}}
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom-soft">
                            <div>
                                <div style="font-weight:500;">Email</div>
                                <small class="text-soft">{{ auth()->user()->email }}</small>
                            </div>
                            {{-- <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm">Edit</a> --}}
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div style="font-weight:500;">Mobile</div>
                                <small class="text-soft">{{ auth()->user()->mobile }}</small>
                            </div>
                            {{-- <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm">Edit</a> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection