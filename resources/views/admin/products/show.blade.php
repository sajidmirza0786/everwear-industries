@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --surface:      #ffffff;
        --surface-2:    #f7f8fa;
        --surface-3:    #f0f2f5;
        --border:       #e4e7ec;
        --border-dark:  #d0d5dd;
        --text-primary: #101828;
        --text-muted:   #667085;
        --text-light:   #98a2b3;
        --accent:       #2563eb;
        --accent-soft:  #eff4ff;
        --accent-mid:   #bfdbfe;
        --success:      #12b76a;
        --success-soft: #ecfdf3;
        --danger:       #f04438;
        --danger-soft:  #fef3f2;
        --warning:      #f79009;
        --warning-soft: #fffaeb;
        --radius:       10px;
        --radius-sm:    6px;
        --shadow-xs:    0 1px 2px rgba(16,24,40,.06);
        --shadow-sm:    0 1px 3px rgba(16,24,40,.10), 0 1px 2px rgba(16,24,40,.06);
    }

    body { background: var(--surface-2); font-family: 'DM Sans', sans-serif; color: var(--text-primary); }

    /* Page header */
    .page-title    { font-size: 1.2rem; font-weight: 600; margin: 0; }
    .page-subtitle { font-size: .8rem; color: var(--text-muted); margin-top: 2px; }

    /* Cards */
    .pcard { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-xs); overflow: hidden; }
    .pcard-header { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .pcard-title  { font-size: .85rem; font-weight: 600; display: flex; align-items: center; gap: 8px; margin: 0; }
    .icon-wrap    { width: 28px; height: 28px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: .8rem; flex-shrink: 0; }
    .pcard-body   { padding: 18px; }

    /* Main image */
    .main-img-wrap { background: var(--surface-3); border: 1px solid var(--border); border-radius: var(--radius-sm); height: 300px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .main-img-wrap img { max-height: 100%; max-width: 100%; object-fit: contain; }

    /* Thumbnails */
    .thumb-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .thumb-item { position: relative; width: 66px; height: 66px; border-radius: var(--radius-sm); border: 1.5px solid var(--border); overflow: hidden; cursor: pointer; transition: border-color .15s; flex-shrink: 0; }
    .thumb-item:hover { border-color: var(--accent); }
    .thumb-item img { width: 100%; height: 100%; object-fit: cover; }
    .thumb-del { position: absolute; top: 2px; right: 2px; background: var(--danger); color: #fff; border: none; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: .6rem; cursor: pointer; opacity: 0; transition: opacity .15s; padding: 0; }
    .thumb-item:hover .thumb-del { opacity: 1; }

    /* Upload zone */
    .upload-zone { border: 1.5px dashed var(--border-dark); border-radius: var(--radius-sm); padding: 14px; background: var(--surface-3); text-align: center; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
    .info-item { padding: 11px 0; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; gap: 3px; }
    .info-item:nth-last-child(-n+2) { border-bottom: none; }
    .info-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; color: var(--text-light); font-weight: 600; }
    .info-value { font-size: .838rem; color: var(--text-primary); font-weight: 500; }
    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .info-item:nth-last-child(-n+2) { border-bottom: 1px solid var(--border); }
        .info-item:last-child { border-bottom: none; }
    }

    /* Badges */
    .bdg { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 20px; font-size: .7rem; font-weight: 600; }
    .bdg-success { background: var(--success-soft); color: var(--success); }
    .bdg-danger  { background: var(--danger-soft);  color: var(--danger); }
    .bdg-accent  { background: var(--accent-soft);  color: var(--accent); }
    .bdg-neutral { background: var(--surface-3);    color: var(--text-muted); }

    /* Price */
    .price-mrp     { font-size: .78rem; color: var(--text-light); text-decoration: line-through; }
    .price-selling { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); line-height: 1.1; }

    /* Buttons */
    .btn-p  { background: var(--accent); color: #fff; border: none; padding: 7px 14px; border-radius: var(--radius-sm); font-size: .8rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: opacity .15s; text-decoration: none; }
    .btn-p:hover  { opacity: .85; color: #fff; }
    .btn-g  { background: transparent; color: var(--text-muted); border: 1px solid var(--border); padding: 7px 14px; border-radius: var(--radius-sm); font-size: .8rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: background .15s; text-decoration: none; }
    .btn-g:hover  { background: var(--surface-3); color: var(--text-primary); }
    .btn-d  { background: transparent; color: var(--danger); border: 1px solid var(--danger-soft); padding: 7px 14px; border-radius: var(--radius-sm); font-size: .8rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: background .15s; text-decoration: none; }
    .btn-d:hover  { background: var(--danger-soft); color: var(--danger); }
    .btn-ico { width: 30px; height: 30px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); display: inline-flex; align-items: center; justify-content: center; font-size: .78rem; cursor: pointer; color: var(--text-muted); transition: all .15s; padding: 0; }
    .btn-ico:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }
    .btn-ico.del:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-soft); }

    /* Form controls */
    .f-field { display: flex; flex-direction: column; gap: 4px; }
    .f-label { font-size: .73rem; font-weight: 600; color: var(--text-primary); }
    .f-label span { color: var(--danger); }
    .f-ctrl { border: 1px solid var(--border-dark); border-radius: var(--radius-sm); padding: 7px 10px; font-size: .8rem; color: var(--text-primary); font-family: 'DM Sans', sans-serif; background: var(--surface); transition: border-color .15s, box-shadow .15s; width: 100%; }
    .f-ctrl:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-mid); }

    /* Sizes table */
    .sz-table { width: 100%; border-collapse: collapse; }
    .sz-table thead tr { border-bottom: 2px solid var(--border); }
    .sz-table th { padding: 8px 10px; font-size: .67rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--text-light); text-align: left; white-space: nowrap; }
    .sz-table tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
    .sz-table tbody tr:last-child { border-bottom: none; }
    .sz-table tbody tr:hover { background: var(--surface-2); }
    .sz-table td { padding: 10px; font-size: .8rem; vertical-align: middle; }
    .edit-row { background: var(--accent-soft) !important; }
    .edit-row td { padding: 10px; }

    /* Misc */
    .divider  { height: 1px; background: var(--border); margin: 16px 0; }
    .desc-box { background: var(--surface-3); border-radius: var(--radius-sm); padding: 12px 14px; font-size: .8rem; color: var(--text-muted); line-height: 1.75; border: 1px solid var(--border); }
    code { font-family: 'DM Mono', monospace; font-size: .75rem; background: var(--surface-3); padding: 1px 5px; border-radius: 4px; color: var(--text-muted); }
    .empty-state { text-align: center; padding: 32px 16px; color: var(--text-light); }
    .empty-state i { font-size: 2rem; opacity: .25; display: block; margin-bottom: 8px; }
    .empty-state p { font-size: .8rem; margin: 0; }
    .flash { display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: var(--radius-sm); font-size: .8rem; margin-bottom: 18px; animation: fadeIn .3s ease; }
    .flash-ok { background: var(--success-soft); color: #0d7a4e; border: 1px solid #a9efc5; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:none; } }
</style>


@section('content')
<div class="container-fluid py-4" style="max-width:1280px;">

    @if(session('success'))
    <div class="flash flash-ok">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ── Page header ── --}}
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">
                <code>{{ $product->code }}</code>
                &nbsp;·&nbsp;{{ $product->category->name ?? 'Uncategorized' }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn-g">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <button class="btn-d" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash3"></i> Delete
            </button>
        </div>
    </div>

    <div class="row g-4">

        {{-- ════════════════════════════
             LEFT  · Images + Sizes
        ════════════════════════════ --}}
        <div class="col-xl-5 col-lg-5">

            {{-- Images --}}
            <div class="pcard mb-4">
                <div class="pcard-header">
                    <h2 class="pcard-title">
                        <span class="icon-wrap" style="background:#eff4ff;color:#2563eb;"><i class="bi bi-images"></i></span>
                        Product Images
                    </h2>
                    <button class="btn-g" style="padding:5px 10px;font-size:.73rem;"
                            data-bs-toggle="collapse" data-bs-target="#uploadBox">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
                <div class="pcard-body">
                    <div class="main-img-wrap">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" id="mainImg" alt="{{ $product->name }}">
                        @else
                            <span style="color:var(--text-light);font-size:.8rem;">No image</span>
                        @endif
                    </div>

                    @if($product->images->count())
                    <div class="thumb-grid">
                        @foreach($product->images as $img)
                        <div class="thumb-item" onclick="document.getElementById('mainImg').src='{{ asset('storage/'.$img->image_path) }}'">
                            <img src="{{ asset('storage/'.$img->image_path) }}" alt="">
                            <form action="{{ route('admin.products.images.destroy',[$product,$img]) }}" method="POST" class="img-del-form" style="display:contents;">
                                @csrf @method('DELETE')
                                <button type="submit" class="thumb-del"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="collapse mt-3" id="uploadBox">
                        <div class="divider"></div>
                        <form action="{{ route('admin.products.images.store',$product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-zone">
                                <i class="bi bi-cloud-arrow-up" style="font-size:1.4rem;color:var(--text-light);"></i>
                                <p style="font-size:.72rem;color:var(--text-muted);margin:5px 0 10px;">Recommended: 800×800 px</p>
                                <input type="file" name="image" class="f-ctrl" accept="image/*" required style="margin-bottom:10px;">
                                <button type="submit" class="btn-p" style="width:100%;justify-content:center;">
                                    <i class="bi bi-cloud-arrow-up"></i> Upload Image
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>{{-- /col-left --}}


        {{-- ════════════════════════════
             RIGHT  · Product Info
        ════════════════════════════ --}}
        <div class="col-xl-7 col-lg-7">
            <div class="pcard">
                <div class="pcard-header">
                    <h2 class="pcard-title">
                        <span class="icon-wrap" style="background:#fffaeb;color:#f79009;"><i class="bi bi-box-seam"></i></span>
                        Product Information
                    </h2>
                    <span class="bdg {{ $product->status==='enable' ? 'bdg-success' : 'bdg-danger' }}">
                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                        {{ $product->status==='enable' ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="pcard-body">

                    {{-- Price block --}}
                    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                        <div>
                            <div class="price-mrp">MRP ₹{{ number_format($product->mrp,2) }}</div>
                            <div class="price-selling">₹{{ number_format($product->selling,2) }}</div>
                        </div>
                        @php
                            $gDisc = $product->mrp > 0 ? round((($product->mrp - $product->selling)/$product->mrp)*100) : 0;
                        @endphp
                        @if($gDisc > 0)
                        <span class="bdg bdg-success" style="font-size:.8rem;padding:4px 12px;">{{ $gDisc }}% off</span>
                        @endif
                    </div>

                    {{-- Info grid --}}
                    <div class="info-grid">
                        <div class="info-item" style="padding-right:18px;">
                            <span class="info-label">Category</span>
                            <span class="info-value">{{ $product->category->name ?? '—' }}</span>
                        </div>
                        <div class="info-item" style="padding-left:18px;">
                            <span class="info-label">Product Code</span>
                            <span class="info-value"><code>{{ $product->code }}</code></span>
                        </div>
                        <div class="info-item" style="padding-right:18px;">
                            <span class="info-label">Slug</span>
                            <span class="info-value"><code style="word-break:break-all;">{{ $product->slug }}</code></span>
                        </div>
                        <div class="info-item" style="padding-left:18px;">
                            <span class="info-label">Stock</span>
                            <span class="info-value">{{ $product->stock ?? '—' }}</span>
                        </div>
                        <div class="info-item" style="padding-right:18px;">
                            <span class="info-label">GST</span>
                            <span class="info-value"><code style="word-break:break-all;">{{ number_format($product->gst) }}%</code></span>
                        </div>
                        <div class="info-item" style="padding-left:18px;">
                            <span class="info-label">Exc GST Amount</span>
                            <span class="info-value">₹{{ number_format($product->ex_gst_selling, 2) }}</span>
                        </div>
                        <div class="info-item" style="padding-right:18px;">
                            <span class="info-label">Weight</span>
                            <span class="info-value">{{ $product->gram_weight ? $product->gram_weight.' g' : '—' }}</span>
                        </div>
                        @if($product->size)
                        <div class="info-item" style="padding-left:18px;">
                            <span class="info-label">Base Size</span>
                            <span class="info-value"><span class="bdg bdg-accent">{{ $product->size }}</span></span>
                        </div>
                        @endif
                        @if($product->color)
                        <div class="info-item" style="padding-right:18px;">
                            <span class="info-label">Color</span>
                            <span class="info-value d-flex align-items-center gap-2">
                                <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $product->color }};border:1px solid var(--border);flex-shrink:0;"></span>
                                {{ $product->color }}
                            </span>
                        </div>
                        @endif
                        @if($product->keyword)
                        <div class="info-item" style="padding-left:18px;">
                            <span class="info-label">Keywords</span>
                            <span class="info-value" style="font-size:.76rem;color:var(--text-muted);">{{ $product->keyword }}</span>
                        </div>
                        @endif
                    </div>

                    @if($product->title)
                    <div class="divider"></div>
                    <span class="info-label" style="display:block;margin-bottom:6px;">Title</span>
                    <p style="font-size:.838rem;color:var(--text-muted);margin:0;line-height:1.6;">{{ $product->title }}</p>
                    @endif

                    @if($product->description)
                    <div class="divider"></div>
                    <span class="info-label" style="display:block;margin-bottom:6px;">Short Description</span>
                    <p style="font-size:.813rem;color:var(--text-muted);margin:0;line-height:1.75;">{{ $product->description }}</p>
                    @endif

                    @if($product->long_description)
                    <div class="divider"></div>
                    <span class="info-label" style="display:block;margin-bottom:8px;">Detailed Description</span>
                    <div class="desc-box">{!! nl2br(e($product->long_description)) !!}</div>
                    @endif

                    <div class="divider"></div>
                    <div class="d-flex gap-4" style="font-size:.73rem;color:var(--text-light);">
                        <span><i class="bi bi-calendar3 me-1"></i>Created {{ $product->created_at->format('d M Y') }}</span>
                        <span><i class="bi bi-clock-history me-1"></i>Updated {{ $product->updated_at->format('d M Y, h:i A') }}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12">

            {{-- ── Size Variants ── --}}
            <div class="pcard">
                <div class="pcard-header">
                    <h2 class="pcard-title">
                        <span class="icon-wrap" style="background:#ecfdf3;color:#12b76a;"><i class="bi bi-tag"></i></span>
                        Size Variants
                        <span class="bdg bdg-neutral" style="font-size:.68rem;">{{ $product->attributes->count() }}</span>
                    </h2>
                </div>

                {{-- ADD SINGLE SIZE FORM --}}
                <div class="pcard-body" style="padding-bottom:12px;">
                    <form action="{{ route('admin.products.attributes.store',$product) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-6 col-sm-2">
                                <div class="f-field">
                                    <label class="f-label">Size <span>*</span></label>
                                    <input type="text" name="attributes[0][size]" class="f-ctrl" placeholder="S / XL / 1kg" required>
                                </div>
                            </div>
                            <div class="col-6 col-sm-2">
                                <div class="f-field">
                                    <label class="f-label">MRP (₹) <span>*</span></label>
                                    <input type="number" name="attributes[0][mrp]" class="f-ctrl" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6 col-sm-2">
                                <div class="f-field">
                                    <label class="f-label">Price (₹) <span>*</span></label>
                                    <input type="number" name="attributes[0][selling_price]" class="f-ctrl" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6 col-sm-2">
                                <div class="f-field">
                                    <label class="f-label">Stock <span>*</span></label>
                                    <input type="number" name="attributes[0][stock]" class="f-ctrl" placeholder="0" min="0" required>
                                </div>
                            </div>
                            <div class="col-6 col-sm-2">
                                <div class="f-field">
                                    <label class="f-label">Status</label>
                                    <select name="attributes[0][status]" class="f-ctrl">
                                        <option value="enable">Enable</option>
                                        <option value="disable">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-sm-2 d-flex align-items-end">
                                <button type="submit" class="btn-p" style="width:100%;justify-content:center;">
                                    <i class="bi bi-plus-lg"></i> Add Size
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div style="height:1px;background:var(--border);"></div>

                {{-- SIZES LIST --}}
                @if($product->attributes->count())
                <div style="overflow-x:auto;">
                    <table class="sz-table">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>MRP</th>
                                <th>Price</th>
                                <th>Disc.</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($product->attributes as $attr)
                            {{-- View row --}}
                            <tr id="vr-{{ $attr->id }}">
                                <td>
                                    <span class="bdg bdg-accent" style="font-family:'DM Mono',monospace;letter-spacing:.02em;">{{ $attr->size }}</span>
                                </td>
                                <td style="color:var(--text-light);text-decoration:line-through;font-size:.73rem;">₹{{ number_format($attr->mrp,2) }}</td>
                                <td style="font-weight:600;">₹{{ number_format($attr->selling_price,2) }}</td>
                                <td>
                                    @php $d = $attr->mrp > 0 ? round((($attr->mrp - $attr->selling_price)/$attr->mrp)*100) : 0; @endphp
                                    @if($d > 0)
                                        <span class="bdg bdg-success">{{ $d }}%</span>
                                    @else
                                        <span style="color:var(--text-light);">—</span>
                                    @endif
                                </td>
                                <td>{{ $attr->stock }}</td>
                                <td>
                                    <span class="bdg {{ $attr->status==='enable' ? 'bdg-success' : 'bdg-danger' }}">
                                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                                        {{ $attr->status==='enable' ? 'On' : 'Off' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn-ico" onclick="toggleEdit({{ $attr->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('admin.products.attributes.destroy',[$product,$attr]) }}" method="POST" class="attr-del-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ico del" title="Delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Edit row --}}
                            <tr id="er-{{ $attr->id }}" class="edit-row" style="display:none;">
                                <td colspan="7" style="padding:10px 12px;">
                                    <form action="{{ route('admin.products.attributes.update',[$product,$attr]) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="row g-2 align-items-end">
                                            <div class="col-6 col-md-2">
                                                <div class="f-field"><label class="f-label">Size</label>
                                                    <input type="text" name="size" class="f-ctrl" value="{{ $attr->size }}" required>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <div class="f-field"><label class="f-label">MRP</label>
                                                    <input type="number" name="mrp" class="f-ctrl" value="{{ $attr->mrp }}" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <div class="f-field"><label class="f-label">Price</label>
                                                    <input type="number" name="selling_price" class="f-ctrl" value="{{ $attr->selling_price }}" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <div class="f-field"><label class="f-label">Stock</label>
                                                    <input type="number" name="stock" class="f-ctrl" value="{{ $attr->stock }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <div class="f-field"><label class="f-label">Status</label>
                                                    <select name="status" class="f-ctrl">
                                                        <option value="enable"  {{ $attr->status==='enable'  ? 'selected':'' }}>Enable</option>
                                                        <option value="disable" {{ $attr->status==='disable' ? 'selected':'' }}>Disable</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-2 d-flex gap-1 align-items-end">
                                                <button type="submit" class="btn-p" style="padding:7px 10px;"><i class="bi bi-check-lg"></i></button>
                                                <button type="button" class="btn-g"  style="padding:7px 10px;" onclick="toggleEdit({{ $attr->id }})"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="bi bi-tags"></i>
                    <p>No sizes added yet. Use the form above.</p>
                </div>
                @endif

            </div>{{-- /Size Variants card --}}
        </div>

    </div>{{-- /row --}}
</div>{{-- /container --}}


{{-- Delete Product Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <form action="{{ route('admin.products.destroy',$product) }}" method="POST"
              class="modal-content" style="border-radius:var(--radius);border:1px solid var(--border);box-shadow:0 8px 24px rgba(0,0,0,.12);">
            @csrf @method('DELETE')
            <div class="modal-body p-4 text-center">
                <div style="width:50px;height:50px;background:var(--danger-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.3rem;color:var(--danger);">
                    <i class="bi bi-trash3"></i>
                </div>
                <h5 style="font-weight:600;font-size:1rem;margin-bottom:8px;">Delete this product?</h5>
                <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:22px;">
                    <strong>{{ $product->name }}</strong> will be permanently removed. This cannot be undone.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn-g" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-p" style="background:var(--danger);">
                        <i class="bi bi-trash3"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
<script>
function toggleEdit(id) {
    const vr = document.getElementById('vr-' + id);
    const er = document.getElementById('er-' + id);
    const hidden = vr.style.display === 'none';
    vr.style.display = hidden ? '' : 'none';
    er.style.display = hidden ? 'none' : '';
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.img-del-form').forEach(f =>
        f.addEventListener('submit', e => { if (!confirm('Delete this image?')) e.preventDefault(); })
    );
    document.querySelectorAll('.attr-del-form').forEach(f =>
        f.addEventListener('submit', e => { if (!confirm('Delete this size variant?')) e.preventDefault(); })
    );
});
</script>
@endsection