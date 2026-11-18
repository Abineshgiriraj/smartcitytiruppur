<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\user_renewal;

class UserRenewal extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.user_renewal.data');
    }
    public function add()
    {
        return view('content.user_registration.form');
    }

    public function getdata()
    {
        return view('content.user_registration.data');
    }




    public function edit($id)
    {

        return view('content.user_registration.edit', compact('category'));
    }

    
}