<?php

namespace App\Http\Controllers;

use App\Models\academic_yearCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class academicYearController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('academic_year')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('academic_year.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.academic_year.list'); // Replace with your actual Blade view name
    }

    public function add()
    {
        return view('content.academic_year.form');
    }


   public function store(Request $request)
    {
        $request->validate([
            'academic_year'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_academic_year(?,  ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->academic_year,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'academic_year created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $academic_year = DB::table('academic_year')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$academic_year) {
            return redirect()->back()->with('error', 'academic_year not found.');
        }

        return view('content.academic_year.form', compact('academic_year'));
    }


   public function update(Request $request, $id)
    {
        $request->validate([
            'academic_year'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $academic_year = DB::table('academic_year')->where('unique_id', $id)->first();
            if (!$academic_year) {
                return response()->json(['success' => false, 'message' => 'Academic year not found.'], 404);
            }

            DB::statement("CALL sp_manage_academic_year(?, ?,  ?, ?, ?, ?)", [
                'UPDATE',
                $academic_year->id,
                $id,
                $request->academic_year,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Academic year updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $academic_year = DB::table('academic_year')->where('unique_id', $id)->first();
            if (!$academic_year) {
                return response()->json(['success' => false, 'message' => 'academic_year not found.'], 404);
            }

            DB::statement("CALL sp_manage_academic_year(?,  ?, ?, ?, ?, ?)", [
                'DELETE',
                $academic_year->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Academic year deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
