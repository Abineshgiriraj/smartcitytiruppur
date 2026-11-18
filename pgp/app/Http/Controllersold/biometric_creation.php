<?php

namespace App\Http\Controllers;

use App\Models\BiometricCreation;
use App\Models\DistrictCreation;
use App\Models\Taluk;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Yajra\DataTables\DataTables;



class biometric_creation extends Controller
{
    public function index(Request $request)
    {

                        
        if ($request->ajax()) {
            
        $biometric = DB::table('biometric_creation')
                        ->Join('zones','biometric_creation.zone','=','zones.unique_id')
                        ->Join('district','biometric_creation.district','=','district.unique_id')
                        ->Join('taluk','biometric_creation.taluk','=','taluk.unique_id')
                        ->Join('schools','biometric_creation.school_location','=','schools.unique_id')
                        ->select('biometric_creation.id','zones.zone as zone','district.district as district','taluk.taluk_name as taluk','schools.school_name as school','biometric_creation.device_code','biometric_creation.device_name','biometric_creation.serial_no','biometric_creation.ip_details')
                        ->where('biometric_creation.is_delete',0);
        
            return DataTables::of($biometric)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('biometric.edit', $row->id);
                    $deleteUrl = route('biometric.delete', $row->id);
                    $csrfToken = csrf_token();

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="delete btn btn-danger btn-sm" >Delete</button>';
                    $btn .= '</form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('content.biometric_device.list');
    }
    public function add()
    {
        return view('content.biometric_device.form');
    }
    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'zone'             => 'required|string|max:255',
            'district'         => 'required|string|max:200',
            'taluk'            => 'required|string|max:200',
            'school_name'      => 'required|string|max:200',
            'device_code'      => 'required|string|max:200',
            'device_name'      => 'required|string|max:200',
            'serial_no'        => 'required|string|max:200',
            'ip_details'       => 'required|string|max:200',
            'user_name'        => 'required|string|max:100',
            'password'         => 'required|string|max:200',
            'created_date'     => 'required|string|max:100',
            'created_at'       => 'nullable|date',
            'updated_at'       => 'nullable|date',
        ]);


        $exists = BiometricCreation::where('district', $request->district)
            ->where('zone',   $request->zone)
            // ->where('taluk',  $request->taluk)
            // ->where('school_location', $request->school_location)
            ->where('serial_no',  $request->serial_no)
            ->where('ip_details', $request->ip_details)
            ->exists();


        if ($exists) {
            return response()->json(['error' => ' Already exists.'], 422);
        }


        $biometric = BiometricCreation::create([

            'unique_id'        => unique_id(),
            'zone'             => $request->zone,
            'district'         => $request->district,
            'taluk'            => $request->taluk,
            'school_location'  => $request->school_name,
            'device_code'      => $request->device_code,
            'device_name'      => $request->device_name,
            'serial_no'        => $request->serial_no,
            'ip_details'       => $request->ip_details,
            'user_name'        => $request->user_name,
            'password'         => $request->password,
            'created_date'     => $request->created_date,
            'created_at'       => $request->created_at,
            'updated_at'       => $request->updated_at
        ]);

       


        return response()->json(['success' => 'Biometric added successfully!', 'biometric_device' => $biometric]);
    }

    public function edit($id)
    {
        $biometric = BiometricCreation::findOrFail($id);
        return view('content.biometric_device.edit', compact('biometric'));
    }



    public function update(Request $request, $id)
    {

        $request->validate([
            'zone'             => 'required|string|max:255',
            'district'         => 'required|string|max:200',
            'taluk'            => 'required|string|max:200',
            'school_location'  => 'required|string|max:200',
            'device_code'      => 'required|string|max:200',
            'device_name'      => 'required|string|max:200',
            'serial_no'        => 'required|string|max:200',
            'ip_details'       => 'required|string|max:200',
            'user_name'        => 'required|string|max:100',
            'password'         => 'required|string|max:200',
            'created_date'     => 'required|string|max:100',
            'created_at'       => 'nullable|date',
            'updated_at'       => 'nullable|date',
        ]);

        $exists = BiometricCreation::where('district', $request->district)
            ->where('zone',   $request->zone)
            ->where('serial_no',  $request->serial_no)
            ->where('ip_details', $request->ip_details)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => ' Already exists.'], 422);
        }

        $biometric = BiometricCreation::findOrFail($id);

        // $biometric->update([
        //     'zone'             => $request->zone,
        //     'district'         => $request->district,
        //     'taluk'            => $request->taluk,
        //     'school_location'  => $request->school_location,
        //     'device_code'      => $request->device_code,
        //     'device_name'      => $request->device_name,
        //     'serial_no'        => $request->serial_no,
        //     'ip_details'       => $request->ip_details,
        //     'user_name'        => $request->user_name,
        //     'password'         => $request->password,
        //     'created_date'     => $request->created_date,
        //     'created_at'       => $request->created_at,
        //     'updated_at'       => now(), // Set the updated_at to the current time
        // ]);

        $biometric->zone             = $request->zone;
        $biometric->district         = $request->district;
        $biometric->taluk            = $request->taluk;
        $biometric->school_location  = $request->school_name;
        $biometric->device_code      = $request->device_code;
        $biometric->device_name      = $request->device_name;
        $biometric->serial_no        = $request->serial_no;
        $biometric->ip_details       = $request->ip_details;
        $biometric->user_name        = $request->user_name;
        $biometric->password         = $request->password;
        $biometric->created_date     = $request->created_date;
        $biometric->created_at       = $request->created_at;
        $biometric->updated_at       = $request->now();

        $biometric->save();


        return redirect()->route('biometric_list')->with('success', 'Biometric updated successfully');
    }

    public function destroy($id)
    { {
            $biometric = BiometricCreation::find($id);
            $biometric->is_delete = 1;
            $biometric->save();

            return redirect()->route('biometric_list')->with('Success', 'Deleted Successfully');
        }
    }

    public function getDistricts()
    {
        $districts = DistrictCreation::where('is_delete', 0)->distinct()->get(['district']);
        return response()->json($districts);
    }

    public function zone_district(Request $request)
    {

        $zone = $request->query('zone_id');

        $district = DistrictCreation::where('zone', $zone)->where('is_delete', 0)->get();


        return response()->json(['district' => $district]);
    }

    public function taluk(Request $request)
    {

        $district = $request->query('district_id');

        $taluk = Taluk::where('district_name', $district)->where('is_delete', 0)->get();

        return response()->json(['taluk' => $taluk]);
    }

    public function school(Request $request)
    {
        $taluk = $request->query('taluk_id');

        $school = School::where('taluk_name', $taluk)->where('is_delete', 0)->get();

        return response()->json(['school' => $school]);
    }
}