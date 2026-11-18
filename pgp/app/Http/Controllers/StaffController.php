<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class StaffController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {

            $data = DB::table('staff as s')
                ->where('s.is_delete', 0)
                ->select('s.*', DB::raw('get_designation_name(s.designation) as designation'))
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route('staff.edit', $row->unique_id);
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('content.staff.index'); // Replace with your actual Blade view name
    }

    public function create()
    {
        return view('content.staff.create');
    }

    public function getData(Request $request)
    {
        $staff = Staff::select(['id', 'acc_year', 'emp_name', 'emp_id', 'designation', 'district', 'zone', 'school', 'doj', 'emp_contact'])
            ->where('is_delete', 0); // Filter out deleted records
        return DataTables::of($staff)
            ->addColumn('action', function ($data) {
                return '<button class="btn btn-warning btn-sm edit" data-url="' . route('staff.edit', $data->id) . '">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('staff.destroy', $data->id) . '">Delete</button>';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_name' => 'required|string',
            'emp_id' => 'required|string',
            'designation' => 'required|string',
            'email' => 'required|string|email',
            'emp_contact' => 'required|string',
            'dob' => 'required|date',
            'address' => 'required|string',
        ]);

        // Check if staff with same emp_id already exists and not soft-deleted
        $existingStaff = Staff::where('staff_id', $request->emp_id)
            ->where('is_delete', 0)
            ->first();

        if ($existingStaff) {
            return response()->json(['message' => 'Staff ID already exists.'], 422);
        }

        // Save new staff record
        $staff = new Staff();
        $staff->name = $request->emp_name;
        $staff->staff_id = $request->emp_id;
        $staff->email_id = $request->email;
        $staff->contact_no = $request->emp_contact;
        $staff->designation = $request->designation;
        $staff->dob = $request->dob;
        $staff->Address = $request->address;
        $staff->unique_id = unique_id(); // Make sure this function exists and works
        $staff->is_delete = 0; // Set is_delete if you are using soft delete manually
        $staff->save();

        return response()->json(['message' => 'Staff created successfully.']);
    }


    public function edit($id)
    {
        $staff = Staff::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$staff) {
            return redirect()->back()->with('error', 'Staff not found.');
        }

        return view('content.staff.create', compact('staff'));
    }


    public function update(Request $request, staff $staff, $id)
    {

        // Validate the input data
        $request->validate([
            'emp_name' => 'required|string',
            'emp_id' => 'required|string',
            'designation' => 'required|string',
            'email' => 'required|string|email',
            'emp_contact' => 'required|string',
            'dob' => 'required|date',
            'address' => 'required|string',
        ]);



        $staff = Staff::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        $staff->update([
            'name' => $request->emp_name,
            'staff_id' => $request->emp_id,
            'email_id' => $request->email,
            'contact_no' => $request->emp_contact,
            'designation' => $request->designation,
            'dob' => $request->dob,
            'Address' => $request->address,
        ]);

        return response()->json(['success' => true, 'message' => 'Staff updated successfully.', 'staff' => $staff]);
    }

    public function destroy($id)
    {
        $staff = Staff::where('unique_id', $id)->first();
        // get actual model

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $staff->is_delete = 1;
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }



}
