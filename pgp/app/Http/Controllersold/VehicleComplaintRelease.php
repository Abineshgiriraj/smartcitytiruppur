<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

// use App\Models\user_registration;

class VehicleComplaintRelease extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();
        // , compact( 'categories' )
        return view('content.vehicle_complaint_release.index');
    }

    public function add()
    {
        return view('content.vehicle_complaint_release.create');
    }

    public function getdata()
    {
        return view('content.vehicle_complaint_release.data');
    }

    public function edit($id)
    {

        return view('content.vehicle_complaint_release.edit', compact('category'));
    }
}
