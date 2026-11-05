<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->nomor_transaksi }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1.1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=1.1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1.1">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #f1f5f9;
            padding: 20px;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            border: 1px dashed #94a3b8;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h3 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 10px;
            line-height: 1.3;
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .meta-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 6px;
        }
        .meta-table td {
            padding: 1px 0;
        }
        .item-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
        }
        .item-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .summary-table {
            width: 100%;
            font-size: 11px;
            margin-top: 6px;
        }
        .summary-table td {
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            line-height: 1.4;
        }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12px;
            font-family: Arial, sans-serif;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #1e293b;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .receipt-container {
                max-width: 100%;
                border: none;
                box-shadow: none;
                padding: 5px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
        <button class="btn" onclick="window.close()">Tutup</button>
    </div>

    <div class="receipt-container">
        <div class="header">
            <h3>ATK POS</h3>
            <p>Toko Alat Tulis Kantor & Fotocopy</p>
            <p>Jl. Raya Utama No. 88, Telp: 0812-3456-7890</p>
        </div>

        <div class="dashed-line"></div>

        <table class="meta-table">
            <tr>
                <td>No. Transaksi</td>
                <td>: <span class="bold">{{ $sale->nomor_transaksi }}</span></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $sale->tanggal_transaksi->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $sale->user->name ?? 'Kasir' }}</td>
            </tr>
            <tr>
                <td>Metode</td>
                <td>: {{ $sale->metode_pembayaran }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <table class="item-table">
            @foreach($sale->details as $item)
            <tr>
                <td colspan="3" class="bold">{{ $item->product->nama_barang ?? 'Barang' }}</td>
            </tr>
            <tr>
                <td style="width: 50%;">{{ $item->jumlah }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td style="width: 10%;"></td>
                <td class="text-right bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <table class="summary-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($sale->diskon > 0)
            <tr>
                <td>Diskon Transaksi</td>
                <td class="text-right">- Rp {{ number_format($sale->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="bold" style="font-size: 13px;">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar ({{ $sale->metode_pembayaran }})</td>
                <td class="text-right">Rp {{ number_format($sale->nominal_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr class="bold">
                <td>Kembalian</td>
                <td class="text-right">Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <div class="footer">
            <p class="bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p>Barang yang sudah dibeli<br>tidak dapat ditukar / dikembalikan</p>
            <p style="margin-top: 5px;">Simpan struk ini sebagai bukti pembayaran yang sah</p>
        </div>
    </div>

</body>
</html>

