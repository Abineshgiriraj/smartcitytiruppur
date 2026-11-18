<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LotStockController extends Controller
{

    public function index(Request $request)
    {
        return view('content.lot_stock.list');
    }

    public function add()
    {
        return view('content.lot_stock.form');
    }
    public function update()
    {
        return view('content.lot_stock.update');
    }

}
