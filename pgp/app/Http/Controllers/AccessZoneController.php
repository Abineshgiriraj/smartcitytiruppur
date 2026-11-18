<?php

namespace App\Http\Controllers;

use App\Models\AcessZone;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class AccessZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {


            $access_zone  = DB::table('access_zone')
                ->select('id', 'zone_type', 'description', 'status')
                ->where('is_delete', '=', 0)
                ->get();
            // dd($acess_zone);
            return DataTables::of($access_zone)
                ->addIndexColumn()
                ->addcolumn('action', function ($row) {
                    // $edit_url = route('harbour_permit_category.edit', $row->id);
                    // $delete_url = route('harbour_permit_category.delete', $row->id);
                    $csrfToken = csrf_token();

                return '<a href="' . route('accessZone.edit', $row->id) . '" class="text-primary">
                <i class="fas fa-edit"></i>
            </a>
            <a class="text-danger ml-2" onclick="confirmDelete('. $row->id . ')">
                <i class="fas fa-trash-alt"></i>
            </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('content.access_zone.index');
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.access_zone.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'zone_type' => 'required|string|max:255',
            'status' => 'string|max:255',
            // 'description' =>  'string|max:300',


        ]);

        $access_zones = AcessZone::create([
            'unique_id' => unique_id(),
            'zone_type'  => $request->zone_type,
            'description' => $request->description,
            'status'   => $request->status,

        ]);


        return response()->json(['success,Access Zone  added Successfully', 'access_zones' => $access_zones]);
    }


    public function edit($id)
    {
        $access_zone = AcessZone::findOrFail($id);
        // return view('content.user_type.edit', compact('user_type'));
        return view('content.access_zone.edit', compact('access_zone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validation (if necessary)
        $request->validate([
            'zone_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|string',
        ]);
    
        // Update the access zone record
        $access_zone = AcessZone::findOrFail($id);
        $access_zone->zone_type = $request->zone_type;
        $access_zone->description = $request->description;
        $access_zone->status = $request->status;
        $access_zone->save();
    
        return response()->json(['success' => true, 'message' => 'Zone updated successfully.']);
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
           $acess_zone = AcessZone::find( $id );
   
           // Check if the acess_zone exists
           if ( !$acess_zone ) {
            return redirect()->route('accessZone');
           }
   
           // Soft delete by setting is_delete flag
           $acess_zone->is_delete = 1;
           $acess_zone->save();
   
           // Redirect back to the list page with a success message
           return redirect()->route('accessZone')->with( 'success', 'Deleted Successfully' );
       }
}