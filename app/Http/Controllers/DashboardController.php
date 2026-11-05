<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Stat cards
        $totalProducts = Product::count();
        $totalStock = Product::sum('stok');

        // Sales today
        $salesTodayQuery = Sale::whereDate('tanggal_transaksi', $today);
        $totalTrxToday = (clone $salesTodayQuery)->count();
        $totalRevenueToday = (clone $salesTodayQuery)->sum('total_bayar');

        // Gross Profit today = Sum of (harga_jual - harga_beli) * jumlah for today's sales - discount
        $todaySaleIds = (clone $salesTodayQuery)->pluck('id');
        $profitToday = SaleDetail::whereIn('sale_id', $todaySaleIds)
            ->selectRaw('SUM((harga_jual - harga_beli) * jumlah) as gross_profit')
            ->value('gross_profit') ?? 0;
        
        $totalDiscountToday = (clone $salesTodayQuery)->sum('diskon');
        $netProfitToday = max(0, $profitToday - $totalDiscountToday);

        // Low stock products (stok <= stok_minimum)
        $lowStockProducts = Product::with('category')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->limit(10)
            ->get();
        $lowStockCount = Product::whereColumn('stok', '<=', 'stok_minimum')->count();

        // Top 5 selling products (all time or this month)
        $topProducts = SaleDetail::select('product_id', DB::raw('SUM(jumlah) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // 2. Chart data: Daily sales (last 7 days)
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyLabels[] = $date->translatedFormat('d M');
            $dailyData[] = Sale::whereDate('tanggal_transaksi', $date)->sum('total_bayar');
        }

        // 3. Chart data: Monthly sales (this year)
        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];
        $currentYear = Carbon::now()->year;
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[] = Sale::whereYear('tanggal_transaksi', $currentYear)
                ->whereMonth('tanggal_transaksi', $month)
                ->sum('total_bayar');
        }

        // Recent 5 transactions
        $recentSales = Sale::with('user', 'details.product')
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'totalTrxToday',
            'totalRevenueToday',
            'netProfitToday',
            'lowStockProducts',
            'lowStockCount',
            'topProducts',
            'dailyLabels',
            'dailyData',
            'monthlyLabels',
            'monthlyData',
            'recentSales'
        ));
    }
}

