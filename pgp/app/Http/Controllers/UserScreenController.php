<?php

namespace App\Http\Controllers;

// use DB;
use Illuminate\Support\Facades\DB;

use App\Models\user_screen;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class UserScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view('content.user_screen.index');
    // }

    public function index()
    {
        // Fetch records to pass to the view (if needed)
        $user_screens = user_screen::where('is_delete', 0)->get();

        // dd($user_screens);
        return view('content.user_screen.index', compact('user_screens'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.user_screen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        // dd($request);
        $request->validate([
            'mainscreen' => 'required|string|max:255',
            'sub_screen_icon' => 'nullable|string',
            'screen_name' => 'required|string',
            'folder_name' => 'required|string',
            'orderno' => 'required|integer',
            // 'orderno' => [
            //     'required',
            //     'integer',
            //     Rule::unique('user_screens')->where(function ($query) {
            //         return $query->where('is_delete', 0);
            //     }),
            // ],
            'icon' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Create the record
        user_screen::create([
            'unique_id' => unique_id(),
            'main_screen' => $request->mainscreen,
            'sub_screen' => $request->subscreen,
            'sub_screen_icon' => $request->sub_screen_icon,
            'screen_name' => $request->screen_name,
            'folder_name' => $request->folder_name,
            'order_no' => $request->orderno,
            'icon' => $request->icon,
            'remarks' => $request->remarks,
            'action' => $request->checkaction,
            'is_active' => $request->status,
        ]);

        // Return a JSON response
        return response()->json(['status' => 'User Screen Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(user_screen $user_screen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Find the resource by ID and load the edit view
        $user_screen = user_screen::findOrFail($id);
        // dd($user_screen);
        return view('content.user_screen.edit', compact('user_screen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user_screen = user_screen::findOrFail($id);
        // Validate the request
        $request->validate([
            'mainscreen' => 'required|string|max:255',
            'sub_screen_icon' => 'nullable|string|max:255',
            'screen_name' => 'required|string|max:255',
            'folder_name' => 'required|string|max:255',
            'orderno' => 'required|integer',
            // 'orderno' => [
            //     'required',
            //     'integer',
            //     Rule::unique('user_screens')->ignore($user_screen->id)->where(function ($query) {
            //         return $query->where('is_delete', 0);
            //     }),
            // ],
            'icon' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Find the resource by ID and update it
        $user_screen->update([
            'main_screen' => $request->mainscreen,
            'sub_screen' => $request->subscreen,
            'sub_screen_icon' => $request->sub_screen_icon,
            'screen_name' => $request->screen_name,
            'folder_name' => $request->folder_name,
            'order_no' => $request->orderno,
            'icon' => $request->icon,
            'remarks' => $request->remarks,
            'action' => $request->checkaction,
            'is_active' => $request->status,
        ]);

        // Return a JSON response
        return response()->json(['status' => 'User Screen Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the resource by ID and mark it as deleted
        $user_screen = user_screen::findOrFail($id);
        $user_screen->update(['is_delete' => 1]);

        // Return a JSON response
        return response()->json(['status' => 'User Screen Deleted Successfully']);
    }


    /**
     * Get data for DataTables.
     */
    // public function getData()
    // {
    //     // Fetch data with a join to get additional details if needed
    //     $user_screens = DB::table('user_screens')
    //         ->join('main_user_screens', 'user_screens.main_screen', '=', 'main_user_screens.unique_id')
    //         ->join('user_sub_screen', 'user_screens.sub_screen', '=', 'user_sub_screen.unique_id')
    //         ->select('user_screens.*', 'main_user_screens.screen_name', 'user_sub_screen.sub_screen_name')
    //         ->where('user_screens.is_delete', 0)
    //         ->get();

    //     return DataTables::of($user_screens)
    //         ->addIndexColumn() // Adds a serial number
    //         ->addColumn('action', function ($user_screen) {
    //             // Add action buttons
    //             return
    //                 '<a href="' . route('userscreen.edit', $user_screen->id) . '" class="btn btn-sm btn-warning">Edit</a>
    //                 <button class="btn btn-sm btn-danger delete-btn" data-id="' . $user_screen->id . '">Delete</button>';
    //         })
    //         ->make(true);
    // }
    public function getData()
    {
        $user_screens = DB::table('user_screens')
            ->join('main_user_screens', 'user_screens.main_screen', '=', 'main_user_screens.unique_id')
            // ->join('user_sub_screen', 'user_screens.sub_screen', '=', 'user_sub_screen.unique_id')
            ->select('user_screens.*', 'main_user_screens.screen_name as main_screen_name')
            ->where('user_screens.is_delete', 0)
            ->get();

        return DataTables::of($user_screens)
            ->addIndexColumn() // Adds a serial number
            ->addColumn('action', function ($user_screen) {
                return
                    '<a href="' . route('userscreen.edit', $user_screen->id) . '" class="text-primary"><i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' . $user_screen->id . '"><i class="fas fa-trash-alt"></i></a>';
            })
            ->make(true);
    }
}
