<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductStockController extends Controller
{

    public function index(Request $request)
    {
        return view('content.product_stock.list');
    }

    public function add()
    {
        return view('content.product_stock.form');
    }

    public function update()
    {
        return view('content.product_stock.update');
    }
    public function edit()
    {
        return view('content.product_stock.model');
    }

}
