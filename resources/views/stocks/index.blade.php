@extends('layouts.app')

@section('title', 'Manajemen Stok')
@section('page_title', 'Manajemen Stok & Monitoring Persediaan')
@section('page_subtitle', 'Pantau status ketersediaan barang ATK dan kartu mutasi stok')

@section('content')
<!-- Stat Counters -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card stat-card bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Jenis Produk</span>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalProducts) }}</h3>
            <small class="text-muted">Katalog barang aktif</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card stat-card bg-white h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Stok Aman</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-success">{{ number_format($safeCount) }}</h3>
            <small class="text-muted">Di atas batas minimum</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card stat-card bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Stok Menipis</span>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-warning">{{ number_format($lowCount) }}</h3>
            <small class="text-muted">Perlu segera di-restock</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card stat-card bg-white h-100 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Stok Habis</span>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-danger">{{ number_format($outOfStockCount) }}</h3>
            <small class="text-muted">Stok kosong (0)</small>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('stocks.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
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
            <div class="col-md-3 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-2 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
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
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Status Persediaan Barang (Total {{ $products->total() }} barang)</span>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-cart-plus me-1"></i> Beli Barang Masuk
            </a>
            <a href="{{ route('stock-outs.create') }}" class="btn btn-sm btn-outline-danger">
                <i class="fa-solid fa-minus-circle me-1"></i> Catat Barang Keluar
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Produk ATK</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-center">Stok Minimum</th>
                        <th class="text-center">Stok Tersedia</th>
                        <th class="text-center">Indikator Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $prod)
                    <tr>
                        <td class="text-muted">{{ $products->firstItem() + $index }}</td>
                        <td class="font-monospace fw-bold text-dark">{{ $prod->kode_barang }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $prod->nama_barang }}</div>
                            @if($prod->barcode)
                            <small class="text-muted font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $prod->barcode }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">{{ $prod->category->name ?? '-' }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $prod->satuan }}</span></td>
                        <td class="text-center text-muted font-monospace">{{ $prod->stok_minimum }}</td>
                        <td class="text-center">
                            <span class="fs-6 font-monospace fw-bold {{ $prod->stok <= 0 ? 'text-danger' : ($prod->stok <= $prod->stok_minimum ? 'text-warning' : 'text-success') }}">
                                {{ $prod->stok }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $prod->stock_status_badge['class'] }} px-2 py-1">
                                <i class="fa-solid {{ $prod->stock_status_badge['icon'] }} me-1"></i>
                                {{ $prod->stock_status_badge['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('stocks.card', $prod->id) }}" class="btn btn-sm btn-outline-primary" title="Buka Kartu Stok">
                                <i class="fa-solid fa-clipboard-list me-1"></i> Kartu Stok
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

