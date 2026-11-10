@extends('layouts.app')

@section('title', 'Data & Katalog Produk ATK')
@section('page_title', 'Data & Katalog Produk ATK')
@section('page_subtitle', 'Katalog master barang, pengecekan harga jual, varian warna, dan sisa stok fisik')

@section('content')
<!-- Filter & Pencarian Bar -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-3">
        <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, varian, kode, atau barcode..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3 col-6">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
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
                <button type="submit" class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if($search || $categoryId || $status)
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Katalog Produk -->
<div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark fs-6">Katalog Produk ATK</span>
            <span class="badge bg-light text-secondary border fw-medium px-2 py-1" style="font-size: 0.75rem;">Total {{ $products->total() }} barang</span>
        </div>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Produk Baru</span>
        </a>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="width: 50px;" class="text-center text-secondary small fw-bold">No</th>
                        <th class="text-secondary small fw-bold" style="min-width: 150px;">Kode / Barcode</th>
                        <th class="text-secondary small fw-bold" style="min-width: 240px;">Nama Barang & Varian</th>
                        <th class="text-secondary small fw-bold" style="min-width: 140px;">Kategori</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 90px;">Satuan</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="text-secondary small fw-bold text-end" style="min-width: 120px;">Harga Beli</th>
                        @endif
                        <th class="text-secondary small fw-bold text-end" style="min-width: 120px;">Harga Jual</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 100px;">Sisa Stok</th>
                        <th class="text-secondary small fw-bold text-center" style="min-width: 130px;">Status</th>
                        <th class="text-secondary small fw-bold text-center" style="width: 110px;">Aksi</th>
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
                        @if(auth()->user()->role === 'admin')
                        <td class="text-end text-muted small">
                            Rp {{ number_format($prod->harga_beli, 0, ',', '.') }}
                        </td>
                        @endif
                        <td class="text-end fw-bold text-primary" style="font-size: 0.95rem;">
                            Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <div class="fw-bold {{ $prod->stok <= $prod->stok_minimum ? ($prod->stok <= 0 ? 'text-danger' : 'text-warning-emphasis') : 'text-success' }}" style="font-size: 0.95rem;">
                                {{ $prod->stok }}
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Min: {{ $prod->stok_minimum }}</small>
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
                            <div class="d-inline-flex gap-1 align-items-center">
                                <!-- Tombol Quick Detail Modal (Kasir & Admin) -->
                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#quickDetailModal{{ $prod->id }}" title="Lihat Detail Barang">
                                    <i class="fa-regular fa-id-card"></i>
                                </button>

                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('products.edit', $prod->id) }}" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('products.destroy', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk {{ $prod->nama_barang }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Produk">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>

                            <!-- Modal Quick Detail Produk -->
                            <div class="modal fade" id="quickDetailModal{{ $prod->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom py-3">
                                            <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-box-archive text-primary"></i>
                                                <span>Detail Informasi Produk ATK</span>
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="text-center mb-3 p-3 bg-light rounded-3 border">
                                                <div class="font-monospace fs-4 fw-bold text-dark" style="letter-spacing: 2px;">{{ $prod->barcode ?? $prod->kode_barang }}</div>
                                                <small class="text-muted font-monospace">Barcode / SKU Resmi Toko</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="text-secondary small fw-semibold">Nama Barang & Varian:</label>
                                                <div class="fs-6 fw-bold text-dark">{{ $prod->nama_barang }}</div>
                                                @if($prod->variant_badge)
                                                    <span class="badge mt-1" style="background-color: {{ $prod->variant_badge['bg'] }}; color: {{ $prod->variant_badge['text'] }}; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px;">
                                                        <span style="color: {{ $prod->variant_badge['dot'] }}; margin-right: 4px;">●</span> Varian: {{ $prod->variant_badge['label'] }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="text-secondary small fw-semibold">Kode Barang:</label>
                                                    <div class="font-monospace fw-bold">{{ $prod->kode_barang }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-secondary small fw-semibold">Kategori:</label>
                                                    <div><span class="badge bg-light text-dark border">{{ $prod->category->name ?? '-' }}</span></div>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="text-secondary small fw-semibold">Harga Jual:</label>
                                                    <div class="fs-5 fw-bold text-primary">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</div>
                                                    <small class="text-muted">per {{ $prod->satuan }}</small>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-secondary small fw-semibold">Sisa Stok Fisik:</label>
                                                    <div class="fs-5 fw-bold {{ $prod->stok <= $prod->stok_minimum ? 'text-danger' : 'text-success' }}">
                                                        {{ $prod->stok }} <span class="fs-6 fw-normal text-muted">{{ $prod->satuan }}</span>
                                                    </div>
                                                    <small class="text-muted">Batas Minimum: {{ $prod->stok_minimum }} {{ $prod->satuan }}</small>
                                                </div>
                                            </div>

                                            @if($prod->deskripsi)
                                            <div class="mb-2">
                                                <label class="text-secondary small fw-semibold">Keterangan / Lokasi Rak:</label>
                                                <div class="small text-muted p-2 bg-light rounded border">{{ $prod->deskripsi }}</div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-top py-2 px-3">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            <a href="{{ route('stocks.card', $prod->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-list-check"></i>
                                                <span>Lihat Kartu Stok</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'admin' ? 10 : 9 }}" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada produk ATK yang cocok dengan filter.
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
