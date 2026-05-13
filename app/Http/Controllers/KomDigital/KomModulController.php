<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomModul;
use App\Models\Kom\KomClass;

class KomModulController extends Controller
{
    public function index()
    {
        $moduls = KomModul::with('komClass')->orderBy('order')->get();
        return view('kom_digital.modul.index', compact('moduls'));
    }

    public function create()
    {
        $classes = KomClass::all();
        return view('kom_digital.modul.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'kom_class_id' => 'required|exists:kom_class,id',
        ]);

        KomModul::create($request->only('title', 'description', 'order', 'kom_class_id'));

        return redirect()->route('kom.modul.index')->with('success', 'Modul berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $modul = KomModul::findOrFail($id);
        $classes = KomClass::all();
        return view('kom_digital.modul.edit', compact('modul', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $modul = KomModul::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'kom_class_id' => 'required|exists:kom_class,id',
        ]);

        $modul->update($request->only('title', 'description', 'order', 'kom_class_id'));

        return redirect()->route('kom.modul.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $modul = KomModul::findOrFail($id);
        $modul->delete();

        return redirect()->route('kom.modul.index')->with('success', 'Modul berhasil dihapus.');
    }

    public function show($id)
    {
        $modul = KomModul::with('komVideos')->findOrFail($id);
        return view('kom_digital.modul.show', compact('modul'));
    }

    public function getOrderedVideos($modulId)
    {
        // Menemukan modul berdasarkan ID
        $modul = KomModul::findOrFail($modulId);

        // Mengambil video yang sudah diurutkan berdasarkan urutan
        $videos = $modul->getOrderedVideos; // Menggunakan nama fungsi baru

        return response()->json($videos);
    }


}
