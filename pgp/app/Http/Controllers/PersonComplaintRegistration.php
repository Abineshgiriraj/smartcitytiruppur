<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\person_penality;

class PersonComplaintRegistration extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.person_complaint_registration.index');
    }
    public function add()
    {
        return view('content.person_complaint_registration.create');
    }

    public function getdata()
    {
        return view('content.person_complaint_registration.data');
    }




    public function edit($id)
    {

        return view('content.person_complaint_registration.edit', compact('category'));
    }
}