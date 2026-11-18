<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\DataTables;

use Carbon\Carbon;

class portemployeeReportController extends Controller
{
    public function index(Request $request)
    {
        // DD($request);
        if ($request->ajax()) {
            $query = DB::table('view_employee_registration as er') // Removed trailing space here
                ->select(
                    'form_unique_id',
                    'er.register_no',
                    'er.employee_id',
                    'er.fullname',
                    'er.dob',
                    'er.blood_group',
		            'er.date_of_join',
                    'er.email_id',
                    'er.department',
                    'er.department_name',
                    'er.lic_exp_date',
                    'er.area',
                    'er.rfid_no',
                    'er.register_no' ,
                    'er.authority',
                    'er.mobile'  ,
                    'er.address',
                    'er.status',
                    'er.reject_reason',
                    'er.approved_by',
                    'er.aadhar_no',
                    'er.designation',
                    'er.designation_name',
                    'er.reg_date'  ,
                    'er.user_type' ,
                    'er.user_type_name' ,
                    DB::raw("DATE_FORMAT(er.reg_date,'%d-%m-%Y')as date"),
                    // DB::raw("
                    // (SELECT proof_type 
                    //  FROM employee_reg_doc_sub 
                    //  WHERE employee_reg_doc_sub.screenUniqueId = er.form_unique_id 
                    //  AND is_delete = 0 
                    //  LIMIT 1) as proof_type
                    //     "),

        DB::raw("(
            SELECT COUNT(erds.screenUniqueId) 
            FROM employee_reg_doc_sub erds
            LEFT JOIN employee_registration er_sub 
            ON erds.screenUniqueId = er_sub.screenUniqueId
            WHERE erds.screenUniqueId = er.form_unique_id
            AND erds.is_delete = 0
        ) AS document_count")
);


                // Uncomment the below line if you need to check for `is_delete = 0` on the main table
                // ->where('is_delete', 0)
                // ->where('approval_status', 0);

              // Apply filters
              if ($request->has('from_date') && $request->from_date != '') {
                $query->where('er.reg_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $to_date = Carbon::parse($request->to_date)->endofDay();
                $query->where('er.reg_date', '<=', $to_date);
            }

            if ($request->has('designation') && $request->designation != '') {
                $query->where('er.designation', 'like', '%' . $request->designation . '%');
            }

            if ($request->has('id_no') && $request->id_no != '') {
                $query->where('er.employee_id', 'like', '%' . $request->id_no . '%');
            }
            if ($request->has('emp_name') && $request->emp_name != '') {
                $query->where('er.fullname', 'like', '%' . $request->emp_name . '%');
            }

        
            $employee = $query->get();
            // dd($employee);
        
            return DataTables::of($employee)
                ->addIndexColumn()
                
                ->addColumn('action', function ($employee) {
                    return '<i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" data-id="' . $employee->form_unique_id . '"></i>';
                })
                ->make(true);
        }
        
        return view('content.PortEmployeeReport.index');
    }

    public function getportemployeeDetails($id)
    {
        // Fetch main vehicle registration details
        $employee_reg = DB::table('view_employee_registration') // Updated table name
            ->where('form_unique_id', $id)
            ->first();
    
        // If not found in the main table, fetch from the sub-table
        // if (!$vehicle_registration) {
            $employee_reg_doc_sub = DB::table('employee_reg_doc_sub') // Updated table name
            //  ->leftJoin('document_master', 'employee_reg_doc_sub.certificate_type', '=', 'document_master.unique_id')
                ->where('screenUniqueId', $employee_reg->form_unique_id)
                ->where('employee_reg_doc_sub.is_delete', 0)
            ->select(
                // 'document_master.*',
                'employee_reg_doc_sub.*',
                DB::raw("DATE_FORMAT(employee_reg_doc_sub.lic_exp_date, '%d-%m-%Y') as lic_exp_date"),

                // 'document_master.type_of_proof as type_of_proof',
                
            )
            ->get();

    
            // Return a not found response if the sub-table also has no data
            if ($employee_reg_doc_sub->isEmpty()) {
                return response()->json(['message' => 'Employee details not found'], 404);
            }
    
            // // Return data from the sub-table
            // return response()->json([
            //     'success' => true,
            //     'vehicle_registration_sub' => $vehicle_registration_sub,
            // ]);
        // }
    
        // Return data from the main table
        return response()->json([
            'success' => true,
            'employee_reg' => $employee_reg,
            'employee_reg_doc_sub' => $employee_reg_doc_sub,
            
        ]);
    }
    
}
