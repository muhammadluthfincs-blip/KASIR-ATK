@extends('layouts.app')

@section('title', 'Pusat Notifikasi Sistem')
@section('page_title', 'Pusat Notifikasi Sistem')
@section('page_subtitle', 'Riwayat pemberitahuan transaksi penjualan, stok menipis, barang masuk, dan pengeluaran toko')

@section('content')
<div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px;">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-bell text-primary"></i>
            <span class="fw-bold text-dark fs-6">Daftar Pemberitahuan</span>
            @if(count($notifications) > 0)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-2 px-2 py-1" style="font-size: 0.75rem;">
                    {{ count($notifications) }} Baru
                </span>
            @endif
        </div>

        @if(count($notifications) > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 px-3 py-1 shadow-sm" style="font-size: 0.82rem;">
                <i class="fa-solid fa-check"></i>
                <span>Tandai Semua Sudah Dibaca</span>
            </button>
        </form>
        @else
        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 px-3 py-1 shadow-sm opacity-50" style="font-size: 0.82rem;" disabled>
            <i class="fa-solid fa-check"></i>
            <span>Tandai Semua Sudah Dibaca</span>
        </button>
        @endif
    </div>

    <div class="card-body p-0">
        @if(empty($notifications))
        <!-- Empty State matching Image 4 -->
        <div class="text-center py-5 my-4">
            <div class="mb-3">
                <i class="fa-solid fa-bell-slash text-secondary" style="font-size: 2.8rem; opacity: 0.35;"></i>
            </div>
            <div class="text-secondary fw-medium" style="font-size: 0.95rem;">
                Belum ada pemberitahuan baru.
            </div>
            <small class="text-muted d-block mt-1">Sistem akan memberi tahu Anda jika ada stok menipis atau pembaruan transaksi kasir.</small>
        </div>
        @else
        <!-- List Notifications -->
        <div class="list-group list-group-flush">
            @foreach($notifications as $notif)
            <div class="list-group-item p-3 d-flex align-items-start gap-3 border-bottom transition-all hover-bg">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 40px; height: 40px;
                     @if($notif['type'] === 'danger') background-color: #fee2e2; color: #ef4444;
                     @elseif($notif['type'] === 'warning') background-color: #fef3c7; color: #d97706;
                     @else background-color: #eff6ff; color: #3b82f6; @endif">
                    <i class="fa-solid {{ $notif['icon'] }} fs-6"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $notif['title'] }}</span>
                        <span class="text-muted small" style="font-size: 0.75rem;">{{ $notif['time'] }}</span>
                    </div>
                    <p class="text-secondary mb-2" style="font-size: 0.85rem; line-height: 1.4;">{{ $notif['message'] }}</p>
                    <a href="{{ $notif['url'] }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.78rem;">
                        <span>{{ $notif['action_label'] }}</span>
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

