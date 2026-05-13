<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rayon;

class RayonController extends Controller
{
    public function index()
    {
        $rayons = Rayon::latest()->paginate(10);
        return view('kom_digital.rayons.index', compact('rayons'));
    }

    // Form create rayon
    public function create()
    {
        return view('kom_digital.rayons.create');
    }

    // Simpan rayon baru
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        Rayon::create([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.rayons.index')->with('success', 'Rayon berhasil ditambahkan.');
    }

    // Form edit rayon
    public function edit($id)
    {
        $rayon = Rayon::findOrFail($id);
        return view('kom_digital.rayons.edit', compact('rayon'));
    }

    // Update rayon
    public function update(Request $request, $id)
    {
        $rayon = Rayon::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $rayon->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('kom.rayons.index')->with('success', 'Rayon berhasil diperbarui.');
    }

    // Hapus rayon
    public function destroy($id)
    {
        $rayon = Rayon::findOrFail($id);
        $rayon->delete();

        return redirect()->route('kom.rayons.index')->with('success', 'Rayon berhasil dihapus.');
    }
}
