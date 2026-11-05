@extends('layouts.app')

@section('title', 'Data Supplier')
@section('page_title', 'Data Supplier')
@section('page_subtitle', 'Kelola distributor dan penyedia barang ATK')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, telepon, email..." value="{{ $search }}">
            </div>
            @if($search)
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Supplier</span>
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Supplier</th>
                        <th>Kontak Telepon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th class="text-center" style="width: 120px;">Pembelian</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $sup)
                    <tr>
                        <td class="text-muted">{{ $suppliers->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sup->name }}</div>
                            <small class="text-muted">{{ $sup->description ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">
                                <i class="fa-solid fa-phone me-1 text-primary"></i> {{ $sup->phone }}
                            </span>
                        </td>
                        <td class="small">{{ $sup->email ?: '-' }}</td>
                        <td class="small text-muted">{{ $sup->address ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-1">
                                {{ $sup->purchases_count }} PO
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" title="Edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editSupplierModal"
                                    data-id="{{ $sup->id }}"
                                    data-name="{{ $sup->name }}"
                                    data-phone="{{ $sup->phone }}"
                                    data-email="{{ $sup->email }}"
                                    data-address="{{ $sup->address }}"
                                    data-description="{{ $sup->description }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" {{ $sup->purchases_count > 0 ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-truck-ramp-box fs-3 d-block mb-2 opacity-50"></i>
                            Tidak ada data supplier ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($suppliers->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-truck-field text-primary me-2"></i>Tambah Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Supplier / PT / CV <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: PT. Sinar Dunia Sentosa" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="021-xxxx / 0812xxxx" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="sales@supplier.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Alamat kantor / gudang supplier..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Keterangan Tambahan</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan produk yang disuplai atau terms..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editSupplierForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Supplier / PT / CV <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editSupplierName" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="editSupplierPhone" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" id="editSupplierEmail" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Alamat Lengkap</label>
                    <textarea name="address" id="editSupplierAddress" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Keterangan Tambahan</label>
                    <textarea name="description" id="editSupplierDescription" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Perbarui Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editSupplierModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const phone = button.getAttribute('data-phone');
            const email = button.getAttribute('data-email');
            const address = button.getAttribute('data-address');
            const desc = button.getAttribute('data-description');

            const form = document.getElementById('editSupplierForm');
            form.action = '/suppliers/' + id;

            document.getElementById('editSupplierName').value = name;
            document.getElementById('editSupplierPhone').value = phone;
            document.getElementById('editSupplierEmail').value = email || '';
            document.getElementById('editSupplierAddress').value = address || '';
            document.getElementById('editSupplierDescription').value = desc || '';
        });
    });
</script>
@endsection

