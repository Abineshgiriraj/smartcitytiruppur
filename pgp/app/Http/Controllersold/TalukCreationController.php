<?php

namespace App\Http\Controllers;

use App\Models\DistrictCreation;
use App\Models\Taluk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;


class TalukCreationController extends Controller
{
    public function index(Request  $request)
    {
        // $talukNames = Taluk::all();
      
        
    //     if (request()->ajax()) {
            
    //         // $data = Taluk::select(['id', 'zone_name', 'district_name', 'taluk_name', 'remark'])
    //         //               ->where('is_delete', 0);

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($row) {
    //                 $editUrl = route('taluk_creation.edit', $row->id);
    //                 $deleteUrl = route('taluk_creation.destroy', $row->id);

    //                 return '
    //                 <a href="'.$editUrl.'" class="btn btn-sm btn-primary">Edit</a>
    //                 <button class="btn btn-sm btn-danger delete-btn" data-url="'.$deleteUrl.'">Delete</button>
    //                 ';
    //             })
    //             ->make(true);
    //     }

    //     return view('content.taluk_creation.index');
    // }
    if ($request->ajax()) {
        $data = DB::table('taluk')
        ->Join('zones','taluk.zone_name','=','zones.unique_id')
        ->Join('district','taluk.district_name','=','district.unique_id')
        ->select('taluk.id','zones.zone as zone_name','district.district as district_name', 'taluk.taluk_name as taluk_name','taluk.remark')
        ->where('taluk.is_delete',0)
        ->get();
        
        // $standard = Taluk::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $editUrl = route('taluk_creation.edit', $row->id);
                $deleteUrl = route('taluk_creation.destroy', $row->id);
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

    return view('content.taluk_creation.list');
}
    public function getData()
    {
        // $taluks = DB::table('taluk')
        //             ->Join('zones','taluk.zone_name','=','zones.unique_id')
        //             ->Join('district','taluk.district_name','=','district.unique_id')
        //             ->select('taluk.id','zones.zone as zone_name','district.district as district_name','taluk.taluk_name as taluk_name','taluk.remark')
        //             ->where('taluk.is_delete',0)
        //             ->get();

        // return DataTables::of($taluks)
        //     ->addIndexColumn()
        //     ->addColumn('actions', function ($taluk) {
        //         return '<button class="btn btn-warning btn-sm edit" data-url="'.route('taluk_creation.edit', $taluk->id).'">Edit</button>
        //             <button class="btn btn-danger btn-sm delete-btn" data-url="'.route('taluk_creation.destroy', $taluk->id).'"">Delete</button>';
        //     })
        //     ->rawColumns(['actions'])
        //     ->make(true);
    }

    public function create()
    {
        return view('content.taluk_creation.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'zone_name' => 'required|string|max:255',
    //         'district_name' => 'required|string|max:255',
    //         'taluk_name' => [
    //           'required',
    //           'string',
    //           Rule::unique('taluks')->where(function ($query) {
    //               return $query->where('is_delete', 0);
    //           }),
    //         ],
    //         'remark' => 'required|string',
    //     ]);

    //     Taluk::create([
    //         'zone_name' => $request->zone_name,
    //         'district_name' => $request->district_name,
    //         'taluk_name' => $request->taluk_name,
    //         'remark' => $request->remark,
    //     ]);

    //     return response()->json(['success' => 'Taluk created successfully.']);
    // }
    public function store(Request $request)
    {
        $id = $request->input('id');
        // Validate the request
        $request->validate([
            'zone_name' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
            'taluk_name' => [
                'required',
                'string',
                Rule::unique('taluk')->where(function ($query) {
                    return $query->where('is_delete', 0);
                })->ignore($id),
            ],
            'remark' => 'nullable|string',
        ]);

        // $data = $request->only(['zone_name', 'district_name', 'taluk_name', 'remark']);
        // if ($id) {
        //     // Update the record
        //     $record = Taluk::find($id);
        //     $record->update($data);
        // } else {
        //     // Create a new record
        //     Taluk::create($data);
        // }
        

        // Create a new Taluk record
        Taluk::create([
            'unique_id' => unique_id(),
            'zone_name' => $request->zone_name,
            'district_name' => $request->district_name,
            'taluk_name' => $request->taluk_name,
            'remark' => $request->remark,
        ]);

        // Return a JSON response indicating success
        return response()->json(['success' => 'Taluk created successfully.']);
    }

    public function edit($id)
    {
        $talukName = Taluk::findOrFail($id);

        return view('content.taluk_creation.create', compact('talukName'));
    }

    public function update(Request $request, $id)
    {
        // $request->validate([

        //     'zone_name' => 'required|string|max:255',
        //     'district_name' => 'required|string|max:255',
        //     'taluk_name' => [
        //         'required',
        //         'string',
        //         Rule::unique('taluk')->where(function ($query) {
        //             return $query->where('is_delete', 0);
        //         }),
        //     ],
        //     'remark' => 'required|string',
        // ]);

        $talukName = Taluk::findOrFail($id);
        $talukName->update([
            'zone_name' => $request->zone_name,
            'district_name' => $request->district_name,
            'taluk_name' => $request->taluk_name,
            'remark' => $request->remark,
        ]);

        return response()->json(['success' => 'Taluk updated successfully.']);
    }

    public function get_districts(Request $request)
    {

        $zone_id = $request->query('zone_name');

        $district = DistrictCreation::where('zone', $zone_id)->where('is_delete', 0)->get();

        return response()->json(['districts' => $district]);
    }

    // public function fetchDistricts(Request $request){
        
    //     $zone_id = $request->query('zone_id');
        
    //     $district = DistrictCreation::where('zone',$zone_id)->where('is_delete',0)->get();
        
    //     return response()->json(['districts' => $district]);
        
    // }

    public function fetchDistricts(Request $request)
{
    $zoneId = $request->query('zone_id');

    if (!$zoneId) {
        return response()->json(['districts' =>[]]);
    }

    $districts = DistrictCreation::where('zone', $zoneId)->where('is_delete',0)->get();

    return response()->json(['districts' => $districts]);
}

//     public function destroy($id)
//     {
//         $taluk = Taluk::find($id);
// // dd($taluk);
//         if ($taluk) {
//             $taluk->is_delete = 1;
//             $taluk->save();

//             return response()->json(['success' => 'Taluk deleted successfully.']);
//         } else {
//             return response()->json(['error' => 'Taluk not found.'], 404);
//         }
//     }
public function destroy($id)
{
    // Find the Taluk by ID
    $taluk = Taluk::find($id);

    // Check if Taluk exists
    if (!$taluk) {
        // Redirect back with an error message if not found
        return redirect()->route('taluk_creation.index')->with('error', 'Taluk not found.');
    }

    // Set the is_delete property and save the Taluk
    $taluk->is_delete = 1;
    $taluk->save();

    // Redirect back with a success message
    return redirect()->route('taluk_creation.index')->with('Success', 'Deleted Successfully');
}

}