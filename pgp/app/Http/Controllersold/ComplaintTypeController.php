<?php

namespace App\Http\Controllers;

use App\Models\complaint_type;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class ComplaintTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complaint_type = complaint_type::where('is_delete', 0)->get();
        return view('content.complaint_type.index', compact('complaint_type'));
        // return view('content.user_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.complaint_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'complaint_type' => 'required|string|max:255',    
            'status' => 'required|max:255',    
        ]);

        complaint_type::create([
            'unique_id' => unique_id(),
            'complaint_type' => $request->complaint_type,
            'is_active' => $request->status,
                     
        ]);

        return response()->json(['status' => 'Complaint Type Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(complainttype $user_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $complaint_type = complaint_type::findOrFail($id);
        // return view('content.user_type.edit', compact('user_type'));

        return view('content.complaint_type.edit', compact('complaint_type'));
    }

    /**
     * Update the specified resource incomplaint_type storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'complaint_type' => 'string',
            'status' => 'required|boolean',
        ]);

        $complaint_type = complaint_type::findOrFail($id);
        $complaint_type->update([
            'complaint_type' => $request->complaint_type,
            'is_active' => $request->status,
        ]);
// dd($complaint_type);
        return response()->json(['status' => 'Complaint Type updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user_type = complaint_type::findOrFail($id);
        $user_type->update(['is_delete' => 1]);
        return response()->json(['status' => 'Complaint Type Deleted Successfully']);
    }

    public function getData()
    {
        $complaint_type = complaint_type::where('is_delete', 0)->get();
        return DataTables::of($complaint_type)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('action', function ($complaint_types) {
                return
                    '<a href="' . route('complaint_type.edit', $complaint_types->id) . '" class="text-primary"> <i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' . $complaint_types->id . '"><i class="fas fa-trash-alt"></i></a>';
            })
            ->make(true);
    }
}