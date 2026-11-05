@extends('layouts.app')

@section('title', 'Barang Masuk / Pembelian')
@section('page_title', 'Barang Masuk (Pembelian Supplier)')
@section('page_subtitle', 'Catatan pengadaan barang dan penambahan stok dari supplier')

@section('content')
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('purchases.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. PO..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2 col-6">
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Lunas" {{ $status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Lunas" {{ $status === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($search || $startDate || $endDate || $supplierId || $status)
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-semibold">Riwayat Transaksi Pembelian (Total {{ $purchases->total() }} transaksi)</span>
        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-cart-flatbed"></i>
            <span>Catat Pembelian Baru</span>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No. Pembelian (PO)</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Petugas</th>
                        <th>Metode</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Total Pembelian</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $index => $po)
                    <tr>
                        <td class="text-muted">{{ $purchases->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('purchases.show', $po->id) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                {{ $po->nomor_pembelian }}
                            </a>
                        </td>
                        <td>{{ $po->tanggal_pembelian->format('d/m/Y') }}</td>
                        <td class="fw-semibold text-dark">{{ $po->supplier->name ?? '-' }}</td>
                        <td class="small text-muted">{{ $po->user->name ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $po->metode_pembayaran }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $po->status_pembayaran === 'Lunas' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} px-2 py-1">
                                {{ $po->status_pembayaran }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($po->total_pembelian, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('purchases.show', $po->id) }}" class="btn btn-outline-info" title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('purchases.print', $po->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Bukti">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-cart-flatbed fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data transaksi pembelian barang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($purchases->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $purchases->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

