<?php

namespace App\Http\Controllers\ui;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class school extends Controller
{
    public function index()
    {
        return view('content.school.list');
    }

    public function add()
    {
        return view('content.school.form');
    }
}