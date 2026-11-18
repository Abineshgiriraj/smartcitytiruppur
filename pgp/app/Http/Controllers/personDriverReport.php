<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;


class personDriverReport extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $year = $request->input('academic_year');
            $query = DB::table('person_registration')
                ->join('company_creation', 'person_registration.firm_type', '=', 'company_creation.unique_id')
                ->leftJoin('person_reg_sublist', 'person_registration.form_unique_id', '=', 'person_reg_sublist.form_unique_id')
                ->leftjoin('person_type','person_registration.designation','=','person_type.unique_id')
                ->select([
                    'company_creation.short_name',
                    'person_registration.created_at as created_at',
                    'person_type.person_type as person_desgn',
                    'app_id_prefix',
                    'app_id',
                    'approval_id_prefix',
                    'approval_id',
                    'person_registration.form_unique_id',
                    'appDate',
                    'rf_id',
                    'full_name',
                    'aadhaar_no',
                    'short_name as firm_type',
                    'address',
                    'mobile',
                    'person_registration.status as status',
                    'person_registration.unique_id as unique_id',
                    DB::raw('DATE_FORMAT(person_registration.register_date,"%d-%m-%Y")as date'),

                    // DB::raw('COUNT(person_reg_sublist.form_unique_id) as document_count')

                ])->where('person_registration.is_delete', 0)
                ->orderBy('person_registration.id', 'desc');



            
            // ->groupBy('person_registration.unique_id', 'person_registration.form_unique_id', 'company_creation.short_name', 'person_registration.created_at', 'app_id_prefix', 'app_id', 'approval_id_prefix', 'approval_id', 'appDate', 'rf_id', 'full_name', 'aadhaar_no', 'short_name', 'address', 'mobile', 'person_registration.status');
            if ($year) {
                $query->where('person_registration.academic_year', $year);
            }
            // // Filter based on date range
            if ($request->from_date) {
                $query->where('register_date', '>=', $request->from_date);
            }

            if ($request->to_date) {

                $to_date = Carbon::parse($request->to_date)->endOfDay();

                $query->where('register_date', '<=',$to_date);
            }

            // Filter based on designation if provided
            if ($request->designation_id) {
                $query->where('designation', $request->designation_id);
                // dd($query);

            }

            $person_details = $query->get();



            foreach ($person_details as $person) {

                $person->person_sub = DB::table('person_reg_sublist')
                    ->where('form_unique_id', $person->form_unique_id)
                    ->where('is_delete', 0)
                    ->count();
            }

            // $query = $query->get();

            return DataTables::of($person_details)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails1(\'' . $row->form_unique_id . '\')"></i>';
                })

                ->addColumn('document_count', function ($person) {
                    return $person->person_sub;
                })

                ->rawColumns(['action'])
                ->make(true);



            // $person_registration = DB::table('view_person_registration') // Updated table name
            //     ->select(
            //         'form_unique_id',
            //         'name',
            //         'app_id',
            //         'approval_id_prefix',
            //         DB::raw("CONCAT(COALESCE(approval_id_prefix, ''), COALESCE(app_id, '')) as merged_app_id"),
            //         'appDate',
            //         'register_date',
            //         'address',
            //         'rf_id',
            //         'designation_id',
            //         'designation',
            //         'firm_type',
            //         'firm_name',
            //         'declaration',
            //         'licence_validity_date',
            //         'aadhaar_no',
            //         'nationality',
            //         'country',
            //         'mobile',
            //         'blood_group',
            //         'email_id',
            //         'image_upload',
            //         'port_type',
            //         'status',
            //         'approved_by',
            //         'approved_date',
            //         'proof_type',
            //         'aadhar_licence',
            //         'expiry_date',
            //         'attached_proof'
            //     )
            //     // ->where('status', 0)
            //     ->orderBy('register_date', 'desc');


            // // Filter based on date range
            // if ($request->from_date != '') {
            //     $person_registration->where('register_date', '>=', $request->from_date);
            // }

            // if ($request->to_date != '') {
            //     $person_registration->where('register_date', '<=', $request->to_date);
            // }

            // // Filter based on designation if provided
            // if ($request->designation_id != '') {
            //     $person_registration->where('designation_id', $request->designation_id);
            // }

            // $person_registration = $person_registration->get();
            // // dd($person_registration);


            // return DataTables::of($person_registration)
            //     ->addIndexColumn()
            //     ->addColumn('action', function ($person_registration) {
            //         return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" data-id="' . $person_registration->form_unique_id . '"></i>';
            //     })
            //     ->make(true);
        }

        return view('content.personDriverReport.index');
    }

    // public function getPersonDetails($id)
    // {
    //     $person_registration = DB::table('view_person_registration') // Updated table name
    //         ->where('form_unique_id', $id)
    //         ->first();

    //     if (!$person_registration) {
    //         return response()->json(['message' => 'Person details not found'], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'person_registration' => $person_registration,
    //     ]);
    // }





    public function getFirmDetails($unique_id)
    {
        $data = DB::table('view_firm_registration')->where('unique_id', $unique_id)->first();
        if ($data) {
            return response()->json(['success' => true, 'firm' => $data]);  // Return the record
        }
        return response()->json(['success' => false, 'message' => 'Firm not found']);
    }



    public function getPersonSub($unique_id)
    {


        // dd($unique_id);

        $data = DB::table('person_registration')

            ->where('person_registration.form_unique_id', $unique_id)

            ->leftjoin('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
            // ->join('company_cr')
            ->leftJoin('company_creation', 'person_registration.firm_type', '=', 'company_creation.unique_id')
            ->leftJoin('firms_registration', 'person_registration.firm_name', '=', 'firms_registration.unique_id')
            ->leftJoin('person_reg_sublist', 'person_registration.form_unique_id', '=', 'person_reg_sublist.form_unique_id')
            ->leftJoin('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            ->select(
                'person_registration.*',
                'person_type.person_type as designation_name',
                'company_creation.company_name',
                'firms_registration.name as firm_name',
                'person_reg_sublist.*',
                'document_master.type_of_proof as proof_type_name',
                'person_registration.form_unique_id'
            )

            ->first();

        // dd($data);

        $person_sub = DB::table('person_reg_sublist')
            ->where('person_reg_sublist.form_unique_id', '=', $data->form_unique_id)
            ->leftjoin('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            ->select('person_reg_sublist.*', 'document_master.type_of_proof as proof_name')
            ->get();

        if ($person_sub) {
            return response()->json(['success' => true, 'person_sub' => $person_sub, 'person_registration' => $data]);
        }

        return response()->json(['success' => false, 'message' => 'Details not found']);
    }
}
