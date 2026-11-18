<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

use App\Models\EmployeeRegistration;
use App\Models\EmployeeRegistrationDocument;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;

class EmployeeAuthenticationController extends Controller
{
  public function index(Request $request)
  {
      if ($request->ajax()) {
          $query = DB::table('employee_registration as er')
              ->leftJoin('employee_designation', 'er.designation', '=', 'employee_designation.unique_id')
              ->leftJoin('employee_reg_doc_sub', 'er.screenUniqueId', '=', 'employee_reg_doc_sub.screenUniqueId')
              ->select(
                  'er.register_no',
                  DB::raw('DATE(er.reg_date) as reg_date'), // Get only the date part
                  'er.employee_id',
                  'er.fullname',
                  'employee_designation.designation as designations',
                  'er.aadhar_no',
                  'er.screenUniqueId',
                  'er.designation',
                  'er.status',
                  'er.unique_id'
              )
              ->where('er.is_delete', 0);
  
          // Apply filters based on request
          if ($request->has('from_date') && $request->from_date != '') {
              $query->where('er.reg_date', '>=', $request->from_date);
          }
          if ($request->has('to_date') && $request->to_date != '') {
              $query->where('er.reg_date', '<=', $request->to_date);
          }
          if ($request->has('reg_no') && $request->reg_no != '') {
              $query->where('er.register_no', 'like', '%' . $request->reg_no . '%');
          }
          if ($request->has('name') && $request->name != '') {
              $query->where('er.fullname', 'like', '%' . $request->name . '%');
          }
          if ($request->has('status') && $request->status != '') {
              $query->where('er.status', $request->status);
          }
  
          $employee_registration = $query->distinct()->get();
  
          return Datatables::of($employee_registration)
              ->addIndexColumn()
              ->addColumn('status', function ($row) {
                  return $this->getStatusLabel($row->status); // Call the function to get the status label
              })
              ->addColumn('action', function ($row) {
                  return '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('employee_authentication_add', ['id' => $row->screenUniqueId]) . '\'"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';
              })
              ->rawColumns(['status', 'action']) // Ensure HTML is rendered
              ->make(true);
      }
  
      return view('content.employee_authentication.index');
  }
  
  // public function index(Request $request)
  // {
  //     if ($request->ajax()) {
  //         $query = DB::table('employee_registration as er')
  //             ->leftJoin('employee_designation', 'er.designation', '=', 'employee_designation.unique_id')
  //             ->leftJoin('employee_reg_doc_sub', 'er.screenUniqueId', '=', 'employee_reg_doc_sub.screenUniqueId')
  //             ->select(
  //                 'er.register_no',
  //                 DB::raw('DATE(er.reg_date) as reg_date'),
  //                 'er.employee_id',
  //                 'er.fullname',
  //                 'employee_designation.designation as designations',
  //                 'er.aadhar_no',
  //                 'er.screenUniqueId',
  //                 'er.designation',
  //                 'er.status',
  //                 'er.unique_id'
  //             )
  //             ->where('er.is_delete', 0);
  
  //         if ($request->has('designation') && $request->designation != '') {
  //             $query->where('er.designation', 'like', '%' . $request->designation . '%');
  //         }
  
  //         if ($request->has('id_no') && $request->id_no != '') {
  //             $query->where('employee_reg_doc_sub.proof_no', 'like', '%' . $request->id_no . '%');
  //         }
  
  //         $employee_registration = $query->distinct()->get();
  
  //         return Datatables::of($employee_registration)
  //             ->addIndexColumn()
  //             ->addColumn('status', function ($row) {
  //                 return $this->getStatusLabel($row->status); // Call the function to get the status label
  //             })
  //             ->addColumn('action', function ($row) {
  //                 return '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('employee_authentication_add', ['id' => $row->screenUniqueId]) . '\'"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';
  //             })
  //             ->rawColumns(['status', 'action']) // Ensure HTML is rendered
  //             ->make(true);
  //     }
  
