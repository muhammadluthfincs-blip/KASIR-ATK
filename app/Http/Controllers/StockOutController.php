<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOutDetail;
use App\Models\StockOutTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $reason = $request->query('reason');

        $transactions = StockOutTransaction::with('user', 'details.product')
            ->when($search, function ($q, $search) {
                return $q->where('nomor_transaksi', 'like', "%{$search}%");
            })
            ->when($startDate, function ($q, $startDate) {
                return $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q, $endDate) {
                return $q->whereDate('tanggal', '<=', $endDate);
            })
            ->when($reason, function ($q, $reason) {
                return $q->where('alasan', $reason);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('stock_outs.index', compact('transactions', 'search', 'startDate', 'endDate', 'reason'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->where('stok', '>', 0)->orderBy('nama_barang')->get();
        $nextOutNumber = $this->generateOutNumber();

        return view('stock_outs.create', compact('products', 'nextOutNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'alasan' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal pengeluaran barang wajib diisi.',
            'alasan.required' => 'Alasan barang keluar wajib dipilih/diisi.',
            'items.required' => 'Pilih minimal 1 barang untuk dikeluarkan.',
            'items.min' => 'Pilih minimal 1 barang untuk dikeluarkan.',
            'items.*.jumlah.min' => 'Jumlah barang keluar minimal 1.',
        ]);

        try {
            $transaction = DB::transaction(function () use ($request) {
                $nomorTransaksi = $this->generateOutNumber();
                $totalItem = 0;

                // Validate stock availability
                $validatedItems = [];
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($product->stok < $item['jumlah']) {
                        throw new \Exception("Stok tidak mencukupi untuk '{$product->nama_barang}'. Sisa stok: {$product->stok}, diminta: {$item['jumlah']}.");
                    }

                    $totalItem += $item['jumlah'];
                    $validatedItems[] = [
                        'product' => $product,
                        'jumlah' => $item['jumlah'],
                        'keterangan' => $item['keterangan'] ?? null,
                    ];
                }

                $transaction = StockOutTransaction::create([
                    'nomor_transaksi' => $nomorTransaksi,
                    'tanggal' => $request->tanggal,
                    'user_id' => Auth::id(),
                    'alasan' => $request->alasan,
                    'total_item' => $totalItem,
                    'keterangan' => $request->keterangan,
                ]);

                foreach ($validatedItems as $itemData) {
                    $product = $itemData['product'];
                    $jumlah = $itemData['jumlah'];

                    StockOutDetail::create([
                        'stock_out_transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'jumlah' => $jumlah,
                        'keterangan' => $itemData['keterangan'],
                    ]);

                    // Decrement stock
                    $product->stok -= $jumlah;
                    $product->save();

                    // Record Stock Movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                        'tipe' => 'keluar',
                        'referensi' => $transaction->nomor_transaksi,
                        'jumlah_masuk' => 0,
                        'jumlah_keluar' => $jumlah,
                        'sisa_stok' => $product->stok,
                        'alasan' => $request->alasan,
                        'keterangan' => $itemData['keterangan'] ?: $request->keterangan,
                    ]);
                }

                return $transaction;
            });

            return redirect()->route('stock-outs.show', $transaction->id)->with('success', 'Transaksi barang keluar ' . $transaction->nomor_transaksi . ' berhasil dicatat!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StockOutTransaction $stockOut)
    {
        $stockOut->load('user', 'details.product');
        return view('stock_outs.show', compact('stockOut'));
    }

    private function generateOutNumber(): string
    {
        $datePrefix = 'OUT-' . date('Ymd') . '-';
        $lastOut = StockOutTransaction::where('nomor_transaksi', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOut) {
            $lastSeq = (int) substr($lastOut->nomor_transaksi, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        return $datePrefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

