<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class taluk_creation extends Controller
{
    public function index()
    {
        return view('content.taluk_creation.list');
    }
    public function add()
    {
        return view('content.taluk_creation.form');
    }
}