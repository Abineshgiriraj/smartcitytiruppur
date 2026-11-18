<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\DistrictCreation;
use App\Models\Taluk;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\DB;


class SchoolController extends Controller
{
    // public function generateUniqueId()
    // {
    //     // Generate a random string
    //     $randomString = strtolower(bin2hex(random_bytes(6))); // 12 characters long

    //     // Append current timestamp
    //     $timestamp = date('YmdHis'); // 14 characters long

    //     // Combine both parts to form a unique ID
    //     return $randomString . $timestamp;
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('schools') // Customize this query to fit your needs
                ->Join('zones', 'schools.zone_name', '=', 'zones.unique_id')
                ->Join('district', 'schools.district_name', '=', 'district.unique_id')
                ->Join('taluk', 'schools.taluk_name', '=', 'taluk.unique_id')
                ->select('schools.id', 'zones.zone as zone_name', 'district.district as district_name', 'taluk.taluk_name as taluk_name', 'schools.school_name', 'schools.remark')
                ->where('schools.is_delete', 0)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('school.edit', $row->id) . '" class="edit btn btn-warning btn-sm">Edit</a>';
                    $btn .= '<form action="' . route('school.destroy', $row->id) . '" method="POST" style="display:inline-block;">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class=" delete btn btn-danger btn-sm">Delete</button>';
                    $btn .= '</form>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
            // dd($data);
        }

        return view('content.school.index');
    }

    public function create()
    {
        return view('content.school.create');
    }



    public function edit($id)
    {
        $school = School::findOrFail($id);
        return view('content.school.edit', compact('school'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_name' => 'required',
            'district_name' => 'required',
            'taluk_name' => 'required',
            'school_name' => 'required',
            'remark' => 'nullable',
        ]);


        $school = School::create([
            // School::create($request->all());
            'unique_id' => unique_id(),
            'zone_name' => $request->zone_name,
            'district_name' => $request->district_name,
            'taluk_name' => $request->taluk_name,
            'school_name' => $request->school_name,
            'remark' => $request->remark,
        ]);

        return response()->json(['success' => true, 'message' => 'School updated successfully.', 'school' => $school]);
    }

    public function update(Request $request, School $school, $id)
    {
        $request->validate([
            'zone_name' => 'required',
            'district_name' => 'required',
            'taluk_name' => 'required',
            'school_name' => 'required',
            'remark' => 'nullable',
        ]);

        $school = School::findOrFail($id);

        $school->update($request->all());

        // return view('content.school.index');
        return response()->json(['success' => true, 'message' => 'School updated successfully.', 'school' => $school]);
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('school.index')->with('success', 'School deleted successfully.');
    }


    public function getdetails()
    {
        $schools = School::where('is_delete', 0)
            ->select('zone_name', 'district_name', 'taluk_name', 'school_name')
            ->get();

        return response()->json($schools);
    }


    // public function get_districts(Request $request)
    // {

    //     $zone_id = $request->query('zone_id');

    //     // if (!$zone_id) {
    //     //     return response()->json(['districts' => ['no value']]);
    //     // }

    //     $districts = DistrictCreation::where('zone', $zone_id)->where('is_delete', 0)->get();

    //     return response()->json(['districts' => $districts]);
    // }


    public function get_district(Request $request)
    {
        $zone = $request->query('zone_id');

        if (!$zone) {
            return response()->json(['error' => 'Zone ID is missing']);
        }

        // Enable query logging
        DB::enableQueryLog();

        $districts = DistrictCreation::where('zone', $zone)
            ->where('is_delete', 0)
            ->get();

        // Log the executed query
        $queries = DB::getQueryLog();
        // Log::info('Executed Queries', $queries);

        if ($districts->isEmpty()) {
            return response()->json(['districts' => [], 'message' => 'No districts found for the given zone']);
        }

        return response()->json(['districts' => $districts]);
    }


    public function get_taluk(Request $request)
    {

        $district = $request->query('district');
        if (!$district) {
            return response()->json(['taluk' => []]);
        }

        $taluk = Taluk::where('district_name', $district)->where('is_delete', 0)->get();

        return response()->json(['taluk' => $taluk]);
    }
}