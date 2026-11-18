<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use DB;

use Carbon\Carbon;

use Yajra\DataTables\DataTables;

use App\Models\passentrymainlist;

use App\Models\passentrysub;


class PaymentReport extends Controller
{
    // public function getPaidData(Request $request)
    // {

    //     // dd('test');

    //     if ($request->ajax()) {

    //         $cmp = DB::table('view_permitissue')
    //             ->select('agent_type')
    //             ->first();

    //         if ($cmp->agent_type == '677231ae47a0d26001') {

    
    // $paid_data = DB::table('view_permitissue')
    // ->where('view_permitissue.payment_status', 1)
    // ->leftJoin('pass_entry_main_list', 'view_permitissue.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id')
    // ->leftJoin('pass_entry_sublist', 'view_permitissue.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
    // ->leftJoin('company_creation as cc', 'vpi.agent_type', '=', 'cc.unique_id')
    // ->select(
    //     'view_permitissue.screen_unique_id',
    //     'view_permitissue.group_id',
    //     'view_permitissue.company_name',
    //     'view_permitissue.short_name',
    //     // Corrected gross_amount SUM calculation
    //     DB::raw('(SELECT SUM(vpi.gross_amount) FROM view_permitissue vpi WHERE vpi.screen_unique_id = view_permitissue.screen_unique_id) AS tax_amount'),
    //     DB::raw('SUM(view_permitissue.gross_amount) AS tt_amnt'),
    //     DB::raw('SUM(view_permitissue.sgst) AS total_sgst'),
    //     DB::raw('SUM(view_permitissue.cgst) AS total_cgst'),
    //     'pass_entry_main_list.payment_mode',
    //     'view_permitissue.issue_center_name',
    //     DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.payment_date),'%d-%m-%Y') AS paid_date"),
    //     DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.entry_date),'%d-%m-%Y') AS entries_date"),
    //     DB::raw("(SELECT COUNT(*) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) AS person_count")
    // )
    // ->groupBy(
    //     'view_permitissue.screen_unique_id',
    //     'view_permitissue.group_id',
    //     'view_permitissue.issue_center_name',
    //     'view_permitissue.company_name',
    //     'view_permitissue.short_name',
    //     'pass_entry_main_list.payment_mode'
    // );
    

    // }

                
    //             $paid_data = DB::table('view_permitissue')
    //             ->where('view_permitissue.payment_status', 1)
    //             ->leftJoin('pass_entry_main_list', 'view_permitissue.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id')
    //             ->leftJoin('pass_entry_sublist', 'view_permitissue.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
    //             ->select(
    //                 'view_permitissue.screen_unique_id',
    //                 'view_permitissue.group_id',
    //                 'view_permitissue.company_name',
    //                 'view_permitissue.short_name',
    //                 // Corrected gross_amount SUM calculation
    //                 DB::raw('(SELECT SUM(vpi.gross_amount) FROM view_permitissue vpi WHERE vpi.screen_unique_id = view_permitissue.screen_unique_id) AS tax_amount'),
    //                 DB::raw('SUM(view_permitissue.gross_amount) AS tt_amnt'),
    //                 DB::raw('SUM(view_permitissue.sgst) AS total_sgst'),
    //                 DB::raw('SUM(view_permitissue.cgst) AS total_cgst'),
    //                 'pass_entry_main_list.payment_mode',
    //                 'view_permitissue.issue_center_name',
    //                 DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.payment_date),'%d-%m-%Y') AS paid_date"),
    //                 DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.entry_date),'%d-%m-%Y') AS entries_date"),
    //                 DB::raw("(SELECT COUNT(*) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) AS person_count")
    //             )
    //             ->groupBy(
    //                 'view_permitissue.screen_unique_id',
    //                 'view_permitissue.group_id',
    //                 'view_permitissue.issue_center_name',
    //                 'view_permitissue.company_name',
    //                 'view_permitissue.short_name',
    //                 'pass_entry_main_list.payment_mode'
    //             );
              
            
                

           
    //          }


    //         if ($request->from_date) {
    //             $paid_data->where('pass_entry_main_list.payment_date', '>=', $request->from_date);
    //         }

    //         if ($request->to_date) {

    //             $to_date = Carbon::parse($request->to_date)->endOfDay();

    //             $paid_data->where('pass_entry_main_list.payment_date', '<=', $to_date);
    //         }

    //         if ($request->pay_mode) {
    //             $paid_data->where('pass_entry_main_list.payment_mode', '=', $request->pay_mode);
    //         }

    //         if ($request->center_name) {
    //             $paid_data->where('pass_entry_main_list.issue_center', '=', $request->center_name);
    //         }



    //         $datas = $paid_data->get();

    //         // dd($datas);



    //         return DataTables::of($datas)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($datas) {

