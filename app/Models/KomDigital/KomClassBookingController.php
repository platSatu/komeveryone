<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomClass;
use App\Models\Kom\KomClassBooking;
use App\Models\Kom\KomUser;

class KomClassBookingController extends Controller
{
    public function index()
    {
        $bookings = KomClassBooking::where('user_id', auth()->id())->with('kelas')->get();
        return view('kom_digital.booking.index', compact('bookings'));
    }

    public function create()
    {
        $classes = KomClass::all();
        $users = KomUser::where('role', 'siswa')->get();
        return view('kom_digital.class_booking.create', compact('classes', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:kom_users,id',
            'class_id' => 'required|exists:kom_classes,id',
            'jawaban_1' => 'required|string',
            'jawaban_2' => 'required|string',
            'jawaban_3' => 'required|string',
        ]);

        KomClassBooking::create($request->all());

        return redirect()->route('kom.class_booking.index')->with('success', 'Booking berhasil disimpan');
    }


}
