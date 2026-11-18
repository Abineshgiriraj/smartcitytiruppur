<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContainerController extends Controller
{
    public function index()
    {
        return view('content.new_container.index');
    }

    public function create()
    {
        return view('content.new_container.create');
    }

}
