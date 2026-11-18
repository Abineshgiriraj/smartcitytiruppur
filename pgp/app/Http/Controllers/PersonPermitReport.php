<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\FirmsRegistration;

use Carbon\Carbon;



class PersonPermitReport extends Controller
{
    public function index()
    {

        return view('content.person_permit_report.index');
    }

    // public function personpermitDetails(Request $request)
    // {

    //     if ($request->ajax()) {


    //         $person_permit = DB::table('view_permitissue')
    //             ->select('*')
    //             ->where('pass_type','=' ,'person');


    //         if ($request->from_date) {
    //             $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") >= ?', [$request->from_date]);
    //         }

    //         if ($request->to_date) {
    //             $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d")<= ?', [$request->to_date]);
    //         }



    //         if ($request->permit_type == 'Issue') {

    //             if ($request->from_date && $request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") BETWEEN ? AND ?', [$request->from_date, $request->to_date]);
    //             } elseif ($request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") = ?', [$request->to_date]);
    //             } elseif ($request->from_date) {
    //               $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") = ?', [$request->from_date]);
    //             }
    //         }

    //         if ($request->permit_type == 'Valid') {
    //             // dd('valid');
    //             if ($request->from_date && $request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") BETWEEN ? AND ?', [$request->from_date, $request->to_date]);
    //             } elseif ($request->from_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") >= ?', [$request->from_date]);
    //             } elseif ($request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(sub_entry_date,"%Y-%m-%d") <= ?', [$request->to_date]);
    //             } else {
    //             }
    //         }

    //         if ($request->permit_type == 'Expiry') {

    //             if ($request->from_date && $request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(to_date,"%Y-%m-%d") BETWEEN ? AND ?', [$request->from_date, $request->to_date]);
    //             } elseif ($request->from_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(to_date,"%Y-%m-%d") >= ?', [$request->from_date]);
    //             } elseif ($request->to_date) {
    //                 $person_permit->whereRaw('DATE_FORMAT(to_date,"%Y-%m-%d") <= ?', [$request->to_date]);
    //             }
    //         }



    //         if ($request->agent_type) {
    //             $person_permit->where('agent_type',  '=',$request->agent_type);
    //         }

    //         if ($request->agent_name) {
    //             $person_permit->where('firms_agent', '=',$request->agent_name);
    //         }



    //         $person_permit = $person_permit->get();



    //         return DataTables::of($person_permit)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($person_permit) {

    //                 return '<i class="fas fa-eye" style="color:#5859ae; cursor: pointer;" data-id="' . $person_permit->unique_id . '"></i>';
    //             })
    //             ->make(true);
    //     }
    //     return view('content.person_permit_report.index');
    // }
    public function personpermitDetails(Request $request)
    {
        $currenttime = now();

        if ($request->ajax()) {
            $person_permit = DB::table('view_permitissue')
                ->where('pass_type', '=', 'person')
                ->select('*',DB::raw('DATE_FORMAT(sub_entry_date,"%d-%m-%Y")as date'));


                if($request->from_date){

                    $from_date = Carbon::parse($request->from_date)->startOfDay();
                    $person_permit->where('sub_entry_date','>=',$from_date);
                }

                if($request->to_date){

                    $to_date = Carbon::parse($request->to_date)->endOfDay();
                    $person_permit->where('sub_entry_date','<=',$to_date);
                }

            // if ($request->from_date) {

            //     $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d-m-Y');

            //     $person_permit->whereRaw('STR_TO_DATE(sub_entry_date, "%d-%m-%Y") >= STR_TO_DATE(?, "%Y-%m-%d")', [$request->from_date]);
            // }

            // if ($request->to_date) {
            //     $to_date = Carbon::createFromFormat('Y-m-d',$request->to_date)->format('d-m-Y');

            //     $person_permit->whereRaw('STR_TO_DATE(sub_entry_date, "%d-%m-%Y") <= STR_TO_DATE(?, "%Y-%m-%d")', $to_date);
            // }

            if ($request->permit_type == 'Issue') {
                if ($request->from_date && $request->to_date) {
                    $person_permit->whereRaw('STR_TO_DATE(sub_entry_date, "%d-%m-%Y") BETWEEN STR_TO_DATE(?, "%Y-%m-%d") AND STR_TO_DATE(?, "%Y-%m-%d")', [$request->from_date, $request->to_date]);
                } elseif ($request->from_date) {
                    $person_permit->whereRaw('STR_TO_DATE(sub_entry_date, "%d-%m-%Y") = STR_TO_DATE(?, "%Y-%m-%d")', [$request->from_date]);
                } elseif ($request->to_date) {
                    $person_permit->whereRaw('STR_TO_DATE(sub_entry_date, "%d-%m-%Y") = STR_TO_DATE(?, "%Y-%m-%d")', [$request->to_date]);
                }
            }

            if ($request->permit_type == 'Valid') {
                $person_permit->where(function ($query) use ($currenttime) {
                    $query->whereRaw('STR_TO_DATE(to_date, "%Y-%m-%d %h:%i %p") >= ?', [$currenttime]);
                });
            }

            if ($request->permit_type == 'Expiry') {
                $person_permit->where(function ($query) use ($currenttime) {
                    $query->whereRaw('STR_TO_DATE(to_date, "%Y-%m-%d %h:%i %p") < ?', [$currenttime]);
                });
            }

            if ($request->agent_type) {
                $person_permit->where('agent_type', '=', $request->agent_type);
            }
// dd($request->agent_name);
            if ($request->agent_name) {
                $person_permit->where('firms_agent', '=', $request->agent_name);
            }

            $person_permit = $person_permit->get();

            return DataTables::of($person_permit)
                ->addIndexColumn()
                ->addColumn('action', function ($person_permit) {
                    return '<i class="fas fa-eye" style="color:#5859ae; cursor: pointer;" data-id="' . $person_permit->unique_id . '"></i>';
                })
                ->make(true);
        }

        return view('content.person_permit_report.index');
    }


    public function actionDetail($id)
    {


        $permit_action = DB::table('view_permitissue')
            ->where('unique_id', $id)
            ->get();

        return response()->json(['permit_action' => $permit_action]);
    }


    public function firmName(Request $request)
    {



        $firms = FirmsRegistration::where('firms', $request->agent_type)->get(['unique_id', 'name']);
        //      dd($firms);
        return response()->json(['firms' => $firms]);
    }
    // public function firmName(Request $request)
    // {


    //     $firms = DB::table('firms_registration')
    //         ->where('firms', $request->agent_type)
    //         ->select('unique_id', 'name')
    //         ->get();

    //     dd($firms);


    // }


    //dd($person_permit);



}
