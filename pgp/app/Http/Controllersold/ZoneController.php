<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

// use App\Helpers\helpers;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request  $request)
    {
        // $zones = ZoneCreation::where('is_delete', 0)->get();
        // return view('content.zone.index', compact('zones'));

        if ($request->ajax()) {
            $standard = ZoneCreation::all();
            return DataTables::of($standard)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('zone.edit', $row->id);
                    $deleteUrl = route('zone.destroy', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm">Delete</button>';
                    $btn .= '</form>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.zone.list');
    }


    // public function getData()
    // {
    //     try {
    //         $zones = ZoneCreation::where('is_delete', 0)->select(['id', 'zone', 'remarks']);

    //         return DataTables::of($zones)
    //             ->addIndexColumn() // Add this line to automatically add an index
    //             ->addColumn('action', function ($zone) {
    //                 $editUrl = route('zone.edit', $zone->id);
    //                 $deleteUrl = route('zone.destroy', $zone->id);

    //                 return '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
    //                         <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
    //                             ' . csrf_field() . '
    //                             ' . method_field('DELETE') . '
    //                             <button type="Update" class="btn btn-sm btn-danger"
    //                                 onclick="return confirm(\'Are you sure you want to delete this Zone?\');">Delete</button>
    //                         </form>';
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }

    public function getData(Request  $request)
    {

        
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.zone.create');
    }

    public function edit($id)
    {
        $zone = ZoneCreation::findOrFail($id);
        return view('content.zone.edit', compact('zone'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'zone' => [
    //             'required',
    //             'string',
    //             Rule::unique('zones')->where(function ($query) {
    //                 return $query->where('is_delete', 0);
    //             }),
    //         ],
    //         'remarks' => 'nullable|string|max:255',
    //     ]);

    //     ZoneCreation::create([
    //         'unique_id' => unique_id(),
    //         'zone' => $request->zone,
    //         'remarks' => $request->remarks,
    //         'is_delete' => 0, // Assuming you're setting is_delete to 0 by default
    //     ]);

    //     return redirect('/zone')->with('status', 'Zone Created Successfully');
    // }

    // new change
    public function store(Request $request)
    {

        $request->validate([
            'zone' => 'required|string|max:255',
            'remarks' => 'required|string|max:200',
            // 'description' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        
        
        $exists = ZoneCreation::where('zone',$request->zone)
                                    // ->where('zone',$request->zone)
                                    ->exists();
            if($exists){
                return response()->json(['error','Already exists'],422);
            }
                                    

            $zone = ZoneCreation::create([
            'unique_id' => unique_id(),
            'zone' => $request->zone,
            // 'district' => $request->district,
            'remarks' => $request->remarks,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at
        ]);
        return response()->json(['success' => 'Zone added successfully!', 'zone' => $zone]);
    }


    // public function update(Request $request, $id)
    // {
    //     $zone = ZoneCreation::findOrFail($id);

    //     $request->validate([
    //         'zone' => [
    //             'required',
    //             'string',
    //             Rule::unique('zones')->ignore($zone->id)->where(function ($query) {
    //                 return $query->where('is_delete', 0);
    //             }),
    //         ],
    //         'remarks' => 'nullable|string|max:255',
    //     ]);

    //     $zone->update([
    //         'zone' => $request->zone,
    //         'remarks' => $request->remarks ?: ''
    //     ]);

    //     return redirect()->route('zone.index')->with('status', 'Zone Updated Successfully');
    // }



    // new changes
    public function update(Request $request, $id)
    {
        $request->validate([
            'zone' => 'required|string|max:255',
            // 'district' => 'required|string|max:200',
            'remarks' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $exist_update = ZoneCreation::where('zone',$request->zone)
                                    ->where('remarks',$request->remarks)
                                    ->where('id', '!=', $id)
                                    ->exists();

        
         if($exist_update){
                    return response()->json(['error','Already exists'],422);
                    }

        $zone = ZoneCreation::findOrFail($id);

        $zone->update([

            'unique_id' => unique_id(),
            'zone' => $request->zone,
            // 'district' => $request->district,
            'remarks' => $request->remarks,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at
        ]);
        return redirect()->route('zone.index')->with('success', 'Zone Updated Successfully');
    }


    
    /**
     * Remove the specified resource from storage.
     */  
       
    // public function destroy(ZoneCreation $id)
    // {
    //     $id->update(['is_delete' => 1]);
    //     return redirect('/zone')->with('status', 'Designation Deleted Successfully');
    // }
    // new change
    public function destroy($id)
    { {
            $district = ZoneCreation::find($id);
            $district->is_delete = 1;
            $district->save();

            return redirect()->route('zone.index')->with('Success', 'Deleted Successfully');
        }
    }

    
}