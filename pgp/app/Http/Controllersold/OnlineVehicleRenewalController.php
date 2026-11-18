<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

// use App\Models\user_registration;

class OnlineVehicleRenewalController extends Controller {
    public function index( Request $request ) {
        // $categories = CategoryCreation::all();
        //, compact( 'categories' )
        return view( 'content.online_vehicle_renewal.list' );
    }

    public function add() {
        return view( 'content.online_vehicle_renewal.data' );
    }

    public function getdata() {
        return view( 'content.online_vehicle_renewal.data' );
    }

    public function edit( $id ) {

        return view( 'content.online_vehicle_renewal.edit', compact( 'category' ) );
    }

}