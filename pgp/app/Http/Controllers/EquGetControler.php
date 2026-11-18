<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\person_registration;
use App\Models\Person_reg_sublist;
use App\Models\passentrysub;

use DB;
use Carbon\Carbon;
class EquGetControler extends Controller
 {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index(Request $request)
    {
        // Validate the request input
        $request->validate([
            'chase_no' => 'required|string'
        ]);


        $curDate = Carbon::now()->format('Y-m-d h:i A');

	$validpass = PassEntrySub::where('pass_valid_status', 0)
            ->where('person_vehicle', $request->vec_reg_no)
	    ->where('to_date', '>=', $curDate)
            ->where('is_delete', 0)
            ->orderBy('id', 'desc')->first(); 


        if($validpass){
            $passAlready = 'Already Permit Valid. If You Need? Go To Permit Extension';
        }else{
$passAlready = "";
}

    
        // Perform the query to get vehicle data with related tables
        $vech_register_no = DB::table('vehicle_registration')
            ->join('vehicle_registration_sub', 'vehicle_registration.screenUniqueId', '=', 'vehicle_registration_sub.screenUniqueId')
            ->join('cargo_creation', 'vehicle_registration.vehicle_type', '=', 'cargo_creation.unique_id')
            ->join('vehicle_creation', 'vehicle_registration.vehicle_category', '=', 'vehicle_creation.unique_id')
            ->select(
                'vehicle_registration.*', // Main vehicle registration fields
                'vehicle_registration_sub.*', // Vehicle registration sub-table fields
                'cargo_creation.cargo as vehicle_type',
                'vehicle_registration.id as main_id',
                'vehicle_creation.vehicle_type as vehicle_category' // Vehicle type from cargo_creation
            )
            ->where('vehicle_registration.is_delete', 0)
            ->where('vehicle_registration.chase_no', $request->chase_no)
            ->where('vehicle_registration.vehicle_type', $request->vec_type)
            ->where('vehicle_registration.vehicle_category', $request->vec_category)

            ->get();

$vehicle_main_table = DB::table('vehicle_registration')->where('vehicle_registration.is_delete', 0)
        ->where('vehicle_registration.vech_reg_no', $request->vec_reg_no)
        ->where('vehicle_registration.vehicle_type', $request->vec_type)
        ->where('vehicle_registration.vehicle_category', $request->vec_category)
        ->first();

    // dd( $vech_register_no);
        // Check if any data was returned
        if ($vech_register_no->isEmpty()) {
            return response()->json(['message' => 'No data found for the given chase number'], 404);
        }
    
        // Return the data as an array including the sub-table records
        return response()->json([
            'vech_register_no' => $vech_register_no,
'vehicle_main' => $vehicle_main_table,
"validpass" => $passAlready,

            'vech_register_no_sub' => $vech_register_no->map(function ($item) {
                return [
                    'certificate_type' => $item->certificate_type,
                    'valid_up_to' => $item->valid_up_to,
                    'attached_proof' => $item->attached_proof,
                ];
            })
        ]);
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create( Request $request )
 {

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