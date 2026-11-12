@extends('layouts.app')

@section('title', 'Laporan Harian Kasir')
@section('page_title', 'Laporan Harian Kasir (Closing Shift)')
@section('page_subtitle', 'Rekapitulasi penjualan, kas masuk, dan rincian item per kasir per hari')

@section('styles')
<style>
    .shift-report-banner {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .shift-tag {
        font-size: 0.72rem;
        letter-spacing: 1.5px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }
    .shift-cashier-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }
    .shift-total-amount {
        font-size: 2.2rem;
        font-weight: 800;
        color: #eab308;
        letter-spacing: -0.5px;
    }
    .closing-stat-card {
        border-radius: 12px;
        padding: 20px 22px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .closing-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .payment-method-row {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.15s ease;
    }
    .payment-method-row:last-child {
        border-bottom: none;
    }
    .payment-method-row:hover {
        background-color: #f8fafc;
    }
</style>
@endsection

@section('content')
<!-- Filter & Action Header -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-3">
        <form action="{{ route('cashier.closing') }}" method="GET" class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-semibold text-secondary small text-nowrap">Tanggal Transaksi:</label>
                    <div class="input-group input-group-sm" style="width: 170px;">
                        <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                </div>

                @if(auth()->user()->role === 'admin')
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-semibold text-secondary small text-nowrap">Kasir:</label>
                    <select name="cashier_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 180px;">
                        <option value="">-- Semua Kasir --</option>
                        @foreach($cashiersList as $c)
                            <option value="{{ $c->id }}" {{ $cashier->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <!-- Action Buttons: Thermal & A4 -->
            <div class="d-flex align-items-center gap-2 ms-auto">
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#thermalClosingModal">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Struk Rekap (Thermal)</span>
                </button>
                <a href="{{ route('cashier.closing.print-a4', ['date' => $date, 'cashier_id' => $cashier->id]) }}" target="_blank" class="btn btn-sm btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Cetak Laporan Lengkap (A4)</span>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Shift Report Main Banner -->
<div class="shift-report-banner mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="shift-tag mb-1">Shift Report</div>
            <h3 class="shift-cashier-title mb-1">Laporan Closing: {{ $cashier->name }}</h3>
            <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
        <div class="text-end">
            <span class="shift-total-amount font-monospace">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<!-- 4 Closing Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Uang Tunai di Laci -->
    <div class="col-xxl-3 col-md-6 col-12">
        <div class="closing-stat-card border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Uang Tunai di Laci (Fisik)</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-money-bill-wave fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-success font-monospace" style="font-size: 1.55rem;">Rp {{ number_format($cashInDrawer, 0, ',', '.') }}</h3>
            <span class="text-muted d-block small">Diserahkan ke Owner</span>
        </div>
    </div>

    <!-- Card 2: Non-Tunai -->
    <div class="col-xxl-3 col-md-6 col-12">
        <div class="closing-stat-card border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Non-Tunai (Digital/Bank)</span>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-qrcode fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-primary font-monospace" style="font-size: 1.55rem;">Rp {{ number_format($nonCash, 0, ',', '.') }}</h3>
            <span class="text-muted d-block small">QRIS, Transfer, Debit</span>
        </div>
    </div>

    <!-- Card 3: Total Barang Terjual -->
    <div class="col-xxl-3 col-md-6 col-12">
        <div class="closing-stat-card border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Total Barang Terjual</span>
                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-box-open fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.55rem;">{{ number_format($totalItemsSold) }} Item</h3>
            <span class="text-muted d-block small">Unit produk ATK terjual</span>
        </div>
    </div>

    <!-- Card 4: Total Potongan Diskon -->
    <div class="col-xxl-3 col-md-6 col-12">
        <div class="closing-stat-card border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Total Potongan Diskon</span>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-tags fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-warning font-monospace" style="font-size: 1.55rem;">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h3>
            <span class="text-muted d-block small">Diskon transaksi hari ini</span>
        </div>
    </div>
</div>

<!-- Details: Payment Breakdown & Completed Transactions -->
<div class="row g-3">
    <!-- Left Column: Payment Methods -->
    <div class="col-lg-5 col-12">
        <div class="card h-100 shadow-sm border-0" style="border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fa-solid fa-wallet text-primary"></i>
                <span class="fw-bold text-dark">Rincian Metode Pembayaran</span>
            </div>
            <div class="card-body p-0">
                @foreach($paymentBreakdown as $key => $method)
                <div class="payment-method-row">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-solid {{ $method['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">{{ $method['name'] }}</div>
                            <small class="text-muted">{{ $method['count'] }} transaksi</small>
                        </div>
                    </div>
                    <div class="text-end fw-bold font-monospace text-dark fs-6">
                        Rp {{ number_format($method['total'], 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-light p-3 border-top d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">Total Pemasukan Shift:</span>
                <span class="fw-bold text-primary fs-5 font-monospace">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Right Column: Completed Transactions List -->
    <div class="col-lg-7 col-12">
        <div class="card h-100 shadow-sm border-0" style="border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary"></i>
                    <span class="fw-bold text-dark">Daftar Transaksi Selesai ({{ $totalSalesCount }})</span>
                </div>
                <small class="text-muted">{{ $cashier->name }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Waktu</th>
                                <th class="text-center">Item</th>
                                <th>Metode</th>
                                <th class="text-end">Total Bayar</th>
                                <th class="text-center" style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $s)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $s->id) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                        {{ $s->nomor_transaksi }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $s->tanggal_transaksi ? $s->tanggal_transaksi->format('H:i') . ' WIB' : '-' }}</span>
                                </td>
                                <td class="text-center font-monospace">{{ $s->total_item }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $s->metode_pembayaran }}</span>
                                </td>
                                <td class="text-end fw-bold font-monospace text-dark">
                                    Rp {{ number_format($s->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('sales.receipt', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary p-1 px-2" title="Cetak Struk">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada transaksi selesai pada tanggal ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thermal Rekap Closing Shift -->
<div class="modal fade" id="thermalClosingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fa-solid fa-receipt me-2"></i>Struk Rekap Closing Shift</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="thermal-receipt shadow-sm mx-auto p-3 bg-white" style="max-width: 380px; font-family: 'Courier New', Courier, monospace; font-size: 13px; border: 1px dashed #cbd5e1;" id="printableThermalClosing">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold mb-0">ATK POS</h6>
                        <small>Rekap Closing Shift Kasir</small><br>
                        <small>{{ date('d/m/Y H:i:s') }}</small>
                    </div>

                    <div class="border-top border-bottom py-2 my-2" style="border-style: dashed !important;">
                        <div class="d-flex justify-content-between">
                            <span>Kasir:</span>
                            <strong>{{ $cashier->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tanggal:</span>
                            <span>{{ $date }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Struk:</span>
                            <strong>{{ $totalSalesCount }} Transaksi</strong>
                        </div>
                    </div>

                    <div class="py-1">
                        <div class="d-flex justify-content-between text-success fw-bold">
                            <span>Uang Tunai di Laci:</span>
                            <span>Rp {{ number_format($cashInDrawer, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-primary">
                            <span>Total Non-Tunai:</span>
                            <span>Rp {{ number_format($nonCash, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Barang Terjual:</span>
                            <span>{{ $totalItemsSold }} Pcs</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger">
                            <span>Total Diskon:</span>
                            <span>-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-top border-bottom py-2 my-2 fw-bold" style="border-style: dashed !important; font-size: 14px;">
                        <div class="d-flex justify-content-between">
                            <span>TOTAL OMSET:</span>
                            <span>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 d-flex justify-content-between text-center" style="font-size: 11px;">
                        <div style="width: 45%;">
                            Kasir Bertugas,<br><br><br>
                            ( {{ $cashier->name }} )
                        </div>
                        <div style="width: 45%;">
                            Owner / Supervisor,<br><br><br>
                            ( .................... )
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('cashier.closing.print-thermal', ['date' => $date, 'cashier_id' => $cashier->id]) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-print me-1"></i> Cetak ke Printer Thermal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

