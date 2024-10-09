<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use Illuminate\Http\Request;

class StokBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stokBarangs = StokBarang::all();
        return response()->json($stokBarangs);
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
            'stok_awal' => 'required|integer',
            'stok_akhir' => 'required|integer',
        ]);

        $stokBarang = StokBarang::create($request->all());
        return response()->json($stokBarang, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stokBarang = StokBarang::find($id);
        if (!$stokBarang) {
            return response()->json(['message' => 'Stok Barang not found'], 404);
        }
        return response()->json($stokBarang);
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
        $stokBarang = StokBarang::find($id);
        if (!$stokBarang) {
            return response()->json(['message' => 'Stok Barang not found'], 404);
        }

        $request->validate([
            'stok_awal' => 'sometimes|required|integer',
            'stok_akhir' => 'sometimes|required|integer',
        ]);

        $stokBarang->update($request->all());
        return response()->json($stokBarang);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        StokBarang::destroy($id);
        return response()->json(['message' => 'Transaksi deleted successfully']);
    }
}
