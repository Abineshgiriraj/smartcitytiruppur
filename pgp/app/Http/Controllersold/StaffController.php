<?php

namespace App\Http\Controllers;

use App\Models\DistrictCreation;
use App\Models\Taluk;
use App\Models\School;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class StaffController extends Controller
{
    public function generateUniqueId()
    {
        // Generate a random string
        $randomString = strtolower(bin2hex(random_bytes(6))); // 12 characters long

        // Append current timestamp
        $timestamp = date('YmdHis'); // 14 characters long

        // Combine both parts to form a unique ID
        return $randomString . $timestamp;
    }

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('staff')
                ->Join('zones', 'staff.zone', '=', 'zones.unique_id')
                ->Join('district', 'staff.district', '=', 'district.unique_id')
                ->Join('taluk', 'staff.taluk_name', '=', 'taluk.unique_id')
                ->join('schools', 'staff.school', '=', 'schools.unique_id')
                ->join('designations', 'staff.designation', '=', 'designations.unique_id')
                ->select('staff.id', 'staff.emp_name', 'staff.emp_id', 'designations.designation as designation', 'district.district as district', 'zones.zone as zone', 'taluk.taluk_name as taluk_name', 'schools.school_name as school', 'staff.doj', 'staff.emp_contact')
                ->where('staff.is_delete', 0);


            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('staff.edit', $row->id);
                    $deleteUrl = route('staff.destroy', $row->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>
                    <button class="btn btn-danger btn-sm delete_btn" data-url="' . $deleteUrl . '">Delete</button>';
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.staff.index'); // Replace with your actual Blade view name
    }

    public function create()
    {
        return view('content.staff.create');
    }

    public function getData(Request $request)
    {
        $staff = Staff::select(['id', 'acc_year', 'emp_name', 'emp_id', 'designation', 'district', 'zone', 'school', 'doj', 'emp_contact'])
            ->where('is_delete', 0); // Filter out deleted records
        // if($request->ajax()){
        //     $staff = DB::table('staff')
        //                 ->Join('staff',)
        // }c

        return DataTables::of($staff)
            ->addColumn('action', function ($data) {
                return '<button class="btn btn-warning btn-sm edit" data-url="' . route('staff.edit', $data->id) . '">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('staff.destroy', $data->id) . '">Delete</button>';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validate and process the request

        $request->validate([
            'acc_year' => 'nullable|string',
            'emp_name' => 'required|string',
            // 'emp_id' => 'required|string',
            'emp_id' => [
                'required',
                'string',
                Rule::unique('staff')->where(function ($query) {
                    return $query->where('is_delete', 0);
                }),
            ],
            'designation' => 'required|string',
            'taluk_name' => 'required|string',
            'district' => 'required',
            'zone' => 'required',
            'school' => 'required',
            'doj' => 'required|date',
            'emp_contact' => 'required|string',
        ]);

        // Save the staff record
        $staff = new Staff();
        $staff->zone = $request->zone;
        $staff->acc_year = $request->acc_year;
        $staff->emp_name = $request->emp_name;
        $staff->emp_id = $request->emp_id;
        $staff->designation = $request->designation;
        $staff->district = $request->district;
        $staff->taluk_name = $request->taluk_name;
        $staff->school = $request->school;
        $staff->doj = $request->doj;
        $staff->emp_contact = $request->emp_contact;
        $staff->unique_id =   unique_id();
        $staff->save();

        // try {
        //     $staff = new Staff();
        //     $staff->fill($validatedData);
        //     $staff->save();

        //     return response()->json(['success' => true]);
        // } catch (\Exception $e) {
        //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
        // }
        return response()->json(['message' => 'staff created successfully.']);
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);

        return view('content.staff.edit', compact('staff'));
    }

    public function update(Request $request, staff $staff, $id)
    {

        // dd($request);


        // Validate the input data
        $request->validate([
            'acc_year' => 'required',
            'emp_name' => 'required|string',
            'emp_id' => 'required|string',
            'designation' => 'required|string',
            'district' => 'required|string',
            'taluk_name' => 'required|string',
            'zone' => 'required|string',
            'school' => 'required|string',
            'doj' => 'required|date',
            'emp_contact' => 'required|string',

        ]);



        $staff = Staff::findOrFail($id);

        $staff->update([
            'acc_year' => $request->acc_year,
            'emp_name' => $request->emp_name,
            'emp_id' => $request->emp_id,
            'designation' => $request->designation,
            'district' => $request->district,
            'taluk_name' => $request->taluk_name,
            'zone' => $request->zone,
            'school' => $request->school,
            'doj' => $request->doj,
            'emp_contact' => $request->emp_contact
        ]);
        // Update the staff record

        // $staff->update($request->all());

        return response()->json(['success' => true, 'message' => 'Staff updated successfully.', 'staff' => $staff]);
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->is_delete = 1;
        $staff->save();

        return response()->json(['success' => true]);
    }

    // public function getdetail()
    // {
    //     try {
    //         $staffs = Staff::where('is_delete', 0)
    //             ->select('emp_id', 'emp_name', 'designation', 'school')
    //             ->get();

    //         \Log::info('Staff data fetched successfully:', $staffs->toArray());

    //         return response()->json($staffs);
    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching staff details:', ['exception' => $e->getMessage()]);

    //         return response()->json(['error' => 'An error occurred while fetching staff details.'], 500);
    //     }

    public function getdetail()
    {
        $staff = Staff::where('is_delete', 0)
            ->select('emp_id', 'emp_name', 'designation', 'school')
            ->get();

        return response()->json($staff);
    }

    //fields getting through onchange function
    public function get_districts(Request $request)
    {

        $zone_id = $request->query('zone_name');

        $district = DistrictCreation::where('zone', $zone_id)->where('is_delete', 0)->get();

        return response()->json(['districts' => $district]);
    }

    public function get_taluk(Request $request)
    {

        $district = $request->query('district');

        $taluk = Taluk::where('district_name', $district)->where('is_delete', 0)->get();

        return response()->json(['taluk' => $taluk]);
    }
    public function get_schools(Request $request)
    {

        $taluk = $request->query('taluk');

        $school = School::where('taluk_name', $taluk)->where('is_delete', 0)->get();

        return response()->json(['school' => $school]);
    }
}
