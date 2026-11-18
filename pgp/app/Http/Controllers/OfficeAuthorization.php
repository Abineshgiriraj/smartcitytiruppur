<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\user_registration;

class OfficeAuthorization extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.office_authorization.index');
    }
    public function add()
    {
        return view('content.office_authorization.create');
    }

    public function getdata()
    {
        return view('content.office_authorization.data');
    }




    public function edit($id)
    {

        return view('content.office_authorization.edit', compact('category'));
    }
}