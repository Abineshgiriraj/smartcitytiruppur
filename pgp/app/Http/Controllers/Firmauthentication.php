<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FirmsRegistrationSub;
use App\Models\FirmsRegistration;
use Yajra\DataTables\DataTables;
use DB;

class Firmauthentication extends Controller
{
    public function index(Request $request)
    {
        $firmauth = FirmsRegistration::where('is_delete', 0)->get();

        return view('content.firm_authentication.index', compact('firmauth'));
    }
    public function add()
    {
        return view('content.firm_authentication.create');
    }

    public function update(Request $request, $unique_id)
    {

        // dd($unique_id);
        $request->validate([

            'approve' => 'required|integer',
            'approved_by' => 'required',
            'verification' => 'required|integer',

        ]);


        $firmauth_update = FirmsRegistration::where('unique_id', $unique_id)->first();

        if (!$firmauth_update) {
            return response()->json(['error' => 'FirmsRegistration not found'], 404);
        }
        $firmauth = FirmsRegistration::where('unique_id', $unique_id)->first();

        $firm_split = substr($firmauth->app_id_prefix, 2);

        $lastFirm = FirmsRegistration::where('firm_id_prefix', 'like', "$firm_split%")
            ->orderBy('firm_id_prefix', 'desc')
            ->first();

        $lastFirmNumber = $lastFirm ? (int) substr($lastFirm->firm_id, -5) : 0;
        $newFirmId = sprintf("%05d", $lastFirmNumber + 1);


        if ($request->approve == '1') {

            $firmauth_update->update([
                'status' => $request->approve,
                'reject_reason' => null,
                'approved_by' => $request->approved_by,
                'firm_id_prefix' => $firm_split,
                'firm_id' => $newFirmId,
                'approval_date' => now(),
                'verification' => $request->verification,

            ]);
            // dd($firmauth_update);



            return response()->json(['status' => 'User updated successfully']);
        } else if ($request->approve == '2') {

            $firmauth_update->update([
                'status' => $request->approve,
                'reject_reason' => $request->reject_reason,
                'approved_by' => $request->approved_by,
                'firm_id_prefix' => null,
                'firm_id' => null,
                'approval_date' => now(),

            ]);
            
        } else {

            $firmauth_update->update([
                'status' => $request->approve,
                'reject_reason' => null,
                'firm_id_prefix' => null,
                'firm_id' => null,
                'approved_by' => $request->approved_by,
                'approval_date' => now(),

            ]);
        }
    }


    public function view($unique_id)
    {
        $firmauth = DB::table('firms_registration')
            ->where('unique_id', $unique_id)
            ->where('is_delete', 0)
            ->select('firms_registration.*')
            ->first();

        $alreadyfirm = DB::table('firms_registration')
            ->where('gstin', $firmauth->gstin)
            ->where('is_delete', 0)
            ->select('firms_registration.*')
            ->where('status', 2)
            ->first();

        // dd($alreadyfirm);

        if ($alreadyfirm) {
            $rejectData = 'Already Rejected Firms or Agent';
        } else {
            $rejectData = '';
        }


        return view('content.firm_authentication.create', compact('firmauth', 'rejectData'));
    }

    public function getData(Request $request)
    {
        // dd($request->status);
        $query = FirmsRegistration::where('firms_registration.is_delete', 0)
            ->join('company_creation', 'firms_registration.firms', '=', 'company_creation.unique_id')
            ->select('firms_registration.id', 'firms_registration.unique_id', 'firms_registration.firm_id_prefix', 'firms_registration.firm_id', 'company_creation.company_name', 'firms_registration.company_reg_no', DB::raw("DATE_FORMAT(firms_registration.entry_date,'%d-%m-%Y')as entry_date"), 'firms_registration.name', 'firms_registration.mobile', 'firms_registration.gstin', 'firms_registration.status', 'firms_registration.app_id_prefix', 'firms_registration.app_id');


        if ((!empty($request->from_date)) && !empty($request->to_date)) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('entry_date', $request->from_date);
        }

        if ((!empty($request->to_date)) && !empty($request->from_date)) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        } elseif (!empty($request->to_date)) {
            $query->whereDate('entry_date', $request->to_date);
        }

        if (!empty($request->firm_type)) {
            $query->where('firms', $request->firm_type);
        }

        if (!empty($request->name)) {
            $query->where('name', $request->name);
        }

        if (!empty($request->status)) {
            $query->where('firms_registration.status', $request->status);
        } else {
            $query->where('firms_registration.status', 0);
        }

        $firmauth = $query->orderBy('firms_registration.id', 'desc')->get();

        return DataTables::of($firmauth)
            ->addIndexColumn() // Automatically adds the serial number
            ->addColumn('action', function ($firmsauth) {
                if ($firmsauth->status == 0) {
                    return '<a href="' . route('firmauth.view', ['unique_id' => $firmsauth->unique_id]) . '" class="text-primary">
                        <button class="btn btn-primary btn-right btn-small" type="button">Pending</button>
                    </a>';
                } else if ($firmsauth->status == 1) {
                    return '<span class="text-success fw-bold">Approved&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $firmsauth->unique_id . '\')"></i>
                    </span>';
                } else {
                    return '<span class="text-danger fw-bold">Rejected&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fas fa-eye" style="color: #5859ae; cursor: pointer;" onclick="getPopValue(\'' . $firmsauth->unique_id . '\')"></i></span>'; // Show status as "Rejected"
                }
            })

            ->make(true);
    }

    // public function popupContent()
    // {
    //     // You can return dynamic content here
    //     return view('popup'); // Assuming 'popup.blade.php' is the view file
    // }
    public function popupContent($unique_id)
    {

        $firmauth = DB::table('firms_registration')
            ->where('unique_id', $unique_id)
            ->where('is_delete', 0)
            ->select('firms_registration.*')
            ->first();

        return response()->json(['firm_auth' => $firmauth]);
        // dd($firmauth);
        // return view('content.firm_authentication.index', compact('firmauth'));

    }

    public function getPopupContent($unique_id)
    {
        $firmauth = FirmsRegistration::where('unique_id', $unique_id)->first();

        if ($firmauth) {
            // Render the modal content using a view or directly return as HTML
            $html = view('content.firm_authentication.index', compact('firmauth'))->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'firmauth' => $firmauth
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Firm not found!'
        ], 404);
    }

    public function loadPopupContent($unique_id)
    {
        $firmAuth = FirmsRegistration::where('unique_id', $unique_id)->first(); // Fetch the record based on unique_id
        // dd($firmAuth);
        if (!$firmAuth) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return view('content.firm_authentication.pop', compact('firmAuth'));
    }

    public function popup($unique_id)
    {
        $firmauth = DB::table('firms_registration')
            ->where('firms_registration.unique_id', $unique_id)
            ->join('company_creation', 'firms_registration.firms', '=', 'company_creation.unique_id')
            ->join('user_creation', 'firms_registration.approved_by', '=', 'user_creation.unique_id')
            ->leftjoin('user_types', 'user_creation.user_type', '=', 'user_types.unique_id')
            ->where('firms_registration.is_delete', 0)
            // ->join('')
            ->select('firms_registration.*', 'company_creation.company_name as company_name', 'user_creation.staff_name', 'user_types.user_type')
            ->first();

        if (!$firmauth) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // dd($firmauth);   

        return response()->json(['firmauth' => $firmauth]);
    }
}
