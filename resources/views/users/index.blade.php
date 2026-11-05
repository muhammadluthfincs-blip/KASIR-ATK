@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna Sistem')
@section('page_subtitle', 'Kelola hak akses akun Administrator dan Kasir toko ATK')

@section('content')
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5 col-12">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, email, atau telepon..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4 col-6">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="kasir" {{ $role === 'kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
            </div>
            <div class="col-md-3 col-6 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                @if($search || $role)
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Daftar Akun Pengguna</span>
        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email Login</th>
                        <th>Role Akses</th>
                        <th>No. Telepon</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr>
                        <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background-color: {{ $u->role === 'admin' ? '#ef4444' : '#3b82f6' }};">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">{{ $u->name }}</span>
                                    @if($u->id === auth()->id())
                                        <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">Akun Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td>{{ $u->phone ?: '-' }}</td>
                        <td class="text-center">
                            @if($u->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" title="Edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"
                                    data-id="{{ $u->id }}"
                                    data-name="{{ $u->name }}"
                                    data-email="{{ $u->email }}"
                                    data-role="{{ $u->role }}"
                                    data-phone="{{ $u->phone }}"
                                    data-active="{{ $u->is_active ? '1' : '0' }}"
                                    data-isself="{{ $u->id === auth()->id() ? '1' : '0' }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i>Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="budi@atkpos.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Role Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxx">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Status Akun</label>
                    <select name="is_active" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i>Edit Data Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editUserName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="editUserEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="editUserPassword" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Role Akses <span class="text-danger">*</span></label>
                        <select name="role" id="editUserRole" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">No. Telepon</label>
                        <input type="text" name="phone" id="editUserPhone" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Status Akun</label>
                    <select name="is_active" id="editUserActive" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editUserModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const role = button.getAttribute('data-role');
            const phone = button.getAttribute('data-phone');
            const active = button.getAttribute('data-active');
            const isSelf = button.getAttribute('data-isself');

            const form = document.getElementById('editUserForm');
            form.action = '/users/' + id;

            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserPassword').value = '';
            document.getElementById('editUserRole').value = role;
            document.getElementById('editUserPhone').value = phone || '';
            document.getElementById('editUserActive').value = active;

            // If user is editing self, disable demotion
            const roleSelect = document.getElementById('editUserRole');
            const activeSelect = document.getElementById('editUserActive');
            if (isSelf === '1') {
                roleSelect.disabled = true;
                activeSelect.disabled = true;
            } else {
                roleSelect.disabled = false;
                activeSelect.disabled = false;
            }
        });
    });
</script>
@endsection

