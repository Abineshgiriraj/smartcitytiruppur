<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

// use App\Models\user_registration;

class PassExtendController extends Controller {
    public function index( Request $request ) {
        // $categories = CategoryCreation::all();
        //, compact( 'categories' )
        return view( 'content.pass_extend.index' );
    }

    public function add() {
        return view( 'content.passextent.data' );
    }

    public function getdata() {
        return view( 'content.passextent.data' );
    }

    public function edit( $id ) {

        return view( 'content.passextent.edit', compact( 'category' ) );
    }

}