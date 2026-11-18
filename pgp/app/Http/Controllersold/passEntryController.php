<?php

namespace App\Http\Controllers;

use App\Models\CompanyCreation;
use App\Models\FirmsRegistration;
use App\Models\passentrymainlist;
use App\Models\passentrysub;
use App\Models\PersonType;
use App\Models\Center;
use App\Models\Vehiclecreation;
use Illuminate\Http\Request;

class passEntryController extends Controller
{

    public function index()
    {
        // Fetch all centers from the database
        $pass_entry_list = passentrymainlist::where('is_delete', 0)->get();

        $firm_registration1 = FirmsRegistration::where('is_delete', 0)
            ->pluck('name', 'unique_id');

        $personTypes = PersonType::where('is_delete', 0)
            ->pluck('designation', 'unique_id');


        // This gives you a collection with unique_id as the key and person_type as the value

        // Prepare an array to hold sublist counts
        foreach ($pass_entry_list as $passentrymain_list) {
            $sublist_count = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
                ->where('is_delete', 0)
                ->count();

            // Attach the count to the main entry
            $passentrymain_list->sublist_count = $sublist_count;


        }

        $passentrymain_lists = passentrymainlist::where('is_delete', 0)
            ->where('is_delete', 0)
            ->first();

        if ($passentrymain_lists) {
            $sublist_entries = passentrysub::where('agent_name', $passentrymain_lists->firms_agent)
                ->where('is_delete', 0)
                ->get();
        } else {
            $sublist_entries = collect();
            // or handle as needed ( e.g., log the error or set a default )
        }

        // $sublist_entriess_main = passentrymainlist::where( 'firms_agent', $sublist_entriess->agent_name )
        // ->where( 'is_delete', 0 )
        // ->get();
        if ($sublist_entries->isNotEmpty()) {
            $sublist_entriess_main = passentrymainlist::where('firms_agent', $sublist_entries->first()->agent_name)
                ->where('is_delete', 0)
                ->select('pass_id', 'vec_reg_no') // Select the required fields
                ->get();
        } else {
            $sublist_entriess_main = collect();
            // Handle as needed
        }


        $Center_name = Center::where('unique_id', $passentrymain_lists->issue_center)->where('is_delete', 0)
            ->first();

        // dd( $sublist_entriess_main );

        return view('content.pass_entry.list', compact('pass_entry_list', 'firm_registration1', 'personTypes', 'sublist_entries', 'sublist_entriess_main', 'Center_name'));
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

        ]);
        // Generate screen_unique_id
