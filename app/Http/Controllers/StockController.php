<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Stock overview list.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $status = $request->query('status'); // 'aman', 'menipis', 'habis'

        $categories = Category::orderBy('name')->get();

        // Stats
        $totalProducts = Product::count();
        $safeCount = Product::whereColumn('stok', '>', 'stok_minimum')->count();
        $lowCount = Product::where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum')->count();
        $outOfStockCount = Product::where('stok', '<=', 0)->count();

        $query = Product::with('category')
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($q, $categoryId) {
                return $q->where('category_id', $categoryId);
            })
            ->when($status, function ($q, $status) {
                if ($status === 'habis') {
                    return $q->where('stok', '<=', 0);
                } elseif ($status === 'menipis') {
                    return $q->where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum');
                } elseif ($status === 'aman') {
                    return $q->whereColumn('stok', '>', 'stok_minimum');
                }
            });

        $products = $query->orderBy('stok', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('stocks.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'status',
            'totalProducts',
            'safeCount',
            'lowCount',
            'outOfStockCount'
        ));
    }

    /**
     * Kartu Stok (Stock Card) per product.
     */
    public function card(Request $request, Product $product)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type');

        $product->load('category');

        $movementsQuery = StockMovement::with('user')
            ->where('product_id', $product->id)
            ->when($startDate, function ($q, $startDate) {
                return $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q, $endDate) {
                return $q->whereDate('tanggal', '<=', $endDate);
            })
            ->when($type, function ($q, $type) {
                return $q->where('tipe', $type);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $totalIn = (clone $movementsQuery)->sum('jumlah_masuk');
        $totalOut = (clone $movementsQuery)->sum('jumlah_keluar');

        $movements = $movementsQuery->paginate(20)->withQueryString();

        return view('stocks.card', compact('product', 'movements', 'startDate', 'endDate', 'type', 'totalIn', 'totalOut'));
    }
}

