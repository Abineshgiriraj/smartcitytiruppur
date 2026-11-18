<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\person_registration;
use App\Models\Person_reg_sublist;

use DB;

class AdharGetControler extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        // person
        // $person_aadhar_no = person_registration::where( 'aadhaar_no', $request->aadhaar_no )->first();
        $person_aadhar_no = DB::table('person_registration')
            ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
            ->select('person_registration.*', 'person_type.person_type')
            ->where('person_registration.aadhaar_no', '=', $request->aadhaar_no)
            ->first();
        $aadhar_get_data = Person_reg_sublist::where('aadhar_licence', $request->aadhaar_no)->get();

        // Check if vehicle was found
        if (!$person_aadhar_no) {
            return back()->withErrors(['message' => 'No vehicle found with that registration number.']);
        }
        // Pass data to the view
        // return view( 'content.pass_entry.list', compact( 'vech_register_no', 'vech_get_data' ) );
        return response()->json([

            'person_aadhar_no' => $person_aadhar_no,
            'aadhar_get_data' => $aadhar_get_data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //
    }
}