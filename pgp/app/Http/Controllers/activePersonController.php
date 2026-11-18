<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class activePersonController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('zone')
                ->where('is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('zone.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.active_person.list'); // Replace with your actual Blade view name
    }

public function statusDetails($status)
{
    return view('content.active_person.status_window', compact('status'));
}

    public function add()
    {
        return view('content.zone.form');
    }


   public function store(Request $request)
    {
        $request->validate([
            'zone'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            DB::statement("CALL sp_manage_zone(?,  ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->zone,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'zone created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function edit($id)
    {
    

        return view('content.status_window', compact('id'));
    }


   public function update(Request $request, $id)
    {
        $request->validate([
            'zone'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|string',
        ]);

        try {
            // Find DB id from unique_id
            $zone = DB::table('zone')->where('unique_id', $id)->first();
            if (!$zone) {
                return response()->json(['success' => false, 'message' => 'Zone not found.'], 404);
            }

            DB::statement("CALL sp_manage_zone(?, ?,  ?, ?, ?, ?)", [
                'UPDATE',
                $zone->id,
                $id,
                $request->zone,
                $request->description,
                $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Zone updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $zone = DB::table('zone')->where('unique_id', $id)->first();
            if (!$zone) {
                return response()->json(['success' => false, 'message' => 'zone not found.'], 404);
            }

            DB::statement("CALL sp_manage_zone(?,  ?, ?, ?, ?, ?)", [
                'DELETE',
                $zone->id,
                $id,
                '',
                '',
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Zone deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
