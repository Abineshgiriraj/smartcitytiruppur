<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables; // Make sure this import is also correct

class departmentAllocationController extends Controller
{

public function index()
{
    if (request()->ajax()) {

        $data = DB::table('department_allocation as da')
            ->leftJoin('staff_registration as sr', 'sr.unique_id', '=', 'da.staff_name')
            ->leftJoin('department as d', function ($join) {
                $join->whereRaw("FIND_IN_SET(d.unique_id, da.department)");
            })
            ->leftJoin('designation as des', function ($join) {
                $join->whereRaw("FIND_IN_SET(des.unique_id, da.designation)");
            })
            ->select([
                'da.unique_id',
                'sr.name as name',
                'da.mobile_no',
                DB::raw('GROUP_CONCAT(DISTINCT d.department ORDER BY d.department SEPARATOR ", ") as department'),
                DB::raw('GROUP_CONCAT(DISTINCT des.designation ORDER BY des.designation SEPARATOR ", ") as designation'),
                'da.is_active',
            ])
            ->where('da.is_delete', 0)
            ->groupBy('da.unique_id', 'sr.name', 'da.mobile_no', 'da.is_active')
            ->get();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $editUrl = route('department_allocation.edit', $row->unique_id);
                $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('content.department_allocation.list'); 
}





    public function add()
    {
        $screen_unique_id = Str::uuid()->toString();
        return view('content.department_allocation.form', compact('screen_unique_id'));
    }



public function store1(Request $request)
{
    try {
        $request->validate([
            'screen_unique_id' => 'required|string|max:255',
            'staff_name'       => 'required|string|max:255',
            'mobile_no'        => 'required|string|max:20',
            'designation'      => 'required',
            'department'       => 'required',
        ]);
// dd($request);
     $departments = is_array($request->department)
    ? $request->department
    : explode(',', $request->department);

$designations = is_array($request->designation)
    ? $request->designation
    : explode(',', $request->designation);

        DB::statement("CALL department_allocation(?, ?, ?, ?, ?, ?, ?, ?, ?)", [
    'INSERT',                       // p_action
    null,                           // p_id
    unique_id(),                    // p_unique_id
    $request->screen_unique_id,     // p_screen_unique_id
    $request->staff_name,           // p_staff_name
    $request->mobile_no,            // p_mobile_no
    implode(',', $departments),     // p_department
    implode(',', $designations),    // p_designation
    $request->is_active ?? 1        // p_is_active
]);


        return response()->json([
            'success' => true,
            'message' => 'Department allocation created successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}






public function edit($id)
{
    // ✅ Fetch main allocation record
    $department_allocation = DB::table('department_allocation')
        ->where('unique_id', $id)
        ->first();

    if (!$department_allocation) {
        abort(404, 'Department Allocation not found');
    }

    // ✅ Fetch all sub records linked to this screen_unique_id
    $department_allocation_sub = DB::table('department_allocation_sub')
        ->where('is_delete', 0)
        ->where('screen_unique_id', $department_allocation->unique_id)
        ->get();

    // ✅ Fetch staff, zone, department, etc. (same helpers you use in Blade)
    $get_staff_name   = get_staff_name();
    $get_dept_name    = get_dept_name();
    $get_stage_name   = get_stage();
    $get_dept_category = get_dept_category();
    $get_zone_name    = get_zone();
    $get_ward_name    = get_ward();

    // ✅ Pass everything to the view
    return view('content.department_allocation.edit', [
        'screen_unique_id'         => $department_allocation->unique_id,
        'department_allocation'    => $department_allocation,
        'department_allocation_sub'=> $department_allocation_sub,
        'get_staff_name'           => $get_staff_name,
        'get_dept_name'            => $get_dept_name,
        'get_stage_name'           => $get_stage_name,
        'get_dept_category'        => $get_dept_category,
        'get_zone_name'            => $get_zone_name,
        'get_ward_name'            => $get_ward_name,
    ]);
}






    public function update(Request $request, zone $zone, $id)
    {

        // Validate the input data
        $request->validate([
            'zone' => 'required|string',
            'description' => 'string',
        ]);



        $zone = ZoneCreation::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        $zone->update([
            'zone' => $request->zone,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Zone updated successfully.', 'zone' => $zone]);
    }

   public function destroy($id)
{
    try {
        $user_registration = DB::table('department_allocation')->where('unique_id', $id)->first();
        if (!$user_registration) {
            return response()->json(['success' => false, 'message' => 'Staff not found.'], 404);
        }

        // Call stored procedure with exactly 9 parameters
        DB::statement("CALL department_allocation(?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            'DELETE',                // p_action
            $user_registration->id,  // p_id
            $id,                     // p_unique_id
            '',                      // param 4
            '',                      // param 5
            '',                      // param 6
            '',                      // param 7
            '',                      // param 8
            0                        // p_is_active (param 9)
        ]);

        return response()->json(['success' => true, 'message' => 'Staff deleted successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
}


    public function getUserProfile($staffId)
{
    $user = DB::table('staff_registration as sr')
        ->leftJoin('staff_registration_sub as srs', 'sr.screen_unique_id', '=', 'srs.screen_unique_id')
        ->leftJoin('department as d', 'd.unique_id', '=', 'srs.department')
        ->leftJoin('designation as des', 'des.unique_id', '=', 'srs.designation')
        ->select(
            'sr.name',
            'sr.mobile_no',
            'sr.email_id',
            DB::raw('GROUP_CONCAT(DISTINCT d.unique_id ORDER BY d.department SEPARATOR ",") as department_ids'),
            DB::raw('GROUP_CONCAT(DISTINCT des.unique_id ORDER BY des.designation SEPARATOR ",") as designation_ids'),
            DB::raw('GROUP_CONCAT(DISTINCT d.department ORDER BY d.department SEPARATOR ",") as departments'),
            DB::raw('GROUP_CONCAT(DISTINCT des.designation ORDER BY des.designation SEPARATOR ",") as designations')
        )
        ->where('sr.unique_id', $staffId)
        ->where('sr.is_delete', 0)
        ->where(function ($q) {
            $q->whereNull('srs.is_delete')
              ->orWhere('srs.is_delete', 0);
        })
        ->groupBy('sr.unique_id', 'sr.name', 'sr.mobile_no', 'sr.email_id')
        ->first();
// dd($user);
    if ($user) {
        // Convert CSV into arrays for frontend
        $user->department_ids   = $user->department_ids   ? explode(',', $user->department_ids)   : [];
        $user->designation_ids  = $user->designation_ids  ? explode(',', $user->designation_ids)  : [];
        $user->departments      = $user->departments      ? explode(',', $user->departments)      : [];
        $user->designations     = $user->designations     ? explode(',', $user->designations)     : [];

        return response()->json(['success' => true, 'data' => $user]);
    }

    return response()->json(['success' => false, 'message' => 'User not found']);
}


    public function getDepartmentCategories($departmentId)
    {
        $categories = DB::table('department_category')
            ->where('department', $departmentId) // assuming you have a foreign key `department_id`
            ->select('unique_id', 'department_category')
            ->get();

        return response()->json($categories);
    }
    public function getWardsByZone($zoneId)
    {
        $wards = DB::table('ward')
            ->where('zone', $zoneId) // assuming you have a foreign key `department_id`
            ->select('unique_id', 'ward')
            ->get();

        return response()->json($wards);
    }
public function store(Request $request)
{
    $request->validate([
        'screen_unique_id'     => 'required|string|max:255',
        'staff_name'           => 'required|string|max:255',
        'department'           => 'required|string|max:255',
        'stage'                => 'required|string|max:255',
        'department_category'  => 'required|string|max:255',
        'zone'                 => 'required|string|max:255',
        'ward'                 => 'required|array',
        'ward.*'               => 'string|max:255',
        'days'                 => 'required|string|max:255',
        'is_active'            => 'required|in:0,1',   // expect only 0 or 1
    ]);
// dd($request);
    try {
        foreach ($request->ward as $ward) {
            DB::statement("CALL department_allocation_sub(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->screen_unique_id,
                $request->staff_name,
                $request->department,
                $request->stage,
                $request->department_category,
                $request->zone,
                $ward,
                $request->days,
                (int) $request->is_active
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Department allocation created successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function convertTableData(Request $request)
{
     $staffId = $request->input('staff_name');
    $departmentId = $request->input('department');
    $stageId = $request->input('stage');
    $categoryId = $request->input('department_category');
    $zoneId = $request->input('zone');
    $wardIds = $request->input('ward', []);
    $days = $request->input('days');


    // Convert each ID to its corresponding name
    $department = DB::table('department')
        ->where('unique_id', $departmentId)
        ->value('department');
    $staff_name = DB::table('staff_registration')
        ->where('unique_id', $staffId)
        ->value('name');

    $stage = DB::table('stage')
        ->where('unique_id', $stageId)
        ->value('stage');

    $departmentCategory = DB::table('department_category')
        ->where('unique_id', $categoryId)
        ->value('department_category');

    $zone = DB::table('zone')
        ->where('unique_id', $zoneId)
        ->value('zone');

    $wards = DB::table('ward')
        ->whereIn('unique_id', (array)$wardIds)
        ->pluck('ward')
        ->toArray();
    

    return response()->json([
        'name' =>$staff_name,
        'department' => $department,
        'stage' => $stage,
        'department_category' => $departmentCategory,
        'zone' => $zone,
        'wards' => $wards,
        'days' => $days
    ]);
}

public function getStaffData($staffId)
{
    // Step 1: Get screen_unique_id from staff_registration
    $staff = DB::table('staff_registration')
        ->where('unique_id', $staffId)
        ->where('is_delete', 0)
        ->first();

    if (!$staff) {
        return response()->json(['success' => false, 'message' => 'Staff not found']);
    }

    $screenUniqueId = $staff->screen_unique_id;

    // Step 2: Query staff_registration_sub using screen_unique_id
    $staffData = DB::table('staff_registration_sub as srs')
        ->join('department as d', 'd.unique_id', '=', 'srs.department')
        ->join('department_category as dc', 'dc.unique_id', '=', 'srs.department_category')
        ->select(
            'srs.screen_unique_id',
            'd.unique_id as department_id',
            'd.department as department_name',
            'dc.unique_id as department_category_id',
            'dc.department_category as department_category_name',
        )
        ->where('srs.screen_unique_id', $screenUniqueId)
        ->where('srs.is_delete', 0)
        ->get();
        // dd($staffData);

    if ($staffData->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No staff data found']);
    }

    return response()->json(['success' => true, 'data' => $staffData]);
}






}
