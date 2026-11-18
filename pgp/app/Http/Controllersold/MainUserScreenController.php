<?php

namespace App\Http\Controllers;

use DB;
use App\Models\main_user_screen;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MainUserScreenController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main_user_screen = main_user_screen::where('is_delete', 0)->get();
        // dd($main_user_screen);
        return view('content.main_user_screen.index', compact('main_user_screen'));
        // Load the index view
        // return view('content.main_user_screen.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Load the creation form view
        return view('content.main_user_screen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'screen_type' => 'required|string|max:255',
            'screen_name' => 'required|string|max:255',
            'orderno' => 'required|integer',
            'icon' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Create the record
        main_user_screen::create([
            'unique_id' => unique_id(),
            'screen_type' => $request->screen_type,
            'screen_name' => $request->screen_name,
            'order_no' => $request->orderno,
            'icon' => $request->icon,
            'remarks' => $request->remarks,
            'is_active' => $request->status,
        ]);

        // Return a JSON response
        return response()->json(['status' => 'Main User Screen Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(main_user_screen $main_user_screen)
    {
        // Load a specific view or return the resource
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Find the resource by ID and load the edit view
        $main_user_screen = main_user_screen::findOrFail($id);
        return view('content.main_user_screen.edit', compact('main_user_screen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        // Validate the request
        $request->validate([
            'screen_type' => 'required|string|max:255',
            'screen_name' => 'required|string|max:255',
            'orderno' => 'required|integer',
            'icon' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Find the resource by ID and update it
        $main_user_screen = main_user_screen::findOrFail($id);
        $main_user_screen->update([
            'screen_type' => $request->screen_type,
            'screen_name' => $request->screen_name,
            'order_no' => $request->orderno,
            'icon' => $request->icon,
            'remarks' => $request->remarks,
            'is_active' => $request->status,
        ]);
        // dd($main_user_screen);

        // Return a JSON response
        return response()->json(['status' => 'Main User Screen Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the resource by ID and mark it as deleted
        $main_user_screen = main_user_screen::findOrFail($id);
        $main_user_screen->update(['is_delete' => 1]);

        // Return a JSON response
        return response()->json(['status' => 'Main User Screen Deleted Successfully']);
    }

    /**
     * Get data for DataTables.
     */
    public function getData()
    {
        // Fetch data with a join to get the screen_type_name instead of the unique_id
        $main_user_screen = DB::table('main_user_screens')
            ->join('user_screen_type', 'main_user_screens.screen_type', '=', 'user_screen_type.unique_id')
            ->select('main_user_screens.*', 'user_screen_type.type_name')
            ->where('main_user_screens.is_delete', 0)
            ->get();

        return DataTables::of($main_user_screen)
            ->addIndexColumn() // Adds a serial number
            ->addColumn('action', function ($main_user_screens) {
                // Add action buttons


                return
                    '<a href="' . route('mainuserscreen.edit', $main_user_screens->id) . '" class="text-primary""><i class="fas fa-edit"></i></a>
                <a class="text-danger ml-2 delete-btn" data-id="' . $main_user_screens->id . '"><i class="fas fa-trash-alt"></i></a>';
            })
            ->editColumn('screen_type', function ($main_user_screens) {
                // Return the screen type name instead of the unique_id
                $demo = formatToCapitalizedWords($main_user_screens->type_name);

                return $demo;
            })
            ->make(true);
    }
}