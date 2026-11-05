@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->nama_barang)
@section('page_title', 'Detail Produk ATK')
@section('page_subtitle', 'Informasi spesifikasi, riwayat pergerakan stok, dan valuasi harga')

@section('content')
<div class="row g-4">
    <!-- Left Column: Product Summary Card -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center p-4">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.8rem; border-radius: 16px;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $product->nama_barang }}</h5>
                <span class="badge bg-light text-secondary border mb-3">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge {{ $product->stock_status_badge['class'] }} px-3 py-2">
                        <i class="fa-solid {{ $product->stock_status_badge['icon'] }} me-1"></i>
                        {{ $product->stock_status_badge['label'] }}
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Kode Barang:</span>
                        <strong class="font-monospace">{{ $product->kode_barang }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Barcode:</span>
                        <strong class="font-monospace">{{ $product->barcode ?: '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Satuan:</span>
                        <strong>{{ $product->satuan }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Stok Tersedia:</span>
                        <strong class="fs-5 text-primary">{{ $product->stok }} {{ $product->satuan }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Stok Minimum:</span>
                        <span class="text-muted">{{ $product->stok_minimum }} {{ $product->satuan }}</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('stocks.card', $product->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-clipboard-list me-1"></i> Buka Kartu Stok Lengkap
                    </a>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data Produk
                    </a>
                </div>
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="card">
            <div class="card-header bg-light">
                <span class="fw-bold small text-uppercase text-secondary">Analisa Harga & Margin</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Harga Beli (HPP):</span>
                    <strong class="text-dark">Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Harga Jual:</span>
                    <strong class="text-primary fs-6">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</strong>
                </div>
                <hr class="my-2">
                @php
                    $labaUnit = $product->harga_jual - $product->harga_beli;
                    $margin = $product->harga_jual > 0 ? ($labaUnit / $product->harga_jual) * 100 : 0;
                @endphp
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Laba Kotor Unit:</span>
                    <strong class="text-success">Rp {{ number_format($labaUnit, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Persentase Margin:</span>
                    <strong class="badge bg-success bg-opacity-10 text-success fs-6">{{ number_format($margin, 1) }}%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Description & Stock Movements History -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <span class="fw-bold"><i class="fa-solid fa-circle-info text-primary me-2"></i>Deskripsi Produk</span>
            </div>
            <div class="card-body">
                <p class="mb-0 text-secondary">{{ $product->deskripsi ?: 'Tidak ada deskripsi atau spesifikasi tambahan untuk produk ini.' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Riwayat Mutasi Stok Terakhir</span>
                <a href="{{ route('stocks.card', $product->id) }}" class="btn btn-sm btn-outline-primary">Lihat Semua Mutasi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Referensi</th>
                                <th class="text-center text-success">Masuk</th>
                                <th class="text-center text-danger">Keluar</th>
                                <th class="text-center">Sisa</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->stockMovements()->latest('tanggal')->limit(10)->get() as $mov)
                            <tr>
                                <td class="small">{{ $mov->tanggal->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($mov->tipe === 'masuk' || $mov->tipe === 'pembelian')
                                        <span class="badge bg-success bg-opacity-10 text-success">Masuk</span>
                                    @elseif($mov->tipe === 'penjualan')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Penjualan</span>
                                    @elseif($mov->tipe === 'keluar')
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Keluar</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($mov->tipe) }}</span>
                                    @endif
                                </td>
                                <td class="font-monospace small">{{ $mov->referensi ?: '-' }}</td>
                                <td class="text-center fw-bold text-success">{{ $mov->jumlah_masuk > 0 ? '+' . $mov->jumlah_masuk : '-' }}</td>
                                <td class="text-center fw-bold text-danger">{{ $mov->jumlah_keluar > 0 ? '-' . $mov->jumlah_keluar : '-' }}</td>
                                <td class="text-center fw-bold text-dark">{{ $mov->sisa_stok }}</td>
                                <td class="small text-muted">{{ $mov->alasan }} - {{ $mov->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pergerakan stok untuk produk ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

