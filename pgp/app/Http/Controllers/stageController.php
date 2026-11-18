<?php

namespace App\Http\Controllers;

use App\Models\StageCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class stageController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('stage')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('stage.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.stage.list'); // Replace with your actual Blade view name
    }

    public function add()
    {
        return view('content.stage.form');
    }


   public function store(Request $request)
    {
        $request->validate([
            'stage'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_stage(?,  ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->stage,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Stage created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $stage = DB::table('stage')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$stage) {
            return redirect()->back()->with('error', 'Stage not found.');
        }

        return view('content.stage.form', compact('stage'));
    }


   public function update(Request $request, $id)
    {
        $request->validate([
            'stage'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $stage = DB::table('stage')->where('unique_id', $id)->first();
            if (!$stage) {
                return response()->json(['success' => false, 'message' => 'Stage not found.'], 404);
            }

            DB::statement("CALL sp_manage_stage(?, ?,  ?, ?, ?, ?)", [
                'UPDATE',
                $stage->id,
                $id,
                $request->stage,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Stage updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $stage = DB::table('stage')->where('unique_id', $id)->first();
            if (!$stage) {
                return response()->json(['success' => false, 'message' => 'Stage not found.'], 404);
            }

            DB::statement("CALL sp_manage_stage(?,  ?, ?, ?, ?, ?)", [
                'DELETE',
                $stage->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Stage deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
