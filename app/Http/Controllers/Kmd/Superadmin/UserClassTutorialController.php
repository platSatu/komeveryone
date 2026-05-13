<?php

namespace App\Http\Controllers\Kmd\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserClassTutorialController extends Controller
{
    public function index()
    {
        return view ('a_kom_digital_pusat.superadmin.user_kelastutorial.index');
    }
}
