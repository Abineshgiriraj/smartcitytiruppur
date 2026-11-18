<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FirmsAuthentication;
use App\Models\FirmsRegistrationSub;
use App\Models\passentrymainlist;
use App\Models\passentrysub;

use App\Models\viewpassissue;


use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PassApprovalController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $pass_entry = DB::table('view_permitissue')->whereIn('type_of_pass', ['day', 'monthly', 'yearly', 'half_yearly'])
            
                ->select(
                    'view_permitissue.unique_id',
                    'view_permitissue.pass_id',
                    'view_permitissue.pass_type',
                    'view_permitissue.type_of_pass',
                    'view_permitissue.company_name',
                    'view_permitissue.agent_type_name',
                    'view_permitissue.short_name',
                    'view_permitissue.vehicle_no',
                    'view_permitissue.no_of_days',
                    'view_permitissue.person_driver_name',
                    'view_permitissue.from_date',
                    'view_permitissue.to_date',
                    'view_permitissue.approval_status',
                    'view_permitissue.extend_approval_status',
                    DB::raw('DATE_FORMAT(view_permitissue.entry_date,"%d-%m-%Y")as formatted_date')
                );

            if ($request->has('from_date') && !empty($request->from_date)) {
                try {
                    // Convert from Y-m-d to d-m-Y
                    $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d-m-Y');
                    $pass_entry->where('view_permitissue.entry_date', '>=', $fromDate);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid from_date format'], 400);
                }
            }

            if ($request->has('to_date') && !empty($request->to_date)) {
                try {
                    // Convert from Y-m-d to d-m-Y
                    $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d-m-Y');
                    $pass_entry->where('view_permitissue.entry_date', '<=', $toDate);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid to_date format'], 400);
                }
            }

            // Filter by 'type_of_pass' if provided
            if (!empty($request->type_pass)) {
                $pass_entry->where('view_permitissue.type_of_pass',  $request->type_pass);
            }

            // Filter by 'approval_status' if provided, default to 0 if not
            $approvalStatus = $request->approval_status ?? 0;
            // $approvalStatus = $request->approval_status ?? 0;
            
            // $pass_entry->where('view_permitissue.approval_status', $approvalStatus)
            // ->orwhere('view_permitissue.extend_approval_status','=','0');
            // $pass_entry_sub = $pass_entry->get();
            // Corrected grouping of conditions
    $pass_entry->where(function ($query) use ($approvalStatus) {
        $query->where('view_permitissue.approval_status', $approvalStatus)
              ->orWhere('view_permitissue.extend_approval_status', '0');
    });
    $pass_entry_sub = $pass_entry->get();


            // Map results for additional action formatting
            $pass_entry_sub1 = $pass_entry_sub->map(function ($item) {
                if ($item->approval_status == 0  ) {
                    // || $item->extend_approval_status == 0
                    $item->action = '<a class="text-primary cursor-pointer" onclick="window.location.href=\'' . route('passapproval.view', ['unique_id' => $item->unique_id]) . '\'" >
                                        <i class="i-Add"></i><span class="lab_add">Pending</span>
                                    </a>';
                } elseif ($item->approval_status == 1) {
                    $item->action = '<span class="text-success fw-bold">Approved&nbsp;&nbsp;&nbsp;&nbsp;
                                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $item->unique_id . '\')"></i>
                                     </span>';
                } else {
                    $item->action = '<span class="text-danger fw-bold">Rejected&nbsp;&nbsp;&nbsp;&nbsp;
                                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $item->unique_id . '\')"></i>
                                     </span>';
                }
                return $item;
            });

            return DataTables::of($pass_entry_sub1)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        // This will only run if the request is not AJAX
        return view('content.PassApproval.index');
    }


    public function add()
    {
        return view('content.PassApproval.create');
    }

    public function update(Request $request, $unique_id)
    {
        // dd($unique_id);
        // Validate the request
        $request->validate([
            'approved_by' => 'required|string',
            'main_unique_id' => 'required',
             // Ensure 'approved_by' is provided
        ]);

        // dd($request->unique_id);

        // Fetch the pass entry sub record by unique_id
        $passsub = passentrysub::where('screen_unique_id', $unique_id)
                                ->where('unique_id',$request->main_unique_id)->first(); // Use `first()` for a single record
        //dd( $passsub);
        // Check if the record exists
        if (!$passsub) {
            return response()->json(['error' => 'Pass entry sub record not found'], 404);
        }

        // Update the pass entry sub record
        $passsub->update([
            'approval_status' => 1,
            'approved_by' => $request->approved_by,
            'status_upd_date' => now(),
        ]);

        // Fetch the related pass entry main record using screen_unique_id
        $passmain = passentrymainlist::where('screen_unique_id', $passsub->screen_unique_id)->first();


        

        // Check if the main entry exists and update it
        if ($passmain) {
            $passmain->update([
                'approval_status' => 1,
                'approved_by' => $request->approved_by,
                'status_upd_date' => now(),
            ]);
        }

        // Return a JSON response
        return response()->json(['status' => 'Pass updated successfully']);
    }


    //     public function viewdata($id)
    // {
    //     // Get the specific pass entry sub record by unique_id
    //     $passs_sub = passentrysub::where('unique_id', $id)->first();  // Use first() to get a single record

    //     // If $passs_sub is not found, handle the error
    //     if (!$passs_sub) {
    //         return redirect()->route('passapproval.view')->with('error', 'Pass entry sub record not found.');
    //     }

    //     // Get the main entry by matching screen_unique_id, use first() since we expect a single record
    //     $passmain = passentrymainlist::where('screen_unique_id', $passs_sub->screen_unique_id)->first();

    //     // If $passmain is not found, handle the error
    //     if (!$passmain) {
    //         return redirect()->route('passapproval.view')->with('error', 'Pass entry main record not found.');
    //     }

    //     // Return the view with the data
    //     return view('content.PassApproval.create', compact('passs_sub', 'passmain'));
    // }
    // public function view($unique_id)

    //     {
    //         // dd($unique_id);
    //         // Fetch the sub-entry record by ID
    //         $passSub = viewpassissue::where('unique_id', $unique_id)->first();

    //         // Check if the record exists
    //         if (!$passSub) {
    //             return redirect()->route('passapproval.index')->with('error', 'Pass entry sub record not found.');
    //         }

    //         // Fetch the main entry record by matching the screen_unique_id
    //         $passmain = PassEntryMainList::where('screen_unique_id', $passSub->screen_unique_id)->first();

    //         // Check if the main entry exists
    //         if (!$passmain) {
    //             return redirect()->route('passapproval.index')->with('error', 'Pass entry main record not found.');
    //         }

    //         // Pass data to the view
    //         return view('content.PassApproval.create', compact('passSub', 'passmain'));
    //     }

    public function view($unique_id)

    {
        // Fetch the sub-entry record by ID
        $passSub = DB::table('view_permitissue')->where('unique_id', $unique_id)->first();

        // dd($passSub);


        if ($passSub->pass_type == 'person') {

         
            if ($passSub->person_type == 'Person') {

                $person = DB::table('person_registration')
                    ->where('rf_id', $passSub->rfid)
                    ->select('form_unique_id')
                    ->first();

                $person_sub = DB::table('person_reg_sublist')
                    ->where('form_unique_id', $person->form_unique_id)
                    ->where('aadhar_licence', $passSub->adhaar_licence)
                    ->select('attached_proof')
                    ->first();

               
            } else if ($passSub->person_type == 'Driver') {

                $person = DB::table('person_registration')
                    ->where('rf_id', $passSub->rfid)
                    ->select('form_unique_id')
                    ->first();

                $person_sub = DB::table('person_reg_sublist')
                    ->where('form_unique_id', $person->form_unique_id)
                    ->where('aadhar_licence', $passSub->adhaar_licence)
                    ->select('attached_proof')
                    ->first();

                //dd($person_sub);

            } else {

                $person = DB::table('person_registration')
                    ->where('rf_id', $passSub->rfid)
                    ->select('form_unique_id')
                    ->first();

                $person_sub = DB::table('person_reg_sublist')
                    ->where('form_unique_id', $person->form_unique_id)
                    ->where('aadhar_licence', $passSub->adhaar_licence)
                    ->select('attached_proof')
                    ->first();
            }
        
        } 
        else if($passSub->pass_type == 'vehicle'){


            $vehicle = DB::table('vehicle_registration')
                            ->where('rf_id',$passSub->rfid)
                            ->where('vech_reg_no',$passSub->vehicle_no)
                            ->select('screenUniqueId')
                            ->first();
                            // dd($vehicle);
                          
             $vehicle_sub = DB::table('vehicle_registration_sub')
                                ->where('screenUniqueId',$vehicle->screenUniqueId)
                                ->select('*')
                                ->first();               


        }

        // Check if the record exists
        // if (!$passSub) {
        //     return redirect()->route('passapproval.index')->with('error', 'Pass entry sub record not found.');
        // }
        // Fetch the main entry record by matching the screen_unique_id

        // Pass data to the view
        if($passSub->pass_type == 'person'){
            return view('content.PassApproval.create', compact('passSub', 'person_sub'));

        } else{
            return view('content.PassApproval.create', compact('passSub', 'vehicle_sub'));
        }
    }


    public function eyeview($unique_id)
    {
        // Debug to check the value of $unique_id
        // dd($unique_id);

        // Get the specific pass entry sub record by unique_id
        $passs_sub = DB::table('view_permitissue')->where('unique_id', $unique_id)->first();
        $approvalPerson = DB::table('user_creation')->where('unique_id', $passs_sub->approved_by)->first();


        // Debug to see the result of the query

        // If $passs_sub is not found, handle the error
        if (!$passs_sub) {
            return response()->json(['error' => 'Pass entry sub record not found.'], 404);
        }

        // Return the data as JSON
        return response()->json([
            'passs_sub' => $passs_sub,
            "authperondetails"  => $approvalPerson
        ]);
    }
}
