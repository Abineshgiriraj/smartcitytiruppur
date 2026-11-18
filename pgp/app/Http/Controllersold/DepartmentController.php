<?php

namespace App\Http\Controllers;

use App\Models\department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $port_department = DB::table('department_creation')
                ->select('id', 'department', 'description')
                ->where('is_delete', '=', 0)
                ->where('is_active', '=', 1)
                ->get();

            return DataTables::of($port_department)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $edit_url = route('department.edit', $row->id);
                    $delete_url = route('department.delete', $row->id);

                    $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="delete-btn text-danger" data-id="' . $row->id . '" title="Delete" style="background:none; margin-left: 10px; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</button>';
                    return $btn;
                })
                ->make(true);
        }

        // $user_type = department::where('is_delete', 0)->get();
        return view('content.department_creation.index');
        // return view('content.user_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.department_creation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        $exists = department::where('department', $request->department)
            ->where('is_delete', '=', 0)
            ->exists();
        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }

        $port_department =  department::create([
            'unique_id' => unique_id(),
            'department' => $request->department,
            'description' => $request->description,

        ]);

        return response()->json(['status' => 'Department Created Successfully', 'port_department' => $port_department]);
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $department = department::findOrFail($id);
        // return view('content.user_type.edit', compact('user_type'));
        return view('content.department_creation.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'department' => 'required|string|max:255',
            'description' => 'required|string|max:255',

        ]);

        $port_department = department::findOrFail($id);

        $port_department->update([

            'department' => $request->department,
            'description' => $request->description
        ]);


        return response()->json(['Department Updated Sucessfully', 'port_deparment' => $port_department]);
    }

    public function destroy($id)
    {
        $department = department::find($id);

        if ($department) {
            $department->is_delete = 1; // Mark as deleted
            $department->save();

            return response()->json(['success' => true, 'message' => 'Category marked as deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
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