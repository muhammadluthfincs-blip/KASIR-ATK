@extends('layouts.app')

@section('title', 'Kasir POS')
@section('page_title', 'Kasir Penjualan (POS)')
@section('page_subtitle', 'Point of Sale modern untuk transaksi penjualan kasir toko ATK')

@section('styles')
<style>
    .pos-product-card {
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        padding: 14px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .pos-product-card:hover:not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.12);
        border-color: #3b82f6;
    }
    .pos-product-card.disabled {
        opacity: 0.55;
        cursor: not-allowed;
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }
    .pos-product-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.6em;
        margin-bottom: 4px;
    }
    .pos-product-price {
        font-size: 0.98rem;
        font-weight: 700;
        color: #2563eb;
        line-height: 1.2;
    }
    .pos-add-btn {
        font-size: 0.76rem;
        padding: 5px 10px;
        border-radius: 6px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .pos-product-card:hover:not(.disabled) .pos-add-btn {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .product-grid-container {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    }
    .product-grid-container::-webkit-scrollbar {
        width: 6px;
    }
    .product-grid-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .product-grid-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 6px;
    }
    .product-grid-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    #categoryPills::-webkit-scrollbar {
        height: 5px;
    }
    #categoryPills::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }
    #categoryPills::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .category-pill {
        cursor: pointer;
        border-radius: 20px;
        font-size: 0.83rem;
        font-weight: 500;
        padding: 7px 16px;
        white-space: nowrap;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .category-pill:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .category-pill.active {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        transform: translateY(-1px);
    }
    .category-pill.active i {
        color: #ffffff !important;
    }
    .cart-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        background-color: #f8fafc;
    }
    .cart-table td {
        padding: 10px 8px;
    }
    .quick-cash-btn {
        font-size: 0.78rem;
        padding: 6px 8px;
        border-radius: 6px;
    }
    .thermal-receipt {
        max-width: 380px;
        margin: 0 auto;
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        line-height: 1.4;
        background: #ffffff;
        padding: 20px;
        border: 1px dashed #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="row g-3">
    <!-- Left Column: Product Search & Catalog -->
    <div class="col-xxl-8 col-xl-7 col-lg-7 col-12">
        <!-- Search & Barcode Input -->
        <div class="card mb-3 shadow-sm border-0 bg-white" style="border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-2 p-sm-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-primary ps-3">
                        <i class="fa-solid fa-barcode fs-4"></i>
                    </span>
                    <input type="text" id="posSearchInput" class="form-control form-control-lg border-start-0 border-end-0 ps-1" placeholder="Ketik nama, kode barang, atau scan barcode..." autofocus autocomplete="off">
                    <span class="input-group-text bg-white border-start-0 border-end-0 text-muted px-2 d-none d-sm-flex align-items-center">
                        <kbd class="bg-light text-secondary border font-monospace shadow-sm" style="font-size: 0.72rem; padding: 2px 6px;">F2</kbd>
                    </span>
                    <button class="btn btn-outline-secondary border-start-0 px-3" type="button" id="clearSearchBtn" title="Bersihkan pencarian (Esc)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Pills with Distinctive Retail Icons -->
        <div class="d-flex align-items-center gap-2 overflow-auto pb-2 mb-3" id="categoryPills">
            <button type="button" class="category-pill active" data-category="">
                <i class="fa-solid fa-layer-group text-primary"></i>
                <span>Semua Kategori</span>
            </button>
            @foreach($categories as $cat)
                @php
                    $catLower = strtolower($cat->name);
                    $icon = 'fa-tag';
                    $color = '#64748b';
                    if (str_contains($catLower, 'tulis') || str_contains($catLower, 'pena') || str_contains($catLower, 'pensil')) {
                        $icon = 'fa-pen-ruler';
                        $color = '#6366f1';
                    } elseif (str_contains($catLower, 'kertas') || str_contains($catLower, 'buku') || str_contains($catLower, 'hvs')) {
                        $icon = 'fa-file-lines';
                        $color = '#0284c7';
                    } elseif (str_contains($catLower, 'gambar') || str_contains($catLower, 'lukis') || str_contains($catLower, 'warna')) {
                        $icon = 'fa-palette';
                        $color = '#f59e0b';
                    } elseif (str_contains($catLower, 'kantor') || str_contains($catLower, 'stapler') || str_contains($catLower, 'ordner')) {
                        $icon = 'fa-briefcase';
                        $color = '#10b981';
                    } elseif (str_contains($catLower, 'printer') || str_contains($catLower, 'tinta') || str_contains($catLower, 'toner')) {
                        $icon = 'fa-print';
                        $color = '#8b5cf6';
                    } elseif (str_contains($catLower, 'arsip') || str_contains($catLower, 'map') || str_contains($catLower, 'folder')) {
                        $icon = 'fa-folder-open';
                        $color = '#ec4899';
                    }
                @endphp
                <button type="button" class="category-pill" data-category="{{ $cat->id }}">
                    <i class="fa-solid {{ $icon }}" style="color: {{ $color }};"></i>
                    <span>{{ $cat->name }}</span>
                </button>
            @endforeach
        </div>

        <!-- Product Grid Wrapped Container -->
        <div class="product-grid-container pe-1">
            <div class="row g-2" id="productGrid">
                @foreach($products as $prod)
                <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 product-item"
                     data-id="{{ $prod->id }}"
                     data-nama="{{ strtolower($prod->nama_barang) }}"
                     data-kode="{{ strtolower($prod->kode_barang) }}"
                     data-barcode="{{ strtolower($prod->barcode) }}"
                     data-category="{{ $prod->category_id }}"
                     data-harga="{{ $prod->harga_jual }}"
                     data-stok="{{ $prod->stok }}"
                     data-satuan="{{ $prod->satuan }}">
                    <div class="pos-product-card {{ $prod->stok <= 0 ? 'disabled' : '' }}" onclick="addToCart({{ $prod->id }})">
                        <div>
                            <!-- Header: Code & Stock -->
                            <div class="d-flex justify-content-between align-items-center gap-1 mb-2">
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">
                                    {{ $prod->kode_barang }}
                                </span>
                                <span class="badge {{ $prod->stok <= 0 ? 'bg-danger' : ($prod->stok <= $prod->stok_minimum ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success border border-success border-opacity-25') }}" style="font-size: 0.7rem;">
                                    Stok: {{ $prod->stok }}
                                </span>
                            </div>

                            <!-- Title (2-line clamp) -->
                            <h6 class="pos-product-title" title="{{ $prod->nama_barang }}">
                                {{ $prod->nama_barang }}
                            </h6>

                            <!-- Category -->
                            <div class="mb-2">
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.72rem;">
                                    {{ $prod->category->name ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Footer: Price & Add Button -->
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                            <div>
                                <span class="text-muted d-block lh-1" style="font-size: 0.68rem; text-transform: uppercase;">Harga</span>
                                <span class="pos-product-price">
                                    Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary pos-add-btn d-flex align-items-center gap-1 shadow-sm" {{ $prod->stok <= 0 ? 'disabled' : '' }}>
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Shopping Cart & Checkout -->
    <div class="col-xxl-4 col-xl-5 col-lg-5 col-12">
        <div class="card shadow-sm h-100 d-flex flex-column">
            <!-- Cart Header -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-primary fs-5"></i>
                    <div>
                        <h6 class="fw-bold mb-0">Keranjang Kasir</h6>
                        <small class="text-muted font-monospace" id="trxNumberDisplay">{{ $nextTrxNumber }}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCart()" title="Kosongkan Keranjang">
                    <i class="fa-solid fa-trash-can me-1"></i> Reset
                </button>
            </div>

            <!-- Cart Table Items -->
            <div class="card-body p-0 flex-grow-1 overflow-auto" style="max-height: 280px;">
                <div class="table-responsive">
                    <table class="table table-hover cart-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th style="width: 110px;" class="text-center">Jumlah</th>
                                <th style="width: 110px;" class="text-end">Subtotal</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <!-- Items added dynamically -->
                            <tr id="emptyCartRow">
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-basket-shopping fs-1 d-block mb-2 opacity-50"></i>
                                    Keranjang masih kosong.<br>Pilih produk di sebelah kiri atau scan barcode.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Checkout Details Form -->
            <div class="card-footer bg-light p-3 border-top">
                <!-- Subtotal, Diskon & Total Tagihan Highlight -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Subtotal Belanja:</span>
                        <strong class="text-dark" id="subtotalDisplay">Rp 0</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Diskon Transaksi:</span>
                        <div class="input-group input-group-sm" style="width: 140px;">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" id="discountInput" class="form-control text-end" value="0" min="0" step="500" oninput="calculateTotal()">
                        </div>
                    </div>
                </div>

                <!-- Total Tagihan Banner (High Contrast Modern Retail) -->
                <div class="rounded-3 p-3 mb-3 text-white shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase fw-semibold d-block text-white-50" style="font-size: 0.72rem; letter-spacing: 0.8px;">Total Tagihan</span>
                            <small class="text-white-50" style="font-size: 0.75rem;">Harus Dibayar</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-white font-monospace" id="totalBayarDisplay" style="font-size: 1.65rem;">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Metode Pembayaran:</label>
                    <div class="btn-group w-100 flex-wrap" role="group">
                        <input type="radio" class="btn-check" name="payment_method" id="payTunai" value="Tunai" checked onchange="onPaymentMethodChange()">
                        <label class="btn btn-outline-primary btn-sm py-2 px-1 flex-fill" for="payTunai">
                            <i class="fa-solid fa-money-bill-wave me-1"></i> Tunai
                        </label>

                        <input type="radio" class="btn-check" name="payment_method" id="payQris" value="QRIS" onchange="onPaymentMethodChange()">
                        <label class="btn btn-outline-primary btn-sm py-2 px-1 flex-fill" for="payQris">
                            <i class="fa-solid fa-qrcode me-1"></i> QRIS
                        </label>

                        <input type="radio" class="btn-check" name="payment_method" id="payTransfer" value="Transfer Bank" onchange="onPaymentMethodChange()">
                        <label class="btn btn-outline-primary btn-sm py-2 px-1 flex-fill" for="payTransfer">
                            <i class="fa-solid fa-building-columns me-1"></i> Transfer
                        </label>

                        <input type="radio" class="btn-check" name="payment_method" id="payDebit" value="Debit" onchange="onPaymentMethodChange()">
                        <label class="btn btn-outline-primary btn-sm py-2 px-1 flex-fill" for="payDebit">
                            <i class="fa-solid fa-credit-card me-1"></i> Debit
                        </label>
                    </div>
                </div>

                <!-- Cash Received & Quick Buttons (Only for Tunai) -->
                <div id="tunaiBox" class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold text-muted mb-0">Uang Diterima (Rp):</label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small" onclick="setExactCash()">Uang Pas</button>
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white text-muted">Rp</span>
                        <input type="number" id="cashInput" class="form-control form-control-lg text-end fw-bold" placeholder="0" min="0" step="1000" oninput="calculateChange()">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="addQuickCash(10000)">+10k</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="addQuickCash(20000)">+20k</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="addQuickCash(50000)">+50k</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="addQuickCash(100000)">+100k</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="setCash(50000)">50.000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn flex-fill" onclick="setCash(100000)">100.000</button>
                    </div>
                </div>

                <!-- Kembalian -->
                <div class="p-3 rounded-3 border mb-3 d-flex justify-content-between align-items-center" id="changeBox" style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
                    <div>
                        <span class="fw-bold text-success text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Uang Kembalian:</span>
                        <small class="text-muted" style="font-size: 0.75rem;">Diserahkan ke pembeli</small>
                    </div>
                    <strong class="fs-4 text-success font-monospace" id="changeDisplay">Rp 0</strong>
                </div>

                <!-- Submit Button -->
                <button type="button" id="btnCheckout" class="btn btn-primary btn-lg w-100 d-flex justify-content-center align-items-center gap-2 py-3 shadow" onclick="submitCheckout()" disabled>
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <span class="fw-bold">Bayar & Simpan Transaksi</span>
                    <kbd class="bg-white bg-opacity-25 text-white border-0 rounded px-2 py-0 ms-1 fw-bold" style="font-size: 0.72rem;">F9</kbd>
                </button>

                <!-- Keyboard Shortcut Guide Bar -->
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-2 text-muted small mt-2 pt-1" style="font-size: 0.72rem;">
                    <span><kbd class="bg-white border text-secondary shadow-sm">F2</kbd> Cari Barang</span>
                    <span>&bull;</span>
                    <span><kbd class="bg-white border text-secondary shadow-sm">F4</kbd> Uang Tunai</span>
                    <span>&bull;</span>
                    <span><kbd class="bg-white border text-secondary shadow-sm">F9</kbd> Bayar</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Struk Transaksi Sukses -->
<div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-check me-2"></i>Transaksi Berhasil!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetPos()"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="thermal-receipt shadow-sm" id="thermalReceiptContent">
                    <div class="text-center mb-2">
                        <h6 class="fw-bold mb-0">ATK POS</h6>
                        <small>Toko Alat Tulis Kantor</small><br>
                        <small>Jl. Raya Utama No. 88, Telp: 0812-3456-7890</small>
                    </div>
                    <div class="border-top border-bottom py-1 my-2" style="border-style: dashed !important;">
                        <div class="d-flex justify-content-between">
                            <span>No: <strong id="recTrxNo">TRX-0000</strong></span>
                            <span id="recDate">01/01/2026</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Kasir: <strong id="recCashier">{{ auth()->user()->name }}</strong></span>
                            <span>Metode: <strong id="recMethod">Tunai</strong></span>
                        </div>
                    </div>
                    <div id="recItemsList"></div>
                    <div class="border-top pt-1 mt-2" style="border-style: dashed !important;">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="recSubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between" id="recDiscountRow">
                            <span>Diskon:</span>
                            <span id="recDiscount">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-6 my-1">
                            <span>TOTAL:</span>
                            <span id="recTotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Bayar:</span>
                            <span id="recPaid">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Kembalian:</span>
                            <span id="recChange">Rp 0</span>
                        </div>
                    </div>
                    <div class="text-center mt-3 pt-2 border-top" style="border-style: dashed !important;">
                        <small class="d-block">Terima Kasih Atas Kunjungan Anda!</small>
                        <small class="text-muted" style="font-size: 11px;">Barang yang sudah dibeli tidak dapat dikembalikan</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="resetPos()">
                    <i class="fa-solid fa-plus me-1"></i> Transaksi Baru
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="printReceiptPopup()">
                        <i class="fa-solid fa-print me-1"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Cart state
    let cart = [];
    const productsData = {!! json_encode($products) !!};
    const productsMap = {};
    productsData.forEach(p => { productsMap[p.id] = p; });

    let currentReceiptUrl = '';

    // Search & Category Filter
    const searchInput = document.getElementById('posSearchInput');
    const productGrid = document.getElementById('productGrid');
    const categoryPills = document.querySelectorAll('.category-pill');

    let selectedCategory = '';

    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const items = document.querySelectorAll('.product-item');

        items.forEach(item => {
            const nama = item.getAttribute('data-nama');
            const kode = item.getAttribute('data-kode');
            const barcode = item.getAttribute('data-barcode');
            const cat = item.getAttribute('data-category');

            const matchQuery = !query || nama.includes(query) || kode.includes(query) || barcode.includes(query);
            const matchCat = !selectedCategory || cat === selectedCategory;

            if (matchQuery && matchCat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterProducts);

    // Barcode scanner auto-enter support: if exact match on enter, add to cart
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const query = searchInput.value.toLowerCase().trim();
            if (!query) return;

            // Search for exact match by barcode or kode
            const match = productsData.find(p =>
                (p.barcode && p.barcode.toLowerCase() === query) ||
                (p.kode_barang && p.kode_barang.toLowerCase() === query)
            );

            if (match) {
                addToCart(match.id);
                searchInput.value = '';
                filterProducts();
            }
        }
    });

    document.getElementById('clearSearchBtn').addEventListener('click', function () {
        searchInput.value = '';
        filterProducts();
        searchInput.focus();
    });

    categoryPills.forEach(pill => {
        pill.addEventListener('click', function () {
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            selectedCategory = this.getAttribute('data-category');
            filterProducts();
        });
    });

    // Cart Management
    function addToCart(productId) {
        const product = productsMap[productId];
        if (!product) return;

        if (product.stok <= 0) {
            alert(`Stok '${product.nama_barang}' habis!`);
            return;
        }

        const existingItem = cart.find(item => item.product_id === productId);

        if (existingItem) {
            if (existingItem.jumlah + 1 > product.stok) {
                alert(`Jumlah melebihi stok yang tersedia (${product.stok} ${product.satuan})!`);
                return;
            }
            existingItem.jumlah++;
        } else {
            cart.push({
                product_id: product.id,
                nama_barang: product.nama_barang,
                kode_barang: product.kode_barang,
                harga_jual: parseFloat(product.harga_jual),
                stok: parseInt(product.stok),
                satuan: product.satuan,
                jumlah: 1
            });
        }

        renderCart();
    }

    function updateCartQty(productId, qty) {
        const item = cart.find(i => i.product_id === productId);
        if (!item) return;

        const val = parseInt(qty) || 1;
        if (val > item.stok) {
            alert(`Stok hanya tersedia ${item.stok} ${item.satuan}!`);
            item.jumlah = item.stok;
        } else if (val <= 0) {
            removeFromCart(productId);
            return;
        } else {
            item.jumlah = val;
        }

        renderCart();
    }

    function changeCartQty(productId, delta) {
        const item = cart.find(i => i.product_id === productId);
        if (!item) return;
        updateCartQty(productId, item.jumlah + delta);
    }

    function removeFromCart(productId) {
        cart = cart.filter(i => i.product_id !== productId);
        renderCart();
    }

    function clearCart() {
        if (cart.length > 0 && confirm('Kosongkan keranjang belanja kasir?')) {
            cart = [];
            renderCart();
        }
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const emptyRow = document.getElementById('emptyCartRow');

        if (cart.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyCartRow">
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-basket-shopping fs-1 d-block mb-2 opacity-50"></i>
                        Keranjang masih kosong.<br>Pilih produk di sebelah kiri atau scan barcode.
                    </td>
                </tr>
            `;
            document.getElementById('btnCheckout').disabled = true;
            calculateTotal();
            return;
        }

        let html = '';
        cart.forEach(item => {
            const subtotal = item.jumlah * item.harga_jual;
            html += `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="${item.nama_barang}">
                            ${item.nama_barang}
                        </div>
                        <small class="text-muted">Rp ${Number(item.harga_jual).toLocaleString('id-ID')} / ${item.satuan}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-light border px-2 py-0" onclick="changeCartQty(${item.product_id}, -1)">-</button>
                            <input type="number" class="form-control form-control-sm text-center p-0" style="width: 45px;" value="${item.jumlah}" min="1" max="${item.stok}" onchange="updateCartQty(${item.product_id}, this.value)">
                            <button type="button" class="btn btn-sm btn-light border px-2 py-0" onclick="changeCartQty(${item.product_id}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-dark">
                        Rp ${Number(subtotal).toLocaleString('id-ID')}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm text-danger p-0" onclick="removeFromCart(${item.product_id})" title="Hapus">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        document.getElementById('btnCheckout').disabled = false;
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += (item.jumlah * item.harga_jual);
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total = Math.max(0, subtotal - discount);

        document.getElementById('subtotalDisplay').innerText = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        document.getElementById('totalBayarDisplay').innerText = 'Rp ' + Number(total).toLocaleString('id-ID');

        // Check payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'Tunai';
        if (paymentMethod !== 'Tunai') {
            document.getElementById('cashInput').value = total;
        }

        calculateChange();
    }

    function calculateChange() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += (item.jumlah * item.harga_jual); });
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total = Math.max(0, subtotal - discount);

        const cash = parseFloat(document.getElementById('cashInput').value) || 0;
        const change = cash - total;

        const changeDisplay = document.getElementById('changeDisplay');
        const changeBox = document.getElementById('changeBox');
        if (change >= 0) {
            changeDisplay.innerText = 'Rp ' + Number(change).toLocaleString('id-ID');
            changeDisplay.className = 'fs-4 text-success font-monospace fw-bold';
            if (changeBox) {
                changeBox.style.backgroundColor = '#f0fdf4';
                changeBox.style.borderColor = '#bbf7d0';
            }
        } else {
            changeDisplay.innerText = 'Kurang Rp ' + Number(Math.abs(change)).toLocaleString('id-ID');
            changeDisplay.className = 'fs-4 text-danger font-monospace fw-bold';
            if (changeBox) {
                changeBox.style.backgroundColor = '#fef2f2';
                changeBox.style.borderColor = '#fecaca';
            }
        }
    }

    function onPaymentMethodChange() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;
        const tunaiBox = document.getElementById('tunaiBox');

        if (method === 'Tunai') {
            tunaiBox.style.display = 'block';
        } else {
            tunaiBox.style.display = 'none';
        }
        calculateTotal();
    }

    function setExactCash() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += (item.jumlah * item.harga_jual); });
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total = Math.max(0, subtotal - discount);

        document.getElementById('cashInput').value = total;
        calculateChange();
    }

    function addQuickCash(amount) {
        const current = parseFloat(document.getElementById('cashInput').value) || 0;
        document.getElementById('cashInput').value = current + amount;
        calculateChange();
    }

    function setCash(amount) {
        document.getElementById('cashInput').value = amount;
        calculateChange();
    }

    // Checkout Submit
    async function submitCheckout() {
        if (cart.length === 0) {
            alert('Keranjang belanja kosong!');
            return;
        }

        let subtotal = 0;
        cart.forEach(item => { subtotal += (item.jumlah * item.harga_jual); });
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total = Math.max(0, subtotal - discount);

        const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'Tunai';
        const cash = parseFloat(document.getElementById('cashInput').value) || 0;

        if (method === 'Tunai' && cash < total) {
            alert(`Uang pembayaran kurang! Total tagihan: Rp ${Number(total).toLocaleString('id-ID')}, Uang dibayar: Rp ${Number(cash).toLocaleString('id-ID')}`);
            return;
        }

        const btn = document.getElementById('btnCheckout');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memproses...`;

        try {
            const payload = {
                items: cart.map(i => ({ product_id: i.product_id, jumlah: i.jumlah })),
                diskon: discount,
                nominal_bayar: method === 'Tunai' ? cash : total,
                metode_pembayaran: method,
                catatan: ''
            };

            const response = await fetch("{{ route('sales.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                // Populate receipt modal
                const sale = result.sale;
                currentReceiptUrl = result.receipt_url;

                document.getElementById('recTrxNo').innerText = sale.nomor_transaksi;
                document.getElementById('recDate').innerText = new Date(sale.tanggal_transaksi).toLocaleString('id-ID');
                document.getElementById('recMethod').innerText = sale.metode_pembayaran;

                let itemsHtml = '';
                sale.details.forEach(d => {
                    itemsHtml += `
                        <div class="d-flex justify-content-between mb-1">
                            <div>${d.product ? d.product.nama_barang : 'Barang'} x${d.jumlah}</div>
                            <div>Rp ${Number(d.subtotal).toLocaleString('id-ID')}</div>
                        </div>
                    `;
                });
                document.getElementById('recItemsList').innerHTML = itemsHtml;
                document.getElementById('recSubtotal').innerText = 'Rp ' + Number(sale.subtotal).toLocaleString('id-ID');
                document.getElementById('recDiscount').innerText = '- Rp ' + Number(sale.diskon).toLocaleString('id-ID');
                document.getElementById('recTotal').innerText = 'Rp ' + Number(sale.total_bayar).toLocaleString('id-ID');
                document.getElementById('recPaid').innerText = 'Rp ' + Number(sale.nominal_bayar).toLocaleString('id-ID');
                document.getElementById('recChange').innerText = 'Rp ' + Number(sale.kembalian).toLocaleString('id-ID');

                // Update product stock locally
                sale.details.forEach(d => {
                    if (productsMap[d.product_id]) {
                        productsMap[d.product_id].stok -= d.jumlah;
                    }
                });

                // Open receipt modal
                const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                modal.show();
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan jaringan atau server: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-circle-check fs-5"></i> <span class="fw-bold">Bayar & Simpan Transaksi</span> <kbd class="bg-white bg-opacity-25 text-white border-0 rounded px-2 py-0 ms-1 fw-bold" style="font-size: 0.72rem;">F9</kbd>`;
        }
    }

    function printReceiptPopup() {
        if (currentReceiptUrl) {
            window.open(currentReceiptUrl, '_blank', 'width=420,height=600');
        }
    }

    function resetPos() {
        cart = [];
        document.getElementById('discountInput').value = 0;
        document.getElementById('cashInput').value = '';
        renderCart();
        window.location.reload(); // Refresh next TRX number & updated stock
    }

    // Global Cashier Keyboard Shortcuts (F2, F4, F9, Esc)
    window.addEventListener('keydown', function (e) {
        // F2: Focus Search Barcode
        if (e.key === 'F2') {
            e.preventDefault();
            const searchInput = document.getElementById('posSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        // F4: Focus Cash Input / Uang Tunai
        else if (e.key === 'F4') {
            e.preventDefault();
            const cashInput = document.getElementById('cashInput');
            const tunaiBox = document.getElementById('tunaiBox');
            if (cashInput && tunaiBox && tunaiBox.style.display !== 'none') {
                cashInput.focus();
                cashInput.select();
            }
        }
        // F9: Bayar & Simpan Transaksi
        else if (e.key === 'F9') {
            e.preventDefault();
            const btn = document.getElementById('btnCheckout');
            if (btn && !btn.disabled) {
                submitCheckout();
            }
        }
        // Escape: Clear search box
        else if (e.key === 'Escape') {
            const searchInput = document.getElementById('posSearchInput');
            if (searchInput && searchInput === document.activeElement && searchInput.value) {
                searchInput.value = '';
                filterProducts();
            }
        }
    });
</script>
@endsection

