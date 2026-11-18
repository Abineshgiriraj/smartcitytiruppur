<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class school_report extends Controller
{
    public function index()
    {
        return view('content.reports.school_report');
    }
   
}