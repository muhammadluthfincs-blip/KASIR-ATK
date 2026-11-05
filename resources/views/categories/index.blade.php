@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page_title', 'Kategori Barang')
@section('page_subtitle', 'Kelola kelompok dan klasifikasi produk ATK')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau kode..." value="{{ $search }}">
            </div>
            @if($search)
            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 130px;">Jumlah Produk</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                    <tr>
                        <td class="text-muted">{{ $categories->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $cat->code }}</span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $cat->name }}</td>
                        <td class="text-muted small">{{ $cat->description ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">
                                {{ $cat->products_count }} item
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" title="Edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-code="{{ $cat->code }}"
                                    data-description="{{ $cat->description }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" {{ $cat->products_count > 0 ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fs-3 d-block mb-2 opacity-50"></i>
                            Tidak ada data kategori ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($categories->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-tags text-primary me-2"></i>Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Alat Tulis" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Kategori (Opsional)</label>
                    <input type="text" name="code" class="form-control font-monospace" placeholder="Contoh: ATK (Otomatis jika kosong)">
                    <small class="text-muted">Jika dikosongkan, sistem akan mengenerate kode otomatis.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Keterangan singkat kategori..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editCategoryForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editCategoryName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Kategori</label>
                    <input type="text" name="code" id="editCategoryCode" class="form-control font-monospace" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Deskripsi</label>
                    <textarea name="description" id="editCategoryDescription" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editCategoryModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const code = button.getAttribute('data-code');
            const desc = button.getAttribute('data-description');

            const form = document.getElementById('editCategoryForm');
            form.action = '/categories/' + id;

            document.getElementById('editCategoryName').value = name;
            document.getElementById('editCategoryCode').value = code;
            document.getElementById('editCategoryDescription').value = desc || '';
        });
    });
</script>
@endsection

