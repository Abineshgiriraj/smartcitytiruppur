<?php

namespace App\Http\Controllers;

use App\Models\department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\vehicle_registration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class VehicalAuthenticationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(Request $request)
{
    if ($request->ajax()) {
        $vehicle_reg = DB::table('vehicle_registration')
            ->select(
                'vehicle_registration.unique_id',
                'vehicle_registration.screenUniqueId',
                'vehicle_registration.vech_reg_no',
                DB::raw('(select cargo from cargo_creation where cargo_creation.unique_id = vehicle_registration.vehicle_type) as status'),
                DB::raw('(select vehicle_type from vehicle_creation where vehicle_creation.unique_id = vehicle_registration.vehicle_category) as vehicle_category'),
                'vehicle_registration.entry_date',
                'vehicle_registration.vehicle_serial_no',
                'vehicle_registration.owner_name',
                'vehicle_registration.approval_status'
            )
            ->where('vehicle_registration.is_delete', '=', 0);

        if ($request->has('from_date') && $request->from_date != '') {
            $vehicle_reg->where('vehicle_registration.entry_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $vehicle_reg->where('vehicle_registration.entry_date', '<=', $request->to_date);
        }
        if ($request->has('reg_no') && $request->reg_no != '') {
            $vehicle_reg->where('vehicle_registration.vehicle_serial_no', 'like', '%' . $request->reg_no . '%');
        }
        if ($request->has('vehicle_no') && $request->vehicle_no != '') {
            $vehicle_reg->where('vehicle_registration.vech_reg_no', 'like', '%' . $request->vehicle_no . '%');
        }
        if ($request->has('status_filter') && $request->status_filter != '') {
            $vehicle_reg->where('vehicle_registration.approval_status', $request->status_filter);
        }

        // Fetch and map the results
        $vehicle_reg = $vehicle_reg->get()->map(function ($item) {
            $item->action = '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\''
                . route('vehical_authentication_add', ['screenUniqueId' => $item->screenUniqueId])
                . '\'" type="button"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';

            // Map approval status to labels
            if ($item->approval_status == 1) {
                $item->approval_status = '<span style="color: green;">Approved</span>';
            } elseif ($item->approval_status == 2) {
                $item->approval_status = '<span style="color: red;">Rejected</span>';
            } elseif ($item->approval_status == 0) {
                $item->approval_status = '<span style="color: blue;">Pending</span>';
            } else {
                $item->approval_status = '<span>Status Unknown</span>';
            }

            return $item;
        });

        // Return the data for DataTables
        return DataTables::of($vehicle_reg)
            ->addIndexColumn()
            ->rawColumns(['approval_status', 'action'])
            ->make(true);
    }

    // Return the main view for non-AJAX requests
    return view('content.vehical_authentication.index');
}

    /**
     * Show the form for creating a new resource.
     */
    public function create($screenUniqueId)
    {
        // Fetch vehicle registration based on screenUniqueId
        $vehicle_reg = DB::table('vehicle_registration')
            ->select('*',
            DB::raw('(select cargo from cargo_creation where cargo_creation.unique_id = vehicle_registration.vehicle_type) as status'),
            DB::raw('(select vehicle_type from vehicle_creation where vehicle_creation.unique_id = vehicle_registration.vehicle_category) as vehicle_category'))
                 ->where('screenUniqueId', $screenUniqueId)
            ->where('is_delete', 0)
            ->first();

        // Fetch related sub-registrations
        $vehicle_sub_reg = DB::table('vehicle_registration_sub')
            ->select('*')
            ->where('screenUniqueId', $screenUniqueId)
            ->where('is_delete', 0)
            ->get();

        // if ($vehicle_sub_reg->isEmpty()) {
        //     dd("No records found for screenUniqueId: {$screenUniqueId}");
        // } else {
        //     dd($vehicle_sub_reg); // This will show the contents of the collection
        // }

        return view('content.vehical_authentication.create', compact('vehicle_reg', 'vehicle_sub_reg'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function status_approval(Request $request)
    {
        $request->validate([
            'status' => 'required|string|max:100',
            'reject_reason' => 'nullable|string|max:100',
            'screen_unique_id' => 'required|string|max:255',
            'approved_by'       => 'required|string|max:255'
        ]);



        $status_approval  = vehicle_registration::where('screenUniqueId', $request->screen_unique_id)->first();

        // dd($status_approval);

        if ($request->screen_unique_id) {

            $status_approval->approval_status = $request->status;
            $status_approval->reject_reason =  $request->reject_reason;
            $status_approval->screenUniqueId =  $request->screen_unique_id;
            $status_approval->approved_by =  $request->approved_by;



            $status_approval->approved_date = Carbon::now()->toDateString();
            $status_approval->update();

            return response()->json(['status' => 'Status Approved Successfully']);
        }
    }

    /**
     * Display the specified resource.
     */
}
