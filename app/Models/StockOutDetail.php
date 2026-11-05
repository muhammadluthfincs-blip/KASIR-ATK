<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_transaction_id',
        'product_id',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function stockOutTransaction()
    {
        return $this->belongsTo(StockOutTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

