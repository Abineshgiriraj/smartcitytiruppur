<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class designation extends Controller
{
    public function index()
    {
        return view('content.designation.list');
    }
    public function add()
    {
        return view('content.designation.form');
    }
}