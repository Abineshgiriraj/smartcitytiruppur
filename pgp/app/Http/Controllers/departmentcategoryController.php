<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DepartmentCategoryController extends Controller
{
   public function index()
{
    if (request()->ajax()) {
        $data = DB::table('department_category')
            ->join('department', 'department_category.department', '=', 'department.unique_id')
            ->select(
                'department_category.unique_id as dept_id', // Ensure you have the correct parameter name here
                'department.department',
                'department_category.department_category',
                'department_category.description',
                'department_category.is_active',
            )
            ->where('department_category.is_delete', 0)
            ->get();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a  title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->dept_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->addColumn('status', function ($row) {
                return $row->is_active == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('content.departmentcategory.list');
}

    public function add()
    {
        return view('content.departmentcategory.form');
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:255',
            'dept_category' => [
                'required',
                'string',
                'max:255',
                // Unique per department (ignore deleted)
                Rule::unique('department_category', 'department_category')
                    ->where(fn($q) => $q->where('department', $request->department)
                                        ->where('is_delete', 0)),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);
// dd($request);
        try {
            DB::statement("CALL sp_manage_dept_category(?, ?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->department,
                $request->dept_category,
                $request->description,
                $request->is_active
            ]);

            return response()->json(['success' => true, 'message' => 'department category created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function edit($id)
    {
        $departmentcategory = DB::table('department_category')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$departmentcategory) {
            return redirect()->back()->with('error', 'Department category not found.');
        }

        return view('content.departmentcategory.form', compact('departmentcategory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department' => 'required|string|max:255',
            'dept_category' => [
                'required',
                'string',
                'max:255',
                // Unique per department (exclude current record by unique_id)
                Rule::unique('department_category', 'department_category')
                    ->where(fn($q) => $q->where('department', $request->department)
                                        ->where('is_delete', 0))
                    ->ignore($id, 'unique_id'),
            ],
            'description' => 'nullable|string',
            'is_active' => 'required|string',
        ]);

        try {
            $department_category = DB::table('department_category')->where('unique_id', $id)->first();
            if (!$department_category) {
                return response()->json(['success' => false, 'message' => 'Department category not found.'], 404);
            }

            // placeholder count matches args (7)
            DB::statement("CALL sp_manage_dept_category(?, ?, ?, ?, ?, ?, ?)", [
                'UPDATE',
                $department_category->id,
                $id,
                $request->department,
                $request->dept_category,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Department category updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

public function destroy($id)
{
    try {
        $department_category = DB::table('department_category')->where('unique_id', $id)->first();
        if (!$department_category) {
            return response()->json(['success' => false, 'message' => 'Department category not found.'], 404);
        }

        DB::statement("CALL sp_manage_dept_category(?, ?, ?, ?, ?, ?, ?)", [
            'DELETE',                 // p_action
            $department_category->id, // p_id (numeric id)
            NULL,                     // p_unique_id (unused for delete)
            NULL,                     // p_department
            NULL,                     // p_department_category
            NULL,                     // p_description
            0                         // p_is_active (ignored in delete)
        ]);

        return response()->json(['success' => true, 'message' => 'Department category deleted successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
}



}

?>
