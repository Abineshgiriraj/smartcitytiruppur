<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class afternoon_report extends Controller
{
    public function index()
    {
        return view('content.reports.afternoon_report');
    } 
   
}