<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\students;
use Yajra\DataTables\Facades\DataTables;
// use App\Helpers\OrderHelper;
use Illuminate\Support\Str;


class StudentController extends Controller
{
    // public function index()
    // {
    //     return view('content.student.list');
    // }
    // public function add()
    // {
    //     return view('content.student.form');
    // }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $students = students::select(['id', 'unique_id', 'student_name', 'father_name', 'stu_dob', 'cur_std', 'schl_district', 'schl_block', 'schl_name']);
            return DataTables::of($students)
                ->addIndexColumn()
                // ->addColumn('action', function ($row) {
                //     $edit_route = 'zone.edit';
                //     $delete_route = 'zone.destroy';
                //     $unique_id = $row->unique_id;
                //     $edit_btn = OrderHelper::btn_update($edit_route, $unique_id);
                //     $delete_btn = OrderHelper::btn_delete($delete_route, $unique_id);
                //     return $edit_btn . '' . $delete_btn;
                // })
                ->make(true);
        }


        return view('content.student.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if(session('unique_id') ==''){
        //     \Auth::logout();
        //     session()->flush();
        //     return redirect('/')->with('error', 'Session Expired!. Please Login again.');

        // }
        return view('content.student.form');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($student)
    {
        $authKey = '4acdca2cc493c1ec28e1f68e0d37c49a';

        // Prepare the student data
        $studentData = [
            'EmisId' => $student
        ];

        // Make the API call with query parameters
        $response = Http::withHeaders([
            'Authorization' => $authKey,
            'Accept' => 'application/json',
        ])->post('https://tnega.tnschools.gov.in/tnega/api/GetSchlDetails', $studentData);

        if ($response->successful()) {
            $data = $response->json();

            // Return JSON response to the same page
            return response()->json($data);
        } else {
            // Handle the error
            return response()->json(['error' => 'Unable to fetch data'], $response->status());
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $prefix = "";
        $request->validate([
            'biometric_id' => 'required',
            'district_id' => 'required',
            'zone' => 'required',
            'emis_no' => 'required',
            'udise_code' => 'required',
            'student_name' => 'required',
            'stu_dob' => 'required',
            'current_std' => 'required',
            'schl_medium' => 'required',
            'school_name' => 'required',
            'school_block' => 'required',
            'school_district' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
        ]);
        // $zone_data = zone::where('zone_name', $request->zone)->first();

        $uniqueId = Str::uuid()->toString();

        // if ($zone_data == '') {
        $student = new students;
        $student->biometric_id = $request->biometric_id;
        $student->device_id = $request->district_id;
        $student->school_id = $request->school_id;
        $student->zone = $request->zone;
        $student->emis_no = $request->emis_no;
        $student->udise_code = $request->udise_code;
        $student->student_name = $request->student_name;
        $student->stu_dob = $request->stu_dob;
        $student->cur_std = $request->current_std;
        $student->group_name = $request->group_name;
        $student->class_sec = $request->class_sec;
        $student->medium = $request->schl_medium;
        $student->schl_name = $request->school_name;
        $student->schl_block = $request->school_block;
        $student->schl_district = $request->school_district;
        $student->father_name = $request->father_name;
        $student->mother_name = $request->mother_name;
        $student->is_delete = 0;
        $student->unique_id = $uniqueId;

        $student->save();
        return redirect()->route('student.index')->with('success', 'Student Created Successfully.');
        // } else {
        //     return redirect()->route('zone.create')
        //         ->with('error', 'Already exit Designation Name');
        // }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($unique_id)
    {
        $zone = Zone::findOrFail($unique_id);
        return view('content.zone.form', compact('zone'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::find($id)->delete();

        return response()->json(['success' => 'Product deleted successfully.']);
    }
}