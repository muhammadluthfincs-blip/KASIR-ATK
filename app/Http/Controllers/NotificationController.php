<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display System Notification Center.
     */
    public function index(Request $request)
    {
        $isMarkedRead = $request->session()->get('notifications_marked_read', false);

        $notifications = [];

        if (!$isMarkedRead) {
            // 1. Out of stock products
            $outOfStock = Product::with('category')->where('stok', '<=', 0)->get();
            foreach ($outOfStock as $p) {
                $notifications[] = [
                    'id' => 'oos_' . $p->id,
                    'type' => 'danger',
                    'icon' => 'fa-circle-xmark',
                    'title' => 'Stok Barang Habis',
                    'message' => "Stok produk '{$p->nama_barang}' ({$p->kode_barang}) telah habis (0 {$p->satuan}). Segera lakukan pemesanan ke supplier.",
                    'time' => $p->updated_at ? $p->updated_at->diffForHumans() : 'Hari ini',
                    'url' => route('stocks.card', $p->id),
                    'action_label' => 'Lihat Kartu Stok',
                ];
            }

            // 2. Low stock products
            $lowStock = Product::with('category')
                ->where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->get();
            foreach ($lowStock as $p) {
                $notifications[] = [
                    'id' => 'low_' . $p->id,
                    'type' => 'warning',
                    'icon' => 'fa-triangle-exclamation',
                    'title' => 'Peringatan Stok Menipis',
                    'message' => "Stok produk '{$p->nama_barang}' tersisa {$p->stok} {$p->satuan} (Batas minimum: {$p->stok_minimum}).",
                    'time' => $p->updated_at ? $p->updated_at->diffForHumans() : 'Hari ini',
                    'url' => route('stocks.card', $p->id),
                    'action_label' => 'Cek Stok',
                ];
            }

            // 3. Recent Sales Today
            $recentSales = Sale::whereDate('tanggal_transaksi', Carbon::today())
                ->orderBy('tanggal_transaksi', 'desc')
                ->take(5)
                ->get();
            foreach ($recentSales as $s) {
                $notifications[] = [
                    'id' => 'sale_' . $s->id,
                    'type' => 'info',
                    'icon' => 'fa-receipt',
                    'title' => 'Transaksi Penjualan Berhasil',
                    'message' => "Struk {$s->nomor_transaksi} berhasil dibayar senilai Rp " . number_format($s->total_bayar, 0, ',', '.') . " via {$s->metode_pembayaran}.",
                    'time' => $s->tanggal_transaksi ? $s->tanggal_transaksi->diffForHumans() : 'Baru saja',
                    'url' => route('sales.show', $s->id),
                    'action_label' => 'Lihat Struk',
                ];
            }
        }

        return view('notifications.index', compact('notifications', 'isMarkedRead'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        $request->session()->put('notifications_marked_read', true);

        return redirect()->route('notifications.index')->with('success', 'Semua pemberitahuan sistem telah ditandai sebagai sudah dibaca.');
    }
}
