@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Dashboard')
@section('page_subtitle', 'Statistik dan performa toko ATK hari ini')

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat 1: Total Produk -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Produk</span>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($totalProducts) }}</h3>
            <span class="text-muted" style="font-size: 0.78rem;">Jenis barang ATK aktif</span>
        </div>
    </div>

    <!-- Stat 2: Total Stok -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total Stok</span>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($totalStock) }}</h3>
            <span class="text-muted" style="font-size: 0.78rem;">Unit fisik di gudang/toko</span>
        </div>
    </div>

    <!-- Stat 3: Transaksi Hari Ini -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Transaksi Hari Ini</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($totalTrxToday) }}</h3>
            <span class="text-muted" style="font-size: 0.78rem;">Struk penjualan hari ini</span>
        </div>
    </div>

    <!-- Stat 4: Pendapatan Hari Ini -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Omset Hari Ini</span>
                <div class="stat-icon bg-indigo bg-opacity-10 text-primary">
                    <i class="fa-solid fa-rupiah-sign"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-truncate" title="Rp {{ number_format($totalRevenueToday, 0, ',', '.') }}">
                Rp {{ number_format($totalRevenueToday, 0, ',', '.') }}
            </h4>
            <span class="text-muted" style="font-size: 0.78rem;">Penerimaan kas hari ini</span>
        </div>
    </div>

    <!-- Stat 5: Keuntungan Kotor Hari Ini -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Laba Kotor</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-chart-line-up"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-success text-truncate" title="Rp {{ number_format($netProfitToday, 0, ',', '.') }}">
                Rp {{ number_format($netProfitToday, 0, ',', '.') }}
            </h4>
            <span class="text-muted" style="font-size: 0.78rem;">Penjualan - HPP modal</span>
        </div>
    </div>

    <!-- Stat 6: Stok Menipis -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card stat-card bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Stok Menipis</span>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ number_format($lowStockCount) }}</h3>
            <span class="text-muted" style="font-size: 0.78rem;">Barang butuh restock</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-3 mb-4">
    <!-- Chart 1: Penjualan 7 Hari Terakhir -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-area text-primary"></i>
                    <span>Grafik Penjualan Harian (7 Hari Terakhir)</span>
                </div>
                <small class="text-muted">Total Omset</small>
            </div>
            <div class="card-body">
                <canvas id="dailySalesChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Penjualan Bulanan -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-column text-success"></i>
                    <span>Grafik Penjualan Bulanan ({{ date('Y') }})</span>
                </div>
                <small class="text-muted">Tahun Berjalan</small>
            </div>
            <div class="card-body">
                <canvas id="monthlySalesChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Produk Terlaris -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-fire-flame-curved text-danger"></i>
                    <span>Produk Terlaris</span>
                </div>
                <a href="{{ route('reports.index', ['tab' => 'top_products']) }}" class="small text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Total Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->product->nama_barang ?? 'Produk Dihapus' }}</div>
                                    <small class="text-muted">{{ $item->product->kode_barang ?? '-' }} &bull; {{ $item->product->satuan ?? 'Pcs' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                        {{ $item->total_sold }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-primary">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-box-open d-block fs-3 mb-2 opacity-50"></i>
                                    Belum ada data penjualan tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang Stok Menipis -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span class="text-dark">Peringatan Stok Menipis & Habis</span>
                </div>
                <a href="{{ route('stocks.index', ['status' => 'menipis']) }}" class="small text-decoration-none">Kelola Stok</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $prod)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $prod->nama_barang }}</div>
                                    <small class="text-muted">{{ $prod->kode_barang }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">{{ $prod->category->name ?? '-' }}</span>
                                </td>
                                <td class="text-center fw-bold {{ $prod->stok == 0 ? 'text-danger' : 'text-warning' }}">
                                    {{ $prod->stok }} {{ $prod->satuan }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $prod->stock_status_badge['class'] }}">
                                        {{ $prod->stock_status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-primary" title="Beli Barang">
                                        <i class="fa-solid fa-cart-plus"></i> Beli
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-circle-check text-success fs-3 d-block mb-2"></i>
                                    Semua stok barang dalam kondisi aman.
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Daily Sales Chart
        const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Omset Penjualan (Rp)',
                    data: {!! json_encode($dailyData) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Omset: Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + ' Jt';
                                if (value >= 1000) return (value / 1000) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // Monthly Sales Chart
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Omset Bulanan (Rp)',
                    data: {!! json_encode($monthlyData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Omset: Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + ' Jt';
                                if (value >= 1000) return (value / 1000) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

