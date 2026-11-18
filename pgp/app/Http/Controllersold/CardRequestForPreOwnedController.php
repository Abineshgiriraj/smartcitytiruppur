<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

// use App\Models\user_registration;

class CardRequestForPreOwnedController extends Controller {
    public function index( Request $request ) {
        // $categories = CategoryCreation::all();
        //, compact( 'categories' )
        return view( 'content.card_request.index' );
    }

    public function store() {
        return view( 'content.card_request.create' );
    }

    public function getdata() {
        return view( 'content.passextent.data' );
    }

    public function edit( $id ) {

        return view( 'content.passextent.edit', compact( 'category' ) );
    }

}