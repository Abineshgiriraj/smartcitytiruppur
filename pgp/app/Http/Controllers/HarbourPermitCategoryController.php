<?php

namespace App\Http\Controllers;

use App\Models\Portcategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;





class HarbourPermitCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {


            $port_category  = DB::table('port_category')
                ->select('id', 'category', 'sub_category', 'description')
                ->where('is_delete', '=', 0)
                ->get();
            // dd($port_category);
            return DataTables::of($port_category)
                ->addIndexColumn()
                ->addcolumn('action', function ($row) {
                    // $edit_url = route('harbour_permit_category.edit', $row->id);
                    // $delete_url = route('harbour_permit_category.delete', $row->id);
                    $csrfToken = csrf_token();

                return '<a href="' . route('hepCategory.edit', $row->id) . '" class="text-primary">
                <i class="fas fa-edit"></i>
            </a>
            <a class="text-danger ml-2" onclick="confirmDelete('. $row->id . ')">
                <i class="fas fa-trash-alt"></i>
            </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }




        // $user_type = department::where('is_delete', 0)->get();
        return view('content.harbour_permit_category.index');
        // return view('content.user_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.harbour_permit_category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            // 'description' =>  'string|max:300',


        ]);

        $port_category = Portcategory::create([
            'unique_id' => unique_id(),
            'category'  => $request->category,
            'sub_category' => $request->sub_category,
            'description'   => $request->description,

        ]);


        return response()->json(['success,Port category added Successfully', 'Port_category' => $port_category]);
    }


    public function edit($id)
    {
        $port_category = Portcategory::findOrFail($id);
        // return view('content.user_type.edit', compact('user_type'));
        return view('content.harbour_permit_category.edit', compact('port_category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        $request->validate([
            'category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            // 'description' =>  'string|max:300',
        ]);


        $port_category = Portcategory::findOrFail($id);

        $port_category->update([
            'unique_id' => unique_id(),
            'category'  => $request->category,
            'sub_category' => $request->sub_category,
            'description'   => $request->description,

        ]);

        // $request->validate([
        //     'usertype' => 'required|string|max:255',
        //     'status' => 'required|boolean',
        // ]);

        // $user_type = user_type::findOrFail($id);
        // $user_type->update([
        //     'user_type' => $request->usertype,
        //     'is_active' => $request->status,
        // ]);

        return response()->json(['status' => 'PortCategory updated successfully']);
    }


    // public function destroy($id)
    // {
    //     $portCategory = Portcategory::find($id);
    //     if ($portCategory) {
    //         $portCategory->is_delete = 1; // Mark as deleted
    //         $portCategory->save();

    //         return response()->json(['success' => true, 'message' => 'Category marked as deleted.']);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    // }

    // public function destroy($id)
    // {
    //     $portCategory = Portcategory::find($id);
    //     if ($portCategory) {
    //         $portCategory->delete();

    //         return redirect()->json(['Success', 'Deleted Successfully']);

    //         // return redirect()->route('habour_permit_category');
    //     }

    //     return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    // }
    public function destroy($id )
    {
           // Find the company by ID
           $portCategory = Portcategory::find( $id );
   
           // Check if the portCategory exists
           if ( !$portCategory ) {
            return redirect()->route('hepCategory');
           }
   
           // Soft delete by setting is_delete flag
           $portCategory->is_delete = 1;
           $portCategory->save();
   
           // Redirect back to the list page with a success message
           return redirect()->route('hepCategory')->with( 'success', 'Deleted Successfully' );
       }
}