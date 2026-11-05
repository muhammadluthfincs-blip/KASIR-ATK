<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\StockMovement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplierId = $request->query('supplier_id');
        $status = $request->query('status');

        $suppliers = Supplier::orderBy('name')->get();

        $purchases = Purchase::with('supplier', 'user', 'details.product')
            ->when($search, function ($query, $search) {
                return $query->where('nomor_pembelian', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('tanggal_pembelian', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('tanggal_pembelian', '<=', $endDate);
            })
            ->when($supplierId, function ($query, $supplierId) {
                return $query->where('supplier_id', $supplierId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status_pembayaran', $status);
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'startDate', 'endDate', 'supplierId', 'status'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('nama_barang')->get();
        $nextPoNumber = $this->generatePoNumber();

        return view('purchases.create', compact('suppliers', 'products', 'nextPoNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'required|in:Tunai,Transfer Bank,Tempo',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ], [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'items.required' => 'Minimal harus menambahkan 1 barang.',
            'items.min' => 'Minimal harus menambahkan 1 barang.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
        ]);

        $purchase = DB::transaction(function () use ($request) {
            $nomorPembelian = $this->generatePoNumber();
            $totalPembelian = 0;

            foreach ($request->items as $item) {
                $totalPembelian += $item['jumlah'] * $item['harga_beli'];
            }

            $purchase = Purchase::create([
                'nomor_pembelian' => $nomorPembelian,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'total_pembelian' => $totalPembelian,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli'];

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                // Update product stock and harga_beli
                $product = Product::lockForUpdate()->find($item['product_id']);
                $product->stok += $item['jumlah'];
                $product->harga_beli = $item['harga_beli']; // Update latest purchase price
                $product->save();

                // Record Stock Movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'tanggal' => Carbon::parse($request->tanggal_pembelian)->setTimeFrom(now()),
                    'tipe' => 'pembelian',
                    'referensi' => $purchase->nomor_pembelian,
                    'jumlah_masuk' => $item['jumlah'],
                    'jumlah_keluar' => 0,
                    'sisa_stok' => $product->stok,
                    'alasan' => 'Pembelian dari Supplier',
                    'keterangan' => 'Barang masuk dari supplier ' . ($purchase->supplier->name ?? ''),
                ]);
            }

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase->id)->with('success', 'Transaksi pembelian ' . $purchase->nomor_pembelian . ' berhasil disimpan!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'user', 'details.product');
        return view('purchases.show', compact('purchase'));
    }

    public function print(Purchase $purchase)
    {
        $purchase->load('supplier', 'user', 'details.product');
        return view('purchases.print', compact('purchase'));
    }

    private function generatePoNumber(): string
    {
        $datePrefix = 'PO-' . date('Ymd') . '-';
        $lastPo = Purchase::where('nomor_pembelian', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPo) {
            $lastSeq = (int) substr($lastPo->nomor_pembelian, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        return $datePrefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

