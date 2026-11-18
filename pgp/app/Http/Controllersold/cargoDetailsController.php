<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class cargoDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.customs_permission.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.designation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'remark' => 'nullable|string|max:255',
        ]);

        Designation::create([
            'unique_id' => unique_id(),
            'designation' => $request->designation,
            'remarks' => $request->remark,
        ]);

        return response()->json(['status' => 'Designation Created Successfully']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        return view('content.designation.edit', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'remark' => 'nullable|string|max:255'
        ]);

        $designation = Designation::findOrFail($id);
        $designation->update([
            'designation' => $request->designation,
            'remarks' => $request->remark
        ]);

        return response()->json(['status' => 'Designation Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->update(['is_delete' => 1]);
        return response()->json(['status' => 'Designation Deleted Successfully']);
    }

    /**
     * Get data for DataTable.
     */
    public function getData()
    {
        $designations = Designation::where('is_delete', 0)->get();
        return DataTables::of($designations)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('action', function ($designation) {
                return '
                <a href="' . route('designation.edit', $designation->id) . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger delete-btn" data-id="' . $designation->id . '">Delete</button>
            ';
            })
            ->make(true);
    }

}