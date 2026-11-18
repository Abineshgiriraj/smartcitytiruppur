<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\vehicle_complaint_registration;
use App\Models\complaint_type;
// Ensure your model is imported

class VehicleComplaintRegistrationController extends Controller
 {
    // Display the list of centers

    public function index()
 {
        // Fetch all centers from the database
        $VehicleComplaint = vehicle_complaint_registration::where( 'is_delete', 0 )->get();
        // Fetch all complaint types as an associative array
        $complaintTypes = complaint_type::all()->pluck( 'complaint_type', 'unique_id' );
        // Key-value pair

        // Pass the centers data to the list view
        // return view( 'content.vehicle_complaint_registration.list', compact( 'VehicleComplaint' ) );
        return view( 'content.vehicle_complaint_registration.list', compact( 'VehicleComplaint', 'complaintTypes' ) );
    }

    // Display the form to add a new center

    public function add()
 {
        return view( 'content.vehicle_complaint_registration.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request )
 {
        $uniqueId = uniqid();
        // dd( $uniqueId);
        // Validate the form data
        $validatedData = $request->validate( [
            // 'unique_id' => 'required|string|unique:vehicle_complaint_registrations', // Adjust as necessary
            // 'unique_id' => uniqid(),
            'complaint_type' => 'required',
            'person' => 'max:255',
            'vehicle_no' => 'max:255',
            'user' => 'max:255',
            'equipment' => 'max:255',
            'reg_no' => 'max:255',
            'reg_date' => 'max:255',
            'dl_no' => 'max:255',
            'name' => 'max:255',
            'material' => 'max:255',
            'status' => 'max:255',
        ] );
        $validatedData[ 'unique_id' ] = $uniqueId;

        // dd( $validatedData );
        // Save the validated data to the database
        vehicle_complaint_registration::create( $validatedData );
        //    dd( $validatedData );
        // Return a JSON response for AJAX
        return response()->json( [
            'success' => true,
            'message' => 'Center created successfully!',
        ] );
    }

    //     function edit( $id )
    // {
    //         $complaint_registration = vehicle_complaint_registration::find( $id );

    //         if ( !$complaint_registration ) {
    //             return redirect()->route( 'vehicle_complaint_registration.list' )->with( 'error', 'Center not found.' );
    //         }

    //         return view( 'content.vehicle_complaint_registration.edit', compact( 'complaint_registration' ) );
    //     }

    public function edit( $id )
 {
        $complaint_registration = vehicle_complaint_registration::find( $id );

        if ( !$complaint_registration ) {
            return redirect()->route( 'vehicle_complaint_registration.list' )->with( 'error', 'Center not found.' );
        }

        // Fetch all complaint types to display in the form
        $complaintTypes = complaint_type::all();
        // Adjust this line based on your model

        return view( 'content.vehicle_complaint_registration.edit', compact( 'complaint_registration', 'complaintTypes' ) );
    }

    public function update( Request $request, $id )
 {
        // Validate the incoming request data
        $validatedData = $request->validate( [
            'complaint_type' => 'max:255',
            'person' => 'max:255',
            'vehicle_no' => 'max:255',
            'user' => 'max:255',
            'equipment' => 'max:255',
            'reg_no' => 'max:255',
            'reg_date' => 'max:255',
            'dl_no' => 'max:255',
            'name' => 'max:255',
            'material' => 'max:255',
            // 'statu' => 'max:255',
            'status' => 'in:active,inactive',
        ] );

        $complaintTypes = vehicle_complaint_registration::find( $id );

        if ( !$complaintTypes ) {
            return redirect()->route( 'vehicle_complaint_registration.list' )->with( 'error', 'Center not found.' );
        }

        // Update the center with the validated data
        $complaintTypes->update( $validatedData );

        return redirect()->route( 'vehicle_complaint_registration_list' )->with( 'success', 'Vehicle updated successfully!' );
    }

    public function destroy( $id )
 {
        // dd( $id );
        $complaint_registration_id = vehicle_complaint_registration::find( $id );
        // dd( $complaint_registration_id );
        if ( !$complaint_registration_id ) {
            return response()->json( [ 'success' => false, 'message' => 'Center not found.' ] );
        }

        // Soft delete by setting is_delete flag
        $complaint_registration_id->is_delete = 1;
        $complaint_registration_id->save();

        return response()->json( [ 'success' => true, 'message' => 'Deleted successfully!' ] );
    }

}