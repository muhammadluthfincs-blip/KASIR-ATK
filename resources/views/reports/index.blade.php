@extends('layouts.app')

@section('title', 'Laporan & Keuntungan')
@section('page_title', 'Laporan Operasional & Analisa Keuntungan')
@section('page_subtitle', 'Laporan komprehensif penjualan, pembelian, pergerakan stok, dan laba kotor toko')

@section('content')
<!-- Filter & Export Action Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-md-3 col-6">
                <label class="form-label small text-muted mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small text-muted mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 col-12 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fa-solid fa-filter me-1"></i> Terapkan Periode
                </button>
            </div>
            <div class="col-md-4 col-12 d-flex justify-content-md-end align-items-end gap-2">
                <a href="{{ route('reports.export-excel', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-file-excel me-1"></i> Ekspor ke Excel
                </a>
                <a href="{{ route('reports.print', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-print me-1"></i> Cetak / PDF
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'sales' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-receipt me-1"></i> Penjualan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'profit' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'profit', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-chart-line me-1"></i> Keuntungan Kotor
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'purchases' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'purchases', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-cart-flatbed me-1"></i> Pembelian Supplier
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'stock_out' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'stock_out', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-boxes-packing me-1"></i> Barang Keluar
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'inventory' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'inventory', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-cubes me-1"></i> Valuasi Persediaan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'top_products' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('reports.index', ['tab' => 'top_products', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            <i class="fa-solid fa-fire me-1"></i> Produk Terlaris
        </a>
    </li>
</ul>

