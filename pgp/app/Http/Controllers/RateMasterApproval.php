<?php

namespace  App\Http\Controllers;

use App\Models\Ratemaster;

use Illuminate\Http\Request;

use Carbon\Carbon;


use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;



class RateMasterApproval extends Controller
{


    // public function index(Request $request)
    // {


        // if($request->ajax()){

        //     $rate_master = DB::table('rate_master')
        //                                 ->select('allowed_type')
        //                                 ->first();

        //     if($rate_master->allowed_type == 'vehicle'){

        //         $rate_master_approval =  DB::table('rate_master')
                
        //         ->leftJoin('access_zone','rate_master.access_zone' ,'=','access_zone.unique_id')
        //         ->leftJoin('port_area_access','rate_master.access_gate','=','port_area_access.unique_id')
        //         ->leftJoin('cargo_creation','rate_master.vehicle_person_type','=','cargo_creation.unique_id')

        //     ->where('rate_master.is_delete',0)
        //     ->select('rate_master.*','access_zone.zone_type','port_area_access.area_name','cargo_creation.cargo as vehicle_type','rate_master.unique_id');
           
        //     }
      
        //     else if($rate_master->allowed_type == 'person'){

        //         $rate_master_approval =  DB::table('rate_master')
                
        //         ->leftJoin('access_zone','rate_master.access_zone' ,'=','access_zone.unique_id')
        //         ->leftJoin('port_area_access','rate_master.access_gate','=','port_area_access.unique_id')
        //         ->leftJoin('person_type','rate_master.vehicle_person_type','=','person_type.unique_id')

        //     ->where('rate_master.is_delete',0)
        //     ->select('rate_master.*','access_zone.zone_type','port_area_access.area_name','person_type.person_type as person_type_name','rate_master.unique_id');
            
        //     }

    public function index(Request $request)
{
    if ($request->ajax()) {

        $rate_master_approval = DB::table('rate_master')
            ->leftJoin('access_zone', 'rate_master.access_zone', '=', 'access_zone.unique_id')
            ->leftJoin('port_area_access', 'rate_master.access_gate', '=', 'port_area_access.unique_id')
            ->leftJoin('cargo_creation', function ($join) {
                $join->on('rate_master.vehicle_person_type', '=', 'cargo_creation.unique_id')
                     ->where('rate_master.allowed_type', '=', 'vehicle');
            })
            ->leftJoin('person_type', function ($join) {
                $join->on('rate_master.vehicle_person_type', '=', 'person_type.unique_id')
                     ->where('rate_master.allowed_type', '=', 'person');
            })
            ->where('rate_master.is_delete', 0)
            ->select(
                'rate_master.*',
                'access_zone.zone_type',
                'port_area_access.area_name',
                DB::raw('IF(rate_master.allowed_type = "vehicle", cargo_creation.cargo, NULL) as vehicle_type'),
                DB::raw('IF(rate_master.allowed_type = "person", person_type.person_type, NULL) as person_type_name'),
                'rate_master.unique_id'
            );

            if($request->from_date){
                $rate_master_approval->where('entry_date','>=',$request->from_date);
            }

            if($request->to_date){
                $rate_master_approval->where('entry_date','<=',$request->to_date);
            }

            if($request->allowed_type){
                $rate_master_approval->where('allowed_type',$request->allowed_type);
            }

            if($request->approval_status){
                $rate_master_approval->where('approval_status',$request->approval_status);
            }

            //  dd($rate_master_approval);

            $rate_master = $rate_master_approval->get();



            return DataTables::of($rate_master)

                ->addIndexColumn()
                ->addColumn('action',function($row){

                    if ($row->approval_status == 0) {
                        return '<a href="' . route('rateMasterPending', ['unique_id' => $row->unique_id]) . '" class="text-primary">
                            <button class="btn btn-primary btn-right btn-small" type="button">Pending</button>
                        </a>';
                    } else if ($row->approval_status == 1) {
                        return '<span class="text-success fw-bold">Approved&nbsp;&nbsp;&nbsp;&nbsp;
                            <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $row->unique_id . '\')"></i>
                        </span>';
                    } else {
                        return '<span class="text-danger fw-bold">Rejected&nbsp;&nbsp;&nbsp;&nbsp;
                            <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $row->unique_id . '\')"></i></span>'; // Show status as "Rejected"
                    }

                    return '<i class="fas fa-eye" style="color:#5859ae; cursor:pointer;" onclick="view_approval(\''.$row->unique_id. '\')"></i>';
                }) // Adjust the rawColumns to your specific needs
                ->make(true);

        }

        return view('content.rate_master_approval.index');


                        
    }

    public function view($unique_id){


        $rate_master_approval = DB::table('rate_master')
                    ->where('unique_id',$unique_id)
                    ->select('rate_master.*')
                    ->first();

        return view('content.rate_master_approval.pending-list',compact('rate_master_approval'));

    }

    public function status(Request $request){


        $rate_status  = Ratemaster::where('unique_id',$request->unique_id)->first();



        $rate_status->Update([
            'approval_status' => $request->approval_status,
            'approved_by'     => $request->approve_id,
            'reject_reason'     => $request->reject_reason,
            'approval_date'     => now(),

        ]);
        // dd($rate_status);

    }

    public function approve($unique_id){


        $rate = DB::table('rate_master')
                    ->where('unique_id',$unique_id)
                    ->select('allowed_type')
                    ->first();

                    // dd($rate);
        if($rate->allowed_type == 'vehicle'){

            $rate_master_approval =  DB::table('rate_master')
                
            ->leftJoin('access_zone','rate_master.access_zone' ,'=','access_zone.unique_id')
            ->leftJoin('port_area_access','rate_master.access_gate','=','port_area_access.unique_id')
            ->leftJoin('cargo_creation','rate_master.vehicle_person_type','=','cargo_creation.unique_id')
            ->leftJoin('user_creation','rate_master.approved_by','=','user_creation.unique_id')
            ->leftJoin('user_types','user_creation.user_type','=','user_types.unique_id')
            ->where('rate_master.unique_id',$unique_id)


        ->where('rate_master.is_delete',0)
        ->select('rate_master.*','access_zone.zone_type','port_area_access.area_name','cargo_creation.cargo as vehicle_type','rate_master.unique_id','user_creation.user_name','user_types.user_type')->first();
       

        }

        else if($rate->allowed_type == 'person'){

            $rate_master_approval =  DB::table('rate_master')
            ->where('rate_master.unique_id',$unique_id)

            
            ->leftJoin('access_zone','rate_master.access_zone' ,'=','access_zone.unique_id')
            ->leftJoin('port_area_access','rate_master.access_gate','=','port_area_access.unique_id')
            ->leftJoin('person_type','rate_master.vehicle_person_type','=','person_type.unique_id')
            ->leftJoin('user_creation','rate_master.approved_by','=','user_creation.unique_id')
            ->leftJoin('user_types','user_creation.user_type','=','user_types.unique_id')

        ->where('rate_master.is_delete',0)
        ->select('rate_master.*','access_zone.zone_type','port_area_access.area_name','person_type.person_type as person','rate_master.unique_id','user_creation.user_name','user_types.user_type')->first();
        
        }

        // $rate_approval =  $rate_master_approval->get();

            return response()->json(['rate_master'=> $rate_master_approval]);

        // return view('content.rate_master_approval.pending-list',compact('rate_master_approval'));

    }

}
