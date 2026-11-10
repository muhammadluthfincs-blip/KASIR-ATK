<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Rekap Shift - {{ $cashier->name }} ({{ $date }})</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 8px;
            width: 58mm;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .d-flex { display: flex; justify-content: space-between; }
        .border-dashed { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin: 5px 0; }
        .btn-print {
            padding: 6px 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print text-center" style="margin-bottom: 10px;">
        <button class="btn-print" onclick="window.print()">Cetak Struk Rekap</button>
    </div>

    <div class="text-center">
        <div class="fw-bold" style="font-size: 14px;">ATK POS</div>
        <div>Toko Alat Tulis Kantor</div>
        <div style="font-size: 10px;">LAPORAN REKAP CLOSING SHIFT</div>
    </div>

    <div class="border-dashed">
        <div class="d-flex"><span>Kasir:</span><span class="fw-bold">{{ $cashier->name }}</span></div>
        <div class="d-flex"><span>Tanggal:</span><span>{{ $date }}</span></div>
        <div class="d-flex"><span>Jam Cetak:</span><span>{{ date('H:i:s') }}</span></div>
        <div class="d-flex"><span>Jml Transaksi:</span><span>{{ $sales->count() }} Struk</span></div>
    </div>

    <div>
        <div class="d-flex fw-bold"><span>Uang Fisik di Laci:</span><span>Rp {{ number_format($cashInDrawer, 0, ',', '.') }}</span></div>
        <div class="d-flex"><span>Total Non-Tunai:</span><span>Rp {{ number_format($nonCash, 0, ',', '.') }}</span></div>
        <div class="d-flex"><span>Total Barang:</span><span>{{ $totalItemsSold }} Pcs</span></div>
        <div class="d-flex"><span>Total Diskon:</span><span>-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span></div>
    </div>

    <div class="border-dashed fw-bold" style="font-size: 13px;">
        <div class="d-flex"><span>TOTAL OMSET:</span><span>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></div>
    </div>

    <div style="margin-top: 18px; display: flex; justify-content: space-between; text-align: center; font-size: 10px;">
        <div style="width: 48%;">
            Kasir,<br><br><br>
            ({{ $cashier->name }})
        </div>
        <div style="width: 48%;">
            Owner,<br><br><br>
            (..............)
        </div>
    </div>
</body>
</html>
