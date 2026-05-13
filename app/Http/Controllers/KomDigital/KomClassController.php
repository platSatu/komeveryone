<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomClass;


class KomClassController extends Controller
{
    public function index()
    {
        $classes = KomClass::latest()->paginate(10);
        return view('kom_digital.class.index', compact('classes'));
    }

    // Tampilkan form untuk tambah KomClass
    public function create()
    {
        return view('kom_digital.class.create');
    }

    // Simpan KomClass baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        KomClass::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.kom_class.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    // Tampilkan form untuk edit KomClass
    public function edit($id)
    {
        $class = KomClass::findOrFail($id);
        return view('kom_digital.class.edit', compact('class'));
    }

    // Update KomClass
    public function update(Request $request, $id)
    {
        $class = KomClass::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $class->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.kom_class.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    // Hapus KomClass
    public function destroy($id)
    {
        $class = KomClass::findOrFail($id);
        $class->delete();

        return redirect()->route('kom.kom_class.index')->with('success', 'Kelas berhasil dihapus.');
    }


}
