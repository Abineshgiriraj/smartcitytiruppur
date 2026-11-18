<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\person_registration;
use App\Models\Person_reg_sublist;

use Illuminate\Support\Facades\DB;

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

    public function index(Request $request)
    {
        $license_get_data = Person_reg_sublist::where('aadhar_licence', $request->driving_license)
            ->first();

        // Return the response in JSON format
        if (!$license_get_data) {
            return response()->json(['error' => 'No record found with that driving license number.'], 404);
        } else {
            // $licence_main = person_registration::where( 'unique_id', $license_get_data->form_unique_id )->first();
            $licence_main = DB::table('person_registration')
                ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
                ->select('person_registration.*', 'person_type.person_type')
                ->where('person_registration.unique_id', '=', $license_get_data->form_unique_id)
                ->first();

            if (!$licence_main) {
                return response()->json(['error' => 'No person registration found.'], 404);
            }

            return response()->json([
                'person_license_no' => $licence_main,
                'license_get_data' => $license_get_data,
            ]);
        }
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