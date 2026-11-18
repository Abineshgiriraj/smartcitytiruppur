<?php

namespace App\Http\Controllers;
use App\Models\Portvehicleauthenticationmodel;
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
                ->select('port_vehicle_registration.id', 'port_vehicle_registration.unique_id', 'entry_date', 'vech_reg_no', 'vehicle_type',
                         'vehicle_id', 'contact_number','status', 'screenUniqueId')
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
            }else{
                $port_vehicle_registration->where('port_vehicle_registration.status', 0);
            }

            $port_vehicle_registrations = $port_vehicle_registration->get();
            return DataTables::of($port_vehicle_registrations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-primary" onclick="window.location.href=\'' . route('port_vehicle_authentication_add', ['id' => $row->id,'screenUniqueId' => $row->screenUniqueId]) . '\'">Approval</button>';
                })
                ->make(true);
        }
    
        return view('content.port_vehicle_authentication.index');
    }
    
    public function add()
    {
        return view('content.port_vehicle_authentication.create');
    }
    public function view($id,$screenUniqueId)
    {
     
        $portVehicle = Portvehicleauthenticationmodel::findOrFail($id);

        $portVehicle_sub = PortVehicleDocument::where('screenUniqueId', $screenUniqueId)->get();

        return view('content.port_vehicle_authentication.create', compact('portVehicle', 'portVehicle_sub'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'approve' => 'required|integer',
        'user_id' => 'required|string'
    ]);

    $portVehicle = Portvehicleauthenticationmodel::findOrFail($id);

    $portVehicle->update([
        'status' => $request->approve,
        'reject_reason' => $request->reject_reason,
        'status_upd_date' => now(),
        'approved_by' => $request->user_id,
    ]);
    return response()->json(['status' => 'User updated successfully']);
    // return redirect()->route('port_vehicle_authentication')->with('success', 'Status updated successfully');
}


public function getData(Request $request)
{
    $query = Portvehicleauthenticationmodel::where('is_delete', 0);

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



}
