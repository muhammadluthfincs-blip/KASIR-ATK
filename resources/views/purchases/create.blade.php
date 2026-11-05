@extends('layouts.app')

@section('title', 'Catat Pembelian Barang')
@section('page_title', 'Catat Pembelian Barang Masuk')
@section('page_subtitle', 'Stok barang akan bertambah secara otomatis setelah transaksi disimpan')

@section('content')
<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf
    <div class="row g-4">
        <!-- Informasi Transaksi -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <span class="fw-bold"><i class="fa-solid fa-file-invoice text-primary me-2"></i>Data Transaksi</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Pembelian (PO)</label>
                        <input type="text" class="form-control font-monospace bg-light" value="{{ $nextPoNumber }}" readonly>
                        <small class="text-muted">Nomor PO digenerate otomatis.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tanggal Pembelian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pembelian" class="form-control @error('tanggal_pembelian') is-invalid @enderror" value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                        @error('tanggal_pembelian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Supplier Penyedia <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" class="form-select" required>
                            <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai (Cash)</option>
                            <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Tempo" {{ old('metode_pembayaran') == 'Tempo' ? 'selected' : '' }}>Tempo (Kredit/Utang)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status Pembayaran <span class="text-danger">*</span></label>
                        <select name="status_pembayaran" class="form-select" required>
                            <option value="Lunas" {{ old('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ old('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan / No. Faktur Supplier</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan atau no invoice asli dari supplier...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Total Card -->
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body p-4">
                    <span class="small text-white-50 text-uppercase fw-semibold d-block">Total Pembelian</span>
                    <h2 class="fw-bold mb-0" id="grandTotalDisplay">Rp 0</h2>
                    <small class="text-white-50 mt-1 d-block"><span id="totalItemsCount">0</span> jenis barang dipilih</small>
                </div>
            </div>
        </div>

        <!-- Detail Barang Masuk -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold"><i class="fa-solid fa-boxes-packing text-primary me-2"></i>Daftar Barang yang Dibeli</span>
                    <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Baris Barang
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 250px;">Pilih Produk ATK</th>
                                    <th style="width: 120px;" class="text-center">Jumlah (Qty)</th>
                                    <th style="width: 160px;" class="text-end">Harga Beli (Rp)</th>
                                    <th style="width: 160px;" class="text-end">Subtotal (Rp)</th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows inserted by Javascript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-4 py-2" id="submitBtn">
                        <i class="fa-solid fa-check-circle me-1"></i> Simpan Transaksi Pembelian
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Products dataset for select options
    const availableProducts = {!! json_encode($products) !!};

    let rowIndex = 0;

    function createProductOptions() {
        let options = '<option value="">-- Pilih Barang ATK --</option>';
        availableProducts.forEach(p => {
            options += `<option value="${p.id}" data-harga="${p.harga_beli}" data-stok="${p.stok}" data-satuan="${p.satuan}">
                ${p.nama_barang} (${p.kode_barang}) - Stok: ${p.stok} ${p.satuan}
            </option>`;
        });
        return options;
    }

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;
        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="onProductChange(${rowIndex})">
                    ${createProductOptions()}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="items[${rowIndex}][jumlah]" class="form-control text-center qty-input" value="1" min="1" required oninput="calcSubtotal(${rowIndex})">
                    <span class="input-group-text satuan-badge bg-light" style="font-size: 0.75rem;">Satuan</span>
                </div>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][harga_beli]" class="form-control form-control-sm text-end price-input" value="0" min="0" step="100" required oninput="calcSubtotal(${rowIndex})">
            </td>
            <td class="text-end fw-bold text-dark subtotal-cell">
                Rp 0
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(${rowIndex})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        updateTotals();
    }

    function onProductChange(index) {
        const row = document.getElementById(`row-${index}`);
        const select = row.querySelector('.product-select');
        const selectedOption = select.options[select.selectedIndex];
        const priceInput = row.querySelector('.price-input');
        const satuanBadge = row.querySelector('.satuan-badge');

        if (selectedOption && selectedOption.value) {
            const harga = selectedOption.getAttribute('data-harga');
            const satuan = selectedOption.getAttribute('data-satuan');
            priceInput.value = harga;
            satuanBadge.innerText = satuan;
        } else {
            priceInput.value = 0;
            satuanBadge.innerText = 'Satuan';
        }

        calcSubtotal(index);
    }

    function calcSubtotal(index) {
        const row = document.getElementById(`row-${index}`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.subtotal-cell').innerText = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        updateTotals();
    }

    function removeRow(index) {
        const row = document.getElementById(`row-${index}`);
        if (row) {
            row.remove();
            updateTotals();
        }
    }

    function updateTotals() {
        const rows = document.querySelectorAll('#itemsBody tr');
        let grandTotal = 0;
        let count = 0;

        rows.forEach(r => {
            const qty = parseFloat(r.querySelector('.qty-input')?.value) || 0;
            const price = parseFloat(r.querySelector('.price-input')?.value) || 0;
            grandTotal += (qty * price);
            count++;
        });

        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + Number(grandTotal).toLocaleString('id-ID');
        document.getElementById('totalItemsCount').innerText = count;
    }

    document.getElementById('addRowBtn').addEventListener('click', addRow);

    // Initial first row
    document.addEventListener('DOMContentLoaded', function () {
        addRow();
    });

    // Form submit validation
    document.getElementById('purchaseForm').addEventListener('submit', function (e) {
        const rows = document.querySelectorAll('#itemsBody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Minimal tambahkan 1 barang pembelian!');
            return false;
        }
    });
</script>
@endsection

