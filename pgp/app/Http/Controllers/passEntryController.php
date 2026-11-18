<?php

namespace App\Http\Controllers;

use App\Models\AcessZone;
use App\Models\CardPassIssue;
use App\Models\CompanyCreation;
use App\Models\FirmsRegistration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\PersonType;
use App\Models\PortAreaAccess;
use App\Models\rate_master;
use App\Models\Vehiclecreation;
use App\Models\view_permitissue;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

// Add this at the top of your controller
// use Carbon\Carbon;
// use DB;

class passEntryController extends Controller
{

    public function index(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        $sublist_data = '';
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
            // dd($request->from_date);
            // $fromDate = Carbon::parse($request->from_date)->format('Y-m-d');

            // $from_date = Carbon::parse()
            $query->where('entry_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('entry_date', '<=', $toDate);
        }

        // Fetch the filtered pass entry list
        $pass_entry_list = $query->get();

        // dd($pass_entry_list);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'data' => $pass_entry_list->map(function ($item) {
                    return [
                        // 'pass_id' => $item->pass_id, // Make sure these keys match your DataTable columns
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
// dd($sublist_entries);
        // Fetch main entries related to the sublist if available
        // if ($sublist_entries->isNotEmpty()) {
        //     $sublist_entriess_main = passentrysub::where('firms_agent', $sublist_entries->first()->agent_name)
        //         ->where('is_delete', 0)
        //         ->select('pass_id', 'vec_reg_no')
        //         ->get();
        // } else {
        //     $sublist_entriess_main = collect(); // No entries found
        // }

        return view('content.pass_entry.list', compact('pass_entry_list', 'firm_registration1', 'company_name', 'personTypes', 'sublist_data', 'sublist_entries'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        return view('content.pass_entry.form');
    }

    public function show()
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        return view('content.pass_entry.form_user');
    }
    public function areaData(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // Zone A
        if ($request->access_zone == '67330c5ec0f8862014') {
            $accessArea = DB::table('port_area_access')
                ->select('unique_id', 'area_name')
                ->where('is_delete', 0)
                ->where('is_active', 1)
                ->where('unique_id', '!=', '67567ff508fbc34996')
                ->orderBy('id', 'ASC')
                ->get();
        } else if ($request->access_zone == '67330c65f3d0359804') { //Zone B
            $accessArea = DB::table('port_area_access')
                ->select('unique_id', 'area_name')
                ->where('is_delete', 0)
                ->where('is_active', 1)
                ->where('unique_id', '=', '67567ff508fbc34996')
                ->orderBy('id', 'ASC')
                ->get();
        } else { //Zone A&B
            $accessArea = DB::table('port_area_access')
                ->select('unique_id', 'area_name')
                ->where('is_delete', 0)
                ->where('is_active', 1)
                ->orderBy('id', 'ASC')
                ->get();
        }

        return response()->json(['accessArea' => $accessArea]);

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

        if ($request->vehicle_no != '') {
            $person_vehData = $request->vehicle_no;
        } else if ($request->equ_reg_no != '') {
            $person_vehData = $request->equ_reg_no;
        } else {
            $person_vehData = $request->personData;
        }

        if ($request->persontype != '') {
            $personVehtype = $request->persontype;
        } else {
            $personVehtype = $request->vectype;
        }

        if ($request->person_adhaar != '') {
            $person_driver = $request->person_adhaar;
        } else {
            $person_driver = $request->driver_license;
        }

        if($request->vehicle_category == '67695433bbf8746249'){
            $approval_status = '0';
            $approval_by = '';

        }else{
            if ($request->type_of_pass == 'monthly' || $request->type_of_pass == 'halfyearly' || $request->type_of_pass == 'yearly' || $request->type_of_pass == 'day' && $request->no_of_days > 7) {
                // alert('hi');
                $approval_status = '0';
                $approval_by = '';
            } else if ($request->type_of_pass == 'day' && $request->no_of_days <= 7) {
                // alert('hlo');
                $approval_status = '1';
                $approval_by = session('user_id');
            }
        }

    //    dd($approval_status);

        $toDate = $request->to_date;

        // dd($request->to_date);die();
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

        // Check if there are existing records with the same screen_unique_id
        $validpass = passentrysub::where('pass_valid_status', 0)
            ->where('rfid_hep_no', $request->rfid_hep_no)
            ->where('is_delete', 0)
            ->first();

        if ($validpass) {
            return response()->json([
                'status' => 400,
                'title' => 'Expired',
                'message' => 'This RFID HEP Number is already in use and valid.',
            ], 400);
        }

// dd($request);

         if($request->persontype == '66fe741bb302a94988'){ //Person
            $personSts = '1';
        }elseif($request->persontype == '66fe76256d3a792499'){ //Driver
            $personSts = '2';
        }elseif($request->persontype == '66fe76af145a071845'){ //Cleaner
            $personSts = '3';
        }elseif($request->vectype == '6711f165a22bd51504'){ // Heavy Vehicle
            $personSts = '4'; 
        }elseif($request->vectype == '6711f18659aad63646'){ //Equipment Vehicle
            $personSts = '5'; 
        }elseif($request->vectype == '6711f16e0ac5459691'){ //Light
            $personSts = '6';
        }elseif($request->vectype == '6748102b51e0f44601'){ //Two Wheeler
            $personSts = '7';
        }elseif($request->vectype == '676951171db2c21573'){ // Machinery&Tools
            $personSts = '8'; 
        }
        


        if($request->person_unique){
            $per_veh_unique_id = $request->person_unique;
        }else{
            $per_veh_unique_id = $request->veh_unique;
        }

        $toDateVal = new \DateTime($request->to_date);
        $fromDateVal = new \DateTime($request->from_date);
        // $deactiveDateVal = new \DateTime($request->deactive_date);


        $formattedfromDate = $fromDateVal->format('Y-m-d H:i');

        // Format the date as desired, for example: "Y-m-d H:i"
        $formattedToDate = $toDateVal->format('Y-m-d H:i');
        // $deactivateDate = $deactiveDateVal->format('Y-m-d H:i');

        // dd($formattedToDate);die();


        $pass_sublist = passentrysub::create([
            'unique_id' => unique_id(),
            'screen_unique_id' => $request->screen_unique_id,
            'agent_name' => $request->firms_agent,
            'agent_type' => $request->firms_agent_type,
            'type_of_pass' => $request->type_of_pass,
            'no_of_days' => $request->no_of_days,
            'from_date' => $formattedfromDate,
            'to_date' => $formattedToDate,
            'sgst' => $request->sgst,
            'cgst' => $request->cgst,
            'amount' => $request->amount,
            'gross_amount' => $request->gross_amount,
            'access_area' => $request->access_area,
            'access_zone' => $request->access_zone,
            'rfid_hep_no' => $request->rfid_hep_no,
            'tax_amount' => $request->tax_amount,
            'pass_valid_status' => $request->pass_valid_status ?? '0',
            'approval_status' => $approval_status, 
            'approved_by' => $approval_by,            
            'net_amount' => $request->net_amount,
            // 'deactive_date' => $deactivateDate,
            // 'entry_date' => Carbon::today()->format('Y-m-d H:i'),
            'pass_id' => $this->generateUniqueId(),
            // 'pass_id' => $request->hidden_pass_id,
            'pass_type' => $request->passType,
            'per_veh_unique_id' => $per_veh_unique_id,
            'person_vehicle' => $person_vehData,
            'agent_sortname' => $request->agent_sortname,
            'adhaar_license' => $person_driver,
            'person_veh_type' => $personVehtype,
            'veh_category' => $request->vehicle_category,
            'passTypeSts' => $personSts

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

        $firms_zone_name = AcessZone::where('unique_id', $pass_sublist->access_zone)
            ->value('zone_type');
        // Retrieve the agent name

        $firms_area_name = PortAreaAccess::where('unique_id', $pass_sublist->access_area)
            ->value('area_name');
        // Retrieve the agent name

        $pass_id = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->value('pass_id');

        $gross_amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0)
            ->sum('gross_amount');

        // NEW TABLE CARD PASS ISSUE
        // $uniqueId = uniqid();
        $cardpassissue = CardPassIssue::create([
            'unique_id' => unique_id(),
            // 'screen_unique_id' => $request->screen_unique_id,
            'pass_id' => $request->pass_id,
            'tax_amount' => $request->tax_amount,
            'status' => $request->pass_valid_status ?? '0', // Default to '0' if not provided
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
            'total_pass_amount' => $tax_amount,
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
            'pass_id' => $pass_sublist->pass_id,
            'pass_type' => $pass_sublist->pass_type,
            'gross_amount' => $gross_amount,
            'person_vehicle' => $pass_sublist->person_vehicle,

            // 'sublist' => $sublist,

        ]);

        //  }
    }

    public function sublistDatatable(Request $request)
    {

        // Validate the request input
        $request->validate([
            'screen_unique_id' => 'required|string',
        ]);

        // Retrieve the `screen_unique_id` from the request
        $screenUniqueId = $request->input('screen_unique_id');

        // Retrieve the sublist data
        $sublist = DB::table('pass_entry_sublist')
            ->select(
                'pass_entry_sublist.pass_id',
                'pass_entry_sublist.screen_unique_id',
                'pass_entry_sublist.pass_type',
                'pass_entry_sublist.person_vehicle',
                'pass_entry_sublist.no_of_days',
                'pass_entry_sublist.type_of_pass',
                'pass_entry_sublist.from_date',
                'pass_entry_sublist.to_date',
                'pass_entry_sublist.gross_amount',
                'pass_entry_sublist.unique_id',
                'port_area_access.area_name as areaname',
                'access_zone.zone_type',
                'pass_entry_sublist.agent_type',
                'pass_entry_sublist.agent_name'
            )
            ->leftJoin('access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id')
            ->leftJoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->where('pass_entry_sublist.screen_unique_id', $screenUniqueId)
            ->get();

        // Retrieve agent details
        $getAgentData = DB::table('pass_entry_sublist')
            ->select('agent_type', 'agent_name')
            ->where('screen_unique_id', $screenUniqueId)
            ->first();

        // Return the data as a JSON response
        return response()->json([
            'sublist' => $sublist,
            'agentdetail' => $getAgentData,
        ]);
    }

    public function deleteSublist(Request $request, $unique_id, $screen_id)
    {
        $deleted = DB::table('pass_entry_sublist')->where('unique_id', $unique_id)->delete();

        $sublist = DB::table('pass_entry_sublist')
            ->select(
                'pass_entry_sublist.pass_id',
                'pass_entry_sublist.screen_unique_id',
                'pass_entry_sublist.pass_type',
                'pass_entry_sublist.person_vehicle',
                'pass_entry_sublist.no_of_days',
                'pass_entry_sublist.type_of_pass',
                'pass_entry_sublist.from_date',
                'pass_entry_sublist.to_date',
                'pass_entry_sublist.gross_amount',
                'pass_entry_sublist.unique_id',
                'port_area_access.area_name as areaname',
                'access_zone.zone_type'

            )
            ->leftJoin('access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id')
            ->leftJoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->where('pass_entry_sublist.screen_unique_id', $screen_id)
            ->get();

        return response()->json([
            'success' => $deleted > 0,
            'sublist' => $sublist,
        ]);

    }

    public function store(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // Validate the incoming request data
        $request->validate([
            'unique_id' => 'max:255',
            'screen_unique_id' => 'required|max:255',
            'issue_center' => 'required|max:255',
            // 'firms_agent' => 'required|max:255',
            'firms_agent_type' => 'required|max:255',
            'passType' => 'max:255',
            'vectype' => 'max:255',
            'persontype' => 'max:255',
            'vec_reg_no' => 'max:255',
            'driving_license' => 'max:255',
            // 'access_area' => 'max:255',
            'adhaar_no' => 'max:255',
            'vech_no' => 'max:255',
            //'pass_weight' => 'max:255',
            'pass_amount' => 'max:255',
            // 'cover_letter' => 'required|file|mimes:jpg, jpeg, png, doc, docx, pdf|max:2048',
            // 'cover_letter' => 'required|file|max:2048',
            // 'pass_id' => 'max:255',
            'acadamic_year' => 'max:255',
            'pass_status' => 'max:255',
            'net_amount' => 'max:255',
            'rfid' => 'max:255',
            'student_type' => 'max:255',

        ]);

        // Initialize the variable for the cover letter filename
        $cover_letter_name = null;

        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move('uploads/pass_entry', $cover_letter_name);

            //     $vehicleImage->move(public_path('uploads/vehicle_registration'), $imageName);

            // $cover_letter->move(custom_public_path('uploads/pass_entry'), $cover_letter_name);

            // Debugging line
            // Log::info( 'Cover letter uploaded: ' . $cover_letter_name );
        }

        $getStatus = passentrysub::where('screen_unique_id', $request->screen_unique_id)
        ->where('is_delete', 0)
            ->select('type_of_pass','no_of_days')->get();

            $approvalStatus = '';
            $approvalStatusDay = '';
            $approvalStatusDaymore = '';



            foreach ($getStatus as $entry) {
                if ($entry->type_of_pass === 'monthly') {
                    $approvalStatus = '0'; // Default example
                } elseif ($entry->type_of_pass === 'halfyearly') {
                    $approvalStatus = '0'; // Default example
                } elseif ($entry->type_of_pass === 'yearly') {
                    $approvalStatus = '0'; // Default example
                }
                
                if ($entry->type_of_pass === 'day' && $entry->no_of_days < 7) {
                    $approvalStatusDay = '1';
                }

                if ($entry->type_of_pass === 'day' && $entry->no_of_days > 7) {
                    $approvalStatusDaymore = '0';
                }

            }

            if($approvalStatus == '0'){
                $statusApprove = '0';
            $approval_by = '';

            }else if($approvalStatusDaymore == '0'){
                $statusApprove = $approvalStatusDaymore;
            $approval_by = '';

            }else{
                $statusApprove = $approvalStatusDay;
            $approval_by = session('user_id');

            }

            // dd($statusApprove);

        // Create the person registration record
        $pass_entry_main = passentrymainlist::create([
            'unique_id' => unique_id(),
            'group_id' => $this->groupUnique_id(),
            'screen_unique_id' => $request->screen_unique_id,
            'issue_center' => $request->issue_center,
            'firms_agent' => $request->firms_agent,
            'pass_amount' => $request->pass_amount,
            'cover_letter' => $cover_letter_name,
            'pass_status' => $request->pass_status,
            'acadamic_year' => $request->acadamic_year,
            'agent_type' => $request->firms_agent_type,
            'net_amount' => $request->net_amount,
            'rfid' => $request->rfid_value,
            'prm_sec' => $request->secondary_input,
            'approval_status'=> $statusApprove,
            'approved_by'=> $approval_by,
            'rfid' => $request->rfid_hep_no,
            'tax_amount' => $request->tax_amount, // for total gross amount
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

    public function groupUnique_id()
    {
        // Get current year and month

        $yearMonth = date('Ym');
        $prefix = "GP{$yearMonth}";

        // Retrieve the last inserted record with a similar prefix
        $lastRecord = passentrymainlist::where('group_id', 'LIKE', "{$prefix}%")->orderBy('id', 'desc')->first();

        if (!$lastRecord) {
            // If no record exists, start with 00001
            $newNumber = '00001';
        } else {
            // Extract the numeric part and increment by 1
            $lastNumber = intval(substr($lastRecord->group_id, strlen($prefix))); // Extract after the prefix
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT); // Pad to 5 digits
        }

        // Combine the prefix with the new number
        return $prefix . $newNumber;
    }

    public function update(Request $request, $id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // dd( $id);
        // Validate the incoming request data
        $request->validate([
            'issue_center' => 'required|max:255',
            'pass_id' => 'required',
            // 'firms_agent' => 'max:255',
            'passType' => 'max:255',
            'vectype' => 'max:255',
            'persontype' => 'max:255',
            'vec_reg_no' => 'max:255',
            'driving_license' => 'max:255',
            //'access_area' => 'max:255',
            'adhaar_no' => 'max:255',
            'vech_no' => 'max:255',
            //'pass_weight' => 'max:255',
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
        // Find the firm by ID or fail
        $cover_letter_name = $passentrymainlists->cover_letter;
        // Default to existing filename
        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move('uploads/pass_entry', $cover_letter_name);

            // $cover_letter->move(custom_public_path('uploads/pass_entry'), $cover_letter_name);
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
            // 'firms_agent' => $request->firms_agent,
            'pass_type' => $request->passType,
            'vectype' => $request->vectype,
            'vech_categ' => $request->vehicle_category,
            'persontype' => $request->persontype,
            'vec_reg_no' => $request->vec_reg_no,
            'driving_license' => $request->driving_license,
            //'access_area' => $request->access_area,
            'adhaar_no' => $request->adhaar_no,
            'vech_no' => $request->vech_no,
            //  'pass_amount' => $request->amount,
            //'pass_weight' => $request->pass_weight,
            'cover_letter' => $cover_letter_name, // Save the filename in the database
            'pass_valid_status' => $request->pass_valid_status ?? '0',
            'acadamic_year' => $request->acadamic_year,
            // 'agent_type' => 'max:255',
            'pass_id' => $request->pass_id,
            // 'agent_type' => $request->firms_agent_type,
            'net_amount' => $request->net_amount,
            'rfid' => $request->rfid_value,
            'prm_sec' => $request->prm_sec,
            'pass_amount' => $request->pass_main_amount,
            'rfid' => $request->rfid_hep_no,
            'chase_no' => $request->equ_reg_no,
            'tax_amount' => $request->with_tax_amount,
            'student_type' => $request->visitorType,
            //  'entry_date' => Carbon::today()->format('d-m-Y')
        ]);
        // dd( $passentrymainlists);
        $passentrysub = passentrysub::where('screen_unique_id', $passentrymainlists->screen_unique_id); // Find the firm by ID or fail
        // dd($passentrymainlists);
        return response()->json(['message' => 'Pass Entry updated successfully', 'passentrymainlists' => $passentrymainlists, 'passentrysub' => $passentrysub]);
    }

    public function edit($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // Fetch the main entry
        $passentrymain_list = passentrymainlist::where('screen_unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        // Check if the main entry exists
        if (!$passentrymain_list) {
            return redirect()->route('pass_entry')->with('error', 'Pass Entry not found.');
        }

        $firm_registration_data = FirmsRegistration::where('is_delete', 0)->where('unique_id', $passentrymain_list->firms_agent)
            ->get('name', 'address');

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
        $totalGross = $sublist_entries->sum('gross_amount');
        // $totalTaxAmount = $sublist_entries->sum('tax_amount');

        // Calculate the total gross_amount for the sublist entries

        $main_list = passentrymainlist::select('*', 'firms_agent as firms_agent_ids', 'agent_type as agent_type_ids')
            ->where('screen_unique_id', $passentrymain_list->screen_unique_id)
            // ->where('pass_type', $passentrymain_list->pass_type)
            ->where('pass_amount', $passentrymain_list->pass_amount)
            ->where('is_delete', 0)
            ->first();

            // dd($passentrymain_list);
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
            'firm_registration_data',
            'totalGross',
            // 'totalTaxAmount',

        ));
    }

    public function getLastFirmNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Assuming you have a Firm model set up
        $lastFirm = passentrymainlist::where('group_id', 'like', "GP$year$month%")
            ->orderBy('group_id', 'desc')
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
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // Find the record using unique_id
        $passentrymainlist_id = passentrymainlist::where('screen_unique_id', $id)->first(); // Use first() to retrieve the record
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
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
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
            $newNumber = '00001';
        } else {
            // Extract the numeric part and increment by 1
            $lastNumber = intval(substr($lastRecord->pass_id, strlen($prefix))); // Extract after the prefix
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT); // Pad to 5 digits
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
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        // Validate the incoming request data
        $request->validate([
            // 'extend_pass_type' => 'required|string',
            // 'extend_period' => 'required|integer',
            // 'extend_to_date' => 'required|date',
            // 'extend_file_upload' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048', // Removed extra space
            // 'extend_tax_amount' => 'required|numeric',
        ]);
        $pass = passentrysub::where('pass_id', $id)
            ->where('is_delete', 0)
            ->first();

        if ($request->hasFile('extend_file_upload')) {
            $extend_file_upload = $request->file('extend_file_upload');
            $extend_file_name = time() . '-' . $extend_file_upload->getClientOriginalName();
            $extend_file_upload->move('uploads/pass_entry/', $extend_file_name);
        } else {
            // If no new file, retain the existing file
            $extend_file_name = $pass->extend_file_upload;
        }
        if ($request->extend_period == '') { // Use '==' or '===', not '='
            $extend = $request->extend_period_value;
        } else {
            $extend = $request->extend_period;
        }
        // Default value
        $extend_approval_status= $request->extend_approval_status;
        // $
        if($request->extend_period > 7) {
            $extendapproval = '0';
        }
        else if($extend_approval_status!=''){

            $extendapproval = $request->extend_approval_status;
        }
        else{
            $extendapproval=""; 
        }
    //  $extendapproval= ($request->extend_period >7)
    //  {$approval='0'}
        if ($pass) {
            // Update the pass details
            $pass->update([
                'extend_pass_type' => $request->extend_pass_type,
                'extend_period' => $extend,
                'extend_to_date' => $request->extend_to_date,
                'extend_tax_amount' => $request->extend_tax_amount,
                'extend_gross_amount' => $request->extend_gross_amount,
                'extend_net_amount' => $request->extend_net_amount,
                'extend_sgst' => $request->extend_sgst,
                'extend_cgst' => $request->extend_cgst,
                // 'extend_file_upload' => $extend_file_upload
                'extend_file_upload' => $extend_file_name, // Save the new file name
                'extend_entry_date' => Carbon::today()->format('Y-m-d H:i'),
                'extend_approval_status' => $extendapproval,
                // attached_proof

            ]);
// dd( $pass);
            return response()->json(['success' => true, 'message' => 'Pass updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }
    }

    public function sublistindex1(Request $request)
    {
        // Retrieve the sublist data;
        $sublist = passentrysub::where('is_delete', 0)
            ->where('pass_id', $request->permitidInput)
            ->orWhere('person_vehicle', $request->permitidInput)
            ->orWhere('adhaar_license', $request->permitidInput)
            ->get();
// dd($sublist);
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

        $person_Type = '';

        // Determine the value of $person_Type based on $request->persontype
        if ($request->persontype === '66fe76256d3a792499' || $request->persontype === '66fe76af145a071845') {
            $person_Type = '66fe741bb302a94988';
        } else {
            $person_Type = $request->persontype ?? ''; // Ensure null-safe access
        }

        // Fetch the rate from the database
        if ($request->visitorType) {
            $rate = rate_master::where('access_zone', $accessZone)
                ->where('access_gate', $accessArea)
                ->where('allowed_type', $request->passType)
                ->where('vehicle_person_type', $person_Type)
                ->where('student_type', $request->visitorType)
                ->where('validity', $typeOfPass) // Match the validity
                ->first(); // Fetch the first matching record
        } else {
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
        }

        //   dd($rate);
        // Return the amount as JSON
        if ($rate) {
            return response()->json(['rate' => $rate->rate]);
        } else {
            return response()->json(['amount' => 0]); // Default if no matching rate is found
        }
    }

    public function extendfetchRate(Request $request)
    {
        $accessZone = $request->access_zone;
        $accessArea = $request->access_area;
        $typeOfPass = $request->type_of_pass;
    
        // Determine the value of $person_Type
        $person_Type = ($request->persontype === '66fe76256d3a792499' || $request->persontype === '66fe76af145a071845')
            ? '66fe741bb302a94988'
            : ($request->persontype ?? '');
    
        // Check if visitorType exists, otherwise set to NULL
        // $visitorType = $request->has('visitorType') ? $request->visitorType : null;
    
        // Fetch the rate from the database
        $rate = rate_master::where('access_zone', $accessZone)
            ->where('access_gate', $accessArea)
            ->where('allowed_type', $request->passType)
            ->where('validity', $typeOfPass)
            // ->where(function ($query) use ($visitorType) {
            //     if (!empty($visitorType)) {
            //         $query->where('student_type', $visitorType);
            //     } else {
            //         $query->whereNull('student_type')->orWhere('student_type', '');
            //     }
            // });
            ->where(function ($query) use ($person_Type, $request) {
                $query->where('vehicle_person_type', $person_Type)
                    ->orWhere('vehicle_person_type', $request->vectype);
            })
            ->first();
    
        // Return response
        return response()->json(['rate' => $rate->rate ?? 0]);
    }
    
    

    public function getData(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        
//         $query = DB::table('view_permitissue')
//     ->leftJoin('company_creation', 'view_permitissue.agent_type', '=', 'company_creation.unique_id')
//     ->leftJoin('pass_entry_sublist', 'view_permitissue.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
//     ->select(
//         'view_permitissue.group_id',
//         'view_permitissue.screen_unique_id',
//         'view_permitissue.agent_type',
//         'view_permitissue.entry_date',
//         'view_permitissue.company_name',
//         'view_permitissue.payment_status',
//         'view_permitissue.main_approval_status',       
//         'view_permitissue.short_name',
//         DB::raw("MAX(DATE_FORMAT(pass_entry_sublist.from_date, '%d-%m-%Y %H:%i:%s')) as from_pass_date_time"), // Aggregated column
//         DB::raw("(SELECT SUM(psub.sgst) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as sgst_amount"),
//         DB::raw("(SELECT SUM(psub.cgst) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as cgst_amount"), // Corrected SUM
//         DB::raw("(SELECT SUM(psub.gross_amount) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as tax_amount"), // Corrected SUM
//         DB::raw("(SELECT COUNT(*) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as total_pass_entries")
//     )->groupBy(
//         'view_permitissue.group_id',
//         'view_permitissue.screen_unique_id',
//         'view_permitissue.agent_type',
//         'view_permitissue.entry_date',
//         'view_permitissue.company_name',
//         'view_permitissue.payment_status',
//         'view_permitissue.main_approval_status',       
//         'view_permitissue.short_name'
//     );


// if ($request->from_date) {
//     try {
//         // Parse and format the date as d-m-Y
//         $formattedDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d-m-Y');

//         // Now you can use the formatted date wherever needed
//         // For example, you can apply the formatted date in your query
//         $fromDate = Carbon::createFromFormat('d-m-Y', $formattedDate)->startOfDay();
//         // $query->where('view_permitissue.entry_date', '>=', $fromDate);
//         $query->whereRaw("DATE_FORMAT(view_permitissue.entry_date, '%d-%m-%Y') >= ?", $fromDate);


//     } catch (\Exception $e) {
//         return response()->json(['error' => 'Invalid from_date format'], 400);
//     }
// }


// // Filter by to_date (if you want to change the format for to_date too)
// if ($request->to_date) {
//     try {
//         $formattedDate1 = Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d-m-Y');
//         // Parse the 'to_date' in 'd-m-Y' format and set to the end of the day
//         $toDate = Carbon::createFromFormat('d-m-Y', $formattedDate1)->endOfDay();
        
//         // Apply the filter to the query
//         // $query->where('view_permitissue.entry_date', '<=', $formattedDate1);
//         $query->whereRaw("DATE_FORMAT(view_permitissue.entry_date, '%d-%m-%Y') <= ?", $formattedDate1);

//     } catch (\Exception $e) {
//         return response()->json(['error' => 'Invalid to_date format'], 400);
//     }
// }
// // Filter by agent_type
// if ($request->agent_type) {
//     $query->where('view_permitissue.agent_type', $request->agent_type);
// }

// // Filter by company_name
// if ($request->company_name) {
//     $query->where('view_permitissue.company_name', $request->company_name);
// }

// $firmauth = $query->get();


$query = DB::table('view_permitissue')
    ->leftJoin('company_creation', 'view_permitissue.agent_type', '=', 'company_creation.unique_id')
    ->leftJoin('pass_entry_sublist', 'view_permitissue.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
    ->select(
        'view_permitissue.group_id',
        'view_permitissue.screen_unique_id',
        'view_permitissue.agent_type',
        DB::raw("DATE_FORMAT(view_permitissue.entry_date,'%d-%m-%Y')as entry_date"),

        'view_permitissue.company_name',
        'view_permitissue.payment_status',
        'view_permitissue.main_approval_status',       
        'view_permitissue.short_name',
        DB::raw("MAX(DATE_FORMAT(pass_entry_sublist.from_date, '%d-%m-%Y %H:%i:%s')) as from_pass_date_time"),
        DB::raw("(SELECT SUM(psub.sgst) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as sgst_amount"),
        DB::raw("(SELECT SUM(psub.cgst) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as cgst_amount"),
        DB::raw("(SELECT SUM(psub.gross_amount) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as tax_amount"),
        DB::raw("(SELECT COUNT(*) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) as total_pass_entries")
    )->groupBy(
        'view_permitissue.group_id',
        'view_permitissue.screen_unique_id',
        'view_permitissue.agent_type',
        'view_permitissue.entry_date',
        'view_permitissue.company_name',
        'view_permitissue.payment_status',
        'view_permitissue.main_approval_status',       
        'view_permitissue.short_name'
    );

// Filter by from_date
if ($request->from_date) {

    $query->where('view_permitissue.entry_date','>=',$request->from_date);

    // $query->whereRaw("DATE_FORMAT(view_permitissue.entry_date,'%Y-%m-%d') >= ? ", $request->from_date);
    // try {
    //     $query->whereRaw("DATE_FORMAT(view_permitissue.entry_date, '%d-%m-%Y') >= ?", $request->fromDate);
    // } catch (\Exception $e) {
    //     return response()->json(['error' => 'Invalid from_date format'], 400);
    // }
}

// Filter by to_date
if ($request->to_date) 
{
    try {


        $to_date = Carbon::parse($request->to_date)->endOfDay();

        $query->where('view_permitissue.entry_date','<=',$to_date);
        // $to_date = Carbon::createFromFormat('d-m-Y',$request->to_date)->endOfDay()->format('Y-m-d');

        // $query->whereRaw("DATE_FORMAT(view_permitissue.entry_date, '%Y-%m-%d') <= ?", [$to_date]);

       
        // $query->DB::raw("DATE_FORMAT(view_permitissue.entry_date, '%Y-%m-%d') <= ?", $request->to_date)->endOfDay();

    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid to_date format'], 400);
    }
}

// Filter by agent_type
if (!empty($request->agent_type)) {
    $query->where('view_permitissue.agent_type', $request->agent_type);
}

// Filter by company_name
if (!empty($request->company_name)) {
    $query->where('view_permitissue.company_name', $request->company_name);
}

// Fetch and order the results
$firmauth = $query->orderBy('view_permitissue.screen_unique_id', 'desc')->get();
 // $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d');
        
        // $query->where('view_permitissue.entry_date', '<=', $toDate);

// dd($firmauth);

        // Return data in DataTables format
        return DataTables::of($firmauth)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('payment', function ($firmsauth) {
                // Check the payment_status and return the appropriate content
                if ($firmsauth->payment_status == 0 && $firmsauth->main_approval_status == 1) {
                    return '<button type="button" class="btn btn-primary" onclick="window.location.href=\'' . url('/payment_option') . '?passId=' . $firmsauth->screen_unique_id . '\'">Pay</button>
                    ';

                    // Display the "Pay" button if payment_status is 0
                    // return '<button type="button" class="btn btn-primary" onclick="window.location.href=\'' . url('/payment_option') . '\'">Pay</button>';
                }elseif($firmsauth->payment_status == 0 && $firmsauth->main_approval_status == 0){
                    return '<span style="font-weight: bold; color: red;">Approval Pending</span>';
                }elseif($firmsauth->payment_status == 0 && $firmsauth->main_approval_status == 2){
                    return '<span style="font-weight: bold; color: red;">Approval Rejected</span>';
                }elseif($firmsauth->payment_status == 1 && $firmsauth->main_approval_status == 1) {
                    // Display "Paid" in bold and green if payment_status is 1
                    return '
                     <a href="javascript:void(0)" onclick="paymentData(\'' . $firmsauth->screen_unique_id . '\')">
                        <span style="font-weight: bold; color: green;">Paid</span>
                    </a>
                     </a> <button type="button" onclick="window.location.href=\'' . route('permitprint.data', ['screen_unique_id' => $firmsauth->screen_unique_id]) . '\';" style="border:none">
            <i class="fa fa-print"></i>
        </button>';
                }
            })
            ->addColumn('action', function ($firmsauth) {
                if($firmsauth->payment_status == 1 && $firmsauth->main_approval_status == 1){
                    return '
                    <a href="javascript:void(0)" onclick="popup(\'' . $firmsauth->screen_unique_id . '\')">
                        <i class="fas fa-eye"></i>
                    </a>
                       </a> <button type="button" onclick="window.location.href=\'' . route('permitprint.data', ['screen_unique_id' => $firmsauth->screen_unique_id]) . '\';" style="border:none">
            <i class="fa fa-print"></i>
        </button>';
                }else{
                return '
                    <a href="' . route('pass_entry.edit', ['id' => $firmsauth->screen_unique_id]) . '">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <a href="javascript:void(0)" onclick="deleteCenter(\'' . $firmsauth->screen_unique_id . '\')">
                        <i class="fas fa-trash-alt"></i>
                    </a>

                    <a href="javascript:void(0)" onclick="popup(\'' . $firmsauth->screen_unique_id . '\')">
                        <i class="fas fa-eye"></i>
                   ';
                }
            })
            ->rawColumns(['payment', 'action'])  // Ensure raw HTML is rendered for 'payment' and 'action' columns
            ->make(true);
    }
    
    public function showPopup($id)
    {
        // Retrieve data based on screen_unique_id
        $passMain = PassEntryMainList::where('screen_unique_id', $id)
            ->leftjoin('company_creation', 'pass_entry_main_list.agent_type', '=', 'company_creation.unique_id')
            ->leftjoin('firms_registration', 'pass_entry_main_list.firms_agent', '=', 'firms_registration.unique_id')
            ->leftjoin('centers', 'pass_entry_main_list.issue_center', '=', 'centers.unique_id')
            ->where('pass_entry_main_list.is_delete', '=', 0)
            ->select(
                'pass_entry_main_list.*',
                'firms_registration.name as firm_name',
                'company_creation.company_name as agent_name',
                'centers.center_name as issue_center_name'
            )
            ->first();

        // Check if data exists, if not return an error message
        if (!$passMain) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $passSub = passentrysub::where('pass_entry_sublist.screen_unique_id', $id)
            ->leftjoin('company_creation', 'pass_entry_sublist.agent_type', '=', 'company_creation.unique_id')
            ->leftjoin('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
            ->leftjoin('access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id')
            ->leftjoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->where('pass_entry_sublist.is_delete', '=', 0)
            ->select(
                'pass_entry_sublist.*',
                'firms_registration.name as firm_name',
                'company_creation.company_name as agent_type_name',
                'access_zone.zone_type as zone_name',
                'port_area_access.area_name as area_name'
            )
            ->get();

        // Check if data exists, if not return an error message
        if (!$passSub) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // Return the data as a JSON response
        return response()->json(['passMain' => $passMain, 'passSub' => $passSub]);
    }

    public function showPaymentModel($id)
    {
        // Retrieve data based on screen_unique_id
        $passMain = PassEntryMainList::where('screen_unique_id', $id)
            ->select(
                'pass_entry_main_list.payment_status',
                'pass_entry_main_list.payment_mode',
                'pass_entry_main_list.pay_currency',
                'pass_entry_main_list.rr_id',
                'pass_entry_main_list.payment_date',
                'pass_entry_main_list.tax_amount',
                'pass_entry_main_list.group_id'
            )
            ->first();

    
        // Return the data as a JSON response
        return response()->json(['passMain' => $passMain]);
    }


    public function printPass(Request $request)
{
    $screenUniqueId = $request->screenUniqueId;
    
    // Fetch the permit issue details
    $passEntry = view_permitissue::where('screen_unique_id', $screenUniqueId)->get();

    if (!$passEntry) {
        return response()->json(['error' => 'No record found'], 404);
    }

    return response()->json(['passEntry' => $passEntry]);
}
// new changes 04-02-2025

public function printPass_data($screen_unique_id)
{
    // Fetch the specific record
    $passEntry = view_permitissue::where('screen_unique_id', $screen_unique_id)->first();

    // Fetch related records
    $passEntrymodal = view_permitissue::where('screen_unique_id', $screen_unique_id)->get();
// dd(  $passEntrymodal);
    // Initialize totals
    $totalsubamount = 0;
    $totalAmount = 0;

    // Calculate totals
    foreach ($passEntrymodal as $pass_entrylist) {
        $amount = (float) $pass_entrylist->amount;
        $noOfDays = (int) $pass_entrylist->no_of_days;
        $grossAmount = $amount * $noOfDays;
        $totalsubamount += $grossAmount;

        $total_amount = (float) $pass_entrylist->gross_amount;
        $totalAmount += $total_amount;
    }
// dd( $totalAmount);
    // Pass data to the view
    return view('content.pass_entry.model', compact('passEntry', 'passEntrymodal', 'totalsubamount', 'totalAmount'));
}




}