<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\access_zone;

use DB;

class PassExtendDetailGetController extends Controller
 {
    /**
    * Display a listing of the resource.
    */

    public function index( Request $request )
 {
        // $person_details = person_registration::where( 'rf_id', $request->rfidInput )->where( 'is_delete', 0 )->first();
        // dd( $request->rfidInput );
        $person_details = DB::table( 'person_registration' )
        ->join( 'person_type', 'person_registration.designation', '=', 'person_type.unique_id' )
        ->leftjoin('person_reg_sublist', 'person_registration.form_unique_id','=','person_reg_sublist.form_unique_id' )
        ->select('person_type.person_type', 'person_registration.','person_reg_sublist.', 'person_reg_sublist.expiry_date as expiry_date')
        ->where( 'person_registration.rf_id', $request->rfidInput )
        ->where( 'person_registration.is_delete', 0 ) // Only active and non-deleted
        ->first();

        // dd( $person_details );
        if ( $person_details != '' ) {
            $personal_datas = $person_details;
        } else {
            $personal_datas = DB::table( 'vehicle_registration' )
            // ->join( 'cargo_creation', 'vehicle_registration.vehicle_type', '=', 'cargo_creation.unique_id' )
            // ->join( 'vehicle_registration_sub', 'vehicle_registration.screenUniqueId','=','vehicle_registration_sub.screenUniqueId' )
            ->select( 'vehicle_registration.*')
            // , 'vehicle_registration_sub.*'
            ->where( 'rf_id', $request->rfidInput )->where( 'vehicle_registration.is_delete', 0 )
            ->first();
        }
        // dd( $personal_datas );

        // $current_date =   new date();
        $current_date = new \DateTime();
        $formatted_date = $current_date->format( 'Y-m-d H:i:s' );

        $pass_main = DB::table( 'pass_entry_main_list' )
        ->join( 'pass_entry_sublist', 'pass_entry_main_list.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id' )
        ->join( 'port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id' )
        ->join( 'access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id' )
        ->join( 'firms_registration', 'pass_entry_main_list.firms_agent', '=', 'firms_registration.unique_id' )
        ->leftjoin( 'vehicle_registration_sub', 'pass_entry_main_list.vec_reg_no','=','vehicle_registration_sub.vehicle_id' )

        ->select(
            'pass_entry_main_list.*',
            'pass_entry_sublist.*',
            'port_area_access.area_name as access_area_name',
            'access_zone.zone_type as zone_type',
            'pass_entry_sublist.access_zone as access_zone',
            'pass_entry_sublist.access_area as access_area',
            'pass_entry_main_list.pass_type',
            'firms_registration.name as company_name',
            'firms_registration.address as firm_address',
            'vehicle_registration_sub.certificate_type as certificate_type',
            'vehicle_registration_sub.valid_up_to as valid_up_to',

            
            
        )
        ->where( 'pass_entry_main_list.is_delete', 0 )
        ->where( 'pass_entry_main_list.rfid', $request->rfidInput )

        ->get();

        // dd( $pass_main );

        return response()->json( [ 'data' => $personal_datas, 'pass_main' => $pass_main ] );
    }

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

//     public function showPassEntries( $id )
//  {
//         // Fetch sublist entries from the database
//         $sublist_entries = PassEntrySub::where( 'pass_id', $id )->get();
//         // dd( $sublist_entries );
//         // Pass the data to the view
//         // return view( 'content.pass_extend.index', compact( 'sublist_entries' ) );
//         return view( 'content.pass_extend.index', [ 'sublist_entries' => $sublist_entries ] );

//     }

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

    public function update( Request $request, string $id ){
        //
    }

   

}