<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Closing Shift Kasir - {{ $cashier->name }} ({{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }})</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1.1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #1e293b; font-family: system-ui, -apple-system, sans-serif; }
        .table th, .table td { padding: 6px 10px; }
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .signature-line {
            width: 180px;
            border-bottom: 1px dashed #64748b;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4" onload="window.print()">
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-3">Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm px-3">Tutup</button>
    </div>

    <!-- Header Dokumen -->
    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0 text-primary">ATK POS</h3>
            <div class="fw-medium">Sistem Kasir dan Manajemen Stok Alat Tulis Kantor</div>
            <div class="small text-muted">Jl. Raya Utama No. 88 • Telp: 0812-3456-7890</div>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-1 text-uppercase text-dark">LAPORAN CLOSING SHIFT KASIR</h4>
            <div class="small">Tanggal: <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</strong></div>
            <div class="small text-muted">Kasir Bertugas: <strong>{{ $cashier->name }}</strong></div>
            <div class="small text-muted">Waktu Cetak: {{ date('d/m/Y H:i:s') }} WIB</div>
        </div>
    </div>

    <!-- Ringkasan Shift -->
    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="summary-box">
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Uang Tunai di Laci (Fisik)</div>
                <div class="fs-5 fw-bold text-success mt-1">Rp {{ number_format($cashInDrawer, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">Diserahkan ke Owner/Supervisi</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Non-Tunai (Digital/Bank)</div>
                <div class="fs-5 fw-bold text-primary mt-1">Rp {{ number_format($nonCash, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">QRIS, Transfer, Debit</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Item Terjual</div>
                <div class="fs-5 fw-bold text-dark mt-1">{{ $totalItemsSold }} <span class="fs-6 fw-normal text-muted">Unit</span></div>
                <div class="text-muted" style="font-size: 0.7rem;">Dari {{ $sales->count() }} transaksi</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Omset Shift</div>
                <div class="fs-5 fw-bold text-warning-emphasis mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 0.7rem;">Diskon: Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Rincian Metode Pembayaran -->
    <div class="mb-4">
        <h6 class="fw-bold text-secondary text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Rincian Penerimaan Kas & Non-Kas</h6>
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Metode Pembayaran</th>
                    <th class="text-center" style="width: 150px;">Jumlah Transaksi</th>
                    <th class="text-end" style="width: 200px;">Total Penerimaan</th>
                    <th class="text-end" style="width: 120px;">Porsi (%)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $methods = [
                        'Tunai' => 'Tunai (Cash)',
                        'QRIS' => 'QRIS',
                        'Transfer Bank' => 'Transfer Bank',
                        'Debit' => 'Kartu Debit / EDC'
                    ];
                @endphp
                @foreach($methods as $key => $label)
                    @php
                        $mSales = $sales->where('metode_pembayaran', $key);
                        $mCount = $mSales->count();
                        $mTotal = $mSales->sum('total_bayar');
                        $percent = $totalRevenue > 0 ? ($mTotal / $totalRevenue) * 100 : 0;
                    @endphp
                    <tr>
                        <td class="fw-medium">{{ $label }}</td>
                        <td class="text-center">{{ $mCount }} transaksi</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($mTotal, 0, ',', '.') }}</td>
                        <td class="text-end text-muted">{{ number_format($percent, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>TOTAL PENERIMAAN SHIFT</td>
                    <td class="text-center">{{ $sales->count() }} transaksi</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td class="text-end">100.0%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Daftar Transaksi Kasir -->
    <div class="mb-4">
        <h6 class="fw-bold text-secondary text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Daftar Transaksi Selesai ({{ $sales->count() }} Transaksi)</h6>
        <table class="table table-bordered table-sm table-striped">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>No. Transaksi</th>
                    <th class="text-center" style="width: 80px;">Waktu</th>
                    <th class="text-center" style="width: 60px;">Item</th>
                    <th class="text-end" style="width: 110px;">Subtotal</th>
                    <th class="text-end" style="width: 90px;">Diskon</th>
                    <th class="text-end" style="width: 120px;">Total Bayar</th>
                    <th class="text-center" style="width: 100px;">Metode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-monospace fw-semibold">{{ $s->nomor_transaksi }}</td>
                    <td class="text-center">{{ $s->tanggal_transaksi->format('H:i') }} WIB</td>
                    <td class="text-center">{{ $s->total_item }}</td>
                    <td class="text-end">Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $s->diskon > 0 ? 'Rp ' . number_format($s->diskon, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($s->total_bayar, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $s->metode_pembayaran }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-muted">Belum ada transaksi tercatat pada shift ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($sales->count() > 0)
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="3" class="text-end">TOTAL:</td>
                    <td class="text-center">{{ $totalItemsSold }}</td>
                    <td class="text-end">Rp {{ number_format($sales->sum('subtotal'), 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Tanda Tangan Serah Terima Kas -->
    <div class="row mt-5 pt-3">
        <div class="col-6 text-center">
            <div class="small text-muted mb-1">Kasir yang Bertugas,</div>
            <div class="signature-line mx-auto"></div>
            <div class="fw-bold">{{ $cashier->name }}</div>
            <div class="small text-muted">NIP / ID: {{ $cashier->id }}</div>
        </div>
        <div class="col-6 text-center">
            <div class="small text-muted mb-1">Mengetahui (Owner / Supervisor),</div>
            <div class="signature-line mx-auto"></div>
            <div class="fw-bold">( ........................................ )</div>
            <div class="small text-muted">Penanggung Jawab Toko</div>
        </div>
    </div>
</body>
</html>