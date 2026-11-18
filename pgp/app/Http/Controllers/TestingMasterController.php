<?php

namespace App\Http\Controllers;

use App\Models\TestingMaster;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class TestingMasterController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $testing_masters = DB::table('testing_master as tm')
                ->where('tm.is_delete', 0)
                ->select('tm.*', DB::raw('get_group_name_by_id(tm.product_group) as group_name'))
                ->get();

            return DataTables::of($testing_masters)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('testing_master.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.testing_master.list');
    }



    // Display the form to add a new center

    public function add()
    {
        return view('content.testing_master.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'test_type' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
            'testing_name' => 'required|string|max:255'
        ]);

        // Save the validated data to the database

        TestingMaster::create([
            'unique_id' => unique_id(),
            'test_type' => $request->test_type,
            'product_group' => $request->group_id,
            'testing_name' => $request->testing_name,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Test Master created successfully!',
        ]);
    }

    public function edit($id)
    {
        $testing_master_val = TestingMaster::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        if (!$testing_master_val) {
            return redirect()->route('testing_master')->with('error', 'Testing Master not found.');
        }

        return view('content.testing_master.form', compact('testing_master_val'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'test_type' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
            'testing_name' => 'string|max:255',

        ]);

        $test_master = TestingMaster::where('unique_id', $id);

        $test_master->update([
            'test_type' => $request->test_type,
            'product_group' => $request->group_id,
            'testing_name' => $request->testing_name,
            'description' => $request->description,
            'is_active' => $request->status,
        ]);

        return response()->json(['Test Master Updated Sucessfully', 'test_master' => $test_master]);
    }

    public function destroy($id)
    {
        $test_master = TestingMaster::where('unique_id', $id)->first();
        // get actual model

        if (!$test_master) {
            return response()->json([
                'success' => false,
                'message' => 'Test Master not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $test_master->is_delete = 1;
        $test_master->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }

}