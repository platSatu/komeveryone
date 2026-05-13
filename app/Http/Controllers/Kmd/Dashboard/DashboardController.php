<?php

namespace App\Http\Controllers\Kmd\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {
        return view('a_kom_digital_pusat.dashboard.home');
    }

}
