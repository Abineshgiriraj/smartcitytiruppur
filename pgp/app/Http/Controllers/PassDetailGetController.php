<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use DB;


class PassDetailGetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $current_date = new \DateTime();
        $formatted_date = $current_date->format('Y-m-d H:i:s');

        $access_details = DB::table('permit_access_gate_detail')
        // ->where('rfid_card_no', $request->rfidData)
        ->orderBy('permit_access_gate_detail.id', 'desc')
        ->first();

        // dd($access_details);
        
        $permit_details = DB::table('view_permit_gate_access_details')
        ->where('rfid_hep_no', $access_details->rfid_card_no)
        ->first();

        // dd($permit_details);
        // $permit_details->passTypeSts == '2' || $permit_details->passTypeSts == '3' || 
        if($permit_details->passTypeSts == '4' || $permit_details->passTypeSts == '2' || $permit_details->passTypeSts == '3'){

                $trip_details = DB::table('view_main_cargo_con_trip_details')
            ->where('card_no', $request->rfidData)
            ->first();
            
            // if($trip_details == ''){
            //     return response()->json(['error' => true, 'message' => 'No trip add to this Vehicle']);
            // }else{
                if($permit_details->access_area == $request->accessArea){
                    $person_details = DB::table('view_person_registration')
                    ->where('rf_id', $request->rfidData)
                    ->first();
                    $vehicle_details = DB::table('view_vehicle_registration_main')
                    ->where('rf_id', $request->rfidData)
                    ->first();
                    return response()->json(['permit' => $permit_details,'tripData' => $trip_details,'personData' => $person_details,'vehicleData' => $vehicle_details, 'tripData' => $trip_details]);
                }else{
                    return response()->json(['error' => true, 'message' => 'This Area not access the Permit']);
                }
            // }
        }else{
            if($permit_details->access_area == $request->accessArea){

                $person_details = DB::table('view_person_registration')
                ->where('rf_id', $request->rfidData)
                ->first();
                $vehicle_details = DB::table('view_vehicle_registration_main')
                ->where('rf_id', $request->rfidData)
                ->first();

                return response()->json(['permit' => $permit_details,'personData' => $person_details,'vehicleData' => $vehicle_details]);
            }else{
                return response()->json(['error' => true, 'message' => 'This Area not access the Permit']);
            }
        }


        // $pass = DB::table('pass_entry_sublist')
        //     ->select('agent_type')
        //     ->where('pass_entry_sublist.rfid_hep_no', $request->rfidData)
        //     ->first();

        // if ($pass->agent_type == '677231ae47a0d26001') {

        //     $pass_main = DB::table('pass_entry_sublist')
        //         ->join('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
        //         ->leftjoin('company_creation', 'pass_entry_sublist.agent_type', '=', 'company_creation.unique_id')
        //         ->select(
        //             'pass_entry_sublist.*',
        //             'port_area_access.area_name as access_area',
        //             'company_creation.company_name as company_name',
        //             'pass_entry_sublist.pass_type',
        //             'pass_type',
        //             DB::RAW("DATE_FORMAT(pass_entry_sublist.to_date,'%d-%m-%Y %h:%i %p') as formatted_to_date")
        //         )
        //         ->where('pass_entry_sublist.is_delete', 0)
        //         ->where('pass_entry_sublist.rfid_hep_no', $request->rfidData)
        //         ->first();
        // } else {
        //     $pass_main = DB::table('pass_entry_sublist')
        //         ->join('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
        //         ->join('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
        //         ->select(
        //             'pass_entry_sublist.*',
        //             'port_area_access.area_name as access_area',
        //             'pass_entry_sublist.pass_type',
        //             'firms_registration.name as company_name',
        //             'pass_type',
        //             DB::RAW("DATE_FORMAT(pass_entry_sublist.to_date,'%d-%m-%Y %h:%i %p') as formatted_to_date")
        //         )

        //         ->where('pass_entry_sublist.is_delete', 0)
        //         ->where('pass_entry_sublist.rfid_hep_no', $request->rfidData)

        //         ->first();
        // }


        // if ($pass_main->pass_type == 'person') {

        //     $driver = DB::table('person_registration')
        //         ->where('rf_id', '=', $pass_main->rfid_hep_no)
        //         ->select('designation')
        //         ->first();

        //     if ($driver->designation == '66fe76256d3a792499') {
        //         $person_data = DB::table('view_person_registration')
        //             ->select('view_person_registration.*')
        //             ->where('proof_type', '=', '672f3df8d562432194')
        //             ->where('view_person_registration.rf_id', '=', $pass_main->rfid_hep_no)
        //             ->get();
        //     } else {
        //         $person_data = DB::table('view_person_registration')
        //             ->where('view_person_registration.rf_id', '=', $pass_main->rfid_hep_no)
        //             ->select('view_person_registration.*')
        //             ->get();
        //     }

        //     //dd($person_data);

        // } else if ($pass_main->pass_type == 'vehicle') {

        //     $person_data = DB::table('view_vehicle_registration_main')
        //         ->where('view_vehicle_registration_main.rf_id', '=', $pass_main->rfid_hep_no)
        //         ->select('view_vehicle_registration_main.*')
        //         ->get();
        //     // dd($person_data);
        // } else {
        //     ("No Data for this RFID");
        // }


        // return response()->json(['data' => $person_data, 'pass_main' => $pass_main]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function get_pass_data(Request $request)
    {

        $rf_id = $request->rf_id;
        $pass_main = passentrymainlist::where('rfid', $rf_id)
            ->select('screen_unique_id')
            ->first();
        // dd($pass_main);

        $pass_entry_sub = passentrysub::where('screen_unique_id', '=', $pass_main->screen_unique_id)

            // dd($pass_entry_sub)

            ->join('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->join('pass_entry_main_list', 'pass_entry_sublist.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id')
            ->select('pass_entry_sublist.pass_id', 'port_area_access.area_name as access_area', 'pass_entry_sublist.from_date', 'pass_entry_sublist.to_date')
            ->get();

        // dd($pass_entry_sub);
        // 'pass_entry_main_list.pass_type'



        return response()->json(['pass_entry_sub' => $pass_entry_sub]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
