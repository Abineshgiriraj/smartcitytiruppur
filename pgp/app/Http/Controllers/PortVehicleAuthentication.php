<?php

namespace App\Http\Controllers;

use App\Models\PortVehicle;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\PortVehicleDocument;
use Illuminate\Support\Facades\DB;

class PortVehicleAuthentication extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            // dd($status);
            $port_vehicle_registration = DB::table('port_vehicle_registration')
                ->join('cargo_creation', 'port_vehicle_registration.vehicle_type', '=', 'cargo_creation.unique_id')
                ->select(
                    'port_vehicle_registration.id',
                    'port_vehicle_registration.unique_id',
                    'entry_date',
                    'vech_reg_no',
                    'cargo_creation.cargo as vehicle_type',
                    'vehicle_id',
                    'contact_number',
                    'status',
                    'screenUniqueId'
                )
                ->where('port_vehicle_registration.is_delete', 0);

            if ($request->has('from_date') && $request->from_date != '') {
                $port_vehicle_registration->where('port_vehicle_registration.entry_date', '>=', $request->from_date);
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $port_vehicle_registration->where('port_vehicle_registration.entry_date', '<=', $request->to_date);
            }


            if ($request->has('vech_reg_no') && $request->vech_reg_no != '') {
                $port_vehicle_registration->where('port_vehicle_registration.vech_reg_no', 'like', '%' . $request->vech_reg_no . '%');
            }
            if ($request->has('status') && $request->status != '') {
                $port_vehicle_registration->where('port_vehicle_registration.status', $request->status);
            } else {
                $port_vehicle_registration->where('port_vehicle_registration.status', 0);
            }

            $port_vehicle_registrations = $port_vehicle_registration->get();
            return DataTables::of($port_vehicle_registrations)
                ->addIndexColumn()
                // ->addColumn('action', function ($row) {
                //     return '<button class="btn btn-primary" onclick="window.location.href=\'' . route('port_vehicle_authentication_add', ['id' => $row->id, 'screenUniqueId' => $row->screenUniqueId]) . '\'">Approval</button>';
                // })
                ->addColumn('action', function ($row) {
                    // return '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('employee_authentication_add', ['id' => $row->screenUniqueId]) . '\'"><i class="i-Add"></i><span class="lab_add">Pending</span></button>';
                    if ($row->status == 0) {
                        return '<button class="btn btn-primary btn-right btn-small" onclick="window.location.href=\'' . route('port_vehicle_authentication_add', ['id' => $row->id, 'screenUniqueId' => $row->screenUniqueId]) . '\'"><i class="i-Add"></i><span class="lab_add">Pending</span></button>';
                    } elseif ($row->status == 1) {
                        return '<span class="text text-success">Approved&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $row->screenUniqueId . '\')"></i></span>';
                    } else {
                        return '<span class="text text-danger">Rejected&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $row->screenUniqueId . '\')"></i></span>';
                    }
                })
                ->make(true);
        }

        return view('content.port_vehicle_authentication.index');
    }

    public function add()
    {
        return view('content.port_vehicle_authentication.create');
    }
    public function view($id, $screenUniqueId)
    {

        $portVehicle = PortVehicle::where("port_vehicle_registration.is_delete", 0)
            ->join("cargo_creation", "port_vehicle_registration.vehicle_type", "=", "cargo_creation.unique_id")
            ->select("port_vehicle_registration.*", "cargo_creation.cargo as cargo")
            ->findOrFail($id);

        $portVehicle_sub = PortVehicleDocument::where('screenUniqueId', $screenUniqueId)->where('is_delete', 0)->get();

        return view('content.port_vehicle_authentication.create', compact('portVehicle', 'portVehicle_sub'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'approve' => 'required|integer',
            'user_id' => 'required|string'
        ]);

        $portVehicle = PortVehicle::where('screenUniqueId', $id)->first();

        $vehicle_split = substr($portVehicle->app_prefix_id, 2);

        $lastFirm = PortVehicle::where('approval_id_prefix', 'like', "$vehicle_split%")
            ->orderBy('approval_id_prefix', 'desc')
            ->first();





        $lastFirmNumber = $lastFirm ? (int) ($lastFirm->approval_id) : 0;

        $newapproval_Id = sprintf("%05d", $lastFirmNumber + 1);




        if ($request->approve == '1') {


            $portVehicle->update([
                'status' => $request->approve,
                'approval_id_prefix' => $vehicle_split,
                'approval_id' => $newapproval_Id,
                'reject_reason' => null,
                'approved_date' => now(),
                'approved_by' => $request->user_id,
            ]);
            return response()->json(['status' => 'User updated successfully']);
        } else if ($request->approve == '2') {


            $portVehicle->update([
                'status' => $request->approve,
                'approval_id_prefix' => null,
                'approval_id' => null,
                'reject_reason' => $request->reject_reason,
                'approved_date' => now(),
                'approved_by' => $request->user_id,
            ]);
            return response()->json(['status' => 'User updated successfully']);
        } else {


            $portVehicle->update([
                'status' => $request->approve,
                'approval_id_prefix' => null,
                'approval_id' => null,
                'reject_reason' => null,
                'approved_date' => now(),
                'approved_by' => $request->user_id,
            ]);
            return response()->json(['status' => 'User updated successfully']);
        }





        // return redirect()->route('port_vehicle_authentication')->with('success', 'Status updated successfully');
    }


    public function getData(Request $request)
    {
        $query = PortVehicle::where('is_delete', 0);

        if (($request->has('from_date') && !empty($request->from_date)) && $request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', $request->from_date);
        }

        if (($request->has('to_date') && !empty($request->to_date)) && ($request->has('from_date') && !empty($request->from_date))) {
            $query->whereDate('created_at', '<=', $request->to_date);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', $request->to_date);
        }


        if ($request->has('vech_reg_no') && $request->vech_reg_no != '') {
            $query->where('port_vehicle_registration.vech_reg_no', 'like', '%' . $request->vech_reg_no . '%');
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('port_vehicle_registration.status', $request->status);
        }



        $portVehicle = $query->get();

        return DataTables::of($portVehicle)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('created_at', function ($portVehicle) {
                // Format the 'created_at' date to 'YYYY-MM-DD'
                return $portVehicle->created_at->format('Y-m-d');
            })
            ->addColumn('action', function ($portVehicle) {
                return
                    '<a href="' . route('port_vehicle_authentication.view', ['id' => $portVehicle->id, 'screenUniqueId' => $portVehicle->screenUniqueId]) . '" class="text-primary"> <button class="btn btn-primary btn-right btn-small" type="button">Approve</button></a>';
            })
            ->make(true);
    }


    public function documents()
    {
        return $this->hasMany(PortVehicleDocument::class, 'screenUniqueId', 'screenUniqueId');
    }

    public function popup($unique_id)
    {

        // dd($unique_id);
        $portVehicle = PortVehicle::where("port_vehicle_registration.is_delete", 0)
            ->join("cargo_creation", "port_vehicle_registration.vehicle_type", "=", "cargo_creation.unique_id")
            ->join('user_creation', 'port_vehicle_registration.approved_by', '=', 'user_creation.unique_id')
            ->leftjoin('user_types', 'user_creation.user_type', '=', 'user_types.unique_id')
            ->select("port_vehicle_registration.*", "cargo_creation.cargo as cargo", 'user_creation.staff_name', 'user_types.user_type')
            ->where('port_vehicle_registration.screenUniqueId',$unique_id )
            ->first();
            // ->findOrFail($unique_id);
            // dd($portVehicle);
            
        $portVehicle_sub = PortVehicleDocument::where('port_vehicle_registration_sub.screenUniqueId', $portVehicle->screenUniqueId)->where('port_vehicle_registration_sub.is_delete', 0)->get();
        // dd($portVehicle_sub);
            
        // return view('content.port_vehicle_authentication.create', compact('portVehicle', 'portVehicle_sub'));

        return response()->json(['portVehicle' => $portVehicle, 'portVehicle_sub' => $portVehicle_sub]);
    }


}