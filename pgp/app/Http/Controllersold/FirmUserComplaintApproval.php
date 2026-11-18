<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\person_penality;

class FirmUserComplaintApproval extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.firm_user_complaint_approval.index');
    }
    public function add()
    {
        return view('content.firm_user_complaint_approval.create');
    }

    public function getdata()
    {
        return view('content.firm_user_complaint_approval.data');
    }




    public function edit($id)
    {

        return view('content.firm_user_complaint_approval.edit', compact('category'));
    }
}