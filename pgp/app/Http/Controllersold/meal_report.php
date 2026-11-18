<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class meal_report extends Controller
{
    public function index()
    {
        return view('content.reports.meal_report');
    } 
   
}