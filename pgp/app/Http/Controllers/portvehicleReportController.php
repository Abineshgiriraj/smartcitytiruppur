<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;

use Carbon\Carbon;

class portvehicleReportController extends Controller
{
    public function index(Request $request)
    {
        // DD($request);
        if ($request->ajax()) {
            $query = DB::table('view_portvehicle_registration as pvr') // Removed trailing space here
                ->select(
                    'form_unique_id',
                    'vech_reg_no',
                    // 'vehicle_serial_no',
                    'owner_name',
                    'mobile_num',
                    'vech_type',
                    // 'vehicle_type',
                    'vehicle_type_id',
                    'vehicle_category',
                    'vehicle_type',
                    DB::raw('(SELECT DATE_FORMAT(valid_up_to,"%d-%m-%Y")
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.form_unique_id
                          AND certificate_type = "Fitness" AND is_delete = 0) as fitness'),
                    DB::raw('(SELECT DATE_FORMAT(valid_up_to,"%d-%m-%Y")
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.form_unique_id
                          AND certificate_type = "Insurance" AND is_delete = 0) as insurance'),
                    DB::raw('(SELECT DATE_FORMAT(valid_up_to,"%d-%m-%Y")
                        FROM port_vehicle_registration_sub
                        WHERE port_vehicle_registration_sub.screenUniqueId = pvr.form_unique_id
                          AND certificate_type = "PUC" AND is_delete = 0) as puc'),

                    'model_name',
                    'chassis_no',
                    'approved_by',
                    DB::raw("DATE_FORMAT(pvr.entry_date,'%d-%m-%Y')as date")
                );
            // Uncomment the below line if you need to check for `is_delete = 0` on the main table
            // ->where('is_delete', 0)
            // ->where('pvr.is_delete', 0);

            if ($request->has('from_date') && $request->from_date != '') {
                $query->where('pvr.entry_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $to_date = Carbon::parse($request->to_date)->endofDay();
                $query->where('pvr.entry_date', '<=', $request->to_date);
            }
            if ($request->has('vehicle_no') && $request->vehicle_no != '') {
                $query->where('pvr.vech_reg_no', 'like', '%' . $request->vehicle_no . '%');
            }
            if ($request->has('vehicle_type') && $request->vehicle_type != '') {
                $query->where('pvr.vehicle_type_id', 'like', '%' . $request->vehicle_type . '%');
            }
            if ($request->has('vehicle_category') && $request->vehicle_category != '') {
                $query->where('pvr.vehicle_category', 'like', '%' . $request->vehicle_category . '%');
            }

            $portvehicles = $query->get();

            return DataTables::of($portvehicles)
                ->addIndexColumn()

                ->addColumn('action', function ($portvehicles) {
                    return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" data-id="' . $portvehicles->form_unique_id . '"></i>';
                })
                ->make(true);
        }

        return view('content.portvehicleReport.index');
    }

    public function getportvehicleDetails($id)
    {
        // Fetch main vehicle registration details
        $portvehicle_registration = DB::table('view_portvehicle_registration') // Updated table name
            ->where('form_unique_id', $id)
            ->first();

        // If not found in the main table, fetch from the sub-table
        // if (!$vehicle_registration) {
        $portvehicle_registration_sub = DB::table('port_vehicle_registration_sub') // Updated table name
            //  ->leftJoin('document_master', 'port_vehicle_registration_sub.certificate_type', '=', 'document_master.unique_id')
            ->where('screenUniqueId', $portvehicle_registration->form_unique_id)
            ->where('port_vehicle_registration_sub.is_delete', 0)
            ->select(
                'port_vehicle_registration_sub.*',
                DB::raw('DATE_FORMAT(port_vehicle_registration_sub.valid_up_to,"%d-%m-%Y")as valid_date')
            )
            ->get();

        // Return a not found response if the sub-table also has no data
        if ($portvehicle_registration_sub->isEmpty()) {
            return response()->json(['message' => 'Vehicle details not found'], 404);
        }

        // // Return data from the sub-table
        // return response()->json([
        //     'success' => true,
        //     'vehicle_registration_sub' => $vehicle_registration_sub,
        // ]);
        // }

        // Return data from the main table
        return response()->json([
            'success' => true,
            'portvehicle_registration' => $portvehicle_registration,
            'portvehicle_registration_sub' => $portvehicle_registration_sub,

        ]);
    }
}
