<?php

namespace App\Http\Controllers;

use App\Models\CompanyCreation;
use App\Models\FirmsRegistration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\PersonType;
use App\Models\Vehiclecreation;
use App\Models\AcessZone;
use App\Models\PortAreaAccess;
use App\Models\CardPassIssue;
use App\Models\rate_master;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use DB;
use App\Models\viewpassissue;


// Add this at the top of your controller
// use Carbon\Carbon;
// use DB;

class passEntryController extends Controller
{

    public function index(Request $request)
    {
        // Fetch pass entries with applied filters
       // Initialize the query with the basic condition
    $query = PassEntryMainList::where('is_delete', 0);

    // Add filters based on the request parameters
    if ($request->pass_id) {
        $query->where('pass_id', $request->pass_id);
    }
    if ($request->firms_agent) {
        $query->where('firms_agent', 'LIKE', "%{$request->firms_agent}%");
    }
    if ($request->agent_type) {
        $query->where('agent_type', $request->agent_type);
    }
    if ($request->from_date) {
        $fromDate = Carbon::parse($request->from_date)->format('Y-m-d');
        $query->where('entry_date', '>=', $fromDate);
    }
    if ($request->to_date) {
        $toDate = Carbon::parse($request->to_date)->format('Y-m-d');
        $query->where('entry_date', '<=', $toDate);
    }

    // Fetch the filtered pass entry list
    $pass_entry_list = $query->get();

    // Return JSON response for AJAX requests
    if ($request->ajax()) {
        return response()->json([
            'data' => $pass_entry_list->map(function($item) {
                return [
                    'pass_id' => $item->pass_id,  // Make sure these keys match your DataTable columns
                    'firms_agent' => $item->firms_agent,
                    'agent_type' => $item->agent_type,
                    'entry_date' => $item->entry_date,
                    // Add other columns as necessary
                ];
            }),
        ]);
    }
        // Fetch related data for dropdowns
        $firm_registration1 = FirmsRegistration::where('is_delete', 0)
            ->pluck('name', 'unique_id');
    
        $company_name = CompanyCreation::where('is_delete', 0)
            ->pluck('company_name', 'unique_id');
    
        $personTypes = PersonType::where('is_delete', 0)
            ->pluck('person_type', 'unique_id');
    
        // Fetch sublist counts and data
        foreach ($pass_entry_list as $passentrymain_list) {
            $sublist_count = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
                ->where('is_delete', 0)
                ->count();
            $passentrymain_list->sublist_count = $sublist_count;
    
            $sublist_data = DB::table('pass_entry_sublist')->where('screen_unique_id', $passentrymain_list->screen_unique_id)
                ->where('is_delete', 0)
                ->select('*')
                ->get();
            $passentrymain_list->sublist_data = $sublist_data;
        }
    
        // Fetch sublist entries for the first matched pass entry
        $passentrymain_lists = $query->first(); // Use only first result for the next logic
        if ($passentrymain_lists) {
            $sublist_entries = passentrysub::where('agent_name', $passentrymain_lists->firms_agent)
                ->where('is_delete', 0)
                ->get();
        } else {
            $sublist_entries = collect(); // No entries found
        }
    
        // Fetch main entries related to the sublist if available
        if ($sublist_entries->isNotEmpty()) {
            $sublist_entriess_main = passentrymainlist::where('firms_agent', $sublist_entries->first()->agent_name)
                ->where('is_delete', 0)
                ->select('pass_id', 'vec_reg_no')
                ->get();
        } else {
            $sublist_entriess_main = collect(); // No entries found
        }
    
        return view('content.pass_entry.list', compact('pass_entry_list', 'firm_registration1', 'company_name', 'personTypes', 'sublist_data', 'sublist_entries', 'sublist_entriess_main'));
    }
    
    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return view('content.pass_entry.form');
    }

    public function show()
    {
        return view('content.pass_entry.form_user');
    }
    /**
     * Store a newly created resource in storage.
     */

    public function sublist(Request $request)
    {
        // $uniqueId = uniqid();
        $request->validate([
            'unique_id' => 'max:255',
            'screen_unique_id' => 'required|max:255', // Ensure it's required
            'firms_agent' => 'max:255',
            'firms_agent_type' => 'max:255',
            'type_of_pass' => 'max:255',
            'no_of_days' => 'max:255',
            'from_date' => 'max:255',
            'to_date' => 'max:255',
            'sgst' => 'max:255',
            'cgst' => 'max:255',
            'gross_amount' => 'max:255',
            'pass_status' => 'max:255',
            'access_area' => 'max:255',
            'net_amount' => 'max:255',
            

        ]);


        if ($request->type_of_pass == 'monthly' || $request->type_of_pass == 'halfyearly' || $request->type_of_pass == 'yearly') {
            // alert('hi');
            $approval_status = '0';
        } else if ($request->type_of_pass == 'day' || $request->type_of_pass == 'weekly') {
            // alert('hlo');
            $approval_status = '1';
        }

        $toDate = $request->to_date;

        // Check if there are existing records with the same screen_unique_id
        $count = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->count();
        $gettodate = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->value('to_date');

        // Check if the to_date is in the future compared to current date
        $toDate = strtotime($gettodate);
        $currentDate = Carbon::now()->format('Y-m-d h:i A');

        // if ($toDate <= $currentDate && $count > 0) {
        //     return response()->json([
        //         'message' => 'The to date is not expiry. No new pass added.',
        //     ], 400);
        // }
        if ($toDate <= $currentDate && $count > 0) {
            return response()->json([
                'status' => 400,
                'title' => 'Expired',
                'message' => 'The to date is not expiry. No new pass added.',
            ], 400);
        }

        // Check if there are existing records with the same screen_unique_id
        $validpass = PassEntrySub::where('pass_valid_status', 0)
        ->where('rfid_hep_no', $request->rfid_hep_no)
        ->where('is_delete', 0)
        ->first();

    // if ($validpass) {
    //     // If it exists, return an error message
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'This RFID HEP Number is already in use and valid.',
    //     ], 400); // Bad Request
    // }
    if ($validpass) {
        return response()->json([
            'status' => 400,
            'title' => 'Expired',
            'message' => 'This RFID HEP Number is already in use and valid.',
        ], 400);
    }

    


        $pass_sublist = passentrysub::create([
            'unique_id' => unique_id(),
            'screen_unique_id' => $request->screen_unique_id,
            'agent_name' => $request->firms_agent,
            'agent_type' => $request->firms_agent_type,
            'type_of_pass' => $request->type_of_pass,
            'no_of_days' => $request->no_of_days,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'amount' => $request->amount,
            'gross_amount' => $request->gross_amount,
            'access_area' => $request->access_area,
            'access_zone' => $request->access_zone,
            'rfid_hep_no' => $request->rfid_hep_no,
            'tax_amount' => $request->tax_amount,
            'pass_valid_status' => $request->pass_valid_status ?? '0',
            'approval_status' => $approval_status,  // Default to '0' if not provided
            'net_amount' => $request->net_amount,
            'deactive_date' => $request->deactive_date,
            'entry_date' => Carbon::today()->format('d-m-Y'),
            // 'pass_id' => $this->generateUniqueId()
            'pass_id' => $request->hidden_pass_id,
        ]);
        // Calculate the total pass_amnt for the same screen_unique_id
        $amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->sum('amount');

        $tax_amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0)
            ->sum(DB::raw('amount + cgst + sgst'));

        $firms_agent_name = FirmsRegistration::where('unique_id', $request->firms_agent)
            ->value('name');
        // Retrieve the agent name

        $firms_zone_name = AcessZone::where('unique_id',  $pass_sublist->access_zone)
            ->value('zone_type');
        // Retrieve the agent name

        $firms_area_name = PortAreaAccess::where('unique_id',  $pass_sublist->access_area)
            ->value('area_name');
        // Retrieve the agent name

        $pass_id = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->value('pass_id');

        // NEW TABLE CARD PASS ISSUE
        // $uniqueId = uniqid();
        $cardpassissue = CardPassIssue::create([
            'unique_id' => unique_id(),
            // 'screen_unique_id' => $request->screen_unique_id,
            'pass_id' => $request->pass_id,
            'tax_amount' => $request->tax_amount,
            'status' => $request->pass_valid_status ?? '0',  // Default to '0' if not provided
            'active_date' => $request->active_date,
            // 'deactive_date' => $request->deactive_date,
            'pass_id' => $pass_id,
            'rfid_hep_no' => $request->rfid_hep_no,
            // 'pass_id' => $this->generateUniqueId()
        ]);


        //  dd( $cardpassissue );
        return response()->json([
            'message' => 'Sublist created successfully',
            'pass_sublist' => $pass_sublist,
            'cardpassissue' => $cardpassissue,
            'unique_id' => $pass_sublist->unique_id,
            'screen_unique_id' => $pass_sublist->screen_unique_id,
            'amount' => $amount,
            'tax_amount' => $tax_amount,
            'firms_agent_name' => $firms_agent_name,
            'firms_zone_name' => $firms_zone_name,
            'firms_area_name' => $firms_area_name,
            'pass_valid_status' => $pass_sublist->pass_valid_status,
            'from_date' => $pass_sublist->from_date,
            'type_of_pass' => $pass_sublist->type_of_pass,
            'tax_amount' => $pass_sublist->tax_amount,
            'to_date' => $pass_sublist->to_date,
            // 'sublist' => $sublist,

        ]);

        //  }
    }

    public function store(Request $request)
    {

        // Validate the incoming request data
        $request->validate([
            'unique_id' => 'max:255',
            'screen_unique_id' => 'required|max:255',
            'issue_center' => 'required|max:255',
            'firms_agent' => 'required|max:255',
            'firms_agent_type' => 'required|max:255',
            'passType' => 'max:255',
            'vectype' => 'max:255',
            'persontype' => 'max:255',
            'vec_reg_no' => 'max:255',
            'driving_license' => 'max:255',
            'access_area' => 'max:255',
            'adhaar_no' => 'max:255',
            'vech_no' => 'max:255',
            'pass_weight' => 'max:255',
            'pass_amount' => 'max:255',
            // 'cover_letter' => 'required|file|mimes:jpg, jpeg, png, doc, docx, pdf|max:2048',
            'cover_letter' => 'required|file|max:2048',
            'pass_id' => 'max:255',
            'acadamic_year' => 'max:255',
            'pass_status' => 'max:255',
            'net_amount' => 'max:255',
            'rfid' => 'max:255',
            'student_type'=> 'max:255',

        ]);

        // Initialize the variable for the cover letter filename
        $cover_letter_name = null;

        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move(custom_public_path('uploads/pass_entry'), $cover_letter_name);

            // Debugging line
            // Log::info( 'Cover letter uploaded: ' . $cover_letter_name );
        }
        // $net_amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
        // ->where('is_delete', 0)
        //     ->sum('gross_amount');

        // Create the person registration record
        $pass_entry_main = passentrymainlist::create([
            'unique_id' => unique_id(),
            'screen_unique_id' => $request->screen_unique_id,
            'issue_center' => $request->issue_center,
            'firms_agent' => $request->firms_agent,
            'pass_type' => $request->passType,
            'vectype' => $request->vectype,
            'vech_categ' => $request->vehicle_category,
            'persontype' => $request->persontype,
            'vec_reg_no' => $request->vec_reg_no,
            'driving_license' => $request->driving_license,
            'access_area' => $request->access_area,
            'adhaar_no' => $request->adhaar_no,
            'vech_no' => $request->vech_no,
            'pass_amount' => $request->pass_amount,
            'pass_weight' => $request->pass_weight,
            'cover_letter' => $cover_letter_name, // Save the filename in the database
            'pass_status' => $request->pass_status,
            'acadamic_year' => $request->acadamic_year,
            // 'agent_type' => 'max:255',
            'pass_id' => $request->pass_id,
            'agent_type' => $request->firms_agent_type,
            'net_amount' => $request->net_amount,
            'rfid' => $request->rfid_value,
            'prm_sec' => $request->prm_sec,
            // 'tax_amount'=> $request->with_tax_amount,
            'rfid' => $request->rfid_hep_no,
            'chase_no' => $request->equ_reg_no,
            'tax_amount' => $request->tax_amount,
            'student_type'=> $request->visitorType,
            'entry_date' => Carbon::today()->format('d-m-Y')

        ]);

        // dd($pass_entry_main );
        // dd( $pass_entry_main );
        // Return a success response
        return response()->json([
            'message' => 'Person registered successfully',
            'pass_entry_main' => $pass_entry_main,
            'screen_unique_id' => $pass_entry_main->screen_unique_id,
            'pass_id' => $pass_entry_main->pass_id,
        ]);
    }
    public function update(Request $request, $id)
    {
        // dd( $id);
        // Validate the incoming request data
        $request->validate([
            'issue_center' => 'required|max:255',
            'pass_id' => 'required',
            'firms_agent' => 'max:255',
            'passType' => 'max:255',
            'vectype' => 'max:255',
            'persontype' => 'max:255',
            'vec_reg_no' => 'max:255',
            'driving_license' => 'max:255',
            'access_area' => 'max:255',
            'adhaar_no' => 'max:255',
            'vech_no' => 'max:255',
            'pass_weight' => 'max:255',
            'pass_amount' => 'max:255',
            'net_amount' => 'max:255',
            // 'cover_letter' => '|mimes:jpg, jpeg, png, doc, docx, pdf|max:2048',
            'pass_status' => 'required|in:Approved, NotApproved',
            'acadamic_year' => 'max:255',
        ]);
        // $passentrymainlists = passentrymainlist::findOrFail($id)
        // ->where('is_delete', 0);
        $passentrymainlists = passentrymainlist::where('id', $id)
    ->where('is_delete', 0)
    ->firstOrFail();
// dd($passentrymainlist);
        // Find the firm by ID or fail
        $cover_letter_name = $passentrymainlists->cover_letter;
        // Default to existing filename
        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move(custom_public_path('uploads/pass_entry'), $cover_letter_name);
        }
        // Update firm details
        // $passentrymainlists->update( [
        //    'issue_center' => $request->issue_center,
        //     'firms_agent' => $request->firms_agent,
        //     'pass_type' => $request->passType,
        //     'vectype' => $request->vectype,
        //     'vech_categ' => $request->vech_categ,
        //     'persontype' => $request->persontype,
        //     'vec_reg_no' => $request->vec_reg_no,
        //     'driving_license' => $request->driving_license,
        //     'access_area' => $request->access_area,
        //     'adhaar_no' => $request->adhaar_no,
        //     'vech_no' => $request->vech_no,
        //     'pass_amount' => $request->pass_amount,
        //     'pass_weight' => $request->pass_weight,
        //     'cover_letter' => $cover_letter_name, // Save the filename in the database
        //     'pass_status' => $request->pass_status,
        //     'acadamic_year' => $request->acadamic_year,
        //     'pass_id' => $request->pass_id,
        //     'agent_type' => $request->firms_agent_type,
        //     // 'net_amount' => $net_amount,
        //     'rfid' => $request->rfid,
        //     'prm_sec' => $request->prm_sec,
        //     'tax_amount'=> $request->tax_amount,
        //     'rfid'=> $request->rfid_hep_no,
        //     'chase_no'=> $request->chase_no,

        // ] );
        // Update firm details
        $passentrymainlists->update([
            'issue_center' => $request->issue_center,
            'pass_id' => $request->pass_id,
            'firms_agent' => $request->firms_agent,
            'passType' => $request->passType,
            'vectype' => $request->vectype,
            'vech_categ' => $request->vehicle_category,
            'persontype' => $request->persontype,
            'vec_reg_no' => $request->vec_reg_no,
            'driving_license' => $request->driving_license,
            'access_area' => $request->access_area,
            'vech_no' => $request->vech_no,
            'pass_weight' => $request->pass_weight,
            'pass_amount' => $request->pass_amount,
            'cover_letter' => $cover_letter_name, // Save the filename in the database
            'pass_status' => $request->pass_status,
            'net_amount' => $request->net_amount,
            'acadamic_year' => $request->acadamic_year,
            'rfid' => $request->rfid,
            'prm_sec' => $request->prm_sec,
            'tax_amount' => $request->tax_amount,
            'rfid' => $request->rfid_hep_no,
            'student_type'=> $request->visitorType,
            'chase_no' => $request->equ_reg_no,
        ]);
        // dd( $passentrymainlists);
        $passentrysub = passentrysub::where('screen_unique_id', $passentrymainlists->screen_unique_id); // Find the firm by ID or fail
        $passentrysub->update([
            'rfid_hep_no' => $request->rfid_hep_no,
        ]);
        // dd($passentrymainlists);
        return response()->json(['message' => 'Pass Entry updated successfully', 'passentrymainlists' => $passentrymainlists, 'passentrysub' => $passentrysub]);
    }
  


    public function edit($id)
    {
        // Fetch the main entry
        $passentrymain_list = passentrymainlist::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        // Check if the main entry exists
        if (!$passentrymain_list) {
            return redirect()->route('pass_entry')->with('error', 'Pass Entry not found.');
        }
        // Fetch all firm registrations
        $firm_registration = FirmsRegistration::where('is_delete', 0)
        
            ->get(['unique_id', 'firms']);
        // Fetch all firm registrations
        $firm_registration_name = FirmsRegistration::where('is_delete', 0)
            ->get(['unique_id', 'name']);

        $firm_person_name = personType::where('is_delete', 0)
            ->get(['unique_id', 'person_type']);

        // Fetch all firm agent type
        $company_creation = CompanyCreation::where('is_delete', 0)
            ->get(['unique_id', 'company_name']);

        $firm_registration1 = FirmsRegistration::where('is_delete', 0)
            ->pluck('name', 'unique_id');
        // This gives you a collection with unique_id as the key and name as the value

        // $net_amount = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
        // ->where('is_delete', 0)
        //     ->sum('gross_amount');

        // Fetch all firm registrations
        $vehicle_creation = Vehiclecreation::where('is_delete', 0)
            ->get(['unique_id', 'vehicle_type']);
        // Fetch related sublist entries using the screen_unique_id
        $sublist_entries = passentrysub::where('pass_entry_sublist.screen_unique_id', $passentrymain_list->screen_unique_id)
            ->where('pass_entry_sublist.is_delete', 0) // Specify table for `is_delete`
            ->leftJoin('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
            ->leftJoin('access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id')
            ->leftJoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')

            ->select(
                'pass_entry_sublist.*',
                'firms_registration.name as firms_agent_name',
                'access_zone.zone_type as firms_zone_name',
                'port_area_access.area_name as firms_area_name'
            )

            ->get();
        // Calculate the sums after retrieving the entries
        // $totalGrossAmount = $sublist_entries->sum('net_amount');
        $totalGross = $sublist_entries->sum('amount');
        // $totalTaxAmount = $sublist_entries->sum('tax_amount');


        // Calculate the total gross_amount for the sublist entries

        $main_list = passentrymainlist::where('screen_unique_id', $passentrymain_list->screen_unique_id)
            ->where('pass_type', $passentrymain_list->pass_type)
            ->where('pass_amount', $passentrymain_list->pass_amount)
            ->where('is_delete', 0)
            ->first();

        return view('content.pass_entry.edit', compact(
            'passentrymain_list',
            'sublist_entries',
            'firm_registration',
            // 'totalGrossAmount',
            'main_list',
            'vehicle_creation',
            'firm_registration1',
            'company_creation',
            'firm_registration_name',
            'firm_person_name',
            // 'net_amount',
            'totalGross',
            // 'totalTaxAmount',

        ));
    }


    public function getLastFirmNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Assuming you have a Firm model set up
        $lastFirm = passentrymainlist::where('pass_id', 'like', "P$year$month%")
        ->where('is_delete', 0)
            ->orderBy('pass_id', 'desc')
            ->first();
        // dd( $lastFirm );

        $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->pass_id, -5) : 0;
        $newFirmId = sprintf('P%s%s%05d', $year, $month, $lastFirmNumber + 1);
        // dd( $newFirmId );
        // Return as JSON object
        return response()->json(['pass_id' => $newFirmId]);
    }

   
    public function destroy($id)
    {
        // Find the record using unique_id
        $passentrymainlist_id = passentrymainlist::where('unique_id', $id)->first(); // Use first() to retrieve the record
    
        // Check if the record exists
        if (!$passentrymainlist_id) {
            return response()->json(['success' => false, 'message' => 'Center not found.']);
        }
    
        // Soft delete by setting the is_delete flag
        $passentrymainlist_id->is_delete = 1;
        $passentrymainlist_id->save();
    
        return response()->json(['success' => true, 'message' => 'Deleted successfully!']);
    }
    

    public function delete($unique_id)
    {
        // Find the entry by unique_id
        $passEntry = passentrysub::where('unique_id', $unique_id)->first();

        // Check if the entry exists
        if (!$passEntry) {
            return response()->json(['message' => 'Entry not found'], 404);
        }

        // Delete the entry
        $passEntry->delete();

        return response()->json(['message' => 'Entry deleted successfully']);
    }

    //     public function subdestroy1( $id )
    //  {
    //         // Debug the unique_id
    //         // dd( $id );

    //         // Try to find the entry by unique_id
    //         $passentrysub = passentrysub::where( 'id', $id )->first();

    //         // Debug the result
    //         // dd( $passentrysub );

    //         // Check if the entry exists
    //         if ( !$passentrysub ) {
    //             return response()->json( [ 'success' => false, 'message' => 'Pass not found.' ] );
    //         }

    //         // Soft delete by setting is_delete flag
    //         $passentrysub->is_delete = 1;
    //         $passentrysub->save();

    //         return response()->json( [
    //             'success' => true,
    //             'message' => 'Deleted successfully!',
    //             'id' => $passentrysub->id,
    //         ] );
    //     }

    public function subdestroy1($id)
    {
        // Debug the unique_id
        // dd( $id );

        // Try to find the entry by unique_id
        $passentrysub = passentrysub::where('id', $id)->first();

        // Debug the result
        // dd( $passentrysub);

        // Check if the entry exists
        if (!$passentrysub) {
            return response()->json(['success' => false, 'message' => 'Pass not found.']);
        }

        // Soft delete by setting is_delete flag
        $passentrysub->is_delete = 1;
        $passentrysub->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!',
            'id' => $passentrysub->id,
        ]);
    }

    public function generateUniqueId()
    {
        // Get current year and month
        $prefix = 'P' . date('Ym');

        // Retrieve the last inserted record with a similar prefix
        $lastRecord = passentrysub::where('pass_id', 'LIKE', "{$prefix}%")->orderBy('id', 'desc')->first();

        if (!$lastRecord) {
            // If no record exists, start with 00001
            $newNumber = '001';
        } else {
            // Extract the numeric part and increment by 1
            $lastNumber = intval(substr($lastRecord->pass_id, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        // Combine the prefix with the new number
        return $prefix . $newNumber;
    }

    public function sublistindex(Request $request)
    {
        // Retrieve the sublist data;
        $sublist = DB::table('pass_entry_sublist')->where('pass_entry_sublist.is_delete', 0)
            ->where('screen_unique_id', $request->screen_unique_id)
            ->get();
        foreach ($sublist as $item) {
            $item->firms_zone_name = AcessZone::where('unique_id', $item->access_zone)->value('zone_type');
            $item->firms_area_name = PortAreaAccess::where('unique_id', $item->access_area)->value('area_name');
            $item->agent_name = FirmsRegistration::where('unique_id', $item->agent_name)->value('name');
        }

        // Return the data as a JSON response
        return response()->json($sublist);
    }



    // new changes
    public function updateHEPNo(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'extend_pass_type' => 'required|string',
            'extend_period' => 'required|integer',
            'extend_to_date' => 'required|date',
            'extend_file_upload'  => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048', // Removed extra space
            // 'extend_tax_amount' => 'required|numeric',
        ]);
        $pass = passentrysub::where('pass_id', $id)
        ->where('is_delete', 0)
        ->first();

        if ($request->hasFile('extend_file_upload')) {
            $extend_file_upload = $request->file('extend_file_upload');
            $extend_file_name = time() . '-' . $extend_file_upload->getClientOriginalName();
            $extend_file_upload->move(custom_public_path('uploads/pass_entry/'), $extend_file_name);
        } else {
            // If no new file, retain the existing file
            $extend_file_name = $pass->extend_file_upload;
        }

        if ($pass) {
            // Update the pass details
            $pass->update([
                'extend_pass_type' => $request->extend_pass_type,
                'extend_period' => $request->extend_period,
                'extend_to_date' => $request->extend_to_date,
                'extend_tax_amount' => $request->extend_tax_amount,
                'extend_gross_amount' => $request->extend_gross_amount,
                'extend_net_amount' => $request->extend_net_amount,
                'extend_sgst' => $request->extend_sgst,
                'extend_cgst' => $request->extend_cgst,
                // 'extend_file_upload' => $extend_file_upload
                'extend_file_upload' => $extend_file_name, // Save the new file name
                // 'extend_file_upload' => $request->extend_file_upload,
                // attached_proof

            ]);

            return response()->json(['success' => true, 'message' => 'Pass updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }
    }

    public function sublistindex1(Request $request)
    {
        // Retrieve the sublist data;
        $sublist = passentrysub::where('is_delete', 0)->where('rfid_hep_no', $request->rfidInput)->get();

        // Return the data as a JSON response
        return response()->json($sublist);
    }



    public function updatestatus(Request $request, $id)
    {
        // dd($id);
        // Validate the incoming request data
        $validated = $request->validate([
            'reject_reason' => 'required|string',
        ]);

        // Find the sub entry by pass_id
        $pass_sub = passentrysub::where('pass_id', $id)
        ->where('is_delete', 0)
        ->first();

        if (!$pass_sub) {
            return response()->json(['success' => false, 'message' => 'Sub record not found']);
        }

        // Find the main entry by screen_unique_id
        $pass_main = passentrymainlist::where('screen_unique_id', $pass_sub->screen_unique_id)->first();

        // Update the main record if it exists
        if ($pass_main) {
            $pass_main->update([
                'reject_reason' => $validated['reject_reason'],
                'approval_status' => 2,
                // 'reject_reason' => $request->reject_reason,
                'approved_by' => $request->approved_by,
                'status_upd_date' => now(),
            ]);
        }

        // Update the sub record
        $pass_sub->update([
            'reject_reason' => $validated['reject_reason'],
            'approval_status' => 2,
            // 'reject_reason' => $request->reject_reason,
            'approved_by' => $request->approved_by,
            'status_upd_date' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Reject reason updated successfully']);
    }

// new changes
public function fetchRate(Request $request)
{
    $accessZone = $request->access_zone;
    $accessArea = $request->access_area;
    $typeOfPass = $request->type_of_pass;

    // Query the ratemaster table (adjust your query as necessary)
    // $rate = rate_master::where('access_zone', $accessZone)
    //                   ->where('access_gate', $accessArea)                      
    //                   ->where('validity', $typeOfPass)
    //                   ->where('allowed_type', $request->passType)
    //                   ->where('student_type', $request->visitorType)
    //                   ->where('vehicle_person_type', $request->persontype)
    //                   ->orwhere('vehicle_person_type', $request->vectype)
    //                   ->first();

    $person_Type = '';

    // Determine the value of $person_Type based on $request->persontype
    if ($request->persontype === '66fe76256d3a792499' || $request->persontype === '66fe76af145a071845') {
        $person_Type = '66fe741bb302a94988';
    } else {
        $person_Type = $request->persontype ?? ''; // Ensure null-safe access
    }
    
    // Fetch the rate from the database
    $rate = rate_master::where('access_zone', $accessZone)
        ->where('access_gate', $accessArea)
        ->where('allowed_type', $request->passType)
        ->where('student_type', $request->visitorType)
        ->where(function ($query) use ($person_Type, $request) {
            // Use either vehicle_person_type or vectype as a match
            $query->where('vehicle_person_type', $person_Type)
                  ->orWhere('vehicle_person_type', $request->vectype); // Ensure null-safe access for vectype
        })
        ->where('validity', $typeOfPass) // Match the validity
        ->first(); // Fetch the first matching record
    

                    //   dd($rate);
    // Return the amount as JSON
    if ($rate) {
        return response()->json(['rate' => $rate->rate]);
    } else {
        return response()->json(['amount' => 0]); // Default if no matching rate is found
    }
}

public function getData(Request $request)
{
    // Start the query
    $query = viewpassissue::where('view_passissue.is_delete', 0)
        ->select('view_passissue.unique_id', 'view_passissue.firms_agent', 'view_passissue.agent_type', 'view_passissue.entry_date', 'view_passissue.pass_id', 'view_passissue.company_name', 'view_passissue.agent_type_name','view_passissue.pass_type','view_passissue.type_of_pass','view_passissue.person_type','view_passissue.pass_type');
    // Filter by 'from_date' if provided
if (!empty($request->from_date)) {
    try {
        // Convert from 'Y-m-d' to 'd-m-Y'
        $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d-m-Y');
        $query->where('view_passissue.entry_date', '>=', $fromDate);
    } catch (\Exception $e) {
        // Handle invalid date format
        return response()->json(['error' => 'Invalid from_date format'], 400);
    }
}

// Filter by 'to_date' if provided
if (!empty($request->to_date)) {
    try {
        // Convert from 'Y-m-d' to 'd-m-Y'
        $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d-m-Y');
        $query->where('view_passissue.entry_date', '<=', $toDate);
    } catch (\Exception $e) {
        // Handle invalid date format
        return response()->json(['error' => 'Invalid to_date format'], 400);
    }
}

        
            // Filter by 'pass_id' if provided
            if (!empty($request->pass_id)) {
                $query->where('view_passissue.pass_id', $request->pass_id);
            }
        
            // Filter by 'agent_type' if provided
            if (!empty($request->agent_type)) {
                $query->where('view_passissue.agent_type', $request->agent_type);
            }
        

            
            // Filter by 'agent_type' if provided
            if (!empty($request->company_name)) {
                $query->where('view_passissue.company_name', $request->company_name);
            }
        
            // Fetch data ordered by 'unique_id'
            $firmauth = $query->orderBy('view_passissue.unique_id', 'desc')->get();
        

         

            // Return data in DataTables format
            return DataTables::of($firmauth)
                ->addIndexColumn() // Automatically adds the serial number
                ->addColumn('action', function ($firmsauth) {
                    return '
                    <button type="button" class="btn btn-primary" onclick="window.location.href=\'' . url('/payment_option') . '\'">Pay</button>
                    <a href="' . route('pass_entry.edit', ['id' => $firmsauth->unique_id]) . '">
                                <i class="fas fa-edit"></i>
                            </a>
                    
                    <a href="javascript:void(0)" onclick="deleteCenter(\'' . $firmsauth->unique_id . '\')">
                <i class="fas fa-trash-alt"></i>
            </a>';
        })
        ->make(true);
}


}
