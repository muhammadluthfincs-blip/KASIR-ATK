@extends('layouts.app')

@section('title', 'Manajemen Stok & Monitoring Persediaan')
@section('page_title', 'Manajemen Stok & Monitoring Persediaan')
@section('page_subtitle', 'Pantau status ketersediaan barang ATK dan kartu mutasi stok')

@section('content')
<!-- Stat Counters (Matching Design Reference) -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card bg-white h-100 border-0 shadow-sm p-3" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Total Jenis Produk</span>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.85rem;">{{ number_format($totalProducts) }}</h2>
            <small class="text-muted" style="font-size: 0.78rem;">Katalog barang aktif</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card bg-white h-100 shadow-sm p-3" style="border: 2px solid #10b981 !important; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Stok Aman</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-0 text-success" style="font-size: 1.85rem;">{{ number_format($safeCount) }}</h2>
            <small class="text-muted" style="font-size: 0.78rem;">Di atas batas minimum</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card bg-white h-100 shadow-sm p-3" style="border: 2px solid #eab308 !important; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Stok Menipis</span>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning-emphasis rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-0 text-warning-emphasis" style="font-size: 1.85rem; color: #ca8a04 !important;">{{ number_format($lowCount) }}</h2>
            <small class="text-muted" style="font-size: 0.78rem;">Perlu segera di-restock</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card bg-white h-100 shadow-sm p-3" style="border: 2px solid #ef4444 !important; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Stok Habis</span>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-0 text-danger" style="font-size: 1.85rem;">{{ number_format($outOfStockCount) }}</h2>
            <small class="text-muted" style="font-size: 0.78rem;">Stok kosong (0)</small>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
    <div class="card-body p-3">
        <form action="{{ route('stocks.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau kode barang..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-2 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm">Filter</button>
                @if($search || $categoryId || $status)
                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <span class="fw-bold text-dark fs-6">Status Persediaan Barang (Total {{ $products->total() }} barang)</span>
        @if(auth()->user()->role === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-cart-plus me-1"></i> Beli Barang Masuk
            </a>
            <a href="{{ route('stock-outs.create') }}" class="btn btn-sm btn-outline-danger">
                <i class="fa-solid fa-minus-circle me-1"></i> Catat Barang Keluar
            </a>
        </div>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="width: 50px;" class="text-center text-secondary small fw-bold">No</th>
                        <th class="text-secondary small fw-bold" style="min-width: 140px;">Kode Barang</th>
                        <th class="text-secondary small fw-bold" style="min-width: 240px;">Nama Produk ATK</th>
                        <th class="text-secondary small fw-bold" style="min-width: 140px;">Kategori</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 90px;">Satuan</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 110px;">Stok Minimum</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 110px;">Stok Tersedia</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 140px;">Indikator Status</th>
                        <th class="text-secondary small fw-bold text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $prod)
                    <tr>
                        <td class="text-center text-muted small">{{ $products->firstItem() + $index }}</td>
                        <td>
                            <div class="font-monospace fw-bold text-dark" style="font-size: 0.88rem;">{{ $prod->kode_barang }}</div>
                            <div class="small text-muted font-monospace d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <span style="letter-spacing: -1px; font-weight: 700; color: #64748b;">||||||</span>
                                <span>{{ $prod->barcode ?? $prod->kode_barang }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                {{ $prod->nama_barang }}
                            </div>
                            @if($prod->variant_badge)
                                <div class="mt-1">
                                    <span class="badge" style="background-color: {{ $prod->variant_badge['bg'] }}; color: {{ $prod->variant_badge['text'] }}; font-size: 0.72rem; padding: 3px 8px; border-radius: 6px; font-weight: 600;">
                                        <span style="color: {{ $prod->variant_badge['dot'] }}; margin-right: 3px;">●</span> {{ $prod->variant_badge['label'] }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1 fw-medium" style="font-size: 0.78rem;">
                                {{ $prod->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1 fw-normal" style="font-size: 0.78rem;">
                                {{ $prod->satuan }}
                            </span>
                        </td>
                        <td class="text-center text-secondary font-monospace fw-semibold" style="font-size: 0.9rem;">
                            {{ $prod->stok_minimum }}
                        </td>
                        <td class="text-center">
                            <span class="font-monospace fw-bold fs-6 {{ $prod->stok <= 0 ? 'text-danger' : ($prod->stok <= $prod->stok_minimum ? 'text-warning-emphasis' : 'text-success') }}">
                                {{ $prod->stok }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($prod->stok <= 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> Stok Habis
                                </span>
                            @elseif($prod->stok <= $prod->stok_minimum)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Stok Menipis
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Stok Aman
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('stocks.card', $prod->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.8rem;" title="Buka Kartu Stok">
                                <i class="fa-solid fa-clipboard-list"></i>
                                <span>Kartu Stok</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-boxes-stacked fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data stok produk ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
