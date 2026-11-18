<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductSpecificationController extends Controller
{

    public function index(Request $request)
    {

        return view('content.product_specification.list');
    }




    public function add()
    {
        return view('content.product_specification.form');
    }
}
