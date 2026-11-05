<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $status = $request->query('status'); // 'aman', 'menipis', 'habis'

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('kode_barang', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($status, function ($query, $status) {
                if ($status === 'habis') {
                    return $query->where('stok', '<=', 0);
                } elseif ($status === 'menipis') {
                    return $query->where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum');
                } elseif ($status === 'aman') {
                    return $query->whereColumn('stok', '>', 'stok_minimum');
                }
            })
            ->orderBy('nama_barang', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'status'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $nextCode = $this->generateProductCode();

        return view('products.create', compact('categories', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'nullable|string|max:50|unique:products,kode_barang',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'nama_barang' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:30',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli.',
            'stok.required' => 'Stok awal wajib diisi.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'barcode.unique' => 'Barcode sudah digunakan.',
        ]);

        if (empty($validated['kode_barang'])) {
            $validated['kode_barang'] = $this->generateProductCode();
        }

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if ($product->stok > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'tanggal' => now(),
                    'tipe' => 'penyesuaian',
                    'referensi' => 'STOK-AWAL',
                    'jumlah_masuk' => $product->stok,
                    'jumlah_keluar' => 0,
                    'sisa_stok' => $product->stok,
                    'alasan' => 'Penyesuaian Stok',
                    'keterangan' => 'Saldo awal penambahan produk baru',
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category', 'stockMovements.user');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('products', 'kode_barang')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($product->id)],
            'nama_barang' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|max:30',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleDetails()->count() > 0 || $product->purchaseDetails()->count() > 0) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $product->stockMovements()->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function searchJson(Request $request)
    {
        $q = $request->query('q');

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('nama_barang', 'like', "%{$q}%")
                      ->orWhere('kode_barang', 'like', "%{$q}%")
                      ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    private function generateProductCode(): string
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        return 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}

