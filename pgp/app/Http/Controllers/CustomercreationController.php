<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomercreationController extends Controller {

    public function index( Request $request ) {
        if ( $request->ajax() ) {
            $customer = Customer::where( 'is_delete', 0 )->get();

            return DataTables::of( $customer )
            ->addIndexColumn()
            ->addColumn(
                'action',

                function ( $row ) {
                    $editUrl = route( 'customer_creation.edit', $row->unique_id );
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                }
        )
            ->rawColumns( [ 'action' ] ) // Needed to render HTML
            ->make( true );
        }
        return view( 'content.customer_creation.list' );
    }

    // Display the form to add a new Location

    public function add() {
        return view( 'content.customer_creation.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        // Validate the form data
        $request->validate( [
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'gst' => 'required',
            'address' => 'required',
        ] );
        Customer::create( [
            'unique_id' => unique_id(),
            'cus_name' =>  $request->customer_name,
            'cus_id' =>  $request->customer_id,
            'contact_no' =>  $request->contact_no,
            'contact_name' =>  $request->contact_name,
            'email' =>  $request->email,
            'gst_no' => $request->gst,
            'address' => $request->address
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'customer created successfully!',
        ] );
    }

    public function edit( $id ) {
        $customer = Customer::where( 'unique_id', $id )
        ->where( 'is_delete', 0 )
        ->first();

        if ( !$customer ) {
            return redirect()->route( 'customer_creation' )->with( 'error', 'Customer not found.' );
        }

        return view( 'content.customer_creation.form', compact( 'customer' ) );
    }

    public function update( Request $request, $id ) {
        $request->validate( [
             'customer_name' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'gst' => 'required',
            'address' => 'required',
        ] );

        $customer = Customer::where( 'unique_id', $id )->first();

        if ( !$customer ) {
            return response()->json( [
                'success' => false,
                'message' => 'Customer not found.',
            ], 404 );
        }

        $customer->update( [

            'cus_name' =>  $request->customer_name,
            'cus_id' =>  $request->customer_id,
            'contact_no' =>  $request->contact_no,
            'contact_name' =>  $request->contact_name,
            'email' =>  $request->email,
            'gst_no' => $request->gst,
            'address' => $request->address
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'customer updated successfully!',
            'customer'  => $customer,
        ] );
    }

    public function destroy( $id ) {
        $customer = Customer::where( 'unique_id', $id )->first();
        // get actual model
        if ( !$customer ) {
            return response()->json( [
                'success' => false,
                'message' => 'customer not found.'
            ] );
        }

        // Soft delete by setting is_delete flag
        $customer->is_delete = 1;
        $customer->save();

        return response()->json( [
            'success' => true,
            'message' => 'Deleted successfully!'
        ] );
    }
}
