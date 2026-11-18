<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller {

    public function index( Request $request ) {
        if ( $request->ajax() ) {
            $branch = Branch::where( 'is_delete', 0 )->get();

            return DataTables::of( $branch )
            ->addIndexColumn()
            ->addColumn(
                'action',

                function ( $row ) {
                    $editUrl = route( 'branch_division.edit', $row->unique_id );
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                }
            )
            ->rawColumns( [ 'action' ] ) // Needed to render HTML
            ->make( true );
        }
        return view( 'content.branch_division.list' );
    }

    // Display the form to add a new Location

    public function add() {
        return view( 'content.branch_division.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        // Validate the form data
        $request->validate( [
            'branch_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required',
            'gst' => 'required',
            'email' => 'required',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required',
        ] );
        Branch::create( [
            'unique_id' => unique_id(),
            'branch_name' =>  $request->branch_name,
            'address' =>  $request->address,
            'city' =>  $request->city,
            'district' =>  $request->district,
            'state' =>  $request->state,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst,
            'email' => $request->email,
            'contact_name' => $request->contact_person,
            'contact_no' => $request->contact_no,
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'Branch created successfully!',
        ] );
    }

    public function edit( $id ) {
        $branch = Branch::where( 'unique_id', $id )
        ->where( 'is_delete', 0 )
        ->first();

        if ( !$branch ) {
            return redirect()->route( 'branch_division' )->with( 'error', 'Branch not found.' );
        }

        return view( 'content.branch_division.form', compact( 'branch' ) );
    }

    public function update( Request $request, $id ) {
        $request->validate( [
            'branch_name'     => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'city'            => 'required|string|max:255',
            'district'            => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'pincode'         => 'required',
            'gst'             => 'required',
            'email'           => 'required|email',
            'contact_person'  => 'required|string|max:255',
            'contact_no'      => 'required',
        ] );

        $Branch = Branch::where( 'unique_id', $id )->first();

        if ( !$Branch ) {
            return response()->json( [
                'success' => false,
                'message' => 'Branch not found.',
            ], 404 );
        }

        $Branch->update( [
            'branch_name'    => $request->branch_name,
            'address'        => $request->address,
            'city'           => $request->city,
            'district'       => $request->district,
            'state'          => $request->state,
            'pincode'        => $request->pincode,
            'gst_no'         => $request->gst,
            'email'          => $request->email,
            'contact_name'   => $request->contact_person,
            'contact_no'     => $request->contact_no,
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'Branch updated successfully!',
            'branch'  => $Branch,
        ] );
    }

    public function destroy( $id ) {
        $branch = Branch::where( 'unique_id', $id )->first();
        // get actual model
        if ( !$branch ) {
            return response()->json( [
                'success' => false,
                'message' => 'Branch not found.'
            ] );
        }

        // Soft delete by setting is_delete flag
        $branch->is_delete = 1;
        $branch->save();

        return response()->json( [
            'success' => true,
            'message' => 'Deleted successfully!'
        ] );
    }
}
