<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class RepeatedComplaintsController extends Controller
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

        return view('content.repeated_complaints.list'); // Replace with your actual Blade view name
    }

    public function add()
    {
        return view('content.zone.form');
    }



   public function store(Request $request)
{
    // Validate inputs
    $request->validate([
        'zone'   => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    // Check if zone with same name already exists and not deleted
    $zone = ZoneCreation::where('zone', $request->zone_name)
        ->where('is_delete', 0)
        ->first();

    if ($zone) {
        return response()->json(['message' => 'Zone name already exists.'], 422);
    }

    // Create new zone
    $zone = new ZoneCreation();
    $zone->zone   = $request->zone;
    $zone->description = $request->description;
    $zone->unique_id   = unique_id(); // Make sure you have this helper function
    $zone->is_delete   = 0; // Soft delete flag
    $zone->save();

    return response()->json(['message' => 'Zone created successfully.']);
}

    public function edit($id)
    {
        $zone = ZoneCreation::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$zone) {
            return redirect()->back()->with('error', 'zone not found.');
        }

        return view('content.zone.form', compact('zone'));
    }


    public function update(Request $request, zone $zone, $id)
    {

        // Validate the input data
        $request->validate([
            'zone' => 'required|string',
            'description' => 'string',
        ]);



        $zone = ZoneCreation::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        $zone->update([
            'zone' => $request->zone,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Zone updated successfully.', 'zone' => $zone]);
    }

    public function destroy($id)
    {
        $zone = ZoneCreation::where('unique_id', $id)->first();
        // get actual model

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $zone->is_delete = 1;
        $zone->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }



}
