<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class sparestockController extends Controller {

    public function index( Request $request ) {
        return view( 'content.spare_stock.list' );
    }

    public function add() {
        return view( 'content.spare_stock.form' );
    }

    public function update() {
        return view( 'content.spare_stock.update' );
    }
    public function edit() {
        return view( 'content.spare_stock.model' );
    }

}
