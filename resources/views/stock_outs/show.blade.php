@extends('layouts.app')

@section('title', 'Detail Barang Keluar ' . $stockOut->nomor_transaksi)
@section('page_title', 'Detail Barang Keluar Non-Penjualan')
@section('page_subtitle', 'Rincian pencatatan mutasi pengurangan stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold font-monospace text-danger fs-5">{{ $stockOut->nomor_transaksi }}</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger ms-2 border border-danger border-opacity-25">
                        {{ $stockOut->alasan }}
                    </span>
                </div>
                <a href="{{ route('stock-outs.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Tanggal Pengeluaran:</span>
                        <h6 class="fw-bold text-dark">{{ $stockOut->tanggal->format('d/m/Y') }}</h6>
                        <span class="small text-muted text-uppercase fw-semibold d-block mt-2">Petugas Operator:</span>
                        <div class="fw-semibold">{{ $stockOut->user->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Total Fisik Dikeluarkan:</span>
                        <h3 class="fw-bold text-danger mb-0">-{{ $stockOut->total_item }} Unit</h3>
                        @if($stockOut->keterangan)
                        <small class="text-muted d-block mt-1"><em>"{{ $stockOut->keterangan }}"</em></small>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Produk ATK</th>
                                <th class="text-center">Jumlah Dikeluarkan</th>
                                <th>Keterangan Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOut->details as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-monospace small">{{ $item->product->kode_barang ?? '-' }}</td>
                                <td class="fw-semibold">{{ $item->product->nama_barang ?? '-' }}</td>
                                <td class="text-center font-monospace fw-bold text-danger">
                                    -{{ $item->jumlah }} {{ $item->product->satuan ?? 'Pcs' }}
                                </td>
                                <td class="small text-muted">{{ $item->keterangan ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Seluruh Item:</th>
                                <th class="text-center text-danger fs-6">-{{ $stockOut->total_item }} Unit</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

