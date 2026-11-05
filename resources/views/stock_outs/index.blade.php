@extends('layouts.app')

@section('title', 'Barang Keluar (Non-Penjualan)')
@section('page_title', 'Barang Keluar (Non-Penjualan)')
@section('page_subtitle', 'Pencatatan barang rusak, hilang, keperluan toko, atau penyesuaian stok')

@section('content')
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('stock-outs.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Transaksi..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-2 col-6">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-3 col-6">
                <select name="reason" class="form-select form-select-sm">
                    <option value="">Semua Alasan</option>
                    <option value="Barang Rusak" {{ $reason === 'Barang Rusak' ? 'selected' : '' }}>Barang Rusak</option>
                    <option value="Barang Hilang" {{ $reason === 'Barang Hilang' ? 'selected' : '' }}>Barang Hilang</option>
                    <option value="Keperluan Toko" {{ $reason === 'Keperluan Toko' ? 'selected' : '' }}>Keperluan Toko</option>
                    <option value="Penyesuaian Stok" {{ $reason === 'Penyesuaian Stok' ? 'selected' : '' }}>Penyesuaian Stok</option>
                    <option value="Cacat / Expired" {{ $reason === 'Cacat / Expired' ? 'selected' : '' }}>Cacat / Expired</option>
                </select>
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($search || $startDate || $endDate || $reason)
                <a href="{{ route('stock-outs.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-semibold">Riwayat Barang Keluar (Total {{ $transactions->total() }} catatan)</span>
        <a href="{{ route('stock-outs.create') }}" class="btn btn-sm btn-danger d-flex align-items-center gap-2">
            <i class="fa-solid fa-minus-circle"></i>
            <span>Catat Barang Keluar</span>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Alasan Pengeluaran</th>
                        <th class="text-center">Total Item Keluar</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr>
                        <td class="text-muted">{{ $transactions->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('stock-outs.show', $trx->id) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                {{ $trx->nomor_transaksi }}
                            </a>
                        </td>
                        <td>{{ $trx->tanggal->format('d/m/Y') }}</td>
                        <td class="small">{{ $trx->user->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                {{ $trx->alasan }}
                            </span>
                        </td>
                        <td class="text-center font-monospace fw-bold text-danger">
                            -{{ $trx->total_item }}
                        </td>
                        <td class="small text-muted">{{ $trx->keterangan ?: '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('stock-outs.show', $trx->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada catatan barang keluar non-penjualan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

