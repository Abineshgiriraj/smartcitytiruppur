<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortAreaAccess;

class PortAreaAccessController extends Controller {
    // Show list of port areas

    public function index() {
        $port_area = PortAreaAccess::all();
        return view( 'content.port_area_access.index', compact( 'port_area' ) );
    }

    // Show form for adding new port area

    public function add() {
        return view( 'content.port_area_access.create', [ 'port_area' => null ] );
    }

    // Store new port area

    public function store( Request $request ) {
        $request->validate( [
            'area_name' => 'required',
            'status' => 'required|in:Active,In Active',
        ] );

        PortAreaAccess::create( [
            'unique_id' => unique_id(),
            'area_name' => $request->area_name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'status' => $request->status,
        ] );

        return redirect()->route( 'port_area_access' )->with( 'success', 'Port Area added successfully' );
    }

    // Show form for editing a port area

    public function edit( $id ) {
        $port_area = PortAreaAccess::findOrFail( $id );
        return view( 'content.port_area_access.edit', compact( 'port_area' ) );
    }

    // Update existing port area

    // public function update( Request $request, $id ) {
    //     $request->validate( [
    //         'area_name' => 'required',
    //         'short_name' => 'required',
    //         'description' => 'required',
    //         'status' => 'required|in:Active,In Active',
    // ] );

    //     $port_area = PortAreaAccess::findOrFail( $id );
    //     $port_area->update( [
    //         'area_name' => $request->area_name,
    //         'short_name' => $request->short_name,
    //         'description' => $request->description,
    //         'status' => $request->status,
    // ] );

    //     return redirect()->route( 'port_area_access' )->with( 'success', 'Port Area updated successfully' );
    // }

    public function update( Request $request, $id ) {
        $request->validate( [
            'area_name' => 'required',
            'short_name' => 'required',
            'description' => 'required',
            'status' => 'required|in:Active,In Active',

        ] );

        $port_area = PortAreaAccess::findOrFail( $id );

        $port_area->update( [
            'area_name' => $request->area_name,
            'short_name' => $request->short_name,
            'description' => $request->description,
            'status' => $request->status,
        ] );

        return response()->json( [ 'Department Updated Sucessfully', 'port_area' => $port_area ] );
    }

    // Delete a port area

    public function destroy( $id ) {
        $port_area = PortAreaAccess::findOrFail( $id );
        $port_area->delete();
        return redirect()->route( 'port_area_access' )->with( 'success', 'Port Area deleted successfully' );
    }
}