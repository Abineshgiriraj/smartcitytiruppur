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
            // Fetch the person registration data with the required joins
            $person_registration = DB::table('person_registration')
                ->join('person_type', 'person_registration.designation', '=', 'person_type.unique_id')
                ->select('person_registration.unique_id', 'person_registration.form_unique_id', 'person_registration.register_no', 'person_registration.register_date', 'person_registration.full_name', 'person_type.person_type AS employee_designation', 'person_registration.aadhaar_no', 'person_registration.mobile', 'person_registration.status')
                ->where('person_registration.is_delete', 0)
                ->where('person_type.is_delete', 0);


            if ($request->has('from_date') && $request->from_date != '') {
                $person_registration->where('person_registration.register_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $person_registration->where('person_registration.register_date', '<=', $request->to_date);
            }
            if ($request->has('reg_no') && $request->reg_no != '') {
                $person_registration->where('person_registration.register_no', 'like', '%' . $request->reg_no . '%');
            }
            if ($request->has('name') && $request->name != '') {
                $person_registration->where('person_registration.full_name', 'like', '%' . $request->name . '%');
            }
            if ($request->has('status') && $request->status != '') {
                $person_registration->where('person_registration.status', $request->status);
            }

            $person_registration = $person_registration->get();






            $person_registration = $person_registration->map(function ($item) {
                $item->action = '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('person_authentication_add', ['id' => $item->form_unique_id]) . '\'" type="button"><i class="i-Add"></i><span class="lab_add">Approval</span></button>';


                if ($item->status == 1) {
                    $item->status = '<span style="color: green;">Approved</span>';
                } elseif ($item->status == 2) {
                    $item->status = '<span style="color: red;">Rejected</span>';
                } elseif ($item->status == 0) {
                    $item->status = '<span style="color: blue;">Pending</span>';
                } else {
                    $item->status = '<span>Status Unknown</span>';
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
            ->where('form_unique_id', $id)
            ->where('is_delete', 0)
            ->get();



        // dd($person_sub_reg);

        // Check if the person exists
        if (!$person_registration) {
            return redirect()->route('person_authentication')->with('error', 'Person not found');
        }

        return view('content.person_authentication.create', compact('person_registration', 'person_sub_reg'));
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
            'approved_by'   => 'required|string|max:255'


        ]);

        $status_upload = person_registration::where('form_unique_id', $request->main_unique_id)->first();


        if ($request->status) {



            $status_upload->unique_id = $request->main_unique_id;

            $status_upload->status = $request->status;
            $status_upload->reject_reason = $request->reject_reason;
            $status_upload->approved_by = $request->approved_by;
            $status_upload->approved_date = Carbon::now()->toDateString();



            $status_upload->update();

            return response()->json(['status updated successfully ']);
        }
    }
}
