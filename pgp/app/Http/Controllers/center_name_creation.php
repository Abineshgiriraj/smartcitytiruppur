<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;

// Ensure your model is imported

class center_name_creation extends Controller {
    // Display the list of centers

    public function index() {
        // Fetch all centers from the database
        $centers = Center::where( 'is_delete', 0 )->get();

        // Pass the centers data to the list view
        return view( 'content.center_name.list', compact( 'centers' ) );
    }

    // Display the form to add a new center

    public function add() {
        return view( 'content.center_name.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        // Validate the form data
        $request->validate( [
            'center_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ] );

        // Save the validated data to the database
        // $create = Center::create( $validatedData );

        Center::create( [
            'unique_id' => unique_id(),
            'center_name' => $request->center_name,
            'status' => $request->status,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'created_at' => $request->created_at, // Manually set created_at if needed
            'updated_at' => $request->updated_at,
        ] );

        // dd( $category );
        // Return a JSON response for AJAX
        return response()->json( [
            'success' => true,
            'message' => 'Center created successfully!',
        ] );
    }

    public function edit( $id ) {
        $port_center = Center::find( $id );

        if ( !$port_center ) {
            return redirect()->route( 'center_name.list' )->with( 'error', 'Center not found.' );
        }

        return view( 'content.center_name.edit', compact( 'port_center' ) );
    }

    // public function update( Request $request, $id ) {
    //     // Validate the incoming request data
    //     $validatedData = $request->validate( [
    //         'center_name' => 'required|string|max:255',
    //         'short_name' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'status' => 'required|in:active,inactive',
    // ] );

    //     $center = Center::find( $id );

    //     if ( !$center ) {
    //         return redirect()->route( 'center_name.list' )->with( 'error', 'Center not found.' );
    //     }

    //     // Update the center with the validated data
    //     $center->update( $validatedData );

    //     return redirect()->route( 'center_name_list' )->with( 'success', 'Center updated successfully!' );
    // }

    public function update( Request $request, $id ) {

        $request->validate( [
            'center_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            // 'description' => 'required|string',
            'status' => 'required|in:active,inactive',

        ] );

        $center = Center::findOrFail( $id );

        $center->update([
            'center_name' => $request->center_name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json( [ 'center Updated Sucessfully', 'center' => $center ] );
    }

    public function destroy( $id ) {
        $center_id = Center::find( $id );

        if ( !$center_id ) {
            return response()->json( [ 'success' => false, 'message' => 'Center not found.' ] );
        }

        // Soft delete by setting is_delete flag
        $center_id->is_delete = 1;
        $center_id->save();

        return response()->json( [ 'success' => true, 'message' => 'Deleted successfully!' ] );
    }

}