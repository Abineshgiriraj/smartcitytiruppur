<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\person_registration;
use App\Models\vehicle_registration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\access_zone;
use App\Models\view_permitissue;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

use DB;



class PassExtendDetailGetController extends Controller
 {
    


public function index(Request $request)
{

    return view('content.pass_extend.index');

}

// new changes filter
public function getDataextend(Request $request)
{
    // Initialize the query with filters for sub_extend_to_date
    $pass_entry_list = view_permitissue::query()
        // ->where('is_delete', 0)
        ->whereNotNull('sub_extend_to_date') // Ensures the date is not NULL
        ->where('sub_extend_to_date', '!=', '0') // Ensures the date is not '0'
        ->where('sub_extend_to_date', '!=', ''); // Ensures the date is not empty

   
if ($request->from_date) {

    $pass_entry_list->where('view_permitissue.extend_entry_date','>=',$request->from_date);
    
    }
    
    // Filter by to_date
    if ($request->to_date) 
    {
    try {
    
    
        $to_date = Carbon::parse($request->to_date)->endOfDay();
    
        $pass_entry_list->where('view_permitissue.extend_entry_date','<=',$to_date);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid to_date format'], 400);
    }
    }
   
    // Fetch the filtered pass entry list
    $passextend = $pass_entry_list->get();
// dd($passextend);
    // Process data for DataTables
    // return DataTables::of($passextend)
    //     ->addIndexColumn() // Automatically adds the serial number
    //     ->addColumn('action', function ($passextend) {
    //         // Conditionally render delete button if payment_status is '0'
    //         return $passextend->payment_status == '0' 
    //             ? '<a href="javascript:void(0);" class="delete-btn" data-id="' . $passextend->unique_id.'">
    //                     <i class="fas fa-trash-alt"></i>
    //                </a>'
    //             : '';
    //     })
        return DataTables::of($passextend)
    ->addIndexColumn() // Automatically adds the serial number
    ->addColumn('action', function ($passextend) {
        $actionButtons = ''; // Initialize an empty string

        // If payment_status is '0', show the delete button
        if ($passextend->payment_status == '0') {
            $actionButtons .= '<a href="javascript:void(0);" class="delete-btn" data-id="' . $passextend->unique_id . '">
                                <i class="fas fa-trash-alt"></i>
                              </a>';
        }

        // If some other condition (or the same row) requires a Pay button, add it separately
        if ($passextend->extend_approval_status == '1') { // Change condition as needed
            $actionButtons .= '<button type="button" class="btn btn-primary" 
                                onclick="window.location.href=\'' . url('/payment_option') . '?passId=' . $passextend->screen_unique_id . '\'">
                                Pay
                              </button>';
        }

        return $actionButtons; // Return all buttons if conditions match
    })
    ->make(true);

        
        // ->make(true);
}


public function add(Request $request )
{
      
 // Ensure permitIdInput exists in the request
$permitId = $request->permitidInput;
// dd($permitId);
if (!$permitId) {
    return response()->json(['error' => 'Permit ID is required.'], 400);
}

// Fetch person details
$person_details = DB::table('view_permitissue')
    ->where('pass_id', $permitId)
    ->orWhere('adhaar_licence', $permitId)
    ->orWhere('vehicle_no', $permitId)
    ->first();
// dd($person_details);
if (!$person_details) {
    return response()->json(['error' => 'No person data found for the given Permit ID.'], 404);
}

// Fetch personal data
$personal_datas = DB::table('view_permitissue')
    ->leftJoin('vehicle_registration', function ($join) {
        $join->on('view_permitissue.vehicle_no', '=', 'vehicle_registration.vech_reg_no')
             ->orOn('view_permitissue.vech_chase_no', '=', 'vehicle_registration.chase_no');
    })
    ->leftJoin('vehicle_registration_sub', 'vehicle_registration.screenUniqueId', '=', 'vehicle_registration_sub.screenUniqueId')
    ->where(function ($query) use ($permitId) {
        $query->where('view_permitissue.pass_id', $permitId)
              ->orWhere('view_permitissue.vehicle_no', $permitId)
              ->orWhere('view_permitissue.adhaar_licence', $permitId);
    })
    ->where(function ($query) {
        $query->where('vehicle_registration.is_delete', 0)
              ->orWhereNull('vehicle_registration.is_delete');
    })
    ->select([
        'view_permitissue.*',
        'vehicle_registration.vehicle_type',
        'view_permitissue.extend_approval_status',
        'vehicle_registration.owner_name',
        'vehicle_registration_sub.certificate_type',
        'vehicle_registration_sub.valid_up_to',
    ])
    ->get();
// dd($personal_datas);
// Check if data exists
if ($personal_datas->isEmpty()) {
    return response()->json(['error' => 'No data found for the given Permit ID.'], 404);
}

// Return the combined response
return response()->json(['data' => $personal_datas]);
}
public function destroy1($id)
{
    // Find the passentrysub entry by unique_id
    $passentrysub = passentrysub::where('unique_id', $id)->first();

    // Check if the entry exists before updating
    if ($passentrysub) {
        // Update only if the record exists
        $updated = $passentrysub->update([
            'extend_gross_amount' => null,
            'extend_tax_amount' => null,
            'extend_to_date' => null,
            'extend_period' => null,
            'extend_pass_type' => null,
            'extend_net_amount' => null,
            'extend_cgst' => null,
            'extend_sgst' => null,
            'extend_file_upload' => null,
            'extend_entry_date' => null,
        ]);

        // Return success response only if update succeeds
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'passentrysub Updated Successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Update Failed'], 500);
        }
    }

    // If the entry doesn't exist, return an error response
    return response()->json(['success' => false, 'message' => 'passentrysub Not Found'], 404);
}


// end

//     public function get_pass_data( Request $request )
//  {

//         $rf_id = $request->rf_id;
//         $pass_main = passentrymainlist::where( 'rfid', $rf_id )
//         ->select( 'screen_unique_id' )
//         ->first();
//         // dd( $pass_main );

//         $pass_entry_sub = passentrysub::where( 'screen_unique_id', '=', $pass_main->screen_unique_id )

//         // dd( $pass_entry_sub )

//         ->join( 'port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id' )
//         ->join( 'pass_entry_main_list', 'pass_entry_sublist.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id' )
//         ->select( 'pass_entry_sublist.pass_id', 'port_area_access.area_name as access_area', 'pass_entry_sublist.from_date', 'pass_entry_sublist.to_date' )
//         ->get();

//         // dd( $pass_entry_sub );
//         // 'pass_entry_main_list.pass_type'

//         return response()->json( [ 'pass_entry_sub' => $pass_entry_sub ] );
//     }

    /**
    * Store a newly created resource in storage.
    */


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

//     public function destroy1($id)
// {
//     // Find the passentrysub entry by unique_id
//     $passentrysub = passentrysub::where('unique_id', $id)->first();
    
//     // Check if the entry exists before updating
//     if ($passentrysub) {
//         $passentrysub->update(['is_delete' => 1]);
//         return response()->json(['status' => 'passentrysub Deleted Successfully']);
//     }

//     // If the entry doesn't exist, return an error
//     return response()->json(['status' => 'passentrysub not found'], 404);
// }


// extend_gross_amount
// extend_tax_amount
// extend_to_date
// extend_period
// extend_pass_type
// extend_net_amount
// extend_cgst
// extend_sgst
// extend_file_upload
// extend_entry_date
    
}