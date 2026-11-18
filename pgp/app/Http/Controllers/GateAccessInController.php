<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\gateaccess;
use App\Models\passentrysub;

use App\Models\cargoEiRegistrationiaeasub;
use App\Models\cargoEiRegistrationaadharsub;
use App\Models\cargoEiRegistrationmain;
use DB;
use Carbon\Carbon;



class GateAccessInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('content.gateAccess.penaltyList');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.gateAccess.accessIn');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rfidData' => 'required',
            'pass_id' => 'required',
            'indatetime' => 'required',
            'gate_type' => 'required',
            'user_id' => 'required',
            'gate_no'  => 'required',
            'access_gate' => 'required',
            'access_status' => 'required',
            'access_area' => 'required'
        ]);

        // if ($request->user_id == '66eaecfaaa3f3455c') {
        // } else {

        $curDate = Carbon::now()->format('d-m-Y');

        
        $datass = DB::table('gate_access_details')
        ->orderBy('gate_access_details.id', 'desc')
            ->where('gate_access_details.access_gate',$request->access_gate)
            ->where('gate_access_details.access_area',$request->access_area)
            ->where('gate_access_details.gate_no',$request->gate_no)
            ->where('gate_access_details.rfid',$request->rfidData)
            ->whereRaw("DATE_FORMAT(gate_access_details.in_out_datetime, '%d-%m-%Y') = ?", $curDate)
            ->first();

            if($datass == null){
                $get_access = gateaccess::create([
                    'unique_id' => unique_id(),
                    'rfid' => $request->rfidData,
                    'pass_sub_id' => $request->pass_id,
                    'in_out_datetime' => $request->indatetime,
                    'gate_type' => $request->gate_type,
                    'user_id'    => $request->user_id,
                    'gate_no'   => $request->gate_no,
                    'access_gate' => $request->access_gate,
                    'access_area' => $request->access_area,
                    'access_status' => $request->access_status
                ]);
            }else{
                if($datass->gate_type == 'OUT'){
                    if($request->gate_type == 'OUT'){
                        $get_access = '';
                    }else{
                        $get_access = gateaccess::create([
                            'unique_id' => unique_id(),
                            'rfid' => $request->rfidData,
                            'pass_sub_id' => $request->pass_id,
                            'in_out_datetime' => $request->indatetime,
                            'gate_type' => $request->gate_type,
                            'user_id'    => $request->user_id,
                            'gate_no'   => $request->gate_no,
                            'access_gate' => $request->access_gate,
                            'access_area' => $request->access_area,
                            'access_status' => $request->access_status
                        ]);
                    }
                }else{
                    if($request->gate_type == 'IN'){
                        $get_access = '';

                    }else{
                        $get_access = gateaccess::create([
                            'unique_id' => unique_id(),
                            'rfid' => $request->rfidData,
                            'pass_sub_id' => $request->pass_id,
                            'in_out_datetime' => $request->indatetime,
                            'gate_type' => $request->gate_type,
                            'user_id'    => $request->user_id,
                            'gate_no'   => $request->gate_no,
                            'access_gate' => $request->access_gate,
                            'access_area' => $request->access_area,
                            'access_status' => $request->access_status
                        ]);
                    }
                    // $get_access = gateaccess::create([
                    //     'unique_id' => unique_id(),
                    //     'rfid' => $request->rfidData,
                    //     'pass_sub_id' => $request->pass_id,
                    //     'in_out_datetime' => $request->indatetime,
                    //     'gate_type' => $request->gate_type,
                    //     'user_id'    => $request->user_id,
                    //     'gate_no'   => $request->gate_no,
                    //     'access_gate' => $request->access_gate,
                    //     'access_area' => $request->access_area,
                    //     'access_status' => $request->access_status
                    // ]);
                }
            }

    // $request->gate_type

            

            // dd($get_access);
        // }

        if ($get_access != '' && $get_access->access_status == '1') {
            // Update passentrysub record
            $aadharsub = cargoEiRegistrationmain::where('card_no', $request->rfidData)
                ->where('is_delete', 0) // Ensure is_delete is 0
                ->update([
                    'status' => 1,
                    // Add other fields to update as needed
                ]);
        }

        return response()->json(['get_access' => $get_access]);
    }



    public function indetails(Request $request)
    {
        // $access_in  = DB::table('gate_access_details')
        //     ->where('gate_access_details.access_gate','=', $request->gate_name)
        //     ->where('gate_access_details.access_area','=',$request->accessArea)
        //     ->where('gate_access_details.gate_no','=', $request->gateNo)
        //     ->join('view_permit_gate_access_details', 'gate_access_details.rfid', 'view_permit_gate_access_details.rfid_hep_no')
        //     ->select('view_permit_gate_access_details.*', 'gate_access_details.*')
        //     ->orderBy('gate_access_details.id', 'desc')
        //     ->take(10)
        //     ->get();

        $access_in  = DB::table('permit_access_gate_detail')
        // ->where('permit_access_gate_detail.access_gate','=', $request->gate_name)
        // ->where('permit_access_gate_detail.access_area','=',$request->accessArea)
        ->where('permit_access_gate_detail.gate_id','=', $request->gateNo)
        ->join('view_permit_gate_access_details', 'permit_access_gate_detail.rfid_card_no', 'view_permit_gate_access_details.rfid_hep_no')
        ->select('view_permit_gate_access_details.*', 'permit_access_gate_detail.*')
        ->orderBy('permit_access_gate_detail.id', 'desc')
        ->take(10)
        ->get();

        // dd($access_in);


        $personDatas = [];
        $vehicleDatas = [];


        foreach($access_in as $access){
            if($access->passTypeSts == '4' || $access->passTypeSts == '5' ||$access->passTypeSts == '6' || $access->passTypeSts == '7' ||$access->passTypeSts == '8'){
                $vehicle_data = DB::table('view_vehicle_registration_main')
                ->join('view_permit_gate_access_details', 'view_vehicle_registration_main.rf_id', 'view_permit_gate_access_details.rfid_hep_no')
                ->where('view_vehicle_registration_main.rf_id', $access->rfid_hep_no)->first();
                $vehicleDatas[] = $vehicle_data;
            }else{
                $person_data = DB::table('view_person_registration')
                ->join('view_permit_gate_access_details', 'view_person_registration.rf_id', 'view_permit_gate_access_details.rfid_hep_no')
                ->where('view_person_registration.rf_id', $access->rfid_hep_no)->first();
                $personDatas[] = $person_data;

            }
        }
        

        return response()->json(['datas' => $access_in, 'personData' => $personDatas,'vehicleData' => $vehicleDatas]);

        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $gateNo = DB::table('port_area_master_sub')
        ->where('unique_id', $request->gateNo)
        ->first();

        $gateName = DB::table('area_gate_master')
        ->where('unique_id', $request->gate_name)
        ->first('gate_name');

        return response()->json(['gateNo' => $gateNo, 'gateName' => $gateName]);

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
