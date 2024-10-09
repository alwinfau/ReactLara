<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Barang extends Model
{
    use HasFactory;

    protected $fillable=[
        'nama_barang',
        'jenis_barang',
        'harga_beli',
        'harga_jual',
    ];

    /**
     * Get the user associated with the Barang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // Relasi ke StokBarang (Satu Barang punya satu stok)
    public function stokbarang(): HasOne
    {
        return $this->hasOne(StokBarang::class);
    }

    // Relasi ke Transaksi (Satu Barang bisa punya banyak transaksi)
    /**
     * Get all of the comments for the Barang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
