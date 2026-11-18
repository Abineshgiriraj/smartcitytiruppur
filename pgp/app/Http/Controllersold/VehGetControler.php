<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\person_registration;
use App\Models\Person_reg_sublist;

use DB;

class VehGetControler extends Controller
 {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index( Request $request )
 {
        // Validate the request input
        $request->validate( [
            // 'vec_reg_no' => 'required|string'
        ] );

        // Fetch the vehicle registration details
        $vech_register_no = Vehicle::where( 'vech_reg_no', $request->vec_reg_no )->first();
        $vech_get_data = VehicleDocument::where( 'vehicle_id', $request->vec_reg_no )->get();

        // Check if vehicle was found
        if ( !$vech_register_no ) {
            return back()->withErrors( [ 'message' => 'No vehicle found with that registration number.' ] );
        }

       
        return response()->json( [
            'vech_register_no' => $vech_register_no,
            'vech_get_data' => $vech_get_data,
           
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