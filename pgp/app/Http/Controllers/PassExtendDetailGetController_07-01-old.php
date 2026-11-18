<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\access_zone;
use App\Models\viewpassissue;
use App\Models\viewpasssubextend;
use DB;

class PassExtendDetailGetController extends Controller
 {
    /**
    * Display a listing of the resource.
    */
    // public function index(Request $request)
    // {
    //     // Ensure permitidInput exists in the request
    //     $permitId = $request->permitidInput;
    //     if (!$permitId) {
    //         return response()->json(['error' => 'Permit ID is required.'], 400);
    //     }
    
    //     // Fetch person details
    //     $person_details = DB::table('view_passsub_list')
    //         ->where('pass_id', $permitId)
    //         ->first();
    
    //     // If person details are found, initialize $personal_datas with these details
    //     if ($person_details) {
    //         $personal_datas = $person_details;
    //     } else {
    //         return response()->json(['error' => 'No person data found for the given Permit ID.'], 404);
    //     }
    
    //     // Fetch vehicle registration data using the person details
    //     $personal_datas = DB::table('vehicle_registration')
    //         ->leftJoin('vehicle_registration_sub', 'vehicle_registration.vech_reg_no', '=', 'vehicle_registration_sub.vehicle_id')
    //         ->where('vehicle_registration.vech_reg_no', $person_details->person_vehicle) // Match vehicle number
    //         ->where('vehicle_registration.is_delete', 0) // Exclude deleted records
    //         ->select([
    //             'vehicle_registration.vehicle_type',
    //             'vehicle_registration.owner_name',
    //             'vehicle_registration_sub.certificate_type',
    //             'vehicle_registration_sub.valid_up_to',
    //         ])
    //         ->get();
    
    //     // Check if vehicle registration data exists
    //     if ($personal_datas->isEmpty()) {
    //         return response()->json(['error' => 'No vehicle registration data found for the given vehicle.'], 404);
    //     }
    
    //     // Return the response with vehicle registration data
    //     return response()->json(['data' => $personal_datas]);
    // }
public function index(Request $request)
{
    // Ensure permitIdInput exists in the request
    $permitId = $request->permitidInput;
    if (!$permitId) {
        return response()->json(['error' => 'Permit ID is required.'], 400);
    }

    // Fetch person details
    $person_details = DB::table('view_passsub_extend')
        ->where('pass_id', $permitId)
        ->orWhere('adhaar_license', $permitId)
        ->orWhere('person_vehicle', $permitId)
        ->first();

    if (!$person_details) {
        return response()->json(['error' => 'No person data found for the given Permit ID.'], 404);
    }

    // Fetch personal data
    $personal_datas = DB::table('view_passsub_extend')
        ->leftJoin('vehicle_registration', function ($join) {
            $join->on('view_passsub_extend.person_vehicle', '=', 'vehicle_registration.vech_reg_no')
                 ->orOn('view_passsub_extend.person_vehicle', '=', 'vehicle_registration.chase_no');
        })
        ->leftJoin('vehicle_registration_sub', 'vehicle_registration.screenUniqueId', '=', 'vehicle_registration_sub.screenUniqueId')
        ->where(function ($query) use ($permitId) {
            $query->where('view_passsub_extend.pass_id', $permitId)
                  ->orWhere('view_passsub_extend.person_vehicle', $permitId)
                  ->orWhere('view_passsub_extend.adhaar_license', $permitId);
        })
        ->where(function ($query) {
            $query->where('vehicle_registration.is_delete', 0)
                  ->orWhereNull('vehicle_registration.is_delete');
        })
        ->select([
            'view_passsub_extend.*',
            'vehicle_registration.vehicle_type',
            'vehicle_registration.owner_name',
            'vehicle_registration_sub.certificate_type',
            'vehicle_registration_sub.valid_up_to',
        ])
        ->get();

    // Check if data exists
    if ($personal_datas->isEmpty()) {
        return response()->json(['error' => 'No data found for the given Permit ID.'], 404);
    }

    // Return the combined response
    return response()->json(['data' => $personal_datas]);
}


    // end



    // public function index(Request $request)
    // {
    //     $permitId = $request->permitidInput;
    //     if (!$permitId) {
    //         return response()->json(['error' => 'Permit ID is required.'], 400);
    //     }
    
    //     $person_details = DB::table('view_passsub_list')
    //         ->where('pass_id', $permitId)
    //         ->first();
    
    //     if (!$person_details) {
    //         return response()->json(['error' => 'No person data found for the given Permit ID.'], 404);
    //     }
    
    //     $rfid = $person_details->rfid ?? null;
    //     // dd($rfid);
    //     if (!$rfid) {
    //         return response()->json(['error' => 'RFID not found in person details.'], 404);
    //     }
    
    //     $personal_datas = DB::table('vehicle_registration')
    //         ->leftJoin('vehicle_registration_sub', 'vehicle_registration.vech_reg_no', '=', 'vehicle_registration_sub.vehicle_id')
    //         ->where('vehicle_registration.rf_id', $rfid)
    //         ->where('vehicle_registration.is_delete', 0)
    //         ->select([
    //             'vehicle_registration.vehicle_type',
    //             'vehicle_registration.owner_name',
    //             'vehicle_registration_sub.certificate_type',
    //             'vehicle_registration_sub.valid_up_to'
    //         ])
    //         ->get();
    // // dd($personal_datas);
    //     if ($personal_datas->isEmpty()) {
    //         return response()->json(['error' => 'No vehicle registration data found for the given RFID.'], 404);
    //     }
    
    //     return response()->json(['data' => $personal_datas]);
    // }
    

    // use Illuminate\Support\Facades\DB;

// public function index(Request $request)
// {
//     // Fetch data from the database
//     $personal_datas = DB::table('view_passsub_list')
//         ->where('pass_id', $request->permitidInput)
//         ->get();

//     // Check if data exists
//     if ($personal_datas->isEmpty()) {
//         return response()->json(['message' => 'No data found'], 404);
//     }

//     // Return the data in the response
//     return response()->json(['data' => $personal_datas]);
// }

// public function index(Request $request)
// {
//     // Fetch data for person
//     $personal_datas = DB::table('view_passsub_list')
//         ->where('pass_id', $request->permitidInput)
//         ->where('pass_type', 'person')
//         ->get();

//     // Fetch data for vehicle
//     $vehicle_datas = DB::table('vehicle_registration')
//         ->where('pass_id', $request->permitidInput)
//         ->where('pass_type', 'vehicle')
//         ->get();

//     // Check if both are empty
//     if ($personal_datas->isEmpty() && $vehicle_datas->isEmpty()) {
//         return response()->json(['message' => 'No data found'], 404);
//     }

//     // Combine the data
//     $response_data = [
//         'person' => $personal_datas,
//         'vehicle' => $vehicle_datas,
//     ];

//     // Return the combined response
//     return response()->json(['data' => $response_data]);
// }


    /**
    * Show the form for creating a new resource.
    */

    public function get_pass_data( Request $request )
 {

        $rf_id = $request->rf_id;
        $pass_main = passentrymainlist::where( 'rfid', $rf_id )
        ->select( 'screen_unique_id' )
        ->first();
        // dd( $pass_main );

        $pass_entry_sub = passentrysub::where( 'screen_unique_id', '=', $pass_main->screen_unique_id )

        // dd( $pass_entry_sub )

        ->join( 'port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id' )
        ->join( 'pass_entry_main_list', 'pass_entry_sublist.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id' )
        ->select( 'pass_entry_sublist.pass_id', 'port_area_access.area_name as access_area', 'pass_entry_sublist.from_date', 'pass_entry_sublist.to_date' )
        ->get();

        // dd( $pass_entry_sub );
        // 'pass_entry_main_list.pass_type'

        return response()->json( [ 'pass_entry_sub' => $pass_entry_sub ] );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( Request $request )
 {
        //
    }

    /**
    * Display the specified resource.
    */

    public function show( string $id )
 {
        //
    }

    /**
    * Show the form for editing the specified resource.
    */

    public function edit( string $id )
 {
        //
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( Request $request, string $id )
 {
        //
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( string $id )
 {
        //
    }
}