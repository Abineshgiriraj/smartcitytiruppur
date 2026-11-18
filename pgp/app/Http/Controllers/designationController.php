<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class designationController extends Controller
{

    public function index()
{
    if (request()->ajax()) {
        $data = DB::table('designation as g')
            ->leftJoin('department as d', 'd.unique_id', '=', 'g.department') // <-- join
            ->select(
                'g.unique_id as designation_id',
                DB::raw('COALESCE(d.department, "-") as department'),         // <-- provide department
                'g.designation',
                'g.description',
                'g.is_active'
            )
            ->where('g.is_delete', 0)
            ->get();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $editUrl = route('designation.edit', $row->designation_id);
                $btn = '<a href="'.$editUrl.'" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\''.$row->designation_id.'\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->addColumn('status', function ($row) {
                return $row->is_active == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('content.designation.list');
}


    public function add()
    {
        return view('content.designation.form');
    }


    public function store(Request $request)
    {
        $request->validate([
            'department' => 'string|max:255',
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_designation(?, ?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->department,
                $request->designation,
                $request->description,
                $request->is_active
            ]);

            return response()->json(['success' => true, 'message' => 'designation created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $designation = DB::table('designation')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$designation) {
            return redirect()->back()->with('error', 'designation not found.');
        }

        return view('content.designation.form', compact('designation'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department'  => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $designation = DB::table('designation')->where('unique_id', $id)->first();
            if (!$designation) {
                return response()->json(['success' => false, 'message' => 'designation not found.'], 404);
            }

            DB::statement("CALL sp_manage_designation(?, ?, ?, ?, ?, ?, ?)", [
                'UPDATE',
                $designation->id,
                $id,
                $request->department,
                $request->designation,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'designation updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function destroy($id)
{
    try {
        $designation = DB::table('designation')->where('unique_id', $id)->first();
        if (!$designation) {
            return response()->json(['success' => false, 'message' => 'designation not found.'], 404);
        }

        DB::statement("CALL sp_manage_designation(?, ?, ?, ?, ?, ?, ?)", [
            'DELETE',
            $designation->id,
            $id,
            null,           // department
            null,           // designation
            null,           // description
            0               // is_active or flag as per your SP
        ]);

        return response()->json(['success' => true, 'message' => 'designation deleted successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
}
}
