<?php

namespace App\Http\Controllers;

use App\Models\user_creation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;


class UserCreationController extends Controller
{
    public function generateUniqueId()
    {
        // Generate a random string
        $randomString = strtolower(bin2hex(random_bytes(6))); // 12 characters long

        // Append current timestamp
        $timestamp = date('YmdHis'); // 14 characters long

        // Combine both parts to form a unique ID
        return $randomString . $timestamp;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.user_creation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.user_creation.create');
    }

public function store(Request $request)
{
    // Corrected validation rules
    $validatedData = $request->validate([
        'staff_name'     => 'required|string|max:255',
        'user_name'      => 'required|string|max:255|unique:user_creation,user_name', // ensures username is unique in table
        'password'       => 'required|string|min:8|confirmed',
        'user_type'      => 'required|string|max:255',
        'mobile_number'  => 'required|string|max:10',
        'email_id'       => 'required|email|max:255|unique:user_creation,email_id', // also check email uniqueness
        'remarks'        => 'nullable|string',
        'status'         => 'required|boolean',
    ]);

    // Store the validated data into the database
    user_creation::create([
        'unique_id'     => unique_id(),
        'staff_name'    => $validatedData['staff_name'],
        'user_name'     => $validatedData['user_name'],
        'password'      => Hash::make($validatedData['password']),
        'user_type'     => $validatedData['user_type'],
        'mobile_number' => $validatedData['mobile_number'],
        'email_id'      => $validatedData['email_id'],
        'remarks'       => $validatedData['remarks'] ?? null,
        'status'        => $validatedData['status'],
    ]);

    return response()->json(['message' => 'User created successfully!']);
}

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     // Corrected validation rules
    //     $validatedData = $request->validate([
    //         'staff_name' => 'required|string|max:255',
    //         'user_name' => 'required|string|max:255|unique:user_creation,user_name', // Check uniqueness in user_creation table
    //         'password' => 'required|string|min:8|confirmed', // 'confirmed' will automatically check password_confirmation
    //         'user_type' => 'required|string|max:255',
    //         'mobile_number' => 'required|string|max:10',
    //         'email_id' => 'required|email|max:255',
    //         'remarks' => 'nullable|string',
    //         'status' => 'required|boolean',
    //     ]);

    //     // Store the validated data into the database
    //     user_creation::create([
    //         'unique_id' => unique_id(),
    //         'staff_name' => $validatedData['staff_name'],
    //         'user_name' => $validatedData['user_name'],
    //         'password' => Hash::make($validatedData['password']),
    //       // 'password' => $validatedData['password'],
    //         'user_type' => $validatedData['user_type'],
    //         'mobile_number' => $validatedData['mobile_number'],
    //         'email_id' => $request['email_id'],
    //         'remarks' => $validatedData['remarks'],
    //         'status' => $validatedData['status'],
    //     ]);

    //     return response()->json(['message' => 'User created successfully!']);
    // }

    /**
     * Display the specified resource.
     */
    public function show(user_creation $user_creation) {}

    // public function getData()
    // {
    //     $users = user_creation::where('is_delete', 0)
    //         ->select(['id', 'staff_name', 'user_name', 'mobile_number', 'user_type', 'status']);

    //     return DataTables::of($users)
    //         ->addColumn('actions', function ($user) {
    //             $editUrl = route('usercreation.edit', $user->id);
    //             $deleteUrl = route('usercreation.destroy', $user->id);

    //             return '
    //             <button class="btn btn-warning btn-sm edit-btn" data-url="'.$editUrl.'">Edit</button>
    //             <button class="btn btn-danger btn-sm delete-btn" data-url="'.$deleteUrl.'"">Delete</button>
    //         ';
    //         })
    //         ->editColumn('status', function ($user) {
    //             return $user->status ? 'Active' : 'Inactive';
    //         })
    //         ->rawColumns(['actions'])
    //         ->addIndexColumn() // Adds the index column
    //         ->make(true);
    // }

    public function getData()
    {

        $users = DB::table('user_creation')
            ->join('user_types', 'user_creation.user_type', '=', 'user_types.unique_id')
            ->select('user_creation.id', 'user_creation.staff_name', 'user_creation.user_name', 'user_creation.mobile_number', 'user_types.user_type', 'user_creation.status')
            ->where('user_creation.is_delete', 0)
            ->where('user_creation.is_active', 1)
            ->get();

        // dd($users);
        // $users = user_creation::where('is_delete', 0)
        //     ->select(['id', 'staff_name', 'user_name', 'mobile_number', 'user_type', 'status']);

        return DataTables::of($users)
            ->addColumn('actions', function ($user) {
                return '
                <a class="text-primary edit" data-url="' . route('usercreation.edit', $user->id) . '"><i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' .  $user->id . '""><i class="fas fa-trash-alt"></i></a> ';
            })
            ->editColumn('status', function ($user) {
                return $user->status ? 'Active' : 'Inactive';
            })
            ->rawColumns(['actions'])
            ->addIndexColumn() // Adds the index column
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = user_creation::findOrFail($id);

        return view('content.user_creation.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:user_creation,user_name,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email_id' => 'required|email|max:255',
            'remarks' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $user = user_creation::findOrFail($id);
        $user->staff_name = $request->staff_name;
        $user->user_name = $request->user_name;

        // Only update password if it is provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->user_type = $request->user_type;
        $user->mobile_number = $request->mobile_number;
        $user->email_id = $request->email_id;
        $user->remarks = $request->remarks;
        $user->status = $request->status;
        $user->save();

        return response()->json(['success' => 'User updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy($id)
    {
        // Find the user by id
        $user = user_creation::findOrFail($id);

        // Update the is_delete column to 1
        $user->update(['is_delete' => 1]);

        return response()->json(['success' => 'User deleted successfully!']);
    }
}