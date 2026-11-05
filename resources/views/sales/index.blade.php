@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('page_title', 'Riwayat Transaksi Penjualan')
@section('page_subtitle', 'Daftar struk penjualan kasir, cetak ulang struk, dan rekap omset')

@section('content')
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100">
            <span class="text-muted small fw-semibold">Total Transaksi</span>
            <h3 class="fw-bold mb-0 text-dark">{{ number_format($summary['total_transactions']) }}</h3>
            <small class="text-muted">Struk sesuai filter</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100">
            <span class="text-muted small fw-semibold">Total Omset Penjualan</span>
            <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</h3>
            <small class="text-muted">Total penerimaan kasir</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card bg-white h-100">
            <span class="text-muted small fw-semibold">Total Diskon Diberikan</span>
            <h3 class="fw-bold mb-0 text-danger">Rp {{ number_format($summary['total_discount'], 0, ',', '.') }}</h3>
            <small class="text-muted">Potongan harga transaksi</small>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('sales.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Transaksi..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2 col-6">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="Tunai" {{ $paymentMethod === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ $paymentMethod === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer Bank" {{ $paymentMethod === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="Debit" {{ $paymentMethod === 'Debit' ? 'selected' : '' }}>Debit</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="cashier_id" class="form-select form-select-sm">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $cashierId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($search || $startDate || $endDate || $paymentMethod || $cashierId)
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
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
        <span class="fw-semibold">Daftar Transaksi (Total {{ $sales->total() }} struk)</span>
        <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-calculator me-1"></i> Buka Kasir POS
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal & Waktu</th>
                        <th>Kasir</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-end">Diskon</th>
                        <th class="text-end">Total Bayar</th>
                        <th>Metode</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $sale)
                    <tr>
                        <td class="text-muted">{{ $sales->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('sales.show', $sale->id) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                {{ $sale->nomor_transaksi }}
                            </a>
                        </td>
                        <td>{{ $sale->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td class="small">{{ $sale->user->name ?? '-' }}</td>
                        <td class="text-center font-monospace fw-bold">{{ $sale->total_item }}</td>
                        <td class="text-end text-muted small">
                            {{ $sale->diskon > 0 ? 'Rp ' . number_format($sale->diskon, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-bold text-primary fs-6">
                            Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $sale->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-info" title="Lihat Detail Transaksi">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.receipt', $sale->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Ulang Struk">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada transaksi penjualan yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($sales->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $sales->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