  //     return view('content.employee_authentication.index');
  // }
  private function getStatusLabel($status)
  {
      if ($status == 1) {
          return '<span style="color: green;">Approved</span>'; // Green for Approved
      } elseif ($status == 0) {
          return '<span style="color: blue;">Pending</span>';  // Blue for Pending
      } elseif ($status == 2) {
          return '<span style="color: red;">Rejected</span>'; // Red for Rejected
      } 
  }
    

//     public function index(Request $request)
//     {
//         if ($request->ajax()) {
//             $query = DB::table('employee_registration as er')
//                 ->leftJoin('employee_designation', 'er.designation', '=', 'employee_designation.unique_id')
//                 ->leftJoin('employee_reg_doc_sub', 'er.screenUniqueId', '=', 'employee_reg_doc_sub.screenUniqueId')
//                 ->select(
//                     'er.register_no',
//                     'er.employee_id',
//                     'er.fullname',
//                     'employee_designation.designation as designations',
//                     'er.aadhar_no',
//                     'er.screenUniqueId',
//                     'er.designation',
//                     'er.status',
//                     'er.unique_id'
//                 )
//                 ->where('er.is_delete', 0);

//             if ($request->has('designation') && $request->designation != '') {
//                 $query->where('er.designation', 'like', '%' . $request->designation . '%');
//             }

//             if ($request->has('id_no') && $request->id_no != '') {
//                 $query->where('employee_reg_doc_sub.proof_no', 'like', '%' . $request->id_no . '%');
//             }

//             $employee_registration = $query->get();

//             return Datatables::of($employee_registration)
//     ->addIndexColumn()
//     ->addColumn('status', function ($row) {
//         return $this->getStatusLabel($row->status); // Call the function to get the status label
//     })
//     ->addColumn('action', function ($row) {
//         return '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('employee_authentication_add', ['id' => $row->screenUniqueId]) . '\'"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';
//     })
//     ->make(true);

//         }

//         return view('content.employee_authentication.index');
//     }

//     private function getStatusLabel($status)
// {
//     if ($status == 1) {
//         return '<span class="badge bg-success">Approved</span>'; // Green for Approved
//     } elseif ($status == 0) {
//         return '<span class="badge bg-primary">Pending</span>';  // Blue for Pending
//     } elseif ($status == 2) {
//         return '<span class="badge bg-danger">Rejected</span>'; // Red for Rejected
//     } else {
//         return '<span class="badge bg-secondary">Unknown</span>'; // Handle unexpected status values
//     }
// }


    public function create($screenUniqueId)
    {

       // Fetch the person registration data based on the unique_id
       $employee_registration = DB::table('employee_registration')
       ->where('screenUniqueId', $screenUniqueId)
       ->where('is_delete', 0)
       ->first();



   $employee_reg_sub = DB::table('employee_reg_doc_sub')
       ->where('screenUniqueId', $screenUniqueId)
       ->where('is_delete', 0)
       ->get();



   // dd($person_sub_reg);

   // Check if the person exists
   if (!$employee_registration) {
       return redirect()->route('Employee_authentication')->with('error', 'Person not found');
   }
        return view('content.employee_authentication.create',compact('employee_registration','employee_reg_sub'));
    }

    
    public function status_upload(Request $request)
    {
      $currentDate = now()->format('Y-m-d');
        $request->validate([
            'status' => 'nullable|string|max:100',
            'reject_reason' => 'nullable|string|max:255',
            'main_unique_id' => 'required|string|max:255',
            'approved_by' => 'required|string|max:255',
            


        ]);

        $status_upload = EmployeeRegistration::where('screenUniqueId', $request->main_unique_id)->first();


        if ($request->status) {



            // $status_upload->unique_id = $request->main_unique_id;

            $status_upload->status = $request->status;

            $status_upload->reject_reason = $request->reject_reason;
            $status_upload->approved_by=$request->approved_by;
            $status_upload->approved_date=$currentDate;

            $status_upload->update();

            return response()->json(['status updated successfully ']);
        }
    }

}
