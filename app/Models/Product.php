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

    public function getVariantBadgeAttribute(): ?array
    {
        $name = strtolower($this->nama_barang);
        $map = [
            'bening' => ['label' => 'Bening', 'bg' => '#eff6ff', 'text' => '#1d4ed8', 'dot' => '#3b82f6'],
            'coklat' => ['label' => 'Coklat', 'bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#d97706'],
            'cyan' => ['label' => 'Biru', 'bg' => '#e0f2fe', 'text' => '#0369a1', 'dot' => '#0ea5e9'],
            'biru' => ['label' => 'Biru', 'bg' => '#e0f2fe', 'text' => '#0369a1', 'dot' => '#0ea5e9'],
            'black' => ['label' => 'Hitam', 'bg' => '#f1f5f9', 'text' => '#1e293b', 'dot' => '#0f172a'],
            'hitam' => ['label' => 'Hitam', 'bg' => '#f1f5f9', 'text' => '#1e293b', 'dot' => '#0f172a'],
            'magenta' => ['label' => 'Merah', 'bg' => '#ffe4e6', 'text' => '#be123c', 'dot' => '#e11d48'],
            'merah' => ['label' => 'Merah', 'bg' => '#ffe4e6', 'text' => '#be123c', 'dot' => '#e11d48'],
            'yellow' => ['label' => 'Kuning', 'bg' => '#fef9c3', 'text' => '#854d0e', 'dot' => '#ca8a04'],
            'kuning' => ['label' => 'Kuning', 'bg' => '#fef9c3', 'text' => '#854d0e', 'dot' => '#ca8a04'],
            'hijau' => ['label' => 'Hijau', 'bg' => '#dcfce7', 'text' => '#15803d', 'dot' => '#16a34a'],
        ];

        foreach ($map as $key => $style) {
            if (str_contains($name, $key)) {
                return $style;
            }
        }
        return null;
    }
}

