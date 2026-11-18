<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class zone extends Controller
{
    public function index()
    {
        return view('content.zone.list');
    }
    public function add()
    {
        return view('content.zone.form');
    }
}