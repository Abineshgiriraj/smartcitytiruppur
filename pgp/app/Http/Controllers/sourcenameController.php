<?php

namespace App\Http\Controllers;

use App\Models\SourcenameCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class sourcenameController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('source_name')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('source_name.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.source_name.list'); // Replace with your actual Blade view name
    }

    public function add()
    {
        return view('content.source_name.form');
    }


   public function store(Request $request)
    {
        $request->validate([
            'source_name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_source_name(?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->source_name,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Source Name created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $source_name = DB::table('source_name')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$source_name) {
            return redirect()->back()->with('error', 'Source name not found.');
        }

        return view('content.source_name.form', compact('source_name'));
    }


   public function update(Request $request, $id)
    {
        $request->validate([
            'source_name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $source_name = DB::table('source_name')->where('unique_id', $id)->first();
            if (!$source_name) {
                return response()->json(['success' => false, 'message' => 'Source Name not found.'], 404);
            }

            DB::statement("CALL sp_manage_source_name(?, ?,  ?, ?, ?, ?)", [
                'UPDATE',
                $source_name->id,
                $id,
                $request->source_name,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Source name updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $source_name = DB::table('source_name')->where('unique_id', $id)->first();
            if (!$source_name) {
                return response()->json(['success' => false, 'message' => 'Source Name not found.'], 404);
            }

            DB::statement("CALL sp_manage_source_name(?,  ?, ?, ?, ?, ?)", [
                'DELETE',
                $source_name->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Source Name deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
