<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::all();
        return response()->json($transaksis);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_transaksi' => 'required',
            'jumlah_terjual' => 'required|integer',
            'harga_jual' => 'required|integer',
            'sub_total' => 'required|integer',
        ]);

        // Cek apakah stok barang mencukupi
        $stokBarang = StokBarang::where('barang_id', $request->barang_id)->first();
        if ($stokBarang && $stokBarang->stok_akhir >= $request->jumlah_terjual) {
            // Kurangi stok barang
            $stokBarang->stok_akhir -= $request->jumlah_terjual;
            $stokBarang->save();

            // Simpan transaksi
            $transaksi = Transaksi::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dan stok telah diperbarui.',
                'transaksi' => $transaksi,
                'stok_barang' => $stokBarang
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi untuk melakukan transaksi.',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }
        return response()->json($transaksi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'jumlah_terjual' => 'required|integer|min:0'
        ]);

        // Cari transaksi berdasarkan ID
        $transaksi = Transaksi::findOrFail($id);

        // Cari stok barang yang terkait dengan transaksi
        $stokBarang = StokBarang::where('barang_id', $transaksi->barang_id)->first();

        if ($stokBarang) {
            // Selisih antara jumlah terjual lama dan baru
            $selisihJumlah = $request->jumlah_terjual - $transaksi->jumlah_terjual;

            // Update stok berdasarkan selisih jumlah terjual
            if ($selisihJumlah > 0) {
                // Jika jumlah terjual bertambah, kurangi stok
                $stokBarang->stok_akhir -= $selisihJumlah;
            } else if ($selisihJumlah < 0) {
                // Jika jumlah terjual berkurang, tambahkan stok
                $stokBarang->stok_akhir += abs($selisihJumlah);
            }

            // Simpan perubahan stok
            $stokBarang->save();

            // Update transaksi dengan jumlah terjual yang baru
            $transaksi->jumlah_terjual = $request->jumlah_terjual;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui dan stok barang telah disesuaikan.',
                'stok_barang' => $stokBarang
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Stok barang tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaksi::find($id);

        // Cari stok barang yang terkait dengan transaksi
        $stokBarang = StokBarang::where('barang_id', $transaksi->barang_id)->first();
        if ($stokBarang) {
            // Tambahkan kembali jumlah terjual ke stok barang
            $stokBarang->stok_akhir += $transaksi->jumlah_terjual;
            $stokBarang->save();

            // Hapus transaksi
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dan stok barang telah diperbarui.',
                'stok_barang' => $stokBarang
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Stok barang tidak ditemukan.',
            ], 404);
        }
    }
}
