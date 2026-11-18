<?php

namespace App\Http\Controllers;

use App\Models\ZoneCreation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Yajra\DataTables\DataTables; // Make sure this import is also correct

class newComplaintsController extends Controller
{

   public function index()
{
    if (request()->ajax()) {
        $query = DB::table('new_complaints as nc')
            ->leftJoin('zone as z', 'z.unique_id', '=', 'nc.zone')
            ->leftJoin('ward as w', 'w.unique_id', '=', 'nc.ward')
            ->leftJoin('location as l', 'l.unique_id', '=', 'nc.location')
            ->leftJoin('source_name as sn', 'sn.unique_id', '=', 'nc.source_name')
            ->select([
                'nc.unique_id',
                DB::raw("CONCAT(nc.grievance_prefix, nc.grievance_id) as grievance_full_id"),
                'nc.created_at',
                'nc.name',
                'nc.address1',
                'nc.mobile_no',
                'z.zone as zone_name',
                'w.ward as ward_name',
                'l.location as location_name',
                'sn.source_name as source_name'
            ])
            ->where('nc.is_delete', 0)
            ->where('nc.status', 0)
            ->orderBy('nc.created_at', 'desc');

        // ✅ Apply filters
        if (request()->filled('from_date') && request()->filled('to_date')) {
            $query->whereBetween(DB::raw('DATE(nc.created_at)'), [request('from_date'), request('to_date')]);
        }

        if (request()->filled('zone')) {
            $query->where('nc.zone', request('zone'));
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addColumn('grievance_col', function ($row) {
                return '
                    <div style="font-weight:bold;font-size:16px;">' . e($row->grievance_full_id) . '</div>
                    <div style="font-size:14px;">' . e(date('d-m-Y', strtotime($row->created_at))) . '</div>
                ';
            })
            ->addColumn('name_col', function ($row) {
                return '
                    <div style="font-weight:bold;font-size:16px;">' . e($row->name) . '</div>
                    <div style="font-size:14px;">' . e($row->address1) . '</div>
                ';
            })
            ->addColumn('zone_col', function ($row) {
                return '
                    <div style="font-weight:bold;font-size:16px;">' . e($row->zone_name) . '</div>
                    <div style="font-size:14px;">' . e($row->ward_name) . '</div>
                ';
            })
            ->addColumn('action', function ($row) {
                return '<a href="#" class="text-danger" onclick="confirmDelete(\'' . $row->unique_id . '\')"><i class="fas fa-trash-alt"></i></a>';
            })
            ->rawColumns(['grievance_col', 'name_col', 'zone_col', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('content.new_complaints.list');
}

public function index1(Request $request)
{
    if ($request->ajax()) {
        $query = DB::table('new_complaints as nc')
            ->leftJoin('zone as z', 'z.unique_id', '=', 'nc.zone')
            ->leftJoin('ward as w', 'w.unique_id', '=', 'nc.ward')
            ->leftJoin('location as l', 'l.unique_id', '=', 'nc.location')
            ->leftJoin('source_name as sn', 'sn.unique_id', '=', 'nc.source_name')
            ->where('nc.status', '2')   // completed complaints
            ->where('nc.is_delete', 0)
            ->orderBy('nc.created_at', 'desc');

        // ✅ Apply Month Filter (if provided)
        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('nc.created_at', $request->month);
        }

        // ✅ Apply Zone Filter (if provided)
        if ($request->has('zone') && !empty($request->zone)) {
            $query->where('nc.zone', $request->zone);
        }

        $query->select([
            'nc.unique_id',
            DB::raw("CONCAT(nc.grievance_prefix, nc.grievance_id) as grievance_full_id"),
            'nc.created_at',
            'nc.name',
            'nc.address1',
            'nc.mobile_no',
            'z.zone as zone_name',
            'w.ward as ward_name',
            'l.location as location_name',
            'sn.source_name',
        ]);

        return DataTables::of($query)
            ->addColumn('grievance_col_1', function ($row) {
                return '<div style="font-weight:bold;font-size:16px;">' . e($row->grievance_full_id) . '</div>
                        <div style="font-size:14px;">' . (date('d-m-Y', strtotime($row->created_at))) . '</div>';
            })
            ->addColumn('name_col_1', function ($row) {
                return '<div style="font-weight:bold;font-size:16px;">' . e($row->name) . '</div>
                        <div style="font-size:14px;">' . e($row->address1) . '</div>';
            })
            ->addColumn('zone_col_1', function ($row) {
                return '<div style="font-weight:bold;font-size:16px;">' . e($row->zone_name) . '</div>
                        <div style="font-size:14px;">' . e($row->ward_name) . '</div>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="#" class="text-danger" onclick="confirmDelete(\'' . $row->unique_id . '\')"><i class="fas fa-trash-alt"></i></a>';
            })
            ->rawColumns(['grievance_col_1', 'name_col_1', 'zone_col_1', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('content.new_complaints.list');
}




    public function add()
    {
        return view('content.new_complaints.form');
    }



    // public function store(Request $request)
// {
//     try {
//         // Validation
//         $request->validate([
//             'name'                 => 'required|string|max:255',
//             'mobile_no'            => 'required|string|max:20',
//             'email_id'             => 'nullable|string|email|max:100',
//             'address1'             => 'nullable|string|max:255',
//             'zone'                 => 'nullable|string|max:100',
//             'ward'                 => 'nullable',
//             'location'             => 'nullable|string|max:100',
//             'door_no'              => 'nullable|string|max:100',
//             'address2'             => 'nullable|string|max:255',
//             'source_name'          => 'nullable|string|max:100',
//             'department'           => 'nullable|string|max:100',
//             'department_category'  => 'nullable|string|max:100',
//             'priority'             => 'nullable|string|max:100',
//             'attachments'          => 'nullable|string|max:255',
//             'complaint_eng'        => 'nullable|string|max:255',
//             'complaint_tam'        => 'nullable|string|max:255',
//             'is_active'            => 'nullable|boolean',
//         ]);

    //         // Generate unique_id
//         $unique_id = unique_id();

    //         // Convert ward to string if it's an array
//         $ward = is_array($request->ward) ? implode(',', $request->ward) : $request->ward;

    //         // Generate grievance_id
//         $prefix = 'COM-';
//         $yearMonth = now()->format('Ym');
//         $last = DB::table('new_complaints')
//             ->where('grievance_id', 'like', $prefix . $yearMonth . '%')
//             ->orderBy('grievance_id', 'desc')
//             ->first();

    //         if ($last) {
//             $lastNumber = (int) substr($last->grievance_id, -6);
//             $nextNumber = $lastNumber + 1;
//         } else {
//             $nextNumber = 1;
//         }

    //         $grievance_id = $prefix . $yearMonth . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

    //         // Call the stored procedure
//         DB::statement("CALL new_complaints(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
//             'INSERT',
//             null,
//             $unique_id,
//             $request->name,
//             $grievance_id,        // <-- use generated grievance_id
//             $request->mobile_no,
//             $request->email_id,
//             $request->address1,
//             $request->zone,
//             $ward,
//             $request->location,
//             $request->door_no,
//             $request->address2,
//             $request->source_name,
//             $request->department,
//             $request->department_category,
//             $request->priority,
//             $request->attachments,
//             $request->complaint_eng,
//             $request->complaint_tam,
//             $request->is_active ?? 1
//         ]);

    //         return response()->json([
//             'success' => true,
//             'message' => 'Complaint created successfully.',
//             'grievance_id' => $grievance_id
//         ]);

    //     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 422);
//     }
// }


   public function store(Request $request)
{
    try {
        // Step 1: Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:20',
            'email_id' => 'nullable|string|email|max:100',
            'address1' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:100',
            'ward' => 'nullable',
            'location' => 'nullable',
            'door_no' => 'nullable|string|max:100',
            'address2' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:100',
            'department_category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|max:100',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,xlsx,xls|max:5120',
            'complaint_eng' => 'nullable|string|max:500',
            'complaint_tam' => 'nullable|string|max:500',
        ]);

        // Step 2: Generate unique_id (same as PHP helper)
        $unique_id = unique_id();
 
       $prefix = 'TGN-'; $yearMonth = now()->format('ym'); // e.g. 2509 
       $last = DB::table('new_complaints') 
       ->where('grievance_prefix', $prefix)
       ->where('grievance_id', 'like', $yearMonth . '%') 
       ->orderBy('grievance_id', 'desc') ->first(); 
       if ($last) {
           $lastNumber = intval(substr($last->grievance_id, 4));
           $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
           } 
           else {
               $newNumber = "00001"; 
               
           } $grievance_id = $yearMonth . $newNumber; $refNo = $prefix . $grievance_id;

        // Step 4: Handle attachments
        $uploadedFiles = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $complaintLast4 = substr($grievance_id, -4);
                $filename = $complaintLast4 . '-' . ($index + 1) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/new_complaints'), $filename);
                $uploadedFiles[] = 'uploads/new_complaints/' . $filename;
            }
        }
        $attachments = !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null;

        // Step 5: Department allocation
        $allocation = DB::table('department_allocation_sub')
            ->where('department', $request->department)
            ->where('department_category', $request->department_category)
            ->where('zone', $request->zone)
            ->where('ward', $request->ward)
            ->where('stage', '68b7e95b9129423796')
            ->where('is_delete', 0)
            ->first();

        $assign_staff = $allocation->staff_name ?? null;
        $escalation_day = $allocation->days ?? null;
        $stage = $allocation->stage ?? null;

        // Step 6: Store complaint
        DB::table('new_complaints')->insert([
            'unique_id' => $unique_id,
            'grievance_id' => $grievance_id,
            'grievance_prefix' => $prefix,
            'name' => $request->name,
            'mobile_no' => $request->mobile_no,
            'email_id' => $request->email_id,
            'address1' => $request->address1,
            'door_no' => $request->door_no,
            'zone' => $request->zone,
            'ward' => $request->ward,
            'location' => $request->location,
            'address2' => $request->address2,
            'source_name' => $request->source_name,
            'department' => $request->department,
            'department_category' => $request->department_category,
            'priority' => $request->priority,
            'complaint_eng' => $request->complaint_eng,
            'complaint_tam' => $request->complaint_tam,
            'attachments' => $attachments,
            'assign_staff' => $assign_staff,
            'escalation_day' => $escalation_day,
            'stage' => $stage,
            'source_status' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        /*
        |--------------------------------------------------------------------------
        | Step 7: SMS Notifications (same as PHP)
        |--------------------------------------------------------------------------
        */
        $apiKey = "x40mp6mzDES5Ai0bMm59Yg";
        $sid = "PGTSCL";
        $url = "https://cloud.smsindiahub.in/vendorsms/pushsms.aspx";
        $dlt_entity_id = "1001821773487954916";

        if ($assign_staff) {
    $staff = DB::table('staff_registration')
        ->where('unique_id', $assign_staff)
        ->first();

    if ($staff) {
        $staffMobile = $staff->mobile_no ?? null;
        $staffName = $staff->name ?? null;

        // ✅ Step 2: Find matching name in staff_registration_sub
        $staffSub = DB::table('staff_registration_sub')
            ->where('staff_name', $staffName)
            ->first();

        if ($staffSub && $staffSub->department) {
            $departmentUniqueId = $staffSub->department;
            // dd($departmentUniqueId);

            // ✅ Step 3: Get department name from department_allocation
            $dept = DB::table('department')
                ->where('unique_id', $departmentUniqueId)
                ->first();

            if ($dept) {
                $staffDepartmentName = $dept->department ?? null;
            }
            // dd($staffDepartmentName);
        }
    }
}
        // Citizen SMS
        $citizenMsg = "Your Complaint refno $refNo has been forwarded to $staffMobile. If not resolved please contact. Tiruppur City Municipal Corporation TCMC";
        $citizenTemplateId = "1007157894429878024";

        Http::withoutVerifying()->asForm()->post($url, [
            'APIKey' => $apiKey,
            'msisdn' => $request->mobile_no,
            'sid' => $sid,
            'msg' => $citizenMsg,
            'fl' => 0,
            'gwid' => 2,
            'dlt_entityid' => $dlt_entity_id,
            'dlt_templateid' => $citizenTemplateId
        ]);
// dd($assign_staff, $staffMobile);

        // Officer SMS
      if ($staffMobile) {
    $deptText = $staffDepartmentName ?? 'your department'; // fallback text

    $officerMsg = "A new complaint with refno $refNo has been assigned to you regarding $deptText. Contact number {$request->mobile_no} - Tiruppur City Municipal Corporation TCMC";
    $officerTemplateId = "1007318363221524065";

    $response = Http::withoutVerifying()->asForm()->post($url, [
        'APIKey' => $apiKey,
        'msisdn' => $staffMobile,
        'sid' => $sid,
        'msg' => $officerMsg,
        'fl' => 0,
        'gwid' => 2,
        'dlt_entityid' => $dlt_entity_id,
        'dlt_templateid' => $officerTemplateId
    ]);

    // Optional: log for debugging
    \Log::info('Officer SMS Sent', [
        'to' => $staffMobile,
        'dept' => $deptText,
        'response' => $response->body()
    ]);
}


        return response()->json([
            'success' => true,
            'message' => 'Complaint created successfully and SMS sent.',
            'grievance_id' => $refNo
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
        $zone = ZoneCreation::where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$zone) {
            return redirect()->back()->with('error', 'zone not found.');
        }

        return view('content.zone.form', compact('zone'));
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
        $zone = ZoneCreation::where('unique_id', $id)->first();
        // get actual model

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $zone->is_delete = 1;
        $zone->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
   public function getNextGrievanceId()
{
    $prefix = 'TGN-';
    $yearMonth = now()->format('ym'); // e.g., 2510

    // Get last grievance_id overall
    $last = DB::table('new_complaints')
        ->where('grievance_prefix', $prefix)
        ->orderBy('id', 'desc') // use numeric ID to get last row
        ->first();

    if ($last) {
        // Take last 5 digits only (sequence)
        $lastNumber = (int) substr($last->grievance_id, -5);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $nextNumberPadded = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    $grievanceId = $prefix . $yearMonth . $nextNumberPadded;

    return response()->json([
        'grievance_id' => $grievanceId
    ]);
}




}
