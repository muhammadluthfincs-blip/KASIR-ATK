<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display POS screen.
     */
    public function pos()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('nama_barang', 'asc')
            ->get();

        $nextTrxNumber = $this->generateTrxNumber();

        return view('sales.pos', compact('categories', 'products', 'nextTrxNumber'));
    }

    /**
     * Store a new sale transaction via AJAX or Form Submit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'diskon' => 'nullable|numeric|min:0',
            'nominal_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:Tunai,QRIS,Transfer Bank,Debit',
            'catatan' => 'nullable|string',
        ], [
            'items.required' => 'Keranjang belanja masih kosong.',
            'items.min' => 'Keranjang belanja minimal harus memiliki 1 barang.',
            'nominal_bayar.required' => 'Nominal uang pembayaran wajib diisi.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $totalItems = 0;

                // Validate each item and check stock availability with row lock
                $validatedItems = [];
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if (!$product) {
                        throw new \Exception("Produk dengan ID {$item['product_id']} tidak ditemukan.");
                    }

                    if ($product->stok < $item['jumlah']) {
                        throw new \Exception("Stok tidak mencukupi untuk '{$product->nama_barang}'. Sisa stok: {$product->stok}, diminta: {$item['jumlah']}.");
                    }

                    $itemSubtotal = $product->harga_jual * $item['jumlah'];
                    $subtotal += $itemSubtotal;
                    $totalItems += $item['jumlah'];

                    $validatedItems[] = [
                        'product' => $product,
                        'jumlah' => $item['jumlah'],
                        'harga_beli' => $product->harga_beli, // historical HPP
                        'harga_jual' => $product->harga_jual,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $diskon = floatval($request->diskon ?? 0);
                if ($diskon > $subtotal) {
                    $diskon = $subtotal;
                }
                $totalBayar = $subtotal - $diskon;
                $nominalBayar = floatval($request->nominal_bayar);

                // If non-tunai and nominal bayar is 0, auto-fill with totalBayar
                if ($request->metode_pembayaran !== 'Tunai' && $nominalBayar < $totalBayar) {
                    $nominalBayar = $totalBayar;
                }

                if ($nominalBayar < $totalBayar) {
                    throw new \Exception("Uang pembayaran kurang! Total tagihan: Rp " . number_format($totalBayar, 0, ',', '.') . ", dibayar: Rp " . number_format($nominalBayar, 0, ',', '.'));
                }

                $kembalian = $nominalBayar - $totalBayar;
                $nomorTransaksi = $this->generateTrxNumber();

                // Create Sale Header
                $sale = Sale::create([
                    'nomor_transaksi' => $nomorTransaksi,
                    'tanggal_transaksi' => now(),
                    'user_id' => Auth::id(),
                    'total_item' => $totalItems,
                    'subtotal' => $subtotal,
                    'diskon' => $diskon,
                    'total_bayar' => $totalBayar,
                    'nominal_bayar' => $nominalBayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'catatan' => $request->catatan,
                ]);

                // Create Details & Update Stock & Record Movements
                foreach ($validatedItems as $itemData) {
                    $product = $itemData['product'];
                    $jumlah = $itemData['jumlah'];

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'jumlah' => $jumlah,
                        'harga_beli' => $itemData['harga_beli'],
                        'harga_jual' => $itemData['harga_jual'],
                        'subtotal' => $itemData['subtotal'],
                    ]);

                    // Decrement stock
                    $product->stok -= $jumlah;
                    $product->save();

                    // Record Stock Movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'tanggal' => now(),
                        'tipe' => 'penjualan',
                        'referensi' => $sale->nomor_transaksi,
                        'jumlah_masuk' => 0,
                        'jumlah_keluar' => $jumlah,
                        'sisa_stok' => $product->stok,
                        'alasan' => 'Penjualan Kasir POS',
                        'keterangan' => 'Penjualan ke pelanggan (' . $sale->nomor_transaksi . ')',
                    ]);
                }

                return $sale;
            });

            if ($request->ajax() || $request->wantsJson()) {
                $sale->load('user', 'details.product');
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan!',
                    'sale' => $sale,
                    'receipt_url' => route('sales.receipt', $sale->id),
                ]);
            }

            return redirect()->route('sales.receipt', $sale->id)->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display Sales History.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $paymentMethod = $request->query('payment_method');
        $cashierId = $request->query('cashier_id');

        $cashiers = User::where('role', 'kasir')->orWhere('role', 'admin')->orderBy('name')->get();

        $query = Sale::with('user', 'details.product')
            ->when($search, function ($q, $search) {
                return $q->where('nomor_transaksi', 'like', "%{$search}%");
            })
            ->when($startDate, function ($q, $startDate) {
                return $q->whereDate('tanggal_transaksi', '>=', $startDate);
            })
            ->when($endDate, function ($q, $endDate) {
                return $q->whereDate('tanggal_transaksi', '<=', $endDate);
            })
            ->when($paymentMethod, function ($q, $paymentMethod) {
                return $q->where('metode_pembayaran', $paymentMethod);
            })
            ->when($cashierId, function ($q, $cashierId) {
                return $q->where('user_id', $cashierId);
            });

        // Summary calculations for filtered list
        $summary = [
            'total_transactions' => (clone $query)->count(),
            'total_sales' => (clone $query)->sum('total_bayar'),
            'total_discount' => (clone $query)->sum('diskon'),
        ];

        $sales = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', compact('sales', 'cashiers', 'search', 'startDate', 'endDate', 'paymentMethod', 'cashierId', 'summary'));
    }

    /**
     * Display Sale details.
     */
    public function show(Sale $sale)
    {
        $sale->load('user', 'details.product.category');
        return view('sales.show', compact('sale'));
    }

    /**
     * Thermal Receipt View.
     */
    public function receipt(Sale $sale)
    {
        $sale->load('user', 'details.product');
        return view('sales.receipt', compact('sale'));
    }

    private function generateTrxNumber(): string
    {
        $datePrefix = 'TRX-' . date('Ymd') . '-';
        $lastSale = Sale::where('nomor_transaksi', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastSeq = (int) substr($lastSale->nomor_transaksi, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        return $datePrefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

