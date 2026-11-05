@extends('layouts.app')

@section('title', 'Kartu Stok - ' . $product->nama_barang)
@section('page_title', 'Kartu Stok Barang')
@section('page_subtitle', 'Riwayat lengkap mutasi masuk, keluar, dan saldo persediaan')

@section('content')
<!-- Product Summary Header -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary" style="width: 56px; height: 56px; font-size: 1.5rem; border-radius: 12px;">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-light text-secondary border font-monospace">{{ $product->kode_barang }}</span>
                        @if($product->barcode)
                        <span class="badge bg-light text-muted border font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $product->barcode }}</span>
                        @endif
                        <span class="badge bg-light text-primary border">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">{{ $product->nama_barang }}</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="text-end">
                    <span class="small text-muted d-block">Sisa Stok Fisik Saat Ini:</span>
                    <span class="fs-4 fw-bold font-monospace {{ $product->stok <= 0 ? 'text-danger' : ($product->stok <= $product->stok_minimum ? 'text-warning' : 'text-success') }}">
                        {{ $product->stok }} {{ $product->satuan }}
                    </span>
                </div>
                <span class="badge {{ $product->stock_status_badge['class'] }} px-3 py-2">
                    <i class="fa-solid {{ $product->stock_status_badge['icon'] }} me-1"></i>
                    {{ $product->stock_status_badge['label'] }}
                </span>
                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mutation Stats in Filter Period -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100 border-start border-4 border-success">
            <span class="text-muted small fw-semibold">Total Barang Masuk (Periode Ini)</span>
            <h3 class="fw-bold mb-0 text-success">+{{ number_format($totalIn) }} {{ $product->satuan }}</h3>
            <small class="text-muted">Dari pembelian & saldo awal</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100 border-start border-4 border-danger">
            <span class="text-muted small fw-semibold">Total Barang Keluar (Periode Ini)</span>
            <h3 class="fw-bold mb-0 text-danger">-{{ number_format($totalOut) }} {{ $product->satuan }}</h3>
            <small class="text-muted">Dari penjualan kasir & pengeluaran toko</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100 border-start border-4 border-primary">
            <span class="text-muted small fw-semibold">Saldo Stok Fisik</span>
            <h3 class="fw-bold mb-0 text-primary">{{ number_format($product->stok) }} {{ $product->satuan }}</h3>
            <small class="text-muted">Batas minimum: {{ $product->stok_minimum }} {{ $product->satuan }}</small>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('stocks.card', $product->id) }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-6">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-4 col-6">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-3 col-12">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="pembelian" {{ $type === 'pembelian' ? 'selected' : '' }}>Pembelian Supplier (Masuk)</option>
                    <option value="penjualan" {{ $type === 'penjualan' ? 'selected' : '' }}>Penjualan Kasir (Keluar)</option>
                    <option value="keluar" {{ $type === 'keluar' ? 'selected' : '' }}>Barang Keluar / Rusak / Hilang</option>
                    <option value="penyesuaian" {{ $type === 'penyesuaian' ? 'selected' : '' }}>Penyesuaian / Stok Awal</option>
                </select>
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($startDate || $endDate || $type)
                <a href="{{ route('stocks.card', $product->id) }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Movement Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Buku Mutasi Stok (Total {{ $movements->total() }} catatan pergerakan)</span>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-cart-plus me-1"></i> Beli Barang Ini
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
                        <th>Tanggal & Waktu</th>
                        <th>Tipe Mutasi</th>
                        <th>No. Referensi</th>
                        <th class="text-center text-success" style="width: 110px;">Masuk (+)</th>
                        <th class="text-center text-danger" style="width: 110px;">Keluar (-)</th>
                        <th class="text-center" style="width: 110px;">Sisa Stok</th>
                        <th>Alasan & Keterangan</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $index => $mov)
                    <tr>
                        <td class="text-muted">{{ $movements->firstItem() + $index }}</td>
                        <td>{{ $mov->tanggal->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($mov->tipe === 'pembelian' || $mov->tipe === 'masuk')
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="fa-solid fa-arrow-down me-1"></i> Pembelian
                                </span>
                            @elseif($mov->tipe === 'penjualan')
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="fa-solid fa-cart-shopping me-1"></i> Penjualan
                                </span>
                            @elseif($mov->tipe === 'keluar')
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="fa-solid fa-arrow-up me-1"></i> Keluar
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    <i class="fa-solid fa-sliders me-1"></i> {{ ucfirst($mov->tipe) }}
                                </span>
                            @endif
                        </td>
                        <td class="font-monospace small fw-bold">
                            @if(str_starts_with($mov->referensi, 'TRX-'))
                                <a href="{{ route('sales.index', ['search' => $mov->referensi]) }}" class="text-decoration-none">
                                    {{ $mov->referensi }}
                                </a>
                            @elseif(str_starts_with($mov->referensi, 'PO-'))
                                <a href="{{ route('purchases.index', ['search' => $mov->referensi]) }}" class="text-decoration-none">
                                    {{ $mov->referensi }}
                                </a>
                            @elseif(str_starts_with($mov->referensi, 'OUT-'))
                                <a href="{{ route('stock-outs.index', ['search' => $mov->referensi]) }}" class="text-decoration-none text-danger">
                                    {{ $mov->referensi }}
                                </a>
                            @else
                                <span class="text-muted">{{ $mov->referensi ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="text-center font-monospace fw-bold text-success">
                            {{ $mov->jumlah_masuk > 0 ? '+' . $mov->jumlah_masuk : '-' }}
                        </td>
                        <td class="text-center font-monospace fw-bold text-danger">
                            {{ $mov->jumlah_keluar > 0 ? '-' . $mov->jumlah_keluar : '-' }}
                        </td>
                        <td class="text-center font-monospace fw-bold text-dark fs-6">
                            {{ $mov->sisa_stok }}
                        </td>
                        <td>
                            <div class="fw-semibold small text-dark">{{ $mov->alasan }}</div>
                            <small class="text-muted">{{ $mov->keterangan ?: '-' }}</small>
                        </td>
                        <td class="small text-muted">{{ $mov->user->name ?? 'Sistem' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-check fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada riwayat mutasi stok untuk produk ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($movements->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

