@extends('layouts.app')

@section('title', 'Detail Pembelian ' . $purchase->nomor_pembelian)
@section('page_title', 'Detail Pembelian Barang Masuk')
@section('page_subtitle', 'Bukti transaksi penerimaan barang dari supplier')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold font-monospace text-primary fs-5">{{ $purchase->nomor_pembelian }}</span>
                    <span class="badge {{ $purchase->status_pembayaran === 'Lunas' ? 'bg-success' : 'bg-warning' }} ms-2">
                        {{ $purchase->status_pembayaran }}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-print me-1"></i> Cetak Bukti
                    </a>
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-light border">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Supplier:</span>
                        <h6 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name ?? '-' }}</h6>
                        <small class="text-muted d-block">{{ $purchase->supplier->phone ?? '-' }}</small>
                        <small class="text-muted d-block">{{ $purchase->supplier->address ?? '-' }}</small>
                    </div>
                    <div class="col-md-4">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Detail Transaksi:</span>
                        <div><strong>Tanggal:</strong> {{ $purchase->tanggal_pembelian->format('d/m/Y') }}</div>
                        <div><strong>Metode:</strong> {{ $purchase->metode_pembayaran }}</div>
                        <div><strong>Petugas:</strong> {{ $purchase->user->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="small text-muted text-uppercase fw-semibold d-block">Total Pembayaran:</span>
                        <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($purchase->total_pembelian, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Produk ATK</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->details as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-monospace small">{{ $item->product->kode_barang ?? '-' }}</td>
                                <td class="fw-semibold">{{ $item->product->nama_barang ?? '-' }}</td>
                                <td class="text-center font-monospace fw-bold">{{ $item->jumlah }} {{ $item->product->satuan ?? 'Pcs' }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total Pembelian:</th>
                                <th class="text-end text-primary fs-6">Rp {{ number_format($purchase->total_pembelian, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($purchase->catatan)
                <div class="p-3 bg-light rounded-3 border">
                    <span class="fw-semibold small d-block mb-1 text-secondary">Catatan:</span>
                    <p class="mb-0 text-muted small">{{ $purchase->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

