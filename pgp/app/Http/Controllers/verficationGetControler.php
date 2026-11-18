<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class verficationGetControler extends Controller
{
    public function index()
    {
        return view('content.authentications.verfication');
    }
}
