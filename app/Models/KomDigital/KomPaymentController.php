<?php

namespace App\Http\Controllers\KomDigital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kom\KomPayment;
use App\Models\Kom\KomClassBooking;

class KomPaymentController extends Controller
{
    public function index()
    {
        $payments = KomPayment::where('user_id', auth()->id())->get();
        return view('kom_digital.payment.index', compact('payments'));
    }

    public function create()
    {
        $bookings = KomClassBooking::all();
        return view('kom_digital.payment.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:kom_class_bookings,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,failed',
            'method' => 'required|string',
        ]);

        KomPayment::create($request->all());

        return redirect()->route('kom.payment.index')->with('success', 'Pembayaran berhasil disimpan');
    }

}
