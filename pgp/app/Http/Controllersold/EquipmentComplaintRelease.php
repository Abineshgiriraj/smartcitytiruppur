<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class EquipmentComplaintRelease extends Controller
{
    public function index(Request $request)
    {
        // $categories = CategoryCreation::all();, compact('categories')
        return view('content.equipment_complaint_release.index');
    }
    public function add()
    {
        return view('content.equipment_complaint_release.create');
    }
}