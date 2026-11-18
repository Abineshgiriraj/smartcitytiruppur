<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class category_user_assign extends Controller
{
    public function index()
    {
        return view('content.category_user_assign.list');
    }
    public function add()
    {
        return view('content.category_user_assign.form');
    }
}