<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class unitController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $unit = Unit::where('is_delete', 0)->get();

            return DataTables::of($unit)
                ->addIndexColumn()
                ->addColumn(
                    'action',

                    function ($row) {
                        $editUrl = route('unit.edit', $row->unique_id);
                        $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                        $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    }
                )
                ->rawColumns(['action']) // Needed to render HTML
                ->make(true);
        }

        return view('content.unit.list');
        // No need to pass $unit, it's loaded via AJAX
    }

    // Display the form to add a new center

    public function add()
    {
        return view('content.unit.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);

        // Save the validated data to the database

        Unit::create([
            'unique_id' => unique_id(),
            'unit_id' => unit_id(),
            'unit_name' => $request->unit_name,
            'description' => $request->description,
        ]);

        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully!',
        ]);
    }

    public function edit($id)
    {
        $Unit_id = Unit::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        if (!$Unit_id) {
            return redirect()->route('unit')->with('error', 'Unit not found.');
        }

        return view('content.unit.form', compact('Unit_id'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'unit_name' => 'required|string|max:255'

        ]);

        $Unit = Unit::where('unique_id', $id);

        $Unit->update([
            'unit_name' => $request->unit_name,
            'description' => $request->description,
        ]);

        return response()->json(['Unit Updated Sucessfully', 'Unit' => $Unit]);
    }

    public function destroy($id)
    {
        $Unit = Unit::where('unique_id', $id)->first();
        // get actual model

        if (!$Unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $Unit->is_delete = 1;
        $Unit->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}


function unit_id()
{
    $lastUnit = Unit::where('is_delete', 0)
        ->select('unit_id')
        ->orderByDesc('id')
        ->first();

    // ❌ No spaces in regex pattern
    if ($lastUnit && preg_match('/UNIT(\d+)/', $lastUnit->unit_id, $matches)) {
        $nextId = (int) $matches[1] + 1;
    } else {
        $nextId = 1;
    }

    return 'UNIT' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}
