<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
{
    return view('holiday.index');
}

public function calendar()
{
    return view('holiday.calendar');
}
}
