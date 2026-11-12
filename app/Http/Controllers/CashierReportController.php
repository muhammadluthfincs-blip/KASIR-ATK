<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierReportController extends Controller
{
    /**
     * Display the Daily Cashier Shift Closing Report.
     */
    public function closing(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));

        // If user is cashier, only view their own transactions; if admin, allow filtering cashier
        $cashierId = $user->role === 'kasir' ? $user->id : $request->query('cashier_id', $user->id);
        $cashier = User::find($cashierId) ?: $user;
        $cashiersList = $user->role === 'admin' ? User::where('role', 'kasir')->get() : collect([$user]);

        $query = Sale::with(['details.product', 'user'])
            ->whereDate('tanggal_transaksi', $date);

        if ($user->role === 'kasir' || ($request->has('cashier_id') && $cashierId)) {
            $query->where('user_id', $cashierId);
        }

        $sales = $query->orderBy('tanggal_transaksi', 'desc')->get();

        // Calculations for Shift Report
        $totalSalesCount = $sales->count();
        $totalRevenue = $sales->sum('total_bayar');
        $cashInDrawer = $sales->where('metode_pembayaran', 'Tunai')->sum('total_bayar');
        $nonCash = $sales->whereIn('metode_pembayaran', ['QRIS', 'Transfer Bank', 'Debit'])->sum('total_bayar');
        $totalItemsSold = $sales->sum('total_item');
        $totalDiscount = $sales->sum('diskon');

        // Payment Methods Breakdown
        $methods = ['Tunai', 'QRIS', 'Transfer Bank', 'Debit'];
        $paymentBreakdown = [];
        foreach ($methods as $m) {
            $group = $sales->where('metode_pembayaran', $m);
            $paymentBreakdown[$m] = [
                'name' => $m === 'Tunai' ? 'Tunai (Cash)' : $m,
                'count' => $group->count(),
                'total' => $group->sum('total_bayar'),
                'icon' => match ($m) {
                    'Tunai' => 'fa-money-bill-wave text-success',
                    'QRIS' => 'fa-qrcode text-primary',
                    'Transfer Bank' => 'fa-building-columns text-info',
                    'Debit' => 'fa-credit-card text-warning',
                    default => 'fa-wallet text-secondary',
                },
            ];
        }

        return view('cashier.closing', compact(
            'date',
            'cashier',
            'cashiersList',
            'sales',
            'totalSalesCount',
            'totalRevenue',
            'cashInDrawer',
            'nonCash',
            'totalItemsSold',
            'totalDiscount',
            'paymentBreakdown'
        ));
    }

    /**
     * Thermal Receipt Format for Closing Shift.
     */
    public function printThermal(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $cashierId = $user->role === 'kasir' ? $user->id : $request->query('cashier_id', $user->id);
        $cashier = User::find($cashierId) ?: $user;

        $sales = Sale::with(['details.product'])
            ->whereDate('tanggal_transaksi', $date)
            ->where('user_id', $cashier->id)
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        $totalRevenue = $sales->sum('total_bayar');
        $cashInDrawer = $sales->where('metode_pembayaran', 'Tunai')->sum('total_bayar');
        $nonCash = $sales->whereIn('metode_pembayaran', ['QRIS', 'Transfer Bank', 'Debit'])->sum('total_bayar');
        $totalItemsSold = $sales->sum('total_item');
        $totalDiscount = $sales->sum('diskon');

        return view('cashier.print_thermal', compact(
            'date',
            'cashier',
            'sales',
            'totalRevenue',
            'cashInDrawer',
            'nonCash',
            'totalItemsSold',
            'totalDiscount'
        ));
    }

    /**
     * A4 Printable Format for Closing Shift.
     */
    public function printA4(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $cashierId = $user->role === 'kasir' ? $user->id : $request->query('cashier_id', $user->id);
        $cashier = User::find($cashierId) ?: $user;

        $sales = Sale::with(['details.product'])
            ->whereDate('tanggal_transaksi', $date)
            ->where('user_id', $cashier->id)
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        $totalRevenue = $sales->sum('total_bayar');
        $cashInDrawer = $sales->where('metode_pembayaran', 'Tunai')->sum('total_bayar');
        $nonCash = $sales->whereIn('metode_pembayaran', ['QRIS', 'Transfer Bank', 'Debit'])->sum('total_bayar');
        $totalItemsSold = $sales->sum('total_item');
        $totalDiscount = $sales->sum('diskon');

        return view('cashier.print_a4', compact(
            'date',
            'cashier',
            'sales',
            'totalRevenue',
            'cashInDrawer',
            'nonCash',
            'totalItemsSold',
            'totalDiscount'
        ));
    }
}

