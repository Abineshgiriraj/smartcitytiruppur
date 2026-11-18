<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class attendance_report extends Controller
{
    public function index()
    {
        return view('content.reports.attendance_report');
    } 
   
}