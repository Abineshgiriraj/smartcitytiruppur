<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

// use App\Models\user_registration;

class PersonRegistration extends Controller {
    public function index( Request $request ) {
        // $categories = CategoryCreation::all();
        // , compact( 'categories' )
        return view( 'content.person_registration.index' );
    }

    public function add() {
        return view( 'content.person_registration.create' );
    }
  


    public function sublist(Request $request){
        $request->validate([
           'aadhaar_no' => 'required|int|max:255',
        'licence_no'    => 'required|int|max:255',
        'aadhaar_proof'  => 'required|mimes:jpg,jpeg,png,doc,docx,pdf|max:2048' 
        ]);
    }

    public function getdata() {
        return view( 'content.person_registration.data' );
    }

    public function edit( $id ) {

        return view( 'content.user_registration.edit', compact( 'category' ) );
    }

}