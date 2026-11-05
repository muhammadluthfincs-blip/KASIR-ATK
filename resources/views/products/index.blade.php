@extends('layouts.app')

@section('title', 'Data Produk ATK')
@section('page_title', 'Data Produk ATK')
@section('page_subtitle', 'Katalog master barang, harga, dan ketersediaan stok')

@section('content')
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, kode, atau barcode..." value="{{ $search }}">
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
            <div class="col-md-3 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Stok Aman (&gt; Min)</option>
                    <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                    <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
            </div>
            <div class="col-md-2 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($search || $categoryId || $status)
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-semibold">Daftar Produk (Total {{ $products->total() }} barang)</span>
        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Produk Baru</span>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode / Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok Fisik</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $prod)
                    <tr>
                        <td class="text-muted">{{ $products->firstItem() + $index }}</td>
                        <td>
                            <div class="font-monospace fw-bold text-dark">{{ $prod->kode_barang }}</div>
                            @if($prod->barcode)
                            <small class="text-muted font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $prod->barcode }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $prod->id) }}" class="fw-semibold text-dark text-decoration-none hover-primary">
                                {{ $prod->nama_barang }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">
                                {{ $prod->category->name ?? '-' }}
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $prod->satuan }}</span></td>
                        <td class="text-end text-muted small">Rp {{ number_format($prod->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-primary">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center fw-bold">
                            {{ $prod->stok }}
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Min: {{ $prod->stok_minimum }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $prod->stock_status_badge['class'] }}">
                                <i class="fa-solid {{ $prod->stock_status_badge['icon'] }} me-1"></i>
                                {{ $prod->stock_status_badge['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $prod->id) }}" class="btn btn-outline-info" title="Detail Produk">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $prod->id) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('products.destroy', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada produk yang cocok dengan filter.
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

