<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use DB;

class PassDetailOutGetController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $gate = DB::table('gate_access_details')
            ->where('rfid', $request->rfidData)
            ->where('gate_type', 'IN')
            ->select('*', 'in_out_datetime as access_in')
            ->first();

        $pass_main = DB::table('pass_entry_sublist')
            ->where('pass_entry_sublist.is_delete', 0)
            ->where('pass_entry_sublist.rfid_hep_no', $gate->rfid)

            ->join('pass_entry_main_list', 'pass_entry_sublist.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id')
            ->leftJoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->leftJoin('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
            ->select(
                'pass_entry_main_list.*',
                'pass_entry_sublist.*',
                'port_area_access.area_name as access_area',
                'pass_entry_sublist.pass_type',
                'firms_registration.name as company_name',
            )
            ->where('pass_entry_sublist.is_delete', 0)
            ->where('pass_entry_sublist.rfid_hep_no', $request->rfidData)
            ->first();



        if ($pass_main->pass_type == 'person') {

            if ($pass_main->persontype == '66fe76256d3a792499') {
                // dd($pass_main->persontype);
                $personal_datas = DB::table('view_person_registration')
                    // ->join('pass_entry_main_list', 'view_person_registration.aadhaar_no', '=', 'pass_entry_main_list.adhaar_no')
                    ->where('view_person_registration.aadhar_licence', '=', $pass_main->driving_license)
                    ->select('view_person_registration.*')
                    ->get();
            } else {
                $personal_datas = DB::table('view_person_registration')
                    ->join('pass_entry_main_list', 'view_person_registration.aadhaar_no', '=', 'pass_entry_main_list.adhaar_no')
                    ->where('view_person_registration.aadhaar_no', '=', $pass_main->adhaar_no)
                    ->select('view_person_registration.*')
                    ->get();
            }
        } else if ($pass_main->pass_type = 'vehicle') {

            $personal_datas = DB::table('view_vehicle_registration_main')
                ->where('view_vehicle_registration_main.vech_reg_no', '=', $pass_main->person_vehicle)
                ->select('view_vehicle_registration_main.*')
                ->get();
        }

        // dd($pass_details);

        return response()->json(['data' => $personal_datas, 'pass_details' => $pass_main]);
    }

    // }

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
