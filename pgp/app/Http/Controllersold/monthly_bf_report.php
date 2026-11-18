<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class monthly_bf_report extends Controller
{
    public function index()
    {
        return view('content.reports.monthly_bf_report');
    } 
   
}