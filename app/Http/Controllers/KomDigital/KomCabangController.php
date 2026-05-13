<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomCabang;

class KomCabangController extends Controller
{
    public function index()
    {
        $cabangs = KomCabang::all();
        return view('kom_digital.cabang.index', compact('cabangs'));
    }

    public function create()
    {
        return view('kom_digital.cabang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nmcabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kotakab' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        KomCabang::create($request->all());

        return redirect()->route('kom.cabang.index')->with('success', 'Cabang berhasil ditambahkan');
    }


}
