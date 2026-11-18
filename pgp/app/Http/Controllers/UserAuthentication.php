<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\user_registration;

class UserAuthentication extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.user_authentication.index');
    }
    public function add()
    {
        return view('content.user_authentication.create');
    }

    public function getdata()
    {
        return view('content.user_authentication.data');
    }




    public function edit($id)
    {

        return view('content.user_authentication.edit', compact('category'));
    }
}