    //                 // return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails1(\''  '\')"></i>';
    //                 //     <a href="javascript:void(0)" onclick="popup(\'' . $firmsauth->screen_unique_id . '\')">
    //                 //     <i class="fas fa-eye"></i>
    //                 // </a>';
    //                 return '<a href="javascript:void(0)" onclick="view(\'' . $datas->screen_unique_id . '\')">
    //                 <i class="fas fa-eye"></i>
    //                 </a>';
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }



    //     return view('content.payment_report.index');
    // }


    public function getPaidData(Request $request)
    {
        if ($request->ajax()) {
            $cmp = DB::table('view_permitissue')
                ->select('agent_type')
                ->first();
    
            $query = DB::table('view_permitissue')
                ->where('view_permitissue.payment_status', 1)
                ->leftJoin('pass_entry_main_list', 'view_permitissue.screen_unique_id', '=', 'pass_entry_main_list.screen_unique_id')
                ->leftJoin('pass_entry_sublist', 'view_permitissue.screen_unique_id', '=', 'pass_entry_sublist.screen_unique_id')
                ->select(
                    'view_permitissue.screen_unique_id',
                    'view_permitissue.group_id',
                    'view_permitissue.company_name',
                    'view_permitissue.short_name',
                    DB::raw('(SELECT SUM(vpi.gross_amount) FROM view_permitissue vpi WHERE vpi.screen_unique_id = view_permitissue.screen_unique_id) AS tax_amount'),
                    DB::raw('(SELECT SUM(vpi.gross_amount) FROM view_permitissue vpi WHERE vpi.screen_unique_id = view_permitissue.screen_unique_id) AS  tt_amnt'),
                    DB::raw('SUM(view_permitissue.sgst) AS total_sgst'),
                    DB::raw('SUM(view_permitissue.cgst) AS total_cgst'),
                    'pass_entry_main_list.payment_mode',
                    'view_permitissue.issue_center_name',
                    DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.payment_date), '%d-%m-%Y') AS paid_date"),
                    DB::raw("DATE_FORMAT(MAX(pass_entry_main_list.entry_date), '%d-%m-%Y') AS entries_date"),
                    DB::raw("(SELECT COUNT(*) FROM pass_entry_sublist AS psub WHERE psub.screen_unique_id = view_permitissue.screen_unique_id) AS person_count")
                )
                ->groupBy(
                    'view_permitissue.screen_unique_id',
                    'view_permitissue.group_id',
                    'view_permitissue.issue_center_name',
                    'view_permitissue.company_name',
                    'view_permitissue.short_name',
                    'pass_entry_main_list.payment_mode'
                );
    
            // Extra Join when agent_type is '677231ae47a0d26001'
            if ($cmp && $cmp->agent_type == '677231ae47a0d26001') {
                $query->leftJoin('company_creation as cc', 'view_permitissue.agent_type', '=', 'cc.unique_id');
            }
    
            // Apply Filters
            if ($request->filled('from_date')) {
                $query->where('pass_entry_main_list.payment_date', '>=', $request->from_date);
            }
    
            if ($request->filled('to_date')) {
                $query->where('pass_entry_main_list.payment_date', '<=', Carbon::parse($request->to_date)->endOfDay());
            }
    
            if ($request->filled('pay_mode')) {
                $query->where('pass_entry_main_list.payment_mode', $request->pay_mode);
            }
    
            if ($request->filled('center_name')) {
                $query->where('pass_entry_main_list.issue_center', $request->center_name);
            }
    
            // Fetch Data
            $datas = $query->get();
    
            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('action', function ($datas) {
                    return '<a href="javascript:void(0)" onclick="view(\'' . $datas->screen_unique_id . '\')">
                                <i class="fas fa-eye"></i>
                            </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
        return view('content.payment_report.index');
    }
    
    public function getpaidsub_data($screen_id)
    {




        $main_data = passentrymainlist::where('screen_unique_id', $screen_id)
            ->leftjoin('company_creation', 'pass_entry_main_list.agent_type', '=', 'company_creation.unique_id')
            ->leftjoin('firms_registration', 'pass_entry_main_list.firms_agent', '=', 'firms_registration.unique_id')
            ->leftjoin('centers', 'pass_entry_main_list.issue_center', '=', 'centers.unique_id')
            ->where('pass_entry_main_list.is_delete', '=', 0)
            ->select(
                'pass_entry_main_list.*',
                'firms_registration.name as firm_name',
                'company_creation.company_name as agent_name',
                'centers.center_name as issue_center_name',
                DB::raw("DATE_FORMAT(pass_entry_main_list.entry_date,'%d-%m-%Y')as date")
            )
            ->first();

        $sub_data = passentrysub::where('pass_entry_sublist.screen_unique_id', $screen_id)
            ->leftjoin('company_creation', 'pass_entry_sublist.agent_type', '=', 'company_creation.unique_id')
            ->leftjoin('firms_registration', 'pass_entry_sublist.agent_name', '=', 'firms_registration.unique_id')
            ->leftjoin('access_zone', 'pass_entry_sublist.access_zone', '=', 'access_zone.unique_id')
            ->leftjoin('port_area_access', 'pass_entry_sublist.access_area', '=', 'port_area_access.unique_id')
            ->where('pass_entry_sublist.is_delete', '=', 0)
            ->select(
                'pass_entry_sublist.*',
                DB::raw('DATE_FORMAT(from_date,"%d-%m-%Y %h:%i %p")as formatted_from_date'),
                DB::raw('DATE_FORMAT(to_date,"%d-%m-%Y %h:%i %p")as formatted_to_date'),

                'firms_registration.name as firm_name',
                'company_creation.company_name as agent_type_name',
                'access_zone.zone_type as zone_name',
                'port_area_access.area_name as area_name'
            )
            ->get();

        // dd($sub_data);

        return response()->json(['sub_data' => $sub_data, 'main_data' => $main_data]);
    }
}
