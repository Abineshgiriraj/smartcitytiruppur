<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;

use App\Models\cargoEiRegistrationiaeasub;
use App\Models\cargoEiRegistrationaadharsub;
use App\Models\cargoEiRegistrationmain;


use App\Models\gateaccess;
use DB;

class GateAccessOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $rfid = $request->rfidData;
        // dd( $rfid );

        $pass_details = DB::table('pass_entry_main_list')
            ->where('pass_entry_main_list.rfid', $request->rfidData)
            ->where('pass_entry_main_list.is_delete', 0) // Only active and non-deleted
            ->get();

        // $pass_details = DB::table( 'pass_entry_main_list' )
        // ->where( 'pass_entry_main_list.rfid', $request->rfidData )
        // ->where( 'pass_entry_main_list.is_delete', 0 ) // Only active and non-deleted
        // ->get();
        // dd( $pass_details );

        // }
        return view('content.gateAccess.accessOut', compact('pass_details'));
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

    public function store(Request $request)
    {


        $request->validate([
            'pass_id'  => 'required',
            'indatetime' => 'required',
            'gate_type'   => 'required',
            'user_id'      => 'required',
            'gate_no'       => 'required',
            'access_gate'  => 'required',
            'access_status' => 'required'
        ]);

        if ($request->user_id == '66eaecfaaa3f3455c') {
        } else {

            $get_access = gateaccess::create([
                'unique_id' => unique_id(),
                'rfid' => $request->rfidData,
                'pass_sub_id' => $request->pass_id,
                'in_out_datetime' => $request->indatetime,
                'gate_type' => $request->gate_type,
                'user_id'    => $request->user_id,
                'gate_no'   => $request->gate_no,
                'access_gate' => $request->access_gate,
                'access_status' => $request->access_status,

            ]);
        }

        if ($get_access->access_status == '1') {
            // $cargosubisub= passentrysub::where('rfid_hep_no',  $request->rfidData)->update([
            //     // 'trip_no' => $tripNo,
            //     // 'penality_status' =>$request->penaltyStatus ?? '0',
            //     'cargo_status' => 2,
            //     // 'port_status' => 1,

            // ]);
            // Update passentrysub record
            // $aadharsub = cargoEiRegistrationaadharsub::where('rf_id', $request->rfidData)
            // ->where('is_delete', 0) // Ensure is_delete is 0
            // ->update([
            //     'status' => 2,
            //     // Add other fields to update as needed
            // ]);

            // Update passentrysub record
            $aadharsub = cargoEiRegistrationmain::where('card_no', $request->rfidData)
                ->where('is_delete', 0) // Ensure is_delete is 0
                ->update([
                    'status' => 2,
                    // Add other fields to update as needed
                ]);
        }



        return response()->json(['get_access' => $get_access]);
    }


    public function outdetails()
    {



        $access_in  = DB::table('gate_access_details')
            ->where('gate_access_details.gate_type', 'OUT')
            ->join('pass_entry_sublist', 'gate_access_details.rfid', 'pass_entry_sublist.rfid_hep_no')
            ->leftjoin('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
            ->select('firms_registration.name as company_name', 'pass_entry_sublist.*', 'gate_access_details.*')
            ->orderBy('gate_access_details.id', 'desc')
            ->take(10)
            ->get();

        $access_details = [];

        foreach ($access_in as $access) {
            if ($access->pass_type == 'person') {

                $person_data = DB::table('view_person_registration')
                    ->where('view_person_registration.rf_id', $access->rfid)
                    ->join('view_permitissue', 'view_person_registration.rf_id', '=', 'view_permitissue.rfid')
                    // ->leftjoin('firms_registration', 'view_person_registration.firm_name_id', '=', 'firms_registration.unique_id')
                    // ->select('firms_registration.name as company_name', 'view_person_registration.*', 'pass_entry_sublist.*')
                    ->select('view_person_registration.*', 'view_permitissue.*')
                    ->first();

                $access_details[] = $person_data;
            } elseif ($access->pass_type == 'vehicle') {

                $vehicle_data = DB::table('view_vehicle_registration_main')
                    ->where('view_vehicle_registration_main.rf_id', $access->rfid)
                    ->join('view_permitissue', 'view_vehicle_registration_main.rf_id', '=', 'view_permitissue.rfid')
                    ->select('view_vehicle_registration_main.*', 'view_permitissue.*')
                    ->first();

                $access_details[] = $vehicle_data;

                // dd($access_details);
            }
        }





        return response()->json(['datas' => $access_in, 'access_details' => $access_details]);
    }




    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        //
    }
}
