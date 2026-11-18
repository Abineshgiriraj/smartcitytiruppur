<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\port_vehicle_registration;

class PortVehicleRegistration extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.port_vehicle_registration.index');
    }
    public function create()
    {
        return view('content.port_vehicle_registration.create');
    }

    public function renewal()
    {
        return view('content.port_vehicle_registration.renewal');
    }


    public function edit($id)
    {

        return view('content.port_vehicle_registration.edit', compact('category'));
    }

    // public function destroy($id)
    // {
    //     $category = CategoryCreation::find($id);
    //     $category->is_delete = 1;
    //     $category->save();

    //     return redirect()->route('category_list')->with('Success', 'Deleted Successfully');
    // }
}