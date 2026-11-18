<?php

namespace App\Http\Controllers;

use App\Models\Ratemaster;
use Illuminate\Http\Request;


use Carbon\Carbon;


use Yajra\DataTables\DataTables;


use Illuminate\Support\Facades\DB;

class RateMasterApproval extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Get all distinct allowed types from the rate_master table
            $rate_master_approval_query = DB::table('rate_master')
                ->select('allowed_type')
                ->where('is_delete', 0)
                ->distinct()
                ->get();

            if ($rate_master_approval_query->isEmpty()) {
                return response()->json(['error' => 'No allowed types found.'], 404);
            }

            $results = [];

            // Loop through each allowed type
            foreach ($rate_master_approval_query as $type) {
                $allowedType = $type->allowed_type;

                // Start building the base query
                $rate_master_approval = DB::table('rate_master')
                    ->join('access_gate', 'rate_master.access_gate', '=', 'access_gate.unique_id')
                    ->where('rate_master.is_delete', 0);

                // Adjust the query based on allowed type
                if ($allowedType === 'Vehicle') {
                    $rate_master_approval->join('vehicle_creation', 'rate_master.vehicle_person_type', '=', 'vehicle_creation.unique_id')
                        ->select(
                            'access_gate.access_gate',
                            'rate_master.allowed_type',
                            'vehicle_creation.vehicle_type as vehicle_person',
                            'rate_master.validity',
                            'rate_master.rate',
                            'rate_master.unique_id', // Ensure you have unique_id for action
                            'rate_master.approval_status' // Ensure you have approval_status for status mapping
                        );
                } elseif ($allowedType === 'Person') {
                    $rate_master_approval->join('person_type', 'rate_master.vehicle_person_type', '=', 'person_type.unique_id')
                        ->select(
                            'access_gate.access_gate',
                            'rate_master.allowed_type',
                            DB::raw('NULL as vehicle_type'),
                            'person_type.person_type as vehicle_person',
                            'rate_master.validity',
                            'rate_master.rate',
                            'rate_master.unique_id', // Ensure you have unique_id for action
                            'rate_master.approval_status' // Ensure you have approval_status for status mapping
                        );
                }


                if ($request->has('from_date') && $request->from_date != '') {
                    $rate_master_approval->where('rate_master.entry_date', '>=', $request->from_date);
                }
                if ($request->has('to_date') && $request->to_date != '') {
                    $rate_master_approval->where('rate_master.entry_date', '<=', $request->to_date);
                }

                if ($request->has('allowed_type') && $request->allowed_type != '') {
                    $rate_master_approval->where('rate_master.allowed_type', 'like', '%' . $request->allowed_type . '%');
                }
                if ($request->has('status_filter') && $request->status_filter != '') {
                    $rate_master_approval->where('rate_master.approval_status', $request->status_filter);
                }




                // Execute the query and get results
                $rate_master_approval_results = $rate_master_approval->get();

                // Merge the results into the final array
                $results = array_merge($results, $rate_master_approval_results->toArray());
            }
            // die($results);
            // Map the results for action buttons and status display
            $results = collect($results)->map(function ($item) {
                $item->action = '<button class="btn btn-primary btn-small btn-right" onclick="window.location.href=\'' . route('rate_master_approval_add', ['id' => $item->unique_id]) . '\'"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';

                if ($item->approval_status == 1) {
                    $item->approval_status = '<span style="color:green;">Approved</span>';
                } elseif ($item->approval_status == 2) {
                    $item->approval_status = '<span style="color:red;">Rejected</span>';
                } elseif ($item->approval_status == 0) {
                    $item->approval_status = '<span style="color:blue;">Pending</span>';
                }

                return $item;
            });

            // Return the final results as DataTables
            return DataTables::of($results)
                ->addIndexColumn()
                ->rawColumns(['approval_status', 'action']) // Adjust the rawColumns to your specific needs
                ->make(true);
        }

        return view('content.rate_master_approval.index');
    }

    public function add($id)
    {


        $rate_master_approval = DB::table('rate_master')
            ->where('unique_id', $id)
            ->first();

        return view('content.rate_master_approval.create', compact('rate_master_approval'));
    }


    public function status(Request $request)
    {
        $request->validate([
            'status' => 'required|string|max:100',
            'approved_by' => 'required|string|max:255',
            'main_unique_id' => 'required|string|max:255',
            'reject_reason' => 'nullable|string|max:255',

        ]);


        $rate_master_approval = Ratemaster::where('unique_id', $request->main_unique_id)->first();

        if ($rate_master_approval) {
            $rate_master_approval->update([
                'unique_id' => $request->main_unique_id,
                'approval_status' => $request->status,
                'reject_reason' => $request->reject_reason,
                'approved_by' => $request->approved_by,
                'approval_date' => Carbon::now()->toDateString(),
            ]);

            return response()->json(['status' => 'Status Approved Successfully']);
        } else {
            return response()->json(['status' => 'Record not found'], 404);
        }
    }
}
