<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class student_creation extends Controller
{
    public function index()
    {
        return view('content.student.list');
    }
    public function add()
    {
        return view('content.student.form');
    }
}