<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssignCargo extends Controller
{
    public function index()
    {
        return view('content.assign_cargo.index');
    }
}
