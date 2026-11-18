<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\CategoryCreation;

class category_creation extends Controller
{
    public function index(Request $request)
    {
            
        $categories = CategoryCreation::where('is_delete',0)->get();
        if ($request->ajax()) {
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('category.edit', $row->id);
                    $deleteUrl = route('category.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm" >Delete</button>';
                    $btn .= '</form>';

                    // $btn = '<a href="' . route('category_edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    // $btn .= ' <button onclick="deleteCategory(' . $row->id . ')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.category_creation.list');
    }
    public function add()
    {
        return view('content.category_creation.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $exists = CategoryCreation::where('category_type',$request->category_type)
                                    ->exists();

        if($exists){
            return response()->json(['error','Already Exist'],422);
        }

        $category =   CategoryCreation::create([
            'unique_id' => unique_id(),
            'category_type' => $request->category_type,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at,
        ]);

        return response()->json(['success' => 'Category added successfully!', 'category' => $category]);
    }

   

    public function edit($id)
    {
        $category = CategoryCreation::findOrFail($id);
        return view('content.category_creation.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        
        $exists_update= CategoryCreation::where('category_type',$request->category_type)
                                        ->where('id', '!=', $id)
                                    ->exists();

        if($exists_update){
            
            return response()->json(['error','Already Exist'],422);
        }

        $category = CategoryCreation::findOrFail($id);
        $category->update([
            'category_type' => $request->category_type,
            'description' => $request->description,
        ]);

        return redirect()->route('category_list')->with('success', 'Category updated successfully');
    }
    public function destroy($id)
    {
        $category = CategoryCreation::find($id);
        $category->is_delete = 1;
        $category->save();

        return redirect()->route('category_list')->with('Success', 'Deleted Successfully');

       
    }
}