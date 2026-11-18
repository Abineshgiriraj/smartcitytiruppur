<?php

namespace App\Http\Controllers;

use App\Models\ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class wardController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $data = DB::table('ward')
                ->join('zone', 'ward.zone', '=', 'zone.unique_id')
                ->select(
                    'ward.unique_id as ward_id',
                    'ward.ward',
                    'ward.description',
                    'ward.is_active',
                    'zone.zone as zone_name'
                )
                ->where('ward.is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('ward.edit', $row->ward_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->ward_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.ward.list'); // Replace with your actual Blade view name
    }


    public function add()
    {
        return view('content.ward.form');
    }


    public function store(Request $request)
    {
        $request->validate([
            'zone'        => 'required|string|max:255',
            'ward'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_ward(?, ?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->zone,
                $request->ward,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Ward created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
        $ward = DB::table('ward')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$ward) {
            return redirect()->back()->with('error', 'Ward not found.');
        }

        return view('content.ward.form', compact('ward'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'zone'        => 'required|string|max:255',
            'ward'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $ward = DB::table('ward')->where('unique_id', $id)->first();
            if (!$ward) {
                return response()->json(['success' => false, 'message' => 'Ward not found.'], 404);
            }

            DB::statement("CALL sp_manage_ward(?, ?, ?, ?, ?, ?, ?)", [
                'UPDATE',
                $ward->id,
                $id,
                $request->zone,
                $request->ward,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Ward updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function destroy($id)
    {
        try {
            $ward = DB::table('ward')->where('unique_id', $id)->first();
            if (!$ward) {
                return response()->json(['success' => false, 'message' => 'Ward not found.'], 404);
            }

            DB::statement("CALL sp_manage_ward(?, ?, ?, ?, ?, ?, ?)", [
                'DELETE',
                $ward->id,
                $id,
                '',
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Ward deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
