<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class monthly_af_report extends Controller
{
    public function index()
    {
        return view('content.reports.monthly_af_report');  
    } 
   
}