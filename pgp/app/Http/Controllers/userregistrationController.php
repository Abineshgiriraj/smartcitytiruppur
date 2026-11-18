<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\userregistration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class userregistrationController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('staff_registration as sr')
                ->leftJoin('staff_registration_sub as srs', function ($join) {
                    $join->on('sr.screen_unique_id', '=', 'srs.screen_unique_id')
                        ->where('srs.is_delete', 0);
                })
                ->leftJoin('department as d', 'd.unique_id', '=', 'srs.department')
                ->leftJoin('designation as des', 'des.unique_id', '=', 'srs.designation')
                ->select(
                    'sr.unique_id',
                    'sr.name',
                    'sr.mobile_no',
                    DB::raw('GROUP_CONCAT(DISTINCT d.department ORDER BY d.department SEPARATOR ", ") as departments'),
                    DB::raw('GROUP_CONCAT(DISTINCT des.designation ORDER BY des.designation SEPARATOR ", ") as designations')
                )
                ->where('sr.is_delete', 0)
                ->groupBy('sr.unique_id', 'sr.name', 'sr.mobile_no')
                ->get();


            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('user_registration.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a> &nbsp;';
                    $btn .= '<a href="javascript:void(0)" class="text-danger" title="Delete" 
                            onclick="confirmDelete(\'' . $row->unique_id . '\')" 
                            style="background:none; border:none; padding:0;">
                            <i class="fas fa-trash-alt"></i>
                        </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.user_registration.list');
    }




    public function add()
    {
        $screen_unique_id = Str::uuid()->toString();

        return view('content.user_registration.form', compact('screen_unique_id'));
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:60',
            'mobile_no' => 'required|string|max:10',
            'amobile_no' => 'required|string|max:10',
            'email_id' => 'string|max:60',
            'user_name' => 'required|string|max:200',
            'user_type' => 'required|string|max:200',
            'new_password' => 'required|string|max:200',
            'conf_password' => 'required|string|max:200',
        ]);

        try {
            DB::statement("CALL staff_registration(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)", [
                'INSERT',                // p_action
                NULL,                    // p_id
                unique_id(),
                $request->screen_unique_id,           // p_unique_id
                $request->name,          // p_name
                $request->mobile_no,     // p_mobile_no
                $request->amobile_no,    // p_amobile_no
                $request->email_id,      // p_email_id
                $request->user_name,     // p_user_name
                $request->user_type,
                $request->new_password,  // p_new_password
                $request->conf_password, // p_conf_password
                $request->is_active ?? 1 // p_is_active
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function edit($id)
    {
        // Get main record
        $user_registration = DB::table('staff_registration')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$user_registration) {
            return redirect()->back()->with('error', 'Staff not found.');
        }

        // Get all sub-records
        $user_registration_sub = DB::table('staff_registration_sub')
            ->where('is_delete', 0)
            ->where('screen_unique_id', $user_registration->unique_id)
            ->get();

        // Get reference data
        $get_designation_name = DB::table('designation')->where('is_delete', 0)->get();
        $get_department_name = DB::table('department')->where('is_delete', 0)->get();
        $get_category_name = DB::table('department_category')->where('is_delete', 0)->get();

        $screen_unique_id = $user_registration->unique_id;

        return view('content.user_registration.edit', compact(
            'user_registration',
            'user_registration_sub',
            'get_designation_name',
            'get_department_name',
            'get_category_name',
            'screen_unique_id'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'mobile_no' => 'required|string|max:10',
            'amobile_no' => 'required|string|max:10',
            'email_id' => 'required|string|max:60',
            'user_name' => 'required|string|max:200',
            'user_type' => 'required|string|max:200',
            'new_password' => 'required|string|max:200',
            'conf_password' => 'required|string|max:200',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Fetch staff record by unique_id
            $user_registration = DB::table('staff_registration')
                ->where('unique_id', $id)
                ->first();

            if (!$user_registration) {
                return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
            }

            // Call stored procedure with 12 parameters (include screen_unique_id)
            DB::statement("CALL staff_registration(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)", [
                'UPDATE',                          // p_action
                $user_registration->id ?? null,    // p_id
                $id,                               // p_unique_id
                $user_registration->unique_id,     // p_screen_unique_id
                $request->name,                    // p_name
                $request->mobile_no,               // p_mobile_no
                $request->amobile_no,              // p_amobile_no
                $request->email_id,                // p_email_id
                $request->user_name,               // p_user_name
                $request->user_type,
                $request->new_password,            // p_new_password
                $request->conf_password,           // p_confirm_password
                $request->is_active ?? 1           // p_is_active
            ]);

            return response()->json(['success' => true, 'message' => 'Staff updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }





    public function destroy($id)
    {
        try {
            $user_registration = DB::table('staff_registration')->where('unique_id', $id)->first();
            if (!$user_registration) {
                return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
            }

            // Call stored procedure with exactly 12 parameters
            DB::statement("CALL staff_registration(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                'DELETE',                        // p_action
                $user_registration->id,           // p_id
                $id,                              // p_unique_id
                '',                             // p_screen_unique_id
                '',                             // p_name
                '',                             // p_mobile_no
                '',                             // p_amobile_no
                '',                             // p_email_id
                '',                             // p_user_name
                '',
                '',                             // p_new_password
                '',                             // p_confirm_password
                0                                 // p_is_active
            ]);

            return response()->json(['success' => true, 'message' => 'Staff deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function store1(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'department_category' => 'required|string|max:255',
            'is_active' => 'required|integer',
        ]);

        try {
            DB::statement("CALL staff_registration_sub(?, ?, ?, ?, ?, ?, ?, ?,?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->screen_unique_id,
                $request->name,
                $request->designation,
                $request->department,
                $request->department_category,
                $request->is_active ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff Register created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function tableConvert(Request $request)
    {
        // dd($request);
        $staffName = $request->input('name');
        $designationId = $request->input('designation');
        $departmentId = $request->input('department');
        $categoryId = $request->input('department_category');

        $department = DB::table('department')->where('unique_id', $departmentId)->value('department');
        $designation = DB::table('designation')->where('unique_id', $designationId)->value('designation');
        $departmentCategory = DB::table('department_category')->where('unique_id', $categoryId)->value('department_category');

        return response()->json([
            'name' => $staffName,
            'designation' => $designation,
            'department' => $department,
            'department_category' => $departmentCategory,
        ]);
    }




}