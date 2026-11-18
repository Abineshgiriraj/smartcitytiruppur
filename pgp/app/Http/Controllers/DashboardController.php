<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        if (session('user_id') == '') {
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        return view('content.dashboard.dashboards-analytics-one.blade');
    }
    public function getFirmCount(Request $request)
    {
        $year = $request->input('year'); // Get year from the request
        $count = DB::table('firms_registration')
            ->where('academic_year', $year)
            ->where('is_delete', 0)
            ->count();
        // dd($count);


        return response()->json(['count' => $count]);
    }

    public function getPersonCount(Request $request)
    {

        $year = $request->input('year');
        $count = DB::table('person_registration')

            ->where('academic_year', $year)
            ->where('is_delete', 0)
            //->distinct('form_unique_id')
            ->count();

        // dd($count);

        return response()->json(['count' => $count]);
    }

    public function getVehicleCount(Request $request)
    {
        $year = $request->input('year');
        $count = DB::table('vehicle_registration')
            ->where('acc_year', $year)
            ->where('is_delete', 0)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getCargoCount(Request $request)
    {
        $year = $request->input('year');

        $count = DB::table('cargo_registration')
            ->where('is_delete', 0)
            ->where('acc_year', $year)
            ->count();

        // dd($count);

        return response()->json(['count' => $count]);
    }
    public function getContainerCount(Request $request)
    {
        $year = $request->input('year');

        $count = DB::table('container_registration')
            ->where('is_delete', 0)
            ->where('acc_year', $year)
            ->count();

        return response()->json(['count' => $count]);
    }
    public function getFirmDetails($unique_id)
    {
        $data = DB::table('view_firm_registration')->where('unique_id', $unique_id)->first();
        if ($data) {
            return response()->json(['success' => true, 'firm' => $data]);  // Return the record
        }
        return response()->json(['success' => false, 'message' => 'Firm not found']);
    }

    public function getissueCount(Request $request)
    {

        $monthYear = $request->month;



        $query = DB::table('view_permitissue')->where('pass_type', '=', 'person')
            ->where('approval_status', 1);





        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        $count = $query->count();

        // dd($count);

        return response()->json(['count' => $count]);
    }


    public function getvalidCount(Request $request)
    {


        $monthYear = $request->month;

        $currentDateTime = now()->format('Y-m-d h:i:s A');

        $carbonDate = Carbon::createFromFormat('Y-m', $monthYear);
        // dd('tes');
        // dd($carbonDate);
        $year = $carbonDate->year;   // 2024
        $month = $carbonDate->month;

        $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $endOfMonth   = Carbon::create($year, $month, 1, 0, 0, 0)->endOfMonth();

        $startNextMonth = now()->addMonth()->startOfMonth()->toDateString(); // First day of next month (e.g., '2025-02-01')
        $endNextMonth = now()->addMonth()->endOfMonth()->toDateString();   // Last day of next month (e.g., '2025-02-28')

        // Query to get data from next month and exclude today
        $query = DB::table('view_permitissue')
            ->where('pass_type', '=', 'person')
            ->where('approval_status', 1)
            ->whereBetween('sub_entry_date', [$startOfMonth, $endOfMonth]) // Assuming $monthYear is in 'YYYY-MM' format
            ->where(function ($query) use ($currentDateTime, $startNextMonth, $endNextMonth) {
                $query->where('to_date', '>', $currentDateTime)  // Exclude today's date and time
                    ->orWhereBetween('to_date', [$startNextMonth, $endNextMonth]); // Get records from next month onward
            })
            ->count();

        // dd($query);


        return response()->json(['count' => $query]);
    }

    public function getexpiryCount(Request $request)
    {


        $m_y  = $request->month;
        // dd($m_y);
        $monthYear = trim($request->month); // Remove leading/trailing whitespace

        $monthYear = str_replace(' ', '-', $monthYear);
        $monthYear = preg_replace('/[^0-9\-]/', '', $monthYear);
        // Remove all spaces (if any)
        //  dd($monthYear);

        try {
            // dd($monthYear);
            // dd('ee');
            $carbonDate = Carbon::createFromFormat('Y-m', $monthYear);
            // dd('tes');
            // dd($carbonDate);
            $year = $carbonDate->year;   // 2024
            $month = $carbonDate->month;  // 2 (February)
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            // Handle the error, log it, or return a response
            dd("Invalid date format: " . $e->getMessage());
        }

        $currentDateTime = Carbon::now();


        // Get the first and last day of the given month and year
        $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $endOfMonth   = Carbon::create($year, $month, 1, 0, 0, 0)->endOfMonth();

        // $startOfMonth = Carbon::createFromFormat('Y-m-d', "$year-$month-01")->startOfMonth();  // First day of the month
        // $endOfMonth = Carbon::createFromFormat('Y-m-d', "$year-$month-01")->endOfMonth();     // Last day of the month

        // Query to get expired count for the given month and year
        $query = DB::table('view_permitissue')
            ->where('pass_type', '=', 'person')
            ->where('approval_status', 1)
            ->whereBetween('sub_entry_date', [$startOfMonth, $endOfMonth])
            ->whereBetween('to_date', [$startOfMonth, $endOfMonth]) // Filter for records in the given month
            ->where('to_date', '<', $currentDateTime) // Ensure to get only expired records
            ->count();

        //  dd($query);






        return response()->json(['count' => $query]);
    }



    public function getVehicleIssueCount(Request $request)
    {
        $monthYear = $request->month;



        // [$month, $year] = explode('-', $monthYear);
        // dd(  [$month, $year]);

        // dd($monthYear);
        //$monthYear = \DateTime::createFromFormat('Y-m', $request->month)->format('m-Y'); // Convert to 'm-Y'
        $query = DB::table('view_permitissue')->where('pass_type', '=', 'vehicle')
            ->where('approval_status', 1);


        // dd($count);

        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        $count = $query->count();
        // dd($count);
        return response()->json(['count' => $count]);
    }

    public function getVehicleValidCount(Request $request)
    {


        $monthYear = $request->month;

        $currentDateTime = now()->format('Y-m-d h:i:s A');

        $carbonDate = Carbon::createFromFormat('Y-m', $monthYear);
        // dd('tes');
        // dd($carbonDate);
        $year = $carbonDate->year;   // 2024
        $month = $carbonDate->month;

        $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $endOfMonth   = Carbon::create($year, $month, 1, 0, 0, 0)->endOfMonth();

        $startNextMonth = now()->addMonth()->startOfMonth()->toDateString(); // First day of next month (e.g., '2025-02-01')
        $endNextMonth = now()->addMonth()->endOfMonth()->toDateString();   // Last day of next month (e.g., '2025-02-28')

        // Query to get data from next month and exclude today
        $query = DB::table('view_permitissue')
            ->where('pass_type', '=', 'vehicle')
            ->where('approval_status', 1)
            ->whereBetween('sub_entry_date', [$startOfMonth, $endOfMonth]) // Assuming $monthYear is in 'YYYY-MM' format
            ->where(function ($query) use ($currentDateTime, $startNextMonth, $endNextMonth) {
                $query->where('to_date', '>', $currentDateTime)  // Exclude today's date and time
                    ->orWhereBetween('to_date', [$startNextMonth, $endNextMonth]); // Get records from next month onward
            })
            ->count();



        return response()->json(['count' => $query]);

        // dd($query);
    }

    // public function getVehicleValidCount(Request $request)
    // {
    //     $monthYear = \DateTime::createFromFormat('Y-m', $request->month)->format('m-Y'); // Convert to 'm-Y'
    //     $query = DB::table('view_permitissue')
    //         ->where(function ($query) {
    //             $query->where('to_date', '>', now())
    //                 ->orWhere('pass_valid_status', 0);
    //         })
    //         ->where('pass_type', '=', 'vehicle');

    //     if ($monthYear) {
    //         $query->where('entry_date', 'LIKE', '%' . $monthYear . '%'); // Match month and year
    //     }

    //     $count = $query->count();
    //     return response()->json(['count' => $count]);
    // }

    public function getVehicleExpiryCount(Request $request)
    {



        $m_y  = $request->month;
        // dd($m_y);
        $monthYear = trim($request->month); // Remove leading/trailing whitespace

        $monthYear = str_replace(' ', '-', $monthYear);
        $monthYear = preg_replace('/[^0-9\-]/', '', $monthYear);
        // Remove all spaces (if any)
        //  dd($monthYear);

        try {
            // dd($monthYear);
            // dd('ee');
            $carbonDate = Carbon::createFromFormat('Y-m', $monthYear);
            // dd('tes');
            // dd($carbonDate);
            $year = $carbonDate->year;   // 2024
            $month = $carbonDate->month;  // 2 (February)
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            // Handle the error, log it, or return a response
            dd("Invalid date format: " . $e->getMessage());
        }

        $currentDateTime = Carbon::now();


        // Get the first and last day of the given month and year
        $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $endOfMonth   = Carbon::create($year, $month, 1, 0, 0, 0)->endOfMonth();

        // dd($endOfMonth);

        // $startOfMonth = Carbon::createFromFormat('Y-m-d', "$year-$month-01")->startOfMonth();  // First day of the month
        // $endOfMonth = Carbon::createFromFormat('Y-m-d', "$year-$month-01")->endOfMonth();     // Last day of the month

        // Query to get expired count for the given month and year
        $query = DB::table('view_permitissue')
            ->where('pass_type', '=', 'vehicle')
            ->where('approval_status', 1)
            ->whereBetween('sub_entry_date', [$startOfMonth, $endOfMonth])
            ->whereBetween('to_date', [$startOfMonth, $endOfMonth]) // Filter for records in the given month
            ->where('to_date', '<', $endOfMonth) // Ensure to get only expired records
            ->count();

        return response()->json(['count' => $query]);
    }




    public function getfirmWindowDetials($unique_id)
    {

        return view('content.dashboard.firm-details', compact('unique_id'));
    }

    public function getpersonWindowDetials($unique_id)
    {
        // dd($unique_id);
        return view('content.dashboard.person-details', compact('unique_id'));
    }
    public function getcontainerWindowDetials($unique_id)
    {

        $cargo = DB::table('container_registration')
            ->leftJoin('company_creation', 'container_registration.agent_type', '=', 'company_creation.unique_id')
            ->leftJoin('firms_registration', 'container_registration.agent_name', '=', 'firms_registration.unique_id')
            ->where('container_registration.unique_id', $unique_id)
            ->where('container_registration.is_delete', 0)
            ->select(
                'container_registration.*',
                'company_creation.company_name as agent_type',
                'firms_registration.name as agent_name'
            )
            ->first();

        return view('content.dashboard.container-details', compact('cargo'));
    }
    public function getcargoWindowDetials($unique_id)
    {

        $cargo = DB::table('cargo_registration')
            ->leftJoin('company_creation', 'cargo_registration.agent_type', '=', 'company_creation.unique_id')
            ->leftJoin('firms_registration', 'cargo_registration.agent_name', '=', 'firms_registration.unique_id')
            ->where('cargo_registration.unique_id', $unique_id)
            ->where('cargo_registration.is_delete', 0)
            ->select(
                'cargo_registration.*',
                'company_creation.company_name as agent_type',
                'firms_registration.name as agent_name'
            )
            ->first();

        //dd($cargo);

        return view('content.dashboard.cargo-details', compact('cargo'));
    }


    public function getpersonDetails($unique_id)
    {
        $personData = DB::table('person_registration')
            ->leftJoin('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
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
                'document_master.type_of_proof as proof_type_name'
            )
            ->where('person_registration.unique_id', $unique_id)
            ->first();


        if ($personData) {
            $documents = DB::table('person_reg_sublist')
                ->where('form_unique_id', $personData->form_unique_id)
                ->leftJoin('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
                ->select(
                    'person_reg_sublist.*',
                    'document_master.type_of_proof as proof_type_name'
                )
                ->get();
            // dd($documents);

            return response()->json([
                'success' => true,
                'person_registration' => $personData,
                'person_reg_sublist' => $documents
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Details not found'
        ]);
    }

    public function getData(Request $request)
    {
        $year = $request->input('academic_year');
        $query = DB::table('person_registration')
            ->join('company_creation', 'person_registration.firm_type', '=', 'company_creation.unique_id')
            ->leftJoin('person_reg_sublist', 'person_registration.form_unique_id', '=', 'person_reg_sublist.form_unique_id')
            ->select([
                'company_creation.short_name',
                'app_id_prefix',
                'app_id',
                'appDate',
                'rf_id',
                'full_name',
                'aadhaar_no',
                'short_name as firm_type',
                'address',
                'mobile',
                'person_registration.unique_id as unique_id',
                'person_registration.form_unique_id',
                DB::raw('DATE_FORMAT(person_registration.register_date,"%d-%m-%Y")as date'),
                //         DB::raw('CASE
                //        WHEN person_registration.status = 0 THEN DATE_FORMAT(person_registration.created_at, "%d-%m-%Y")
                //             WHEN person_registration.status = 1 THEN DATE_FORMAT(person_registration.appDate, "%d-%m-%Y")
                //             WHEN person_registration.status = 2 THEN DATE_FORMAT(person_registration.appDate, "%d-%m-%Y")
                //             ELSE NULL
                //      END AS date'),
                //         DB::raw('CASE
                //      WHEN person_registration.status = 0 THEN CONCAT(person_registration.app_id_prefix, person_registration.app_id)
                //      WHEN person_registration.status = 1 THEN CONCAT(person_registration.approval_id_prefix, person_registration.approval_id)
                //     WHEN person_registration.status = 2 THEN CONCAT(person_registration.app_id_prefix, person_registration.app_id)

                //      ELSE NULL
                //   END AS app_reg_id'),
                // DB::raw('COUNT(person_reg_sublist.form_unique_id) as document_count');

            ])->where('person_registration.is_delete', 0);
        // ->groupBy('person_registration.unique_id', 'company_creation.short_name', 'person_registration.app_id_prefix', 'person_registration.app_id', 'person_registration.appDate', 'person_registration.rf_id', 'person_registration.full_name', 'person_registration.aadhaar_no', 'person_registration.address', 'person_registration.mobile', 'person_registration.register_date',);
        // ->groupBy('person_registration.unique_id', 'company_creation.short_name', 'app_id_prefix', 'app_id', 'rf_id', 'full_name', 'aadhaar_no', 'short_name', 'address', 'mobile');
        if ($year) {
            $query->where('person_registration.academic_year', $year);
        }

        $doc_count = $query->get();


        foreach ($doc_count as $row) {
            $row->count = DB::table('person_reg_sublist')
                ->where('form_unique_id', '=', $row->form_unique_id)
                ->where('is_delete', 0)
                ->count();

            // Pluck 'person_vehicle' values based on 'screen_unique_id' and 'permit_type'
            // $row->cargo_ei_count = DB::table('cargo_ei_registration_ia_ea_sub')
            // ->where('screen_unique_id', '=', $row->screen_unique_id)

            // ->where('is_delete', 0)
            // ->count();
        }


        return DataTables::of($doc_count)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails1(\'' . $row->unique_id . '\')"></i>';
            })

            ->addColumn('document_count', function ($row) {
                return $row->count;
            })

            ->rawColumns(['action'])
            ->make(true);
    }


    public function getDatafirms(Request $request)
    {

        $year = $request->input('academic_year');

        $query = DB::table('firms_registration')
            ->join('company_creation', 'firms_registration.firms', '=', 'company_creation.unique_id')
            ->select([
                'company_creation.company_name',
                'entry_date',
                'firm_id',
                'firm_id_prefix',
                'app_id_prefix',
                'app_id',
                'name',
                'company_name as firms',
                'address',
                'proprietor_name_1',
                'mobile_1',
                'port_sap_no',
                'firms_registration.unique_id as unique_id',
                'firms_registration.status',
                DB::raw('CASE 
                   WHEN firms_registration.status = 0 THEN DATE_FORMAT(firms_registration.entry_date, "%d-%m-%Y")
                        WHEN firms_registration.status = 1 THEN DATE_FORMAT(firms_registration.approval_date, "%d-%m-%Y")
                        WHEN firms_registration.status = 2 THEN DATE_FORMAT(firms_registration.approval_date, "%d-%m-%Y")
                        ELSE NULL 
                 END AS date'),
                DB::raw('CASE 
                 WHEN firms_registration.status = 0 THEN CONCAT(firms_registration.app_id_prefix, firms_registration.app_id) 
                 WHEN firms_registration.status = 1 THEN CONCAT(firms_registration.firm_id_prefix, firms_registration.firm_id)
                WHEN firms_registration.status = 2 THEN CONCAT(firms_registration.app_id_prefix, firms_registration.app_id)

                 ELSE NULL 
              END AS app_reg_id'),
            ])
            ->where('firms_registration.is_delete', 0);
        //dd($query);
        if ($year) {
            $query->where('firms_registration.academic_year', $year);
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getDataCargo(Request $request)
    {
        // dd($request);


        $year = $request->input('year');
        // dd($year);

        $query = DB::table('cargo_registration')
            // ->where('is_delete', 0)->where('created_at', $year)
            ->select([
                'entry_date',
                'application_type',
                'application_no',
                'vessel_no',
                'vessel_names',
                // 'ship_bill_no',
                'cargo_desc',
                // 'con_name',
                'party_name',
                'actual_qty',
                'unique_id'
            ])

            ->where('cargo_registration.is_delete', 0);

        // dd($query);
        if ($year) {
            $query->where('cargo_registration.acc_year', $year);
        }
        // dd($query);

        // dd($query);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDataContainer(Request $request)
    {
        $year = $request->input('year');

        $query = DB::table('container_registration')
            // ->where('is_delete', 0)->whereYear('created_at', $year)

            ->select([
                'entry_date',
                'transaction_type',
                'import_export_no',
                'vessel_name',
                'container_no',
                'container_size',
                'cfs_name',
                'unique_id',
                DB::raw("CONCAT(container_no, ' / ', container_size) AS container_info")
            ])
            ->where('container_registration.is_delete', 0);
        if ($year) {
            $query->where('container_registration.acc_year', $year);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDatavehicle(Request $request)
    {
        $year = $request->input('acc_year');
        $query = DB::table('vehicle_registration')
            ->select([
                'entry_date',
                'approved_date',
                'app_id_prefix',
                'app_id',
                'approval_id_prefix',
                'approval_id',
                'rf_id',
                'vech_reg_no',
                'chase_no',
                'vehicle_category',
                'vehicle_type',
                'owner_name',
                'contact_number',
                'unique_id',
                'approval_status',
                DB::raw('
            -- Fitness
            (SELECT valid_up_to 
                FROM vehicle_registration_sub 
                WHERE vehicle_registration_sub.screenUniqueId = vehicle_registration.screenUniqueId 
                AND certificate_type IN ("672f3e2e33d1696167", "672f3ea5ba0a951850", "672f3ee2dd0fe38236") 
                AND is_delete = 0 
                LIMIT 1) as fitness,
        
            -- Insurance
            (SELECT valid_up_to 
                FROM vehicle_registration_sub 
                WHERE vehicle_registration_sub.screenUniqueId = vehicle_registration.screenUniqueId 
                AND certificate_type IN ("672f3e3d632fa69886", "672f3eb91192d39247", "672f3ef444bb784509") 
                AND is_delete = 0 
                LIMIT 1) as insurance,
        
            -- PUC
            (SELECT valid_up_to 
                FROM vehicle_registration_sub 
                WHERE vehicle_registration_sub.screenUniqueId = vehicle_registration.screenUniqueId 
                AND certificate_type IN ("672f3e67e5df337110", "672f3ec42b90931412", "672f3f0213c3650709") 
                AND is_delete = 0 
                LIMIT 1) as puc,
        
            CASE 
                WHEN vehicle_registration.approval_status = 0 THEN DATE_FORMAT(vehicle_registration.entry_date, "%d-%m-%Y")
                WHEN vehicle_registration.approval_status = 1 THEN DATE_FORMAT(vehicle_registration.approved_date, "%d-%m-%Y")
                WHEN vehicle_registration.approval_status = 2 THEN DATE_FORMAT(vehicle_registration.approved_date, "%d-%m-%Y")
                ELSE NULL 
            END AS date,
        
            CASE 
                WHEN vehicle_registration.approval_status = 0 THEN CONCAT(vehicle_registration.app_id_prefix, vehicle_registration.app_id) 
                WHEN vehicle_registration.approval_status = 1 THEN CONCAT(vehicle_registration.approval_id_prefix, vehicle_registration.approval_id)
                WHEN vehicle_registration.approval_status = 2 THEN CONCAT(vehicle_registration.app_id_prefix, vehicle_registration.app_id)
                ELSE NULL 
            END AS app_reg_id
        '),
                DB::raw('(SELECT cc.cargo
        FROM cargo_creation cc
        WHERE cc.unique_id = vehicle_registration.vehicle_type) as vehicle_type'),
                DB::raw('(SELECT vc.vehicle_type
        FROM vehicle_creation vc
        WHERE vc.unique_id = vehicle_registration.vehicle_category) as vehicle_category'),

            ])
            ->where('vehicle_registration.is_delete', 0);
        if ($year) {
            $query->where('vehicle_registration.acc_year', $year);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails2(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getvehicleDetails($unique_id)
    {
        // dd($unique_id);
        $vehicleData = DB::table('vehicle_registration')
            ->leftJoin('cargo_creation', 'vehicle_registration.vehicle_type', '=', 'cargo_creation.unique_id')
            ->leftJoin('vehicle_creation', 'vehicle_registration.vehicle_category', '=', 'vehicle_creation.unique_id')
            ->leftJoin('company_creation', 'vehicle_registration.firm_type', '=', 'company_creation.unique_id')
            ->leftJoin('firms_registration', 'vehicle_registration.firm_name', '=', 'firms_registration.unique_id')
            ->select(
                'vehicle_registration.*',
                'cargo_creation.cargo as vehicle_type',
                'vehicle_creation.vehicle_type as vehicle_category',
                'company_creation.company_name as agent_type',
                'firms_registration.name as agent_name'
            )
            ->where('vehicle_registration.unique_id', $unique_id)
            ->first();

        if ($vehicleData) {
            $documents = DB::table('vehicle_registration_sub')
                ->leftJoin('document_master', 'vehicle_registration_sub.certificate_type', '=', 'document_master.unique_id')  // Join to document_master for proof type
                ->select(
                    'vehicle_registration_sub.*',
                    'document_master.type_of_proof as proof_type_name'
                )
                ->where('screenUniqueId', $vehicleData->screenUniqueId)
                ->get();

            return response()->json([
                'success' => true,
                'vehicle_registration' => $vehicleData,
                'vehicle_registration_sub' => $documents
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Details not found'
        ]);
    }


    public function getPersonIssuecount(Request $request)
    {

        $monthYear = $request->month;



        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', 'person')
            ->where('approval_status', 1);

        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        // dd($query);

        // Return DataTable response
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getPersonissueDetails($unique_id)
    {
        // dd($unique_id);
        $data = DB::table('view_permitissue')->where('unique_id', $unique_id)->first();
        // dd($data);
        if ($data) {
            return response()->json(['success' => true, 'firm' => $data]);  // Return the record
        }
        return response()->json(['success' => false, 'message' => 'Firm not found']);
    }

    public function getPersonValidcount(Request $request)
    {
        // if ($request->month) {
        //     $monthYear = \DateTime::createFromFormat('Y-m', $request->month);

        //     if ($monthYear) {
        //         $monthYear = $monthYear->format('m-Y');
        //     } else {
        //         $monthYear = null;
        //     }
        // } else {
        //     $monthYear = null;
        // }

        $monthYear = $request->month;
        // dd($monthYear);
        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', 'person')
            ->where('approval_status', 1)
            ->where(function ($query) {
                $query->where('to_date', '>', now());
                // ->orWhere('pass_valid_status', 0);
            });

        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getVehicleExpirycountpop(Request $request)
    {

        $monthYear = $request->month;

        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', 'vehicle')
            ->where('approval_status', 1)
            ->where(function ($query) {
                $query->where('to_date', '<', now());
                // ->orWhereIn('pass_valid_status', [1, 2]);
            });

        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getVehicleValidcountpop(Request $request)
    {

        $monthYear = $request->month;

        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', 'vehicle')
            ->where('approval_status', 1)
            ->where(function ($query) {
                $query->where('to_date', '>', now());
                // ->orWhere('pass_valid_status', 0);
            });

        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getPersonExpirycount(Request $request)
    {

        $monthYear = $request->month;



        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', '=', 'person')
            ->where('approval_status', 1)

            ->where(function ($query) {
                $query->where('to_date', '<', now());
                // ->orWhere('pass_valid_status', 0);
            });



        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }

        // if ($monthYear) {
        //     $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        // }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getCargoDetails(Request $request)
    {

        $query = DB::table('view_cargo_ei_registration')
            ->select([
                'agentType',
                'agentName',
                'ia_ea',
                'import_export_no',
                'trip_no',
                'total_quantity',
                'unique_id',
            ]);
        // dd($query);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getCargopersonDetails($unique_id)
    {
        $data = DB::table('view_cargo_ei_registration')->where('unique_id', $unique_id)->first();
        // dd($data);
        if ($data) {
            return response()->json(['success' => true, 'person_registration' => $data]);
        }

        return response()->json(['success' => false, 'message' => 'Details not found']);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails5(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getVehicalIssueData(Request $request)
    {

        $monthYear = $request->month;

        $query = DB::table('view_permitissue')
            ->select([
                'pass_id',
                'agent_type_name',
                'company_name',
                'pass_type',
                'type_of_pass',
                'unique_id'
            ])
            ->where('pass_type', '=', 'vehicle')
            ->where('approval_status', 1);
        // ->get();
        // dd($query);
        if ($monthYear) {
            $query->where('sub_entry_date', 'LIKE', '%' . $monthYear . '%');
        }



        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails10(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }




    public function getvehicleWindowDetials($unique_id)
    {


        return view('content.dashboard.vehicle-details', compact('unique_id'));
    }


    public function vehicleIssueWindow($unique_id)
    {

        // dd($unique_id);
        return view('content.dashboard.vehicleIssuedetails', compact('unique_id'));
    }

    public function vehicleExpiryWindow($unique_id)
    {

        // dd($unique_id);
        return view('content.dashboard.vehicleExpirydetails', compact('unique_id'));
    }

    public function vehicleValidWindow($unique_id)
    {

        return view('content.dashboard.vehicleValiddetails', compact('unique_id'));
    }


    public function vehicleIssueDetails($unique_id)
    {
        $vehicle_issue = DB::table('view_permitissue')
            ->where('unique_id', '=', $unique_id)
            ->select('view_permitissue.*')
            ->first();


        $vehicle_main = DB::table('vehicle_registration')
            ->where('rf_id', '=', $vehicle_issue->rfid)
            ->select('screenUniqueId', 'owner_name', 'contact_number')
            ->first();

        // $vehicle_sub = DB::table('vehicle_registration_sub')
        //     ->where('screenUniqueId', '=', $vehicle_main->screenUniqueId)
        //     ->join('document_master', 'vehicle_registration_sub.certificate_type', '=', 'document_master.unique_id')

        //     ->select('vehicle_registration_sub.*', 'document_master.type_of_proof')
        //     ->get();

        //dd($vehicle_issue);

        return response()->json(['vehicle_issue' => $vehicle_issue, 'vehicle_main' => $vehicle_main, 'success' => true]);
    }

    public function getContainerpersonDetails($unique_id)
    {
        $data = DB::table('view_container_ei_registration')->where('unique_id', $unique_id)->first();
        if ($data) {
            return response()->json(['success' => true, 'person_registration' => $data]);
        }

        return response()->json(['success' => false, 'message' => 'Details not found']);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="viewDetails6(\'' . $row->unique_id . '\')"></i>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getpersonIssueoWindowDetials($unique_id)
    {

        return view('content.dashboard.person-issue-details', compact('unique_id'));
    }
    public function getpersonValidWindowDetials($unique_id)
    {

        return view('content.dashboard.person-valid-details', compact('unique_id'));
    }
    public function getpersonExpiryWindowDetials($unique_id)
    {

        return view('content.dashboard.person-expiry-details', compact('unique_id'));
    }
}
