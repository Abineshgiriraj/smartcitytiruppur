<?php

namespace App\Http\Controllers;

use App\Models\user_permission;
use Illuminate\Http\Request;
use App\Models\user_screen;
use App\Models\user_type;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;


class UserPermissionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
          
            $user_permission = DB::table('user_screen_permission')
            ->join('user_types', 'user_screen_permission.user_type', '=', 'user_types.unique_id')
            ->select(
                'user_screen_permission.user_type as userType_uniqueId',
                'user_types.user_type as user_type'
            )
            ->groupBy('user_screen_permission.user_type','user_types.user_type')
            ->get();

            return DataTables::of($user_permission)
            ->addIndexColumn()
            ->addColumn('action', function ($user_permissions) {
                $uniqueIds = $user_permissions->userType_uniqueId; // Use the concatenated unique_ids
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

    public function getdata(Request $request)
    {
        $main_screen_id         = $request->main_screen;
        $user_type =               $request->user_type;

        
        $screen_unique_id_count = DB::table('user_screen_permission')->where([['main_screen_id', $main_screen_id],['user_type', $user_type]])->select('screen_unique_id')->groupBy('screen_unique_id')->get();

        $action_unique = DB::table('user_screen_permission')->where([['main_screen_id', $main_screen_id],['user_type', $user_type]])->select('action_unique_id')->get();

        
        $screen_unique_ids = DB::table('user_screen_permission')->where([['main_screen_id', $main_screen_id],['user_type', $user_type]])->select('screen_unique_id')->select('screen_unique_id')->get();



        $action_unique_ids = DB::table('user_screen_permission')->where('main_screen_id', $main_screen_id)->select('action_unique_id')->get();



        $action_unique_id_count = DB::table('user_screen_permission')->where('main_screen_id', $main_screen_id)->select('action_unique_id')->count();


        $demo = user_permission_ui_update($action_unique,$main_screen_id,$user_type,$screen_unique_id_count,$action_unique_id_count,$action_unique_id_count,$action_unique_ids,$screen_unique_ids);


        return \Response::json($demo);
    }

    /**        // $demo = user_permission_ui($main_screen_id, $user_type);

     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.user_permission.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_type' => 'string|max:255',
    //         'permissions' => 'required|array',
    //         'permissions.*.user_screen_id' => 'string|max:255',
    //         'permissions.*.action_id' => 'string|max:255',
    //         'permissions.*.main_screen_id' => 'string|max:255',
    //         'created_at' => 'nullable|date',
    //         'updated_at' => 'nullable|date',
    //     ]);

    //     $storedPermissions = [];

    //     foreach ($request->permissions as $permission) {
    //         $user_permission = user_permission::create([
    //             'unique_id' => unique_id(),
    //             'user_type'  => $request->user_type,
    //             'main_screen_id' => $permission['main_screen_id'],
    //             'screen_unique_id' => $permission['user_screen_id'],
    //             'action_unique_id' => $permission['action_id'],
    //             'created_at'        => $request->created_at ?? now(),
    //             'updated_at'        => $request->updated_at ?? now(),
    //         ]);

    //         $storedPermissions[] = $user_permission;
    //     }

    //     return response()->json(['success' => 'Permissions stored successfully', 'user_permissions' => $storedPermissions]);
    // }
public function store(Request $request)
{
    try {
        $request->validate([
            'user_type' => 'required|string|max:255',
            'main_screen' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*.user_screen_id' => 'required|string|max:255',
            'permissions.*.action_id' => 'required|string|max:255',
            'permissions.*.main_screen_id' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'error'   => true,
            'message' => $e->validator->errors()->first() // just the first error
        ], 200); // return 200 so JS won’t treat it as network error
    }

    $storedPermissions = [];

    foreach ($request->permissions as $permission) {
        $user_permission = user_permission::create([
            'unique_id'        => unique_id(),
            'user_type'        => $request->user_type,
            'main_screen_id'   => $permission['main_screen_id'],
            'screen_unique_id' => $permission['user_screen_id'],
            'action_unique_id' => $permission['action_id'],
            'created_at'       => $request->created_at ?? now(),
            'updated_at'       => $request->updated_at ?? now(),
        ]);

        $storedPermissions[] = $user_permission;
    }

    return response()->json([
        'success' => 'Permissions stored successfully',
        'user_permissions' => $storedPermissions
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    public function edit($uniqueIds)
    {
        $user_permission = user_permission::where('user_type',$uniqueIds)->first();

        return view('content.user_permission.edit', compact('user_permission'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, user_permission $user_permission)
{
    $validatedData = $request->validate([
        'user_type' => 'required',
        'main_screen' => 'required',
        'permissions' => 'nullable|array',
    ]);

    $permissions = $validatedData['permissions'] ?? [];

    $existingPermissions = [];
    foreach ($permissions as $permission) {
        $existingPermissions[] = [
            'screen_unique_id' => $permission['screen'],
            'action_unique_id' => $permission['action'],
        ];

        // Update or create the permissions
        user_permission::updateOrCreate(
            [
                'user_type' => $validatedData['user_type'],
                'main_screen_id' => $validatedData['main_screen'],
                'screen_unique_id' => $permission['screen'],
                'action_unique_id' => $permission['action'],
            ],
            [
                'unique_id' => unique_id(),
                'main_screen_id' => $permission['main'],
                'section_id' => $permission['section'],
                'screen_unique_id' => $permission['screen'],
                'action_unique_id' => $permission['action'],
            ]
        );
    }

    // Find and delete permissions that are no longer in the request
    user_permission::where('user_type', $validatedData['user_type'])
        ->where('main_screen_id', $validatedData['main_screen'])
        ->whereNotIn(DB::raw('CONCAT(screen_unique_id, ":", action_unique_id)'), 
            array_map(function($perm) {
                return $perm['screen_unique_id'] . ':' . $perm['action_unique_id'];
            }, $existingPermissions)
        )
        ->delete();

    return response()->json(['success' => 'Permissions updated successfully']);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_permission $user_permission)
    {
        //
    }
}