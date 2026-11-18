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
        // $person_details = person_registration::where( 'rf_id', $request->rfidData )->where( 'is_delete', 0 )->first();

        $person_details = DB::table('person_registration')
            ->join('acc_years', 'person_registration.academic_year', '=', 'acc_years.unique_id')
            ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
            ->join('firms_registration', 'person_registration.company_name', '=', 'firms_registration.unique_id')
            ->select('acc_years.acc_year', 'person_type.person_type', 'firms_registration.name', 'person_registration.*')
            ->where('person_registration.rf_id', $request->rfidData)
            ->where('person_registration.is_delete', 0) // Only active and non-deleted
            ->first();

        if ($person_details) {
            $personal_datas = $person_details;
        } else {
            // If no person details found, check vehicle_registration
            $personal_datas = DB::table('vehicle_registration')
                ->join('acc_years', 'vehicle_registration.acc_year', '=', 'acc_years.unique_id')
                ->join('cargo_creation', 'vehicle_registration.vehicle_type', '=', 'cargo_creation.unique_id')
                ->join('vehicle_creation', 'vehicle_registration.vehicle_category', '=', 'vehicle_creation.unique_id')
                ->select('acc_years.acc_year as acc_yr', 'cargo_creation.cargo as vech_type', 'vehicle_creation.vehicle_type as vehicle_categ', 'vehicle_registration.owner_name', 'vehicle_registration.contact_number', 'vehicle_registration.*')
                ->where('rf_id', $request->rfidData)->where('vehicle_registration.is_delete', 0)
                ->first();
        }

        // If you want to ensure a JSON response in both cases, you can check here
        if (!$personal_datas) {
            return response()->json(['message' => 'No records found'], 404);
        }
        // dd($personal_datas);
        // $pass_details = DB::table('pass_entry_main_list')
        //     ->join('pass_entry_sublist', 'pass_entry_main_list.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
        //     // ->join('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
        //     ->select(
        //         'pass_entry_main_list.*',
        //         'pass_entry_sublist.*',
        //         // 'port_area_access.area_name as access_area',
        //         'pass_entry_main_list.pass_type'

        //     )
        //     ->where('pass_entry_main_list.is_delete', 0)
        //     ->where('pass_entry_main_list.rfid', $request->rfidData)
        //     ->get();

        // return response()->json([

        //     'person_details' => $personal_datas,
        //     'pass_details' => $pass_details,
        //     // 'pass_details_sub' => $pass_details_sub,

        // ]);
        $pass_details = DB::table('pass_entry_main_list')
            ->join('pass_entry_sublist', 'pass_entry_main_list.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
            // ->join('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->select(
                'pass_entry_main_list.*',
                'pass_entry_sublist.*',
                // 'port_area_access.area_name as access_area',
                'pass_entry_main_list.pass_type'

            )
            ->where('pass_entry_main_list.is_delete', 0)
            ->where('pass_entry_main_list.rfid', $request->rfidData)
            ->get();

        // dd($pass_main);

        return response()->json(['person_details' => $personal_datas, 'pass_details' => $pass_details]);
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
