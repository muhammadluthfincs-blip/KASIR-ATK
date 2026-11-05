<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ strtoupper($tab) }} - ATK POS</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1.1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1.1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #111; }
        .table th, .table td { padding: 6px 8px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4" onload="window.print()">
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    <!-- Header Dokumen -->
    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0">ATK POS</h3>
            <div>Sistem Kasir dan Manajemen Stok Alat Tulis Kantor</div>
            <div class="small text-muted">Jl. Raya Utama No. 88, Telp: 0812-3456-7890</div>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-1 text-primary text-uppercase">LAPORAN {{ str_replace('_', ' ', $tab) }}</h4>
            <div class="small">Periode: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong></div>
            <div class="small text-muted">Dicetak pada: {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($tab === 'sales')
        <div class="row mb-3">
            <div class="col-3"><strong>Total Transaksi:</strong> {{ $data['totalTrx'] }}</div>
            <div class="col-3"><strong>Item Terjual:</strong> {{ $data['totalItemsSold'] }} unit</div>
            <div class="col-3"><strong>Total Diskon:</strong> Rp {{ number_format($data['totalDiscount'], 0, ',', '.') }}</div>
            <div class="col-3 text-end"><strong>Total Omset:</strong> Rp {{ number_format($data['totalRevenue'], 0, ',', '.') }}</div>
        </div>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th class="text-center">Item</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Total Bayar</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['sales'] as $idx => $s)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="font-monospace">{{ $s->nomor_transaksi }}</td>
                    <td>{{ $s->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $s->total_item }}</td>
                    <td class="text-end">Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $s->diskon > 0 ? 'Rp ' . number_format($s->diskon, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($s->total_bayar, 0, ',', '.') }}</td>
                    <td>{{ $s->metode_pembayaran }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($tab === 'profit')
        <div class="row mb-3">
            <div class="col-3"><strong>Omset Penjualan:</strong> Rp {{ number_format($data['totalRevenue'], 0, ',', '.') }}</div>
            <div class="col-3"><strong>Modal Pokok (HPP):</strong> Rp {{ number_format($data['totalHpp'], 0, ',', '.') }}</div>
            <div class="col-3"><strong>Laba Kotor:</strong> Rp {{ number_format($data['grossProfit'], 0, ',', '.') }}</div>
            <div class="col-3 text-end"><strong>Margin:</strong> {{ number_format($data['marginPercent'], 2) }}%</div>
        </div>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">HPP Modal</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Total HPP</th>
                    <th class="text-end">Total Jual</th>
                    <th class="text-end">Laba Kotor</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($data['sales'] as $sale)
                    @foreach($sale->details as $d)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td class="font-monospace">{{ $sale->nomor_transaksi }}</td>
                        <td>{{ $sale->tanggal_transaksi->format('d/m/Y') }}</td>
                        <td>{{ $d->product->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $d->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga_beli * $d->jumlah, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format(($d->harga_jual - $d->harga_beli) * $d->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

    @elseif($tab === 'purchases')
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No. PO</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="text-end">Total Pembelian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['purchases'] as $idx => $p)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="font-monospace">{{ $p->nomor_pembelian }}</td>
                    <td>{{ $p->tanggal_pembelian->format('d/m/Y') }}</td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td>{{ $p->metode_pembayaran }}</td>
                    <td>{{ $p->status_pembayaran }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($p->total_pembelian, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($tab === 'stock_out')
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th>Alasan</th>
                    <th class="text-center">Total Item Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['stockOuts'] as $idx => $so)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="font-monospace">{{ $so->nomor_transaksi }}</td>
                    <td>{{ $so->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $so->user->name ?? '-' }}</td>
                    <td>{{ $so->alasan }}</td>
                    <td class="text-center font-monospace fw-bold text-danger">-{{ $so->total_item }}</td>
                    <td>{{ $so->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($tab === 'inventory')
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-end">Valuasi Modal</th>
                    <th class="text-end">Potensi Omset</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['products'] as $idx => $p)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="font-monospace">{{ $p->kode_barang }}</td>
                    <td>{{ $p->nama_barang }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->satuan }}</td>
                    <td class="text-end">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ $p->stok }}</td>
                    <td class="text-end">Rp {{ number_format($p->stok * $p->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($p->stok * $p->harga_jual, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($tab === 'top_products')
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Ranking</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah Terjual</th>
                    <th class="text-end">Total Omset</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['topProducts'] as $rank => $item)
                <tr>
                    <td class="text-center fw-bold">#{{ $rank + 1 }}</td>
                    <td class="font-monospace">{{ $item->product->kode_barang ?? '-' }}</td>
                    <td>{{ $item->product->nama_barang ?? '-' }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $item->total_sold }} {{ $item->product->satuan ?? 'unit' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="row text-center mt-5">
        <div class="col-6">
            <p class="mb-4">Mengetahui,</p>
            <br><br>
            <strong>( Pimpinan / Pemilik Toko )</strong>
        </div>
        <div class="col-6">
            <p class="mb-4">Petugas Pembuat Laporan,</p>
            <br><br>
            <strong>( {{ auth()->user()->name }} )</strong>
        </div>
    </div>
</body>
</html>

