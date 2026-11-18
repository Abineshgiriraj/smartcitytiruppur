<?php

namespace App\Http\Controllers;  

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\DistrictCreation;
use App\Models\ZoneCreation;
use Illuminate\Support\Facades\DB;

class district_creation extends Controller
{
    public function index(Request $request)

    {
        if ($request->ajax()) {

            $district = DB::table('district')
                ->Join('zones', 'district.zone', '=', 'zones.unique_id')
                ->select('district.*', 'zones.zone as zone_name')
                ->where('district.is_delete', 0)
                ->get();

            return DataTables::of($district)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {


                    $editUrl = route('district.edit', $row->id);
                    $deleteUrl = route('district.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm">Delete</button>';
                    $btn .= '</form>';
                    return $btn;
                })
                
                ->make(true);
        }
        return view('content.district.list');
    }
    public function add()
    {

        return view('content.district.form');
    }

    public function store(Request $request)
    {

        $request->validate([
            'zone' => 'required|string|max:255',
            'district' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $exists = DistrictCreation::where('district', $request->district)
            // ->where('zone',$request->zone)
            ->exists();
        if ($exists) {
            return response()->json(['error', 'Already exists'], 422);
        }


        $district = DistrictCreation::create([
            'unique_id' => unique_id(),
            'zone' => $request->zone,
            'district' => $request->district,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at
        ]);
        return response()->json(['success' => 'District added successfully!', 'district' => $district]);
    }
    public function edit($id)
    {
        $district = DistrictCreation::findOrFail($id);
        return view('content.district.edit', compact('district'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'zone' => 'required|string|max:255',
            'district' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $exist_update = DistrictCreation::where('district', $request->district)
            ->where('zone', $request->zone)
            ->where('id', '!=', $id)
            ->exists();


        if ($exist_update) {
            return response()->json(['error', 'Already exists'], 422);
        }

        $district = DistrictCreation::findOrFail($id);

        $district->update([

            'unique_id' => unique_id(),
            'zone' => $request->zone,
            'district' => $request->district,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at
        ]);
        return redirect()->route('district')->with('success', 'District updated successfully');
    }


    public function getDistrict(Request  $request)
    {

        if ($request->ajax()) {

            $district = DistrictCreation::all();
            return DataTables::of($district)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $editUrl = route('district.edit', $row->id);
                    $deleteUrl = route('district.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</button>';
                    $btn .= '</form>';
                    return $btn;

                    // $btn = '<a href="' . route('category_edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    // $btn .= '<button data-url="' . route('district.destroy', $row->id) . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    // return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function destroy($id)
    { {
            $district = DistrictCreation::find($id);
            $district->is_delete = 1;
            $district->save();

            return redirect()->route('district')->with('Success', 'Deleted Successfully');
        }
    }

    public function getzone()
    {
        $zone = ZoneCreation::where('is_delete', 0)->distinct()->get(['zone']);
        return response()->json($zone);
    }

}