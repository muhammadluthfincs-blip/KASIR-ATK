<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'kode_barang',
        'barcode',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'deskripsi',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok' => 'integer',
            'stok_minimum' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function stockOutDetails()
    {
        return $this->hasMany(StockOutDetail::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'habis';
        } elseif ($this->stok <= $this->stok_minimum) {
            return 'menipis';
        }
        return 'aman';
    }

    public function getStockStatusBadgeAttribute(): array
    {
        if ($this->stok <= 0) {
            return [
                'class' => 'bg-danger text-white',
                'label' => 'Stok Habis',
                'icon' => 'fa-circle-xmark',
            ];
        } elseif ($this->stok <= $this->stok_minimum) {
            return [
                'class' => 'bg-warning text-dark',
                'label' => 'Stok Menipis',
                'icon' => 'fa-triangle-exclamation',
            ];
        }
        return [
            'class' => 'bg-success text-white',
            'label' => 'Stok Aman',
            'icon' => 'fa-circle-check',
        ];
    }
}

