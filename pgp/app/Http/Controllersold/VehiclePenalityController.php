<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\user_registration;

class VehiclePenalityController extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.vehicle_penality_check.index');
    }
    public function add()
    {
        return view('content.vehicle_penality_check.create');
    }

    public function getdata()
    {
        return view('content.vehicle_penality_check.data');
    }




    public function edit($id)
    {

        return view('content.vehicle_penality_check.edit', compact('category'));
    }
}
