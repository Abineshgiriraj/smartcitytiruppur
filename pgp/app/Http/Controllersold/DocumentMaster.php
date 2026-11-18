<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentMaster extends Controller
{
    public function index()
    {
        return view('content.document_master.index');
    }
    public function add()
    {
        return view('content.document_master.create');
    }
}
