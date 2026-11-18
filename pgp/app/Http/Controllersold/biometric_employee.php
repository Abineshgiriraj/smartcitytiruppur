<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class biometric_employee extends Controller
{
    public function index()
    {
        return view('content.biometric_employee.form');
    }
}