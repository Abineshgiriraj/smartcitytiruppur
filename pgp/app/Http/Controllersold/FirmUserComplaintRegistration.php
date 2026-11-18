<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class FirmUserComplaintRegistration extends Controller
{
    public function index(Request $request)
  {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.firm_user_complaint_registration.index');
  }
    public function add()
    {
        return view('content.firm_user_complaint_registration.create');
    }





}