<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockOutTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reports index with tabs/subpages.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'sales'); // sales, profit, purchases, stock_out, inventory, top_products
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $data = [];

        switch ($tab) {
            case 'profit':
                // Gross profit: Sales between start and end date
                $sales = Sale::with('details.product')
                    ->whereDate('tanggal_transaksi', '>=', $startDate)
                    ->whereDate('tanggal_transaksi', '<=', $endDate)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->get();

                $totalRevenue = $sales->sum('total_bayar');
                $totalDiscount = $sales->sum('diskon');

                $totalHpp = 0;
                foreach ($sales as $sale) {
                    foreach ($sale->details as $detail) {
                        $totalHpp += $detail->harga_beli * $detail->jumlah;
                    }
                }

                $grossProfit = $totalRevenue - $totalHpp;
                $marginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

                $data = [
                    'sales' => $sales,
                    'totalRevenue' => $totalRevenue,
                    'totalDiscount' => $totalDiscount,
                    'totalHpp' => $totalHpp,
                    'grossProfit' => $grossProfit,
                    'marginPercent' => $marginPercent,
                ];
                break;

            case 'purchases':
                $purchases = Purchase::with('supplier', 'user', 'details.product')
                    ->whereDate('tanggal_pembelian', '>=', $startDate)
                    ->whereDate('tanggal_pembelian', '<=', $endDate)
                    ->orderBy('tanggal_pembelian', 'desc')
                    ->get();

                $totalPurchase = $purchases->sum('total_pembelian');

                $data = [
                    'purchases' => $purchases,
                    'totalPurchase' => $totalPurchase,
                ];
                break;

            case 'stock_out':
                $stockOuts = StockOutTransaction::with('user', 'details.product')
                    ->whereDate('tanggal', '>=', $startDate)
                    ->whereDate('tanggal', '<=', $endDate)
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $totalItemOut = $stockOuts->sum('total_item');

                $data = [
                    'stockOuts' => $stockOuts,
                    'totalItemOut' => $totalItemOut,
                ];
                break;

            case 'inventory':
                $products = Product::with('category')->orderBy('nama_barang')->get();

                $totalStock = $products->sum('stok');
                $totalAssetCost = $products->sum(function ($p) {
                    return $p->stok * $p->harga_beli;
                });
                $totalAssetSale = $products->sum(function ($p) {
                    return $p->stok * $p->harga_jual;
                });
                $projectedProfit = $totalAssetSale - $totalAssetCost;

                $data = [
                    'products' => $products,
                    'totalStock' => $totalStock,
                    'totalAssetCost' => $totalAssetCost,
                    'totalAssetSale' => $totalAssetSale,
                    'projectedProfit' => $projectedProfit,
                ];
                break;

            case 'top_products':
                $topProducts = SaleDetail::select('product_id', DB::raw('SUM(jumlah) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
                    ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                        $q->whereDate('tanggal_transaksi', '>=', $startDate)
                          ->whereDate('tanggal_transaksi', '<=', $endDate);
                    })
                    ->with('product.category')
                    ->groupBy('product_id')
                    ->orderByDesc('total_sold')
                    ->get();

                $data = [
                    'topProducts' => $topProducts,
                ];
                break;

            case 'sales':
            default:
                $sales = Sale::with('user', 'details.product')
                    ->whereDate('tanggal_transaksi', '>=', $startDate)
                    ->whereDate('tanggal_transaksi', '<=', $endDate)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->get();

                $totalTrx = $sales->count();
                $totalRevenue = $sales->sum('total_bayar');
                $totalDiscount = $sales->sum('diskon');
                $totalItemsSold = $sales->sum('total_item');

                $data = [
                    'sales' => $sales,
                    'totalTrx' => $totalTrx,
                    'totalRevenue' => $totalRevenue,
                    'totalDiscount' => $totalDiscount,
                    'totalItemsSold' => $totalItemsSold,
                ];
                break;
        }

        return view('reports.index', compact('tab', 'startDate', 'endDate', 'data'));
    }

    /**
     * Print View for Reports.
     */
    public function print(Request $request)
    {
        $tab = $request->query('tab', 'sales');
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        // Gather report data same as index
        $data = $this->getReportData($tab, $startDate, $endDate);

        return view('reports.print', compact('tab', 'startDate', 'endDate', 'data'));
    }

    /**
     * Export Report to Excel (CSV with UTF-8 BOM).
     */
    public function exportExcel(Request $request)
    {
        $tab = $request->query('tab', 'sales');
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $data = $this->getReportData($tab, $startDate, $endDate);

        $filename = 'laporan-' . $tab . '-' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tab, $data, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');

            // Insert UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ATK POS - SISTEM KASIR DAN MANAJEMEN STOK']);
            fputcsv($handle, ['LAPORAN: ' . strtoupper($tab)]);
            fputcsv($handle, ['PERIODE: ' . $startDate . ' s/d ' . $endDate]);
            fputcsv($handle, []); // blank line

            if ($tab === 'sales') {
                fputcsv($handle, ['No', 'No. Transaksi', 'Tanggal & Waktu', 'Kasir', 'Total Item', 'Subtotal (Rp)', 'Diskon (Rp)', 'Total Bayar (Rp)', 'Metode']);
                $no = 1;
                foreach ($data['sales'] as $sale) {
                    fputcsv($handle, [
                        $no++,
                        $sale->nomor_transaksi,
                        $sale->tanggal_transaksi->format('d/m/Y H:i'),
                        $sale->user->name ?? '-',
                        $sale->total_item,
                        $sale->subtotal,
                        $sale->diskon,
                        $sale->total_bayar,
                        $sale->metode_pembayaran,
                    ]);
                }
                fputcsv($handle, []);
                fputcsv($handle, ['Total Transaksi', $data['totalTrx']]);
                fputcsv($handle, ['Total Item Terjual', $data['totalItemsSold']]);
                fputcsv($handle, ['Total Diskon', $data['totalDiscount']]);
                fputcsv($handle, ['Total Omset Pendapatan', $data['totalRevenue']]);

            } elseif ($tab === 'profit') {
                fputcsv($handle, ['No', 'No. Transaksi', 'Tanggal', 'Item', 'Qty', 'Harga Beli (HPP)', 'Harga Jual', 'Total HPP (Rp)', 'Total Jual (Rp)', 'Laba Kotor (Rp)']);
                $no = 1;
                foreach ($data['sales'] as $sale) {
                    foreach ($sale->details as $d) {
                        $hpp = $d->harga_beli * $d->jumlah;
                        $jual = $d->subtotal;
                        $laba = $jual - $hpp;
                        fputcsv($handle, [
                            $no++,
                            $sale->nomor_transaksi,
                            $sale->tanggal_transaksi->format('d/m/Y H:i'),
                            $d->product->nama_barang ?? '-',
                            $d->jumlah,
                            $d->harga_beli,
                            $d->harga_jual,
                            $hpp,
                            $jual,
                            $laba,
                        ]);
                    }
                }
                fputcsv($handle, []);
                fputcsv($handle, ['Total Pendapatan Penjualan', $data['totalRevenue']]);
                fputcsv($handle, ['Total HPP / Modal Pokok', $data['totalHpp']]);
                fputcsv($handle, ['Estimasi Keuntungan Kotor', $data['grossProfit']]);
                fputcsv($handle, ['Margin Keuntungan', number_format($data['marginPercent'], 2) . '%']);

            } elseif ($tab === 'purchases') {
                fputcsv($handle, ['No', 'No. PO Pembelian', 'Tanggal', 'Supplier', 'Metode', 'Status', 'Total (Rp)']);
                $no = 1;
                foreach ($data['purchases'] as $p) {
                    fputcsv($handle, [
                        $no++,
                        $p->nomor_pembelian,
                        $p->tanggal_pembelian->format('d/m/Y'),
                        $p->supplier->name ?? '-',
                        $p->metode_pembayaran,
                        $p->status_pembayaran,
                        $p->total_pembelian,
                    ]);
                }
                fputcsv($handle, []);
                fputcsv($handle, ['Total Pembelian', $data['totalPurchase']]);

            } elseif ($tab === 'stock_out') {
                fputcsv($handle, ['No', 'No. Transaksi', 'Tanggal', 'Operator', 'Alasan', 'Total Item', 'Keterangan']);
                $no = 1;
                foreach ($data['stockOuts'] as $so) {
                    fputcsv($handle, [
                        $no++,
                        $so->nomor_transaksi,
                        $so->tanggal->format('d/m/Y'),
                        $so->user->name ?? '-',
                        $so->alasan,
                        $so->total_item,
                        $so->keterangan ?? '-',
                    ]);
                }
                fputcsv($handle, []);
                fputcsv($handle, ['Total Item Keluar', $data['totalItemOut']]);

            } elseif ($tab === 'inventory') {
                fputcsv($handle, ['No', 'Kode', 'Nama Barang', 'Kategori', 'Satuan', 'Harga Beli (Rp)', 'Harga Jual (Rp)', 'Stok', 'Nilai Beli (Aset)', 'Nilai Jual (Potensi Omset)', 'Status']);
                $no = 1;
                foreach ($data['products'] as $p) {
                    fputcsv($handle, [
                        $no++,
                        $p->kode_barang,
                        $p->nama_barang,
                        $p->category->name ?? '-',
                        $p->satuan,
                        $p->harga_beli,
                        $p->harga_jual,
                        $p->stok,
                        $p->stok * $p->harga_beli,
                        $p->stok * $p->harga_jual,
                        $p->stock_status,
                    ]);
                }
                fputcsv($handle, []);
                fputcsv($handle, ['Total Jumlah Stok Fisik', $data['totalStock']]);
                fputcsv($handle, ['Total Nilai Modal Persediaan (Aset)', $data['totalAssetCost']]);
                fputcsv($handle, ['Total Potensi Nilai Jual', $data['totalAssetSale']]);
                fputcsv($handle, ['Proyeksi Laba Bersih Persediaan', $data['projectedProfit']]);

            } elseif ($tab === 'top_products') {
                fputcsv($handle, ['Ranking', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah Terjual', 'Total Omset (Rp)']);
                $rank = 1;
                foreach ($data['topProducts'] as $item) {
                    fputcsv($handle, [
                        $rank++,
                        $item->product->kode_barang ?? '-',
                        $item->product->nama_barang ?? '-',
                        $item->product->category->name ?? '-',
                        $item->total_sold,
                        $item->total_revenue,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(string $tab, string $startDate, string $endDate): array
    {
        switch ($tab) {
            case 'profit':
                $sales = Sale::with('details.product')
                    ->whereDate('tanggal_transaksi', '>=', $startDate)
                    ->whereDate('tanggal_transaksi', '<=', $endDate)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->get();

                $totalRevenue = $sales->sum('total_bayar');
                $totalDiscount = $sales->sum('diskon');

                $totalHpp = 0;
                foreach ($sales as $sale) {
                    foreach ($sale->details as $detail) {
                        $totalHpp += $detail->harga_beli * $detail->jumlah;
                    }
                }

                $grossProfit = $totalRevenue - $totalHpp;
                $marginPercent = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

                return [
                    'sales' => $sales,
                    'totalRevenue' => $totalRevenue,
                    'totalDiscount' => $totalDiscount,
                    'totalHpp' => $totalHpp,
                    'grossProfit' => $grossProfit,
                    'marginPercent' => $marginPercent,
                ];

            case 'purchases':
                $purchases = Purchase::with('supplier', 'user', 'details.product')
                    ->whereDate('tanggal_pembelian', '>=', $startDate)
                    ->whereDate('tanggal_pembelian', '<=', $endDate)
                    ->orderBy('tanggal_pembelian', 'desc')
                    ->get();

                return [
                    'purchases' => $purchases,
                    'totalPurchase' => $purchases->sum('total_pembelian'),
                ];

            case 'stock_out':
                $stockOuts = StockOutTransaction::with('user', 'details.product')
                    ->whereDate('tanggal', '>=', $startDate)
                    ->whereDate('tanggal', '<=', $endDate)
                    ->orderBy('tanggal', 'desc')
                    ->get();

                return [
                    'stockOuts' => $stockOuts,
                    'totalItemOut' => $stockOuts->sum('total_item'),
                ];

            case 'inventory':
                $products = Product::with('category')->orderBy('nama_barang')->get();
                $totalStock = $products->sum('stok');
                $totalAssetCost = $products->sum(fn ($p) => $p->stok * $p->harga_beli);
                $totalAssetSale = $products->sum(fn ($p) => $p->stok * $p->harga_jual);

                return [
                    'products' => $products,
                    'totalStock' => $totalStock,
                    'totalAssetCost' => $totalAssetCost,
                    'totalAssetSale' => $totalAssetSale,
                    'projectedProfit' => $totalAssetSale - $totalAssetCost,
                ];

            case 'top_products':
                $topProducts = SaleDetail::select('product_id', DB::raw('SUM(jumlah) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
                    ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                        $q->whereDate('tanggal_transaksi', '>=', $startDate)
                          ->whereDate('tanggal_transaksi', '<=', $endDate);
                    })
                    ->with('product.category')
                    ->groupBy('product_id')
                    ->orderByDesc('total_sold')
                    ->get();

                return [
                    'topProducts' => $topProducts,
                ];

            case 'sales':
            default:
                $sales = Sale::with('user', 'details.product')
                    ->whereDate('tanggal_transaksi', '>=', $startDate)
                    ->whereDate('tanggal_transaksi', '<=', $endDate)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->get();

                return [
                    'sales' => $sales,
                    'totalTrx' => $sales->count(),
                    'totalRevenue' => $sales->sum('total_bayar'),
                    'totalDiscount' => $sales->sum('diskon'),
                    'totalItemsSold' => $sales->sum('total_item'),
                ];
        }
    }
}

