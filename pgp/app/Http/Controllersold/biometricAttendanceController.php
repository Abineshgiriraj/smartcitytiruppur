<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\biometric_attandance;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


// use App\Helpers\OrderHelper;


class biometricAttendanceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return view('content.biometric_attendance.list');

        if ($request->ajax()) {
            $students = biometric_attandance::select(['id', 'enroll_no', 'enroll_name', 'department_name', 'verify_mode', 'punching_datetime']);
            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('formatted_punching_datetime', function ($row) {
                    return $row->formatted_punching_datetime;
                })
                ->rawColumns(['formatted_punching_datetime'])
                ->make(true);
        }


        return view('content.biometric_attendance.list');
    }

 

    public function punch(Request $request)
    {

        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        $punching_category = $request->punching_category;
        
        if(request()->ajax()) {

            $fromDateObj = \DateTime::createFromFormat('Y-m-d', $from_date);
            $toDateObj = \DateTime::createFromFormat('Y-m-d', $to_date);

            $fromDateStr = $fromDateObj->format('d-m-Y');
            $toDateStr = $toDateObj->format('d-m-Y');


            $biometric = DB::table('biometric_attendance')
                ->select('id','enroll_no', 'enroll_name', 'department_name', 'verify_mode', DB::raw("DATE_FORMAT(STR_TO_DATE(punching_datetime, '%d-%m-%Y %H:%i'), '%d-%m-%Y %H:%i') as formatted_punching_datetime"))
                ->whereDate(DB::raw("STR_TO_DATE(punching_datetime, '%d-%m-%Y %H:%i')"), '>=', $from_date)
                ->whereDate(DB::raw("STR_TO_DATE(punching_datetime, '%d-%m-%Y %H:%i')"), '<=', $to_date);
                if (!empty($punching_category)) {
                    $biometric->where('verify_mode', $punching_category);
                }
            $biometric = $biometric->get();
          


            return DataTables::of($biometric)->make(true);
        }
        return view('content.biometric_attendance.list');
    }
}