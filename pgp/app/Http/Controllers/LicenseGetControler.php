<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\personRegistration;
use App\Models\Person_reg_sublist;
use App\Models\passentrysub;
use DB;
use Carbon\Carbon;

class LicenseGetControler extends Controller
 {
    /**
    * Display a listing of the resource.
    *

    * @return \Illuminate\Http\Response
    */

    //     public function index( Request $request )
    // {

    //         $license_get_data = Person_reg_sublist::where( 'aadhar_licence', $request->driving_license )
    //         ->first();

    //         // $aadhar_get_data = Person_reg_sublist::where( 'aadhar_licence', $request->aadhaar_no )->get();
    //         // Return the response in JSON format
    //         if ( !$license_get_data ) {
    //             return back()->withErrors( [ 'message' => 'No vehicle found with that registration number.' ] );
    //         } else {
    //             $licence_main = person_registration::where( 'unique_id', $license_get_data->form_unique_id )->first();

    //             return response()->json( [
    //                 'person_license_no' => $licence_main,
    //                 'license_get_data' => $license_get_data,
    // ] );
    //         }

    //     }

    public function index( Request $request )
 {
        $license_get_data = DB::table('person_reg_sublist')
    ->where('aadhar_licence', $request->driving_license)
    ->leftjoin('document_master','person_reg_sublist.proof_type','=','document_master.unique_id') // Corrected column usage
    ->select('person_reg_sublist.*','document_master.type_of_proof as proof_name') // Ensure correct columns are included
    ->orderBy('id', 'desc')
    ->first();


        $curDate = Carbon::now()->format('Y-m-d h:i A');


$validpass = PassEntrySub::where('pass_valid_status', 0)
    ->where('adhaar_license', $request->driving_license)
    ->whereNotNull('adhaar_license') // Exclude NULL values
    ->where('to_date', '>=', $curDate)
    ->where('is_delete', 0)
    ->orderBy('id', 'desc')
    ->first();

    if($validpass != ''){
        $passAlready = 'Already Permit Valid. If You Need? Go To Permit Extension';
    }else{
$passAlready = "";
}

       
        // If no license record is found, return a JSON error response
        if ( !$license_get_data ) {
            return response()->json( [ 'error' => 'No record found with that driving license number.' ], 404 );
        }

        // Confirm the form_unique_id from the initial lookup
        $form_unique_id = $license_get_data->form_unique_id;

        $licence_main = DB::table( 'person_registration' )
        ->join( 'person_type', 'person_registration.designation', '=', 'person_type.unique_id' )
        ->select( 'person_registration.*', 'person_type.*', 'person_type.person_type', 'person_registration.id','person_registration.unique_id as main_unique_id' )
         ->where( 'person_registration.form_unique_id', '=', $license_get_data->form_unique_id )
        ->first();

       // dd( $licence_main );
        if ( !$licence_main ) {
            return response()->json( [ 'error' => 'No person registration found.' ], 404 );
        }

        // Return a successful JSON response with the fetched data
        return response()->json( [
            'person_license_no' => $licence_main,
            'license_get_data' => $license_get_data,
            "validpass" => $passAlready,

        ] );
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

    public function store( Request $request )
 {
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show( $id )
 {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit( $id )
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

    public function update( Request $request, $id )
 {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function destroy( $id )
 {
        //
    }
}