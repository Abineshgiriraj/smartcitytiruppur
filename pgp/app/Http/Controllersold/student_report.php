<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class student_report extends Controller
{
    public function index()
    {
        return view('content.reports.student_report');
    }
   
}