<!-- Report Contents -->
<div class="card">
    @if($tab === 'sales')
        <!-- TAB 1: Penjualan -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Laporan Penjualan ({{ $startDate }} s/d {{ $endDate }})</span>
            <span class="badge bg-primary">{{ $data['totalTrx'] }} Transaksi</span>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 border-bottom bg-light p-3">
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Total Transaksi:</span>
                    <strong class="fs-5 text-dark">{{ $data['totalTrx'] }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Item Terjual:</span>
                    <strong class="fs-5 text-dark">{{ $data['totalItemsSold'] }} unit</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Total Diskon:</span>
                    <strong class="fs-5 text-danger">Rp {{ number_format($data['totalDiscount'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <span class="small text-muted d-block">Total Omset Pendapatan:</span>
                    <strong class="fs-5 text-primary">Rp {{ number_format($data['totalRevenue'], 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal & Waktu</th>
                            <th>Kasir</th>
                            <th class="text-center">Total Item</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Diskon</th>
                            <th class="text-end">Total Bayar</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['sales'] as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-monospace fw-bold">{{ $sale->nomor_transaksi }}</td>
                            <td>{{ $sale->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td class="text-center font-monospace">{{ $sale->total_item }}</td>
                            <td class="text-end">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                            <td class="text-end text-muted">{{ $sale->diskon > 0 ? 'Rp ' . number_format($sale->diskon, 0, ',', '.') : '-' }}</td>
                            <td class="text-end fw-bold text-primary">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sale->metode_pembayaran }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi penjualan pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'profit')
        <!-- TAB 2: Keuntungan Kotor -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Laporan Estimasi Keuntungan Kotor (Laba Penjualan)</span>
            <span class="badge bg-success">Margin: {{ number_format($data['marginPercent'], 2) }}%</span>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 border-bottom bg-light p-3">
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Total Omset Penjualan:</span>
                    <strong class="fs-5 text-primary">Rp {{ number_format($data['totalRevenue'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Total Modal Pokok (HPP):</span>
                    <strong class="fs-5 text-secondary">Rp {{ number_format($data['totalHpp'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Laba Kotor Bersih:</span>
                    <strong class="fs-5 text-success">Rp {{ number_format($data['grossProfit'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <span class="small text-muted d-block">Persentase Margin:</span>
                    <strong class="fs-5 text-info">{{ number_format($data['marginPercent'], 2) }}%</strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Produk Terjual</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">HPP / Beli</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-end">Total HPP</th>
                            <th class="text-end">Total Jual</th>
                            <th class="text-end text-success">Laba Kotor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($data['sales'] as $sale)
                            @foreach($sale->details as $d)
                            @php
                                $itemHpp = $d->harga_beli * $d->jumlah;
                                $itemJual = $d->subtotal;
                                $itemProfit = $itemJual - $itemHpp;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td class="font-monospace small">{{ $sale->nomor_transaksi }}</td>
                                <td>{{ $sale->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $d->product->nama_barang ?? 'Produk Dihapus' }}</td>
                                <td class="text-center font-monospace">{{ $d->jumlah }}</td>
                                <td class="text-end text-muted small">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end small">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-end text-muted">Rp {{ number_format($itemHpp, 0, ',', '.') }}</td>
                                <td class="text-end text-dark">Rp {{ number_format($itemJual, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($itemProfit, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada transaksi pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'purchases')
        <!-- TAB 3: Pembelian Supplier -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Laporan Pembelian Barang Masuk</span>
            <span class="badge bg-primary">Total: Rp {{ number_format($data['totalPurchase'], 0, ',', '.') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Petugas</th>
                            <th>Metode</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Total Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['purchases'] as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-monospace fw-bold">{{ $p->nomor_pembelian }}</td>
                            <td>{{ $p->tanggal_pembelian->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $p->supplier->name ?? '-' }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $p->metode_pembayaran }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $p->status_pembayaran === 'Lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $p->status_pembayaran }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($p->total_pembelian, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada data pembelian pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'stock_out')
        <!-- TAB 4: Barang Keluar -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Laporan Barang Keluar (Non-Penjualan)</span>
            <span class="badge bg-danger">Total: {{ $data['totalItemOut'] }} item</span>
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
                            <th>Alasan</th>
                            <th class="text-center">Total Item</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['stockOuts'] as $index => $so)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-monospace fw-bold">{{ $so->nomor_transaksi }}</td>
                            <td>{{ $so->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $so->user->name ?? '-' }}</td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger border">{{ $so->alasan }}</span></td>
                            <td class="text-center font-monospace fw-bold text-danger">-{{ $so->total_item }}</td>
                            <td class="small text-muted">{{ $so->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada data barang keluar pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'inventory')
        <!-- TAB 5: Valuasi Persediaan -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Laporan Valuasi dan Posisi Persediaan Stok</span>
            <span class="badge bg-info">Total Fisik: {{ number_format($data['totalStock']) }} unit</span>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 border-bottom bg-light p-3">
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Total Fisik Persediaan:</span>
                    <strong class="fs-5 text-dark">{{ number_format($data['totalStock']) }} unit</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Nilai Modal (Aset Beli):</span>
                    <strong class="fs-5 text-secondary">Rp {{ number_format($data['totalAssetCost'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center border-end">
                    <span class="small text-muted d-block">Potensi Nilai Jual:</span>
                    <strong class="fs-5 text-primary">Rp {{ number_format($data['totalAssetSale'], 0, ',', '.') }}</strong>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <span class="small text-muted d-block">Proyeksi Laba Bersih:</span>
                    <strong class="fs-5 text-success">Rp {{ number_format($data['projectedProfit'], 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
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
                        @foreach($data['products'] as $index => $prod)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-monospace small fw-bold">{{ $prod->kode_barang }}</td>
                            <td class="fw-semibold">{{ $prod->nama_barang }}</td>
                            <td><span class="badge bg-light text-secondary border">{{ $prod->category->name ?? '-' }}</span></td>
                            <td>{{ $prod->satuan }}</td>
                            <td class="text-end text-muted small">Rp {{ number_format($prod->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-end text-muted small">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center font-monospace fw-bold {{ $prod->stok <= 0 ? 'text-danger' : 'text-dark' }}">{{ $prod->stok }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($prod->stok * $prod->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-primary">Rp {{ number_format($prod->stok * $prod->harga_jual, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'top_products')
        <!-- TAB 6: Produk Terlaris -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-bold">Peringkat Produk Paling Laris Terjual</span>
            <span class="badge bg-success">Periode: {{ $startDate }} s/d {{ $endDate }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;" class="text-center">Ranking</th>
                            <th>Kode Barang</th>
                            <th>Nama Produk ATK</th>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah Terjual</th>
                            <th class="text-end">Total Nilai Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['topProducts'] as $rank => $item)
                        <tr>
                            <td class="text-center">
                                @if($rank === 0)
                                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-crown me-1"></i> #1</span>
                                @elseif($rank === 1)
                                    <span class="badge bg-secondary text-white fs-6">#2</span>
                                @elseif($rank === 2)
                                    <span class="badge bg-danger bg-opacity-75 text-white fs-6">#3</span>
                                @else
                                    <span class="fw-bold text-muted">#{{ $rank + 1 }}</span>
                                @endif
                            </td>
                            <td class="font-monospace small">{{ $item->product->kode_barang ?? '-' }}</td>
                            <td class="fw-semibold">{{ $item->product->nama_barang ?? 'Produk Dihapus' }}</td>
                            <td><span class="badge bg-light text-secondary border">{{ $item->product->category->name ?? '-' }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 fs-6">
                                    {{ $item->total_sold }} {{ $item->product->satuan ?? 'unit' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-primary fs-6">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada penjualan pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

