<?php

namespace App\Http\Controllers;

use App\Models\department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use App\Models\person_registration;

use Carbon\Carbon;



class PersonAuthenticationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // dd($request);
            // Fetch the person registration data with the required joins
            $person_registration = DB::table('person_registration')
                ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
                ->select('person_registration.unique_id', 'person_registration.form_unique_id', 'person_registration.register_date', 'person_registration.app_id_prefix', 'person_registration.app_id', DB::raw("CONCAT(person_registration.app_id_prefix,person_registration.app_id) as merged_app_id"), 'person_registration.appDate', 'person_registration.full_name', 'person_type.person_type AS employee_designation', 'person_registration.aadhaar_no', 'person_registration.mobile', 'person_registration.status')
                ->where('person_registration.is_delete', 0)
                ->where('person_type.is_delete', 0);


            if (!empty($request->from_date)) {
                $person_registration->where('person_registration.register_date', '>=', $request->from_date);
            }
            if (!empty($request->to_date)) {
                $person_registration->where('person_registration.register_date', '<=', $request->to_date);
            }
            if (!empty($request->name)) {
                $person_registration->where('person_registration.full_name', 'like', '%' . $request->name . '%');
            }
            if (!empty($request->status)) {
                $person_registration->where('person_registration.status', $request->status);
            } else {
                $person_registration->where('person_registration.status', 0);
            }
            $person_registration = $person_registration->orderBy('person_registration.id', 'DESC')->get();

            // dd($person_registration);

            $person_registration = $person_registration->map(function ($item) {
                if ($item->status == 0) {
                    $item->action = '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('person_authentication_add', ['id' => $item->form_unique_id]) . '\'" type="button"><i class="i-Add"></i><span class="lab_add">Pending</span></button>';
                } else if($item->status == 1){
                    $item->action = '<span class="text-success fw-bold">Approved&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $item->form_unique_id . '\')"></i></span>'; // Show status as "Approved"
                }else{
                    $item->action =  '<span class="text-danger fw-bold">Rejected&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $item->form_unique_id . '\')"></i></span>'; // Show status as "Rejected"

                }
                
           
                return $item;
            });

            return DataTables::of($person_registration)
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // This will only run if the request is not AJAX
        return view('content.person_authentication.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        // Fetch the person registration data based on the unique_id
        $person_registration = DB::table('person_registration')
            ->where('form_unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        $person_sub_reg = DB::table('person_reg_sublist')
            ->where('person_reg_sublist.form_unique_id', $id)
            ->where('person_reg_sublist.is_delete', 0)
            ->where('document_master.is_delete', 0)
            ->join('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            ->select('document_master.type_of_proof as proof_types', 'person_reg_sublist.*')
            ->get();


        // dd($person_registration->aadhaar_no);

        $alreadyperson= DB::table('person_registration')
        ->where('aadhaar_no', $person_registration->aadhaar_no)
        ->where('is_delete', 0)
        ->where('status', 2)
        ->first();

        // Check if the person exists
        if($alreadyperson){
            $rejectData = 'Already Rejected Person or Driver';
        }else{
            $rejectData = '';
        }


        if (!$person_registration) {
            return redirect()->route('person_authentication')->with('error', 'Person not found');
        }

        return view('content.person_authentication.create', compact('person_registration', 'person_sub_reg','rejectData'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'department' => 'required|string|max:255', 
        //                // 'usertype' is the name attribute given in the input tag
        // ]);

        // user_type::create([
        //     'unique_id' => unique_id(),
        //     'department' => $request->department,
        //     'description' => $request->description                     // 'user_type' is the name of the column in database
        // ]);

        return response()->json(['status' => 'Department Created Successfully']);
    }






    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'usertype' => 'required|string|max:255',
        //     'status' => 'required|boolean',
        // ]);

        // $user_type = user_type::findOrFail($id);
        // $user_type->update([
        //     'user_type' => $request->usertype,
        //     'is_active' => $request->status,
        // ]);

        return response()->json(['status' => 'Department updated successfully']);
    }

    public function status_upload(Request $request)
    {

        $request->validate([
            'status' => 'required|string|max:100',
            'reject_reason' => 'nullable|string|max:255',
            'main_unique_id' => 'required|string|max:255',
            'terms' => 'required|string|max:255',
            'approved_by' => 'required|string|max:255'

        ]);




        $status_upload = person_registration::where('form_unique_id', $request->main_unique_id)->first();

        $person_reg = person_registration::where('form_unique_id', $request->main_unique_id)->first();



        // dd($firmauth);

        $person_split = substr($person_reg->app_id_prefix, 2);

        $lastFirm = person_registration::where('approval_id_prefix', 'like', "$person_split%")
            ->orderBy('approval_id_prefix', 'desc')
            ->first();





        $lastFirmNumber = $lastFirm ? (int) ($lastFirm->approval_id) : 0;

        $newapproval_Id = sprintf("%05d", $lastFirmNumber + 1);



        if ($request->status == '2') {
            $status_upload->form_unique_id = $request->main_unique_id;
            $status_upload->approval_id_prefix = null;
            $status_upload->approval_id = null;
            $status_upload->status = $request->status;
            $status_upload->verification = $request->terms;
            $status_upload->reject_reason = $request->reject_reason;
            $status_upload->approved_by = $request->approved_by;
            // $status_upload->approved_date = Carbon::now()->toDateString();
            $status_upload->approved_date = now();

            $status_upload->update();


            return response()->json(['status updated successfully']);
        } else if ($request->status == '1') {



            $status_upload->unique_id = $request->main_unique_id;

            $status_upload->approval_id_prefix = $person_split;
            $status_upload->approval_id = $newapproval_Id;
            $status_upload->status = $request->status;
            $status_upload->verification = $request->terms;
            $status_upload->reject_reason = null;
            $status_upload->approved_by = $request->approved_by;
            // $status_upload->approved_date = Carbon::now()->toDateString();
            $status_upload->approved_date = now();

            $status_upload->update();

            return response()->json(['status updated successfully ']);
        } else {

            $status_upload->unique_id = $request->main_unique_id;
            $status_upload->status = $request->status;
            $status_upload->verification = $request->terms;
            $status_upload->approval_id_prefix = null;
            $status_upload->approval_id = null;
            $status_upload->reject_reason = null;
            $status_upload->approved_by = $request->approved_by;
            // $status_upload->approved_date = Carbon::now()->toDateString();
            $status_upload->approved_date = now();

            $status_upload->update();


            return response()->json(['status updated successfully']);
        }
    }

    public function popup($id)
    {
        // Fetch the person registration data based on the unique_id
        $person_registration = DB::table('person_registration')
            ->where('person_registration.form_unique_id', $id)
            ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
            ->join('user_creation', 'person_registration.approved_by', '=', 'user_creation.unique_id')
            ->leftjoin('user_types', 'user_creation.user_type', '=', 'user_types.unique_id')
            ->where('person_registration.is_delete', 0)
            ->select('person_registration.*', 'person_type.person_type as person_type', 'user_creation.staff_name', 'user_types.user_type')
            ->first();

        $person_sub_reg = DB::table('person_reg_sublist')
            ->where('person_reg_sublist.form_unique_id', $id)
            ->where('person_reg_sublist.is_delete', 0)
            ->where('document_master.is_delete', 0)
            ->join('document_master', 'person_reg_sublist.proof_type', '=', 'document_master.unique_id')
            ->select('document_master.type_of_proof as proof_types', 'person_reg_sublist.*')
            ->get();

        // dd($person_registration);

        if (!$person_registration) {
            return response()->json(['error' => 'Data not found in Person Registration'], 404);
        }
        // dd($person_registration);
        
        if (!$person_sub_reg) {
            return response()->json(['error' => 'Data not found in Person Sub Registration'], 404);
        }
        // dd($person_sub_reg);

        return response()->json(['person_registration' => $person_registration, 'person_sub_reg' => $person_sub_reg]);

    }
}
