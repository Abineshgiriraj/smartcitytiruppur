<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class staff_report extends Controller
{
    public function index()
    {
        return view('content.reports.staff_report');
    }
   
}