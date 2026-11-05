<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pembelian',
        'supplier_id',
        'user_id',
        'tanggal_pembelian',
        'total_pembelian',
        'metode_pembayaran',
        'status_pembayaran',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pembelian' => 'date',
            'total_pembelian' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}

