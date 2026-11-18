<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\employeee_designation;

class vehicle_accessController extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.vehicle_access_center.index');
    }
    public function add()
    {
        return view('content.port_permit_validation_with_fee.edit');
    }




    public function edit($id)
    {

        return view('content.EmployeeDesignation.edit', compact('category'));
    }

    public function destroy($id)
    {
        $category = CategoryCreation::find($id);
        $category->is_delete = 1;
        $category->save();

        return redirect()->route('category_list')->with('Success', 'Deleted Successfully');
    }
}