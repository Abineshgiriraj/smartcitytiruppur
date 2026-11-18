<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class category_controller_user extends Controller
{
    public function index()
    {
        return view('content.category_controller_user.list');
    } 
        public function add()
    {
        return view('content.category_controller_user.form');
    } 
   
}