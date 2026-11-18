<?php

namespace App\Http\Controllers;

use App\Models\user_permission;
use Illuminate\Http\Request;
use App\Models\user_screen;
use App\Models\user_type;
use Illuminate\Support\Facades\DB;

// use Yajra\DataTables\Contracts\DataTables;

use Yajra\DataTables\DataTables;


class UserPermissionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch user permissions data
            $user_permission = DB::table('user_screen_permission')
                ->join('user_types', 'user_screen_permission.user_type', '=', 'user_types.unique_id')
                ->join('main_user_screens', 'user_screen_permission.main_screen_id', '=', 'main_user_screens.unique_id')
                ->join('user_screens', 'user_screen_permission.screen_unique_id', '=', 'user_screens.unique_id')
                ->join('user_screen_actions', 'user_screen_permission.action_unique_id', '=', 'user_screen_actions.unique_id')
                ->select(
                    'user_types.user_type as user_type', // User type
                    DB::raw('GROUP_CONCAT(DISTINCT user_screen_permission.unique_id) as unique_ids'), // Concatenate unique IDs
                    DB::raw('GROUP_CONCAT(DISTINCT user_screen_actions.action_name ORDER BY user_screen_actions.action_name ASC SEPARATOR ", ") as actions') // Concatenate actions
                )
                ->where('user_screen_permission.is_delete', 0) // Only active and non-deleted
                ->where('user_screen_permission.is_active', 1)
                ->groupBy('user_types.user_type') // Group by user type
                ->get();

            return DataTables::of($user_permission)
                ->addIndexColumn()
                ->addColumn('action', function ($user_permissions) {
                    $uniqueIds = $user_permissions->user_type; // Use the concatenated unique_ids
                    $editUrl = route('userpermission.edit', ['uniqueIds' => $uniqueIds]);

                    return '
                        <a href="' . $editUrl . '" class="text-primary"><i class="fas fa-edit"></i></a>
                        <a class="text-danger ml-2 delete-btn" data-id="' . $uniqueIds . '" onclick="deleteUserPermission(this)"><i class="fas fa-trash-alt"></i></a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.user_permission.index'); // Return the index view
    }


    //         ->addColumn('action', function ($user_permissions) {
    //             $editUrl = route('userpermission.edit', $user_permissions->unique_ids); // Assuming user_type is available
    //             $deleteUrl = route('userpermission.destroy', $user_permissions->unique_ids); // Assuming you have a delete route
    //             $csrfToken = csrf_token();

    //             return '
    //                 <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
    //                 <button class="btn btn-sm btn-danger danger-btn" data-id="' . $user_permissions->unique_ids . '" onclick="deleteUserPermission(this)">Delete</button>';
    //         })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }

    //     return view('content.user_permission.index'); // Return the index view
    // }


    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {

    //     $user_permission = DB::table('user_screen_permission')
    //         ->join('user_types', 'user_screen_permission.user_type', '=', 'user_types.unique_id')
    //         ->join('main_user_screens', 'user_screen_permission.main_screen_id', '=', 'main_user_screens.unique_id')
    //         ->join('user_screens', 'user_screen_permission.screen_unique_id', '=', 'user_screens.unique_id')
    //         ->join('user_screen_actions', 'user_screen_permission.action_unique_id', '=', 'user_screen_actions.unique_id')
    //         ->select('user_screen_permission.id', 'user_types.user_type as user_type')
    //         ->where('user_screen_permission.is_delete', 0)
    //         ->where('user_screen_permission.is_active', 1)
    //         ->get();

    //     return DataTables::of($user_permission)
    //         ->addIndexColumn() // Adds an index column if needed
    //         ->addColumn('action', function ($user_permissions) {
    //             return '
    //                 <a href="' . route('userpermission.edit', $user_permissions->id) . '" class="btn btn-sm btn-warning">Edit</a>
    //                 <button class="btn btn-sm btn-danger danger-btn" data-id="' . $user_permissions->id . '">Delete</button>';
    //         })
    //         ->rawColumns(['action']) // This makes sure the action column is rendered as raw HTML
    //         ->make(true);

    //     return view('content.user_permission.index');
    // }
    //working func

    // public function index(Request $request)
    // {

    //     if ($request->ajax()) {
    //         // $user_type = session('usertype_id');

    //         // if ($user_type == '66eaeaf79982060867' || $user_type == '66eaeb019259320015'  || $user_type == '66eaeb0e76aff51273') {

    //         // Fetch user permissions data
    //         $user_permission = DB::table('user_screen_permission')
    //             ->join('user_types', 'user_screen_permission.user_type', '=', 'user_types.unique_id')
    //             ->join('main_user_screens', 'user_screen_permission.main_screen_id', '=', 'main_user_screens.unique_id')
    //             ->join('user_screens', 'user_screen_permission.screen_unique_id', '=', 'user_screens.unique_id')
    //             ->join('user_screen_actions', 'user_screen_permission.action_unique_id', '=', 'user_screen_actions.unique_id')
    //             ->select(
    //                 'user_screen_permission.id',
    //                 // 'user_screen_permission.unique_id',
    //                 'user_types.user_type as user_type', // User type
    //                 'main_user_screens.screen_name as main_screen', // Main screen name
    //                 'user_screens.screen_name as sub_screen', // Sub screen name
    //                 DB::raw('GROUP_CONCAT(DISTINCT user_screen_actions.action_name ORDER BY user_screen_actions.action_name ASC SEPARATOR ", ") as actions') // Concatenate actions
    //             )

    //             ->where('user_screen_permission.is_delete', 0) // Only active and non-deleted
    //             ->where('user_screen_permission.is_active', 1)
    //             ->groupBy('user_screen_permission.id', 'user_screen_permission.user_type', 'user_screen_permission.main_screen_id', 'user_screen_permission.screen_unique_id', 'user_types.user_type', 'main_user_screens.screen_name', 'user_screens.screen_name') // Grouping by user type, main screen, and sub screen
    //             ->get();

    //         return DataTables::of($user_permission)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($user_permissions) {


    //                 $editUrl = route('userpermission.edit', $user_permissions->id);
    //                 $deleteUrl = route('userpermission.destroy', $user_permissions->id);
    //                 $csrfToken = csrf_token();

    //                 return '
    //                     <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
    //                     <button class="btn btn-sm btn-danger danger-btn" data-id="">Delete</button>';
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //         // }
    //     }
    //     return view('content.user_permission.index');




    // }



    public function getdata(Request $request)
    {

        $main_screen_id         = $request->main_screen;
        $user_type =               $request->user_type;

        $demo = user_permission_ui($main_screen_id, $user_type);

        return \Response::json($demo);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.user_permission.create');
    }

    // public function store(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'user_type' => 'required|string|max:255',
    //         'checkboxvalue' => 'required|array', // Ensure checkbox values are provided
    //         'updated_at' => 'nullable|date',
    //         'created_at' => 'nullable|date',
    //     ]);

    //     // Determine the value for 'check_all'
    //     $check_all = $request->checkall != '' ? 1 : 0;

    //     // Process each checkbox value
    //     foreach ($request->checkboxvalue as $value) {
    //         $demoData = explode('-', $value);

    //         user_permission::create([
    //             'unique_id' => unique_id(),
    //             'user_type' => $request->user_type,
    //             'main_screen_id' => $demoData[0],
    //             'screen_unique_id' => $demoData[1],
    //             'action_unique_id' => $demoData[2],
    //             'check_all' => $check_all,
    //             'updated_at' => $request->updated_at ?? now(),
    //             'created_at' => $request->created_at ?? now()
    //         ]);
    //     }

    //     return response()->json(['message' => 'Successfully stored']);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_type' => 'required|string|max:255',
            // 'main_screen' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*.user_screen_id' => 'required|string|max:255',
            'permissions.*.action_id' => 'required|string|max:255',
            'permissions.*.main_screen_id' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $storedPermissions = [];

        foreach ($request->permissions as $permission) {
            $user_permission = user_permission::create([
                'unique_id' => unique_id(),
                'user_type'  => $request->user_type,
                'main_screen_id' => $permission['main_screen_id'],
                'screen_unique_id' => $permission['user_screen_id'],
                'action_unique_id' => $permission['action_id'],
                'created_at'        => $request->created_at ?? now(),
                'updated_at'        => $request->updated_at ?? now(),
            ]);

            $storedPermissions[] = $user_permission;
        }

        return response()->json(['success' => 'Permissions stored successfully', 'user_permissions' => $storedPermissions]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    // public function edit($uniqueId)
    // {
    //     $user_permission = DB::table('user_screen_permission')->where('unique_id', $uniqueId)->first();
    //     $user_types = DB::table('user_types')->get();
    //     $main_screens = DB::table('main_user_screens')->get();
    //     $user_screens = DB::table('user_screens')->get();

    //     // Get selected screens for the user
    //     $selected_screens = DB::table('user_screen_permission')
    //         ->where('unique_id', $uniqueId)
    //         ->pluck('screen_unique_id')
    //         ->toArray();

    //     return view('userpermission.edit', compact('user_permission', 'user_types', 'main_screens', 'user_screens', 'selected_screens'));
    // }

    // public function edit($uniqueIds)
    // {
    //     $user_permission = user_permission::findOrFail($unique_id);
    //     $user_screens = DB::table('user_screens')->get(); // Fetch all user screens

    //     // Get selected screens for the specific user permission
    //     $selected_screens = DB::table('user_screen_permission')
    //         ->where('unique_id', $unique_id)
    //         ->pluck('screen_unique_id') // Get selected screen IDs
    //         ->toArray();

    //     return view('content.user_permission.edit', compact('user_permission', 'user_screens', 'selected_screens'));
    // }

    // public function edit($unique_id)
    // {
    //     // Fetch the user permission by unique ID
    //     $user_permission = DB::table('user_screen_permission')
    //         ->join('user_types', 'user_screen_permission.user_type', '=', 'user_types.unique_id')
    //         ->join('main_user_screens', 'user_screen_permission.main_screen_id', '=', 'main_user_screens.unique_id')
    //         ->join('user_screens', 'user_screen_permission.screen_unique_id', '=', 'user_screens.unique_id')
    //         ->join('user_screen_actions', 'user_screen_permission.action_unique_id', '=', 'user_screen_actions.unique_id')
    //         ->select(
    //             'user_screen_permission.unique_id',
    //             'user_screen_permission.*', // Get all fields from user_screen_permission
    //             'user_types.user_type as user_type',
    //             'main_user_screens.screen_name as main_screen',
    //             'user_screens.screen_name as sub_screen',
    //             DB::raw('GROUP_CONCAT(DISTINCT user_screen_actions.action_name ORDER BY user_screen_actions.action_name ASC SEPARATOR ", ") as actions')
    //         )
    //         ->where('user_screen_permission.is_delete', 0)
    //         ->where('user_screen_permission.is_active', 1)
    //         ->where('user_screen_permission.unique_id', $unique_id)
    //         ->first(); // Use first() to get a single record


    //     // Check if the user permission exists
    //     if (!$user_permission) {
    //         return redirect()->route('userpermission.index')->with('error', 'User permission not found.');
    //     }

    //     return view('content.user_permission.edit', compact('user_permission')); // Pass the permission data to the edit view
    // }

    // }
    // public function edit($uniqueIds)
    // {
    //     //

    //     $user_permission = user_permission::findOrFail($uniqueIds);

    //     return view('content.userpermission.edit', compact('user_permission'));
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user_permission $user_permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_permission $user_permission)
    {
        //
    }
}