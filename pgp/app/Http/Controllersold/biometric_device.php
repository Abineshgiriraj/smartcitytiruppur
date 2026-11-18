<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class biometric_device extends Controller
{
    public function index()
    {
        return view('content.biometric_device.list');
    }
    public function add()
    {
        return view('content.biometric_device.form');
    }
}