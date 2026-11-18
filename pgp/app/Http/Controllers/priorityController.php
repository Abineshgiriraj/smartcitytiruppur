<?php

namespace App\Http\Controllers;

use App\Models\PriorityCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class priorityController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('priority')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('priority.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.priority.list'); // Replace with your actual Blade view name
    }

    public function add()
    {
        return view('content.priority.form');
    }


   public function store(Request $request)
    {
        $request->validate([
            'priority'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);
// dd($request);
        try {
            DB::statement("CALL sp_manage_priority(?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->priority,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Priority created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $priority = DB::table('priority')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$priority) {
            return redirect()->back()->with('error', 'Priority not found.');
        }

        return view('content.priority.form', compact('priority'));
    }


   public function update(Request $request, $id)
    {
        $request->validate([
            'priority'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $priority = DB::table('priority')->where('unique_id', $id)->first();
            if (!$priority) {
                return response()->json(['success' => false, 'message' => 'priority not found.'], 404);
            }

            DB::statement("CALL sp_manage_priority(?, ?, ?, ?, ?, ?)", [
                'UPDATE',
                $priority->id,
                $id,
                $request->priority,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Priority updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $priority = DB::table('priority')->where('unique_id', $id)->first();
            if (!$priority) {
                return response()->json(['success' => false, 'message' => 'Priority not found.'], 404);
            }

            DB::statement("CALL sp_manage_priority(?, ?, ?, ?, ?, ?)", [
                'DELETE',
                $priority->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Priority deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
