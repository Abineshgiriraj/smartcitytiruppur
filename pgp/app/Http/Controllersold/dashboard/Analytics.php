<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
// use App\Models\School;
// use App\Models\staff;
// use App\Models\students;
// use App\Models\biometric_attandance;

class Analytics extends Controller
{
    public function index()
    {
        // $schoolcount = School::where('is_delete', 0)->count();
        // $staffcount = staff::where('is_delete', 0)->count();
        // $studentcount = students::where('is_delete', 0)->count();



        // $breakfast_present = biometric_attandance::where('verify_mode', 'BreakfastOn')->count();
        // $fnclass_present = biometric_attandance::where('verify_mode', 'MorningClassOn')->count();
        // $noonmeal_present = biometric_attandance::where('verify_mode', 'NoonMealOn')->count();
        // $anclass_present = biometric_attandance::where('verify_mode', 'AfternoonClassOn')->count();

        // $breakfast_absent = biometric_attandance::where('verify_mode', 'BreakfastOn')->count();



        return view('content.dashboard.dashboards-analytics');
    }
}