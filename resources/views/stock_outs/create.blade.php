@extends('layouts.app')

@section('title', 'Catat Barang Keluar')
@section('page_title', 'Catat Barang Keluar Non-Penjualan')
@section('page_subtitle', 'Stok barang akan berkurang secara otomatis dan dicatat dalam riwayat mutasi')

@section('content')
<form action="{{ route('stock-outs.store') }}" method="POST" id="stockOutForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <span class="fw-bold"><i class="fa-solid fa-clipboard text-danger me-2"></i>Informasi Pengeluaran</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Transaksi</label>
                        <input type="text" class="form-control font-monospace bg-light" value="{{ $nextOutNumber }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Pengeluaran <span class="text-danger">*</span></label>
                        <select name="alasan" class="form-select @error('alasan') is-invalid @enderror" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Barang Rusak" {{ old('alasan') == 'Barang Rusak' ? 'selected' : '' }}>Barang Rusak (Damage)</option>
                            <option value="Barang Hilang" {{ old('alasan') == 'Barang Hilang' ? 'selected' : '' }}>Barang Hilang (Lost)</option>
                            <option value="Keperluan Toko" {{ old('alasan') == 'Keperluan Toko' ? 'selected' : '' }}>Keperluan Toko / Kantor</option>
                            <option value="Penyesuaian Stok" {{ old('alasan') == 'Penyesuaian Stok' ? 'selected' : '' }}>Penyesuaian Stok (Opname)</option>
                            <option value="Cacat / Expired" {{ old('alasan') == 'Cacat / Expired' ? 'selected' : '' }}>Cacat Pabrik / Expired</option>
                        </select>
                        @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan pengeluaran secara rinci...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger">
                <div class="card-body p-3 small">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    <strong>Perhatian:</strong> Setiap pengeluaran barang non-penjualan ini akan mengurangi saldo stok barang dan tercatat permanen di Kartu Stok.
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold"><i class="fa-solid fa-boxes-stacked text-danger me-2"></i>Barang yang Dikeluarkan</span>
                    <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Baris
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 250px;">Produk ATK</th>
                                    <th style="width: 130px;" class="text-center">Jumlah Keluar</th>
                                    <th>Keterangan / Kondisi</th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 d-flex justify-content-between">
                    <a href="{{ route('stock-outs.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-sm btn-danger px-4 py-2">
                        <i class="fa-solid fa-check-circle me-1"></i> Simpan Barang Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const availableProducts = {!! json_encode($products) !!};
    let rowIndex = 0;

    function createProductOptions() {
        let options = '<option value="">-- Pilih Barang ATK --</option>';
        availableProducts.forEach(p => {
            options += `<option value="${p.id}" data-stok="${p.stok}" data-satuan="${p.satuan}">
                ${p.nama_barang} (${p.kode_barang}) - Sisa Stok: ${p.stok} ${p.satuan}
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
                <small class="text-muted stock-hint d-block mt-1"></small>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="items[${rowIndex}][jumlah]" class="form-control text-center qty-input" value="1" min="1" required oninput="validateQty(${rowIndex})">
                    <span class="input-group-text satuan-badge bg-light" style="font-size: 0.75rem;">Satuan</span>
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="Contoh: Rusak terbentur / dipakai kasir">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(${rowIndex})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    function onProductChange(index) {
        const row = document.getElementById(`row-${index}`);
        const select = row.querySelector('.product-select');
        const opt = select.options[select.selectedIndex];
        const qtyInput = row.querySelector('.qty-input');
        const satuanBadge = row.querySelector('.satuan-badge');
        const hint = row.querySelector('.stock-hint');

        if (opt && opt.value) {
            const stok = parseInt(opt.getAttribute('data-stok')) || 0;
            const satuan = opt.getAttribute('data-satuan');
            satuanBadge.innerText = satuan;
            qtyInput.max = stok;
            hint.innerText = `Maksimal keluar: ${stok} ${satuan}`;
            hint.className = 'text-primary small d-block mt-1';
        } else {
            satuanBadge.innerText = 'Satuan';
            qtyInput.removeAttribute('max');
            hint.innerText = '';
        }
    }

    function validateQty(index) {
        const row = document.getElementById(`row-${index}`);
        const select = row.querySelector('.product-select');
        const opt = select.options[select.selectedIndex];
        const qtyInput = row.querySelector('.qty-input');

        if (opt && opt.value) {
            const stok = parseInt(opt.getAttribute('data-stok')) || 0;
            const val = parseInt(qtyInput.value) || 0;
            if (val > stok) {
                alert(`Jumlah melebihi stok yang tersedia (${stok})!`);
                qtyInput.value = stok;
            }
        }
    }

    function removeRow(index) {
        const row = document.getElementById(`row-${index}`);
        if (row) row.remove();
    }

    document.getElementById('addRowBtn').addEventListener('click', addRow);
    document.addEventListener('DOMContentLoaded', addRow);
</script>
@endsection

