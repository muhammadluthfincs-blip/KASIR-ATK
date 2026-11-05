<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak PO - {{ $purchase->nomor_pembelian }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1.1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1.1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 13px; color: #111; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4" onload="window.print()">
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0">ATK POS</h4>
            <div class="text-muted small">Sistem Kasir dan Stok Alat Tulis Kantor</div>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-primary">BUKTI PEMBELIAN / PO</h5>
            <div class="font-monospace fw-bold">{{ $purchase->nomor_pembelian }}</div>
            <div class="small text-muted">Tanggal: {{ $purchase->tanggal_pembelian->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <strong>Supplier:</strong><br>
            {{ $purchase->supplier->name ?? '-' }}<br>
            {{ $purchase->supplier->phone ?? '-' }}<br>
            {{ $purchase->supplier->address ?? '-' }}
        </div>
        <div class="col-6 text-end">
            <strong>Metode Pembayaran:</strong> {{ $purchase->metode_pembayaran }}<br>
            <strong>Status:</strong> {{ $purchase->status_pembayaran }}<br>
            <strong>Petugas Penerima:</strong> {{ $purchase->user->name ?? '-' }}
        </div>
    </div>

    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr>
                <th style="width: 40px;">No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Harga Beli</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->details as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="font-monospace">{{ $item->product->kode_barang ?? '-' }}</td>
                <td>{{ $item->product->nama_barang ?? '-' }}</td>
                <td class="text-center font-monospace">{{ $item->jumlah }} {{ $item->product->satuan ?? 'Pcs' }}</td>
                <td class="text-end">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold table-light">
                <td colspan="5" class="text-end">Total Pembelian:</td>
                <td class="text-end">Rp {{ number_format($purchase->total_pembelian, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($purchase->catatan)
    <p class="small text-muted mb-4"><strong>Catatan:</strong> {{ $purchase->catatan }}</p>
    @endif

    <div class="row text-center mt-5">
        <div class="col-6">
            <p class="mb-4">Diterima Oleh,</p>
            <br><br>
            <strong>( {{ $purchase->user->name ?? 'Petugas Toko' }} )</strong>
        </div>
        <div class="col-6">
            <p class="mb-4">Supplier,</p>
            <br><br>
            <strong>( {{ $purchase->supplier->name ?? 'Supplier' }} )</strong>
        </div>
    </div>
</body>
</html>

