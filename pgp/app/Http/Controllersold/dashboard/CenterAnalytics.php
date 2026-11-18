<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;

// use App\Models\School;
// use App\Models\staff;
// use App\Models\students;
// use App\Models\biometric_attandance;

class CenterAnalytics extends Controller
{
    public function index()
    {
        return view('content.dashboard.dashboardPassUser');
    }
    
}