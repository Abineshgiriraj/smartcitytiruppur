<?php

namespace App\Http\Controllers;

use App\Models\groupCreation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class group_creationController extends Controller
{

    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     $group = groupCreation::where('is_delete', 0)->get();

        //     return DataTables::of($group)
        //         ->addIndexColumn()
        //         ->addColumn(
        //             'action',

        //             function ($row) {
        //                 $editUrl = route('group_creation.edit', $row->unique_id);
        //                 $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
        //                 $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
        //                 return $btn;
        //             }
        //         )
        //         ->rawColumns(['action']) // Needed to render HTML
        //         ->make(true);
        // }

        return view('content.group_creation.list');
    }

    // Display the form to add a new center

    public function add()
    {
        return view('content.group_creation.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'group_name' => 'required|string|max:255'
        ]);

        $lastGroupId = groupCreation::orderBy('id', 'desc')->first();

        // Extract and increment group ID
        if ($lastGroupId && $lastGroupId->group_id) {
            // Remove prefix 'G' and convert to integer
            $lastId = intval(substr($lastGroupId->group_id, 1));
            $newIdNumber = $lastId + 1;
        } else {
            $newIdNumber = 1;
        }

        // Format group ID like G001, G002, etc.
        $newGroupId = 'G' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

        // Save the validated data to the database
        // $create = Center::create( $validatedData );

        groupCreation::create([
            'unique_id' => unique_id(),
            'group_id' => $newGroupId,
            'group_name' => $request->group_name,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at,
        ]);

        // dd( $category );
        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Group Name created successfully!',
        ]);
    }

    public function edit()
    {
        // $group_creation = groupCreation::where('unique_id', $id)
        //     ->where('is_delete', 0)
        //     ->first();

        // if (!$group_creation) {
        //     return redirect()->route('group_creation')->with('error', 'Group Name not found.');
        // }

        return view('content.group_creation.form');
    }

    public function update(Request $request, $id)
    {
        // dd($request, $id);
        $request->validate([
            'group_name' => 'required|string|max:255'
        ]);

        $group = groupCreation::where('unique_id', $id);
        $group->update([
            'group_name' => $request->group_name,
            'description' => $request->description,
        ]);

        return response()->json(['group Updated Sucessfully', 'group' => $group]);
    }

    public function destroy($id)
    {
        $group = groupCreation::where('unique_id', $id)->first();

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'group not found.']);
        }

        // Soft delete by setting is_delete flag
        $group->is_delete = 1;
        $group->save();

        return response()->json(['success' => true, 'message' => 'Deleted successfully!']);
    }

}
