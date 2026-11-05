@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $sale->nomor_transaksi)
@section('page_title', 'Detail Transaksi Penjualan')
@section('page_subtitle', 'Rincian struk belanja kasir dan item yang dibeli')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold font-monospace text-primary fs-5">{{ $sale->nomor_transaksi }}</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $sale->metode_pembayaran }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('sales.receipt', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-print me-1"></i> Cetak Ulang Struk
                    </a>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light border">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Waktu Transaksi:</span>
                        <h6 class="fw-bold text-dark mb-1">{{ $sale->tanggal_transaksi->format('d/m/Y H:i:s') }}</h6>
                        <span class="small text-muted text-uppercase fw-semibold d-block mt-2">Kasir Bertugas:</span>
                        <div>{{ $sale->user->name ?? '-' }} ({{ $sale->user->role ?? '-' }})</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Total Tagihan:</span>
                        <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</h2>
                        <small class="text-muted">Dibayar: Rp {{ number_format($sale->nominal_bayar, 0, ',', '.') }} | Kembalian: Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</small>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Produk ATK</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->details as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-monospace small">{{ $item->product->kode_barang ?? '-' }}</td>
                                <td class="fw-semibold">{{ $item->product->nama_barang ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center font-monospace fw-bold">{{ $item->jumlah }} {{ $item->product->satuan ?? 'Pcs' }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal:</th>
                                <th class="text-end">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</th>
                            </tr>
                            @if($sale->diskon > 0)
                            <tr>
                                <th colspan="5" class="text-end text-danger">Potongan Diskon:</th>
                                <th class="text-end text-danger">- Rp {{ number_format($sale->diskon, 0, ',', '.') }}</th>
                            </tr>
                            @endif
                            <tr class="fw-bold fs-6">
                                <th colspan="5" class="text-end text-primary">Total Pembayaran:</th>
                                <th class="text-end text-primary">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-muted">Uang Dibayar ({{ $sale->metode_pembayaran }}):</th>
                                <th class="text-end text-muted">Rp {{ number_format($sale->nominal_bayar, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-success">Kembalian:</th>
                                <th class="text-end text-success">Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($sale->catatan)
                <div class="p-3 bg-light rounded-3 border">
                    <span class="fw-semibold small d-block mb-1 text-secondary">Catatan:</span>
                    <p class="mb-0 text-muted small">{{ $sale->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

