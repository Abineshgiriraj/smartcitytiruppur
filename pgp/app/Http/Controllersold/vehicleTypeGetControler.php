<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FirmsRegistration;
use App\Models\Vehiclecreation;

use DB;

class vehicleTypeGetControler extends Controller
 {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index( Request $request )
 {
   $vehicle_category = Vehiclecreation::where( 'cargo', $request->vectype )->where('is_delete',0)->get();

   $data = array( 'vehicle_category' => $vehicle_category );

   $jsonResponse = json_encode( $data );
  
   echo $jsonResponse;
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
    $vehicle_category = Vehiclecreation::where( 'cargo', $request->vectype )->where('is_delete',0)->get();

    $data = array( 'vehicle_category' => $vehicle_category );

    $jsonResponse = json_encode( $data );
   
    echo $jsonResponse;
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