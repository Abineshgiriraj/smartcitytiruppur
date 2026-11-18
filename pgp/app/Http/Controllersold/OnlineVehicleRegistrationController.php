<?php

namespace App\Http\Controllers;

use App\Models\online_vehicle_registration;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class OnlineVehicleRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $user_type = department::where('is_delete', 0)->get();
        return view('content.online_vehicle_registration.index');
        // return view('content.user_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.online_vehicle_registration.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'department' => 'required|string|max:255', 
        //                // 'usertype' is the name attribute given in the input tag
        // ]);

        // user_type::create([
        //     'unique_id' => unique_id(),
        //     'department' => $request->department,
        //     'description' => $request->description                     // 'user_type' is the name of the column in database
        // ]);

        return response()->json(['status' => 'Department Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(user_type $user_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vehicle_registration = vehicle_registration::findOrFail($id);
        // return view('content.user_type.edit', compact('user_type'));
        return view('content.online_vehicle_registration.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'usertype' => 'required|string|max:255',
        //     'status' => 'required|boolean',
        // ]);

        // $user_type = user_type::findOrFail($id);
        // $user_type->update([
        //     'user_type' => $request->usertype,
        //     'is_active' => $request->status,
        // ]);

        return response()->json(['status' => 'Department updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     $user_type = department::findOrFail($id);
    //     $user_type->update(['is_delete' => 1]);
    //     return response()->json(['status' => 'Department Deleted Successfully']);
    // }

    // public function getData()
    // {
    //     $department = department::where('is_delete', 0)->get();
    //     return DataTables::of($department)
    //         // ->addIndexColumn() // Automatically adds the serial number
    //         // ->addColumn('action', function ($user_types) {
    //         //     return
    //         //         '<a href="' . route('department_creation.edit', $user_types->id) . '" class="btn btn-sm btn-warning">Edit</a>
    //         //     <button class="btn btn-sm btn-danger delete-btn" data-id="' . $user_types->id . '">Delete</button>';
    //         // })
    //         // ->make(true);
    // }
}