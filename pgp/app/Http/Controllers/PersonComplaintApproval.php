<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\person_penality;

use App\Models\vehicle_complaint_registration;
use App\Models\complaint_type;
use App\Models\Vehiclecreation;
use App\Models\PersonType;

class PersonComplaintApproval extends Controller
{
    // public function index(Request $request)
    // {
    //     // $categories = CategoryCreation::all();, compact('categories')
    //     return view('content.person_complaint_approval.index');
    // }

    // public function index()
    // {
    //     // Fetch all centers from the database
    //     $VehicleComplaint = vehicle_complaint_registration::where('is_delete', 0)->get();
    //     // Fetch all complaint types as an associative array
    //     $complaintTypes = complaint_type::all()->pluck('complaint_type', 'unique_id');
    //     // Key-value pair

    //     // Pass the centers data to the list view
    //     // return view( 'content.vehicle_complaint_registration.list', compact( 'VehicleComplaint' ) );
    //     return view('content.person_complaint_approval.index', compact('VehicleComplaint', 'complaintTypes'));
    // }

    public function index(Request $request)
    {
        // Start building the query
        $query = vehicle_complaint_registration::where('is_delete', 0);

        // Apply the 'from_date' filter if it is provided
        if (!empty($request->from_date)) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        // Apply the 'to_date' filter if it is provided
        if (!empty($request->to_date)) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Apply the 'approve' status filter if it is provided
        if ($request->has('approve') && $request->approve !== null) {
            $query->where('status', $request->approve);
        } else {
            // Default to "Pending" status if no filter is applied
            $query->where('status', 0);
        }

        // Fetch the filtered complaints
        $VehicleComplaint = $query->get();

        // Pass filter values and data to the view
        return view('content.person_complaint_approval.index', [
            'VehicleComplaint' => $VehicleComplaint,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'approve' => $request->approve ?? 0, // Default to "Pending" if not provided
        ]);
    }

    // public function index(Request $request)
    // {
    //     // Start building the query
    //     $query = vehicle_complaint_registration::where('is_delete', 0);

    //     // Apply the 'from_date' filter if it is provided
    //     if ($request->from_date) {


    //         $query->whereDate('date', '>=', $request->from_date);
    //     }

    //     // Apply the 'to_date' filter if it is provided
    //     if ($request->to_date) {
    //         $query->whereDate('date', '<=', $request->to_date);
    //     }

    //     if ($request->has('approve') && $request->approve !== null) {
    //         $query->where('status', $request->approve);
    //     } else {
    //         // Default to "Pending" status if no filter is applied
    //         $query->where('status', 0);
    //     }

    //     // Fetch the filtered complaints
    //     $VehicleComplaint = $query->get();






    //     // dd($VehicleComplaint);

    //     // Pass the complaint data to the view
    //     return view('content.person_complaint_approval.index', compact('VehicleComplaint'));
    // }



    public function add()
    {
        return view('content.person_complaint_approval.create');
    }

    public function getdata()
    {
        return view('content.person_complaint_approval.data');
    }

    // public function edit($id)
    // {

    //     return view('content.person_complaint_registration.edit', compact('category'));
    // }

    public function edit($id)
    {
        $complaint_registration = vehicle_complaint_registration::find($id);

        $person_vehicle_type = '';
        if (!$complaint_registration) {
            return redirect()->route('vehicle_complaint_registration.list')->with('error', 'Center not found.');
        }

        $complaintTypes = complaint_type::all();
        $person_vehicle_data = null;

        if ($complaint_registration->Person_vehicle == 'Person') {

            $person_vehicle_data = PersonType::where('unique_id', $complaint_registration->person_vehicle_type)
                ->where('is_active', 1) // Ensure it's active
                ->first();
            if ($person_vehicle_data) {
                $person_vehicle_type = $person_vehicle_data->person_type;
            }
        } elseif ($complaint_registration->Person_vehicle == 'vehicle' || $complaint_registration->Person_vehicle == 'equipment') {

            $person_vehicle_data = Vehiclecreation::where('unique_id', $complaint_registration->person_vehicle_type)
                ->where('is_active', 1) // Ensure it's active
                ->first();
            if ($person_vehicle_data) {
                $person_vehicle_type = $person_vehicle_data->vehicle_type;
            }
        }

        return view('content.person_complaint_approval.create', compact('complaint_registration', 'complaintTypes', 'person_vehicle_type'));
        // return view('content.vehicle_complaint_registration.edit', compact('complaint_registration', 'complaintTypes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'approve' => 'required',
            'remarks' => 'max:255',
            'penality' => 'max:255',
            'approve_date' => 'max:255',
            'user_id' => 'max:255',
        ]);

        // dd($validatedData);

        $complaintTypes = vehicle_complaint_registration::find($id);

        if (!$complaintTypes) {
            return redirect()->route('person_complaint_approval.index')->with('error', 'Center not found.');
        }
        // dd($complaintTypes);

        // Prepare the data to update
        $updateData = [
            'status' => $request->approve, // Always update the status field
            'approve_reject_date' => $request->approve_date, // Always update the approve_reject_date field
            'approved_by' => $request->user_id, // Always update the approved_by field
        ];

        // Conditionally add additional fields based on approval status
        if ($request->approve == 1) {
            // If approved, update penality field
            $updateData['approval_penality'] = $request->penality;
        } elseif ($request->approve == 2) {
            // If rejected, update remarks field
            $updateData['approval_remarks'] = $request->remarks;
        }

        // Update the complaint with the prepared data
        $complaintTypes->update($updateData);

        return redirect()->route('person_complaint_approval')->with('success', 'Successfully Submitted!');
    }

    public function pay($id)
    {
        // Find the complaint by ID
        $complaint = vehicle_complaint_registration::find($id);

        // Check if the complaint exists
        if (!$complaint) {
            return redirect()->route('person_complaint_approval')->with('error', 'Complaint not found.');
        }

        // Update the payment status to 1 (Paid)
        $complaint->payment_status = 1;

        // Save the updated record
        $complaint->save();

        // Redirect back with a success message
        return redirect()->route('person_complaint_approval')->with('success', 'Payment status updated successfully.');
    }

    public function release(Request $request, $id)
    {
        // Find the complaint by ID
        $complaint = vehicle_complaint_registration::find($id);

        // Check if the complaint exists
        if (!$complaint) {
            return redirect()->route('person_complaint_approval')->with('error', 'Complaint not found.');
        }

        $approveDate = $request->input('release_date');

        // Update the payment status to 1 (Paid)
        $complaint->status = 3;
        $complaint->release_date = $approveDate; // Store the approve date

        // Save the updated record
        $complaint->save();

        // Redirect back with a success message
        return redirect()->route('person_complaint_approval')->with('success', 'Release status updated successfully.');
    }
}