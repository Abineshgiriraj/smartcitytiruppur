<?php

namespace App\Http\Controllers;

use App\Models\user_type;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_type = user_type::where('is_delete', 0)->get();
        return view('content.user_type.index', compact('user_type'));
        // return view('content.user_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.user_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'usertype' => 'required|string|max:255',           
        ]);

        user_type::create([
            'unique_id' => unique_id(),
            'user_type' => $request->usertype              
        ]);

        return response()->json(['status' => 'User Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(user_type $user_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user_type = user_type::findOrFail($id);
        return view('content.user_type.edit', compact('user_type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'usertype' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $user_type = user_type::findOrFail($id);
        $user_type->update([
            'user_type' => $request->usertype,
            'is_active' => $request->status,
        ]);

        return response()->json(['status' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user_type = user_type::findOrFail($id);
        $user_type->update(['is_delete' => 1]);
        return response()->json(['status' => 'User Type Deleted Successfully']);
    }

    public function getData()
    {
        $user_type = user_type::where('is_delete', 0)->get();
        return DataTables::of($user_type)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('action', function ($user_types) {
                return
                    '<a href="' . route('usertype.edit', $user_types->id) . '" class="text-primary"> <i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' . $user_types->id . '"><i class="fas fa-trash-alt"></i></a>';
            })
            ->make(true);
    }
}