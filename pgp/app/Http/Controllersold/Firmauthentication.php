<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FirmsAuthentication;
use App\Models\FirmsRegistrationSub;
use Yajra\DataTables\DataTables;





class Firmauthentication extends Controller
{
    public function index(Request $request)
    {
        $firmauth = FirmsAuthentication::where('is_delete', 0)->get();

        return view('content.firm_authentication.index', compact('firmauth'));
    }
    public function add()
    {
        return view('content.firm_authentication.create');
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'approve' => 'required|integer',
        ]);

        $firmauth = FirmsAuthentication::findOrFail($id);


        $firmauth->update([
            'status' => $request->approve,
            'reject_reason' => $request->reject_reason,
            'approved_by' => $request->approved_by,
            'status_upd_date' => now(),

        ]);

        return response()->json(['status' => 'User updated successfully']);
    }

    public function view($id, $unique_id)
    {
        $firmauth = FirmsAuthentication::findOrFail($id);
        $firmauth_sub = FirmsRegistrationSub::where('form_unique_id', $unique_id)->get();

        return view('content.firm_authentication.create', compact('firmauth', 'firmauth_sub'));
    }
    public function getData(Request $request)
    {
        // dd($request->status);
        $query = FirmsAuthentication::where('firms_registration.is_delete', 0)
            ->join('company_creation', 'firms_registration.firms', '=', 'company_creation.unique_id')
            ->select('firms_registration.id', 'firms_registration.unique_id', 'firms_registration.firm_id', 'company_creation.company_name', 'firms_registration.company_reg_no', 'firms_registration.entry_date', 'firms_registration.name', 'firms_registration.mobile', 'firms_registration.gstin', 'firms_registration.status');


        if (($request->has('from_date') && !empty($request->from_date)) && $request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('entry_date', $request->from_date);
        }

        if (($request->has('to_date') && !empty($request->to_date)) && ($request->has('from_date') && !empty($request->from_date))) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('entry_date', $request->to_date);
        }

        if ($request->has('firm_type') && !empty($request->firm_type)) {
            $query->where('firms', $request->firm_type);
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', $request->name);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('firms_registration.status', $request->status);
        } 
        // else {
        //     $query->where('firms_registration.status', 0);
        // }

        $firmauth = $query->get();

        return DataTables::of($firmauth)
            ->addIndexColumn() // Automatically adds the serial number
            // ->addColumn('created_at', function ($firmsauth) {
            //     // Format the 'created_at' date to 'YYYY-MM-DD'
            //     return $firmsauth->created_at->format('Y-m-d');
            // })
            ->addColumn('action', function ($firmsauth) {
                return
                    '<a href="' . route('firmauth.view', ['id' => $firmsauth->id, 'unique_id' => $firmsauth->id]) . '" class="text-primary"> <button class="btn btn-primary btn-right btn-small" type="button">Approve</button></a>';
            })
            ->make(true);
    }
}