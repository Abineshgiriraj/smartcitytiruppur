<?php

namespace App\Http\Controllers;

// use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class departmentController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $data = DB::table('department')
                ->select(
                    'department.unique_id as department_id',
                    'department.department',
                    'department.description',
                    'department.is_active',
                )
                ->where('department.is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('department.edit', $row->department_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->department_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                        return $row->is_active == 1 ? 'Active' : 'Inactive';  })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.department.list'); // Replace with your actual Blade view name
    }


    public function add()
    {
        return view('content.department.form');
    }


    public function store(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_department(?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->department,
                $request->description,
                $request->is_active
            ]);

            return response()->json(['success' => true, 'message' => 'department created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $department = DB::table('department')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$department) {
            return redirect()->back()->with('error', 'department not found.');
        }

        return view('content.department.form', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $department = DB::table('department')->where('unique_id', $id)->first();
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'department not found.'], 404);
            }

            DB::statement("CALL sp_manage_department(?, ?, ?, ?, ?, ?)", [
                'UPDATE',
                $department->id,
                $id,
                $request->department,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'department updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function destroy($id)
    {
        try {
            $department = DB::table('department')->where('unique_id', $id)->first();
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'department not found.'], 404);
            }

            DB::statement("CALL sp_manage_department(?, ?, ?, ?, ?, ?)", [
                'DELETE',
                $department->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'department deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