// $screenUniqueId = generateScreenUniqueId();

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

        ]);

        // Calculate the total pass_amnt for the same screen_unique_id
        $amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->where('is_delete', 0) // Ensure only non-deleted entries are included
            ->sum('amount');

        $firms_agent_name = FirmsRegistration::where('unique_id', $request->firms_agent)
            ->value('name'); // Retrieve the agent name

        return response()->json([
            'message' => 'Sublist created successfully',
            'pass_sublist' => $pass_sublist,
            'unique_id' => $pass_sublist->unique_id,
            'screen_unique_id' => $pass_sublist->screen_unique_id,
            'amount' => $amount,
            'firms_agent_name' => $firms_agent_name,
            // 'sublist' => $sublist,

        ]);
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
            'rfid_value' => 'max:255',

        ]);

        // Initialize the variable for the cover letter filename
        $cover_letter_name = null;

        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move(public_path('uploads/pass_entry'), $cover_letter_name);

            // Debugging line
            // Log::info('Cover letter uploaded: ' . $cover_letter_name);
        }
        $net_amount = passentrysub::where('screen_unique_id', $request->screen_unique_id)
            ->sum('gross_amount');

        // Create the person registration record
        $pass_entry_main = passentrymainlist::create([
            'unique_id' => unique_id(),
            'screen_unique_id' => $request->screen_unique_id,
            'issue_center' => $request->issue_center,
            'firms_agent' => $request->firms_agent,
            'pass_type' => $request->passType,
            'vectype' => $request->vectype,
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
            'net_amount' => $net_amount,
            'rfid' => $request->rfid_value,

        ]);

        // dd($pass_entry_main);
        // Return a success response
        return response()->json([
            'message' => 'Person registered successfully',
            'pass_entry_main' => $pass_entry_main,
            'screen_unique_id' => $pass_entry_main->screen_unique_id,
            'pass_id' => $pass_entry_main->pass_id,
        ]);
    }

    // new sublist

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     $designation = passentrymainlist::findOrFail($id);
    //     return view('content.pass_entry.edit', compact('designation'));
    // }
    public function edit($id)
    {
        // Fetch the main entry
        $passentrymain_list = passentrymainlist::where('id', $id)
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
            ->get(['unique_id', 'designation']);

        // Fetch all firm agent type
        $company_creation = CompanyCreation::where('is_delete', 0)
            ->get(['unique_id', 'company_name']);
        // dd(  $company_creation);
        $firm_registration1 = FirmsRegistration::where('is_delete', 0)
            ->pluck('name', 'unique_id'); // This gives you a collection with unique_id as the key and name as the value

        // dd($firm_registration1);

        $net_amount = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
            ->sum('gross_amount');

        // Fetch all firm registrations
        $vehicle_creation = Vehiclecreation::where('is_delete', 0)
            ->get(['unique_id', 'vehicle_type']);
        // Fetch related sublist entries using the screen_unique_id
        $sublist_entries = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
            ->where('is_delete', 0)
            ->get();
        // dd( $sublist_entries);
        // Calculate the total gross_amount for the sublist entries
        $totalGrossAmount = $sublist_entries->sum('gross_amount');
        $totalGross = $sublist_entries->sum('amount');

        $main_list = passentrymainlist::where('screen_unique_id', $passentrymain_list->screen_unique_id)
            ->where('pass_type', $passentrymain_list->pass_type)
            ->where('pass_amount', $passentrymain_list->pass_amount)
            ->where('is_delete', 0)
            ->first();
        // dd($firm_person_name);
        // Return the view with the fetched data
        return view('content.pass_entry.edit', compact('passentrymain_list', 'sublist_entries', 'firm_registration', 'totalGrossAmount', 'main_list', 'vehicle_creation', 'firm_registration1', 'company_creation', 'firm_registration_name', 'firm_person_name', 'net_amount', 'totalGross'));
    }

    public function getLastFirmNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Assuming you have a Firm model set up
        $lastFirm = passentrymainlist::where('pass_id', 'like', "P$year$month%")
            ->orderBy('pass_id', 'desc')
            ->first();
        // dd($lastFirm);

        $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->pass_id, -5) : 0;
        $newFirmId = sprintf("P%s%s%05d", $year, $month, $lastFirmNumber + 1);
        // dd($newFirmId);
        // Return as JSON object
        return response()->json(['pass_id' => $newFirmId]);
    }

    public function update(Request $request, $id)
    {
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
            // 'acadamic_year' => 'required|file|max:2048',

            'cover_letter' => 'file|mimes:jpg, jpeg, png, doc, docx, pdf|max:2048',
            'pass_status' => 'required|in:Approved, NotApproved',
            'acadamic_year' => 'max:255',
        ]);

        $passentrymainlists = passentrymainlist::findOrFail($id); // Find the firm by ID or fail
        $cover_letter_name = $passentrymainlists->cover_letter; // Default to existing filename

        if ($request->hasFile('cover_letter')) {
            $cover_letter = $request->file('cover_letter');
            $cover_letter_name = time() . '-' . $cover_letter->getClientOriginalName();
            $cover_letter->move(public_path('uploads/pass_entry'), $cover_letter_name);
        }

        // Update firm details
        $passentrymainlists->update([
            'issue_center' => $request->issue_center,
            'pass_id' => $request->pass_id,
            'firms_agent' => $request->firms_agent,
            'passType' => $request->passType,
            'vectype' => $request->vectype,
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

        ]);

        return response()->json(['message' => 'Pass Entry updated successfully', 'passentrymainlists' => $passentrymainlists]);
    }

    public function destroy($id)
    {
        // dd( $id );
        $passentrymainlist_id = passentrymainlist::find($id);
        // dd( $complaint_registration_id );
        if (!$passentrymainlist_id) {

            return response()->json(['success' => false, 'message' => 'Center not found.']);
        }

        // Soft delete by setting is_delete flag
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

    /**
     * Get data for DataTable.
     */

    //         public function getData()
//  {
//             $designations = Designation::where( 'is_delete', 0 )->get();
//             return DataTables::of( $designations )
//             ->addIndexColumn() // Automatically adds the serial number
//             ->addColumn( 'action', function ( $designation ) {
//                 return '
    //                 <a href = "' . route( 'designation.edit', $designation->id ) . '" class = 'btn btn-sm btn-warning'>Edit</a>
    //                 <button class = 'btn btn-sm btn-danger delete-btn' data-id = "' . $designation->id . '">Delete</button>
    //             ';
    //             }
    // )
    //         ->make( true );
    //     }

}