<?php

namespace App\Http\Controllers\ui;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class district extends Controller
{
     public function index()
    {
        return view('content.district.list');
    }

    public function add()
    {
        return view('content.district.form');
    }
}