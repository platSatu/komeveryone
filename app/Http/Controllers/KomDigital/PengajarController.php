<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajarController extends Controller
{
    public function DaftarPeserta()
    {
        return view ('kom_digital.pengajar.daftarpeserta');
    }
}
