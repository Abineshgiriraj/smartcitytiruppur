<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class employee_creation extends Controller
{
    public function index()
    {
        return view('content.employee.list');
    }
    public function add()
    {
        return view('content.employee.form');
    }
}