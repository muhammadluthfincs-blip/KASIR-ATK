@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page_title', 'Tambah Produk Baru')
@section('page_subtitle', 'Tambahkan data barang ATK ke dalam katalog toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header bg-light">
                <span class="fw-bold"><i class="fa-solid fa-box-open text-primary me-2"></i>Formulir Data Produk ATK</span>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control font-monospace @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang', $nextCode) }}" required>
                            <small class="text-muted">Kode unik identifikasi barang.</small>
                            @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Barcode EAN / UPC (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-barcode"></i></span>
                                <input type="text" name="barcode" class="form-control font-monospace @error('barcode') is-invalid @enderror" placeholder="Contoh: 8992345000012" value="{{ old('barcode') }}">
                            </div>
                            <small class="text-muted">Dapat dipindai menggunakan scanner barcode.</small>
                            @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Nama Barang ATK <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" placeholder="Contoh: Buku Tulis Sidu 38 Lembar" value="{{ old('nama_barang') }}" required>
                            @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Satuan Barang <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                @php
                                    $satuanList = ['Pcs', 'Rim', 'Box', 'Lusin', 'Pak', 'Botol', 'Buah', 'Set', 'Roll', 'Lembar', 'Buku'];
                                @endphp
                                @foreach($satuanList as $sat)
                                    <option value="{{ $sat }}" {{ old('satuan', 'Pcs') == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                                @endforeach
                            </select>
                            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Harga Beli / Modal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="harga_beli" id="inputHargaBeli" class="form-control @error('harga_beli') is-invalid @enderror" min="0" step="100" placeholder="0" value="{{ old('harga_beli', 0) }}" required>
                            </div>
                            @error('harga_beli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Harga Jual Konsumen (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="harga_jual" id="inputHargaJual" class="form-control @error('harga_jual') is-invalid @enderror" min="0" step="100" placeholder="0" value="{{ old('harga_jual', 0) }}" required>
                            </div>
                            @error('harga_jual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Live Margin Indicator -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3 border">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-chart-line text-success fs-5"></i>
                                    <div>
                                        <div class="fw-semibold small">Kalkulasi Laba Per Unit:</div>
                                        <small class="text-muted">Selisih antara harga jual dan harga beli</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-4">
                                    <div>
                                        <span class="small text-muted d-block">Laba Kotor Unit:</span>
                                        <strong id="profitPreview" class="text-success fs-6">Rp 0</strong>
                                    </div>
                                    <div>
                                        <span class="small text-muted d-block">Margin:</span>
                                        <strong id="marginPreview" class="text-primary fs-6">0%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" min="0" placeholder="0" value="{{ old('stok', 0) }}" required>
                            <small class="text-muted">Akan dicatat otomatis sebagai saldo stok awal pada kartu stok.</small>
                            @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Batas Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" min="1" placeholder="5" value="{{ old('stok_minimum', 5) }}" required>
                            <small class="text-muted">Sistem akan memberi peringatan jika stok mencapai nilai ini.</small>
                            @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi / Spesifikasi Produk</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Informasi ukuran, isi per pak, warna, merk, dll...">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3 d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const beliInput = document.getElementById('inputHargaBeli');
        const jualInput = document.getElementById('inputHargaJual');
        const profitPreview = document.getElementById('profitPreview');
        const marginPreview = document.getElementById('marginPreview');

        function updateMargin() {
            const beli = parseFloat(beliInput.value) || 0;
            const jual = parseFloat(jualInput.value) || 0;
            const profit = jual - beli;
            const margin = jual > 0 ? (profit / jual) * 100 : 0;

            profitPreview.innerText = 'Rp ' + Number(profit).toLocaleString('id-ID');
            profitPreview.className = profit >= 0 ? 'text-success fs-6' : 'text-danger fs-6';
            marginPreview.innerText = margin.toFixed(1) + '%';
        }

        beliInput.addEventListener('input', updateMargin);
        jualInput.addEventListener('input', updateMargin);
        updateMargin();
    });
</script>
@endsection

