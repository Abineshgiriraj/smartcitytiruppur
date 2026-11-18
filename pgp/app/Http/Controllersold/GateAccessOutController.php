<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use DB;

class GateAccessOutController extends Controller
 {
    /**
    * Display a listing of the resource.
    */

    public function index(Request $request )
 {

        $rfid = $request->rfidData;
        // dd( $rfid );

        $pass_details = DB::table( 'pass_entry_main_list' )
        ->where( 'pass_entry_main_list.rfid', $request->rfidData )
        ->where( 'pass_entry_main_list.is_delete', 0 ) // Only active and non-deleted
        ->get();

        // $pass_details = DB::table( 'pass_entry_main_list' )
        // ->where( 'pass_entry_main_list.rfid', $request->rfidData )
        // ->where( 'pass_entry_main_list.is_delete', 0 ) // Only active and non-deleted
        // ->get();
        // dd( $pass_details );

        // }
        return view( 'content.gateAccess.accessOut', compact( 'pass_details' ) );

    }

    /**
    * Show the form for creating a new resource.
    */

    public function create()
 {
        //
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
