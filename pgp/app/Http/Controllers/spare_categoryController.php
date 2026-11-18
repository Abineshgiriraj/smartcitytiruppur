<?php

namespace App\Http\Controllers;

use App\Models\spareCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class spare_categoryController extends Controller
{

    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     $categories = DB::table('spare_category')
        //         ->where('is_delete', 0)
        //         ->selectRaw('*,get_spare_group(group_id) as group_name') // You can change the selected fields as needed
        //         ->get();

        //     return DataTables::of($categories)
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($row) {
        //             $editUrl = route('spare_category.edit', $row->unique_id);
        //             $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
        //             $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
        //             return $btn;
        //         })
        //         ->rawColumns(['action'])
        //         ->make(true);
        // }

        return view('content.spare_category.list');
    }



    // Display the form to add a new center

    public function add()
    {
        return view('content.spare_category.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'category_name' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
        ]);

        // Save the validated data to the database

        spareCategory::create([
            'unique_id' => unique_id(),
            'category_id' => category_id(),
            'category_name' => $request->category_name,
            'group_id' => $request->group_id,
            'description' => $request->description,
        ]);

        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
        ]);
    }

    public function edit()
    {
        // $Category_id = spareCategory::where('unique_id', $id)
        //     ->where('is_delete', 0)
        //     ->first();

        // if (!$Category_id) {
        //     return redirect()->route('category')->with('error', 'Category not found.');
        // }

        return view('content.spare_category.form');
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'category_name' => 'required|string|max:255',
            'group_id' => 'required|string|max:255'

        ]);

        $Category = spareCategory::where('unique_id', $id);

        $Category->update([
            'category_name' => $request->category_name,
            'group_id' => $request->group_id,
            'description' => $request->description,
        ]);

        return response()->json(['Category Updated Sucessfully', 'Category' => $Category]);
    }

    public function destroy($id)
    {
        $Category = spareCategory::where('unique_id', $id)->first();
        // get actual model

        if (!$Category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $Category->is_delete = 1;
        $Category->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}



function category_id()
{
    // Get the latest non-deleted category
    $lastCategory = spareCategory::where('is_delete', 0)
        ->select('category_id')
        ->orderByDesc('id') // Or orderByDesc( 'category_id' ) if numeric
        ->first();
    // Corrected regex pattern: no spaces!
    if ($lastCategory && preg_match('/SP-CAT-(\d+)/', $lastCategory->category_id, $matches)) {
        $nextId = (int) $matches[1] + 1;
    } else {
        $nextId = 1;
    }

    // Format the next ID: CATG0001, CATG0002, etc.
    return 'SP-CATG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}
