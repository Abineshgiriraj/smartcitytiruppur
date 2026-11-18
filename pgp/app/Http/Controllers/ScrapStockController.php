<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ScrapStockController extends Controller {

    public function index( Request $request ) {

        return view( 'content.scrapstock.list' );
    }

    // Display the form to add a new center

    public function add() {
        return view( 'content.scrapstock.form' );
    }

    // Handle form submission and store the data

    public function update() {
        return view( 'content.scrapstock.update' );
    }

    public function edit() {
        return view( 'content.scrapstock.model' );
    }
}