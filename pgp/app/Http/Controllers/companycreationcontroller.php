<?php

namespace App\Http\Controllers;

use App\Models\CompanyCreation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class companycreationcontroller extends Controller {

    public function index( Request $request ) {
        if ( $request->ajax() ) {
            $company = CompanyCreation::where( 'is_delete', 0 )->get();

            return DataTables::of( $company )
            ->addIndexColumn()
            ->editColumn('is_active', function ($row) {
        if ($row->is_active == 1) {
            return '<span style="color:green">Active</span>';
        } else {
            return '<span style="color:red">Inactive</span>';
        }
    })
            ->addColumn(
                'action',

                function ( $row ) {
                    $editUrl = route( 'company_creation.edit', $row->unique_id );
                    $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                    $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                }
            )
            ->rawColumns( [ 'action','is_active' ] ) // Needed to render HTML
            ->make( true );
        }
        return view( 'content.company_creation.list' );
    }

    // Display the form to add a new Location

    public function add() {
        return view( 'content.company_creation.form' );
    }

    // Handle form submission and store the data

    public function store( Request $request ) {
        $request->validate( [
            'company_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required',
            'gst' => 'required',
            'email' => 'required|email',
            'status' => 'required',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ] );

        // 🟡 If trying to make the new one active ( status = 1 ), check if an active one already exists
        if ( $request->status == 1 ) {
            $existingActive = CompanyCreation::where( 'is_active', 1 )->first();
            // if ( $existingActive ) {
            //     // Make the existing active one inactive
            //     $existingActive->update( [ 'is_active' => 0 ] );
            // }
            if ( $existingActive ) {
                return response()->json( [
                    'success' => false,
                    'error_type' => 'active_exists',
                    'message' => 'Only one active company is allowed. Please deactivate the existing one first.',
                ], 422 );
            }
        }

        $logoName = null;
        $shortLogoName = null;

        if ( $request->hasFile( 'logo' ) ) {
            $logo = $request->file( 'logo' );
            $logoName = time() . '-' . $logo->getClientOriginalName();
            $logo->move( public_path( 'uploads/company_creation/logo' ), $logoName );
        }

        if ( $request->hasFile( 'short_logo' ) ) {
            $shortLogo = $request->file( 'short_logo' );
            $shortLogoName = time() . '-' . $shortLogo->getClientOriginalName();
            $shortLogo->move( public_path( 'uploads/company_creation/short_logo' ), $shortLogoName );
        }

        // dd( $request->status );

        CompanyCreation::create( [
            'unique_id' => unique_id(),
            'company_name' => $request->company_name,
            'branch_name' => $request->branch,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst,
            'email' => $request->email,
            'contact_name' => $request->contact_person,
            'contact_no' => $request->contact_no,
            'created' => $request->created,
            'pan' => $request->pan,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->branch,
            'acc_no' => $request->acc_no,
            'ifsc' => $request->ifsc,
            'web' => $request->web,
            'is_active' => $request->status,
            'logo' => $logoName,
            'short_logo' => $shortLogoName,
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'Company created successfully!',
        ] );
    }

    public function edit( $id ) {
        $company = CompanyCreation::where( 'unique_id', $id )
        ->where( 'is_delete', 0 )
        ->first();

        if ( !$company ) {
            return redirect()->route( 'company_creation' )->with( 'error', 'Company not found.' );
        }

        return view( 'content.company_creation.form', compact( 'company' ) );
    }

    public function update( Request $request, $id ) {
        $request->validate( [
            'company_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required',
            'gst' => 'required',
            'status' => 'required',
            'email' => 'required|email',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ] );

        $Company = CompanyCreation::where( 'unique_id', $id )->first();

        if ( !$Company ) {
            return response()->json( [
                'success' => false,
                'message' => 'Company not found.',
            ], 404 );
        }
        $logoPath = null;
        $shortLogoPath = null;

        if ( $request->status == 1 ) {
            $existingActive = CompanyCreation::where( 'is_active', 1 )
            ->where( 'unique_id', '!=', $id ) // Exclude current record from the check
            ->first();

            if ( $existingActive ) {
                return response()->json( [
                    'success' => false,
                    'error_type' => 'active_exists',
                    'message' => 'Only one active company is allowed. Please deactivate the existing one first.',
                ], 422 );
            }
        }

        // Store logo
        if ( $request->hasFile( 'logo' ) ) {
            $logo = $request->file( 'logo' );
            $logoName = time() . '-' . $logo->getClientOriginalName();
            $logo->move( public_path( 'uploads/company_creation/logo' ), $logoName );
            $logoPath = 'uploads/company_creation/logo/' . $logoName;
        }
        // Store short logo
        if ( $request->hasFile( 'short_logo' ) ) {
            $shortLogo = $request->file( 'short_logo' );
            $shortLogoName = time() . '-' . $shortLogo->getClientOriginalName();
            $shortLogo->move( public_path( 'uploads/company_creation/short_logo' ), $shortLogoName );
            $shortLogoPath = 'uploads/company_creation/short_logo/' . $shortLogoName;
        }
        $Company->update( [
            'company_name' => $request->company_name,
            'branch_name' => $request->branch,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst,
            'email' => $request->email,
            'contact_name' => $request->contact_person,
            'contact_no' => $request->contact_no,
            'created' => $request->created,
            'pan' => $request->pan,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->branch,
            'acc_no' => $request->acc_no,
            'ifsc' => $request->ifsc,
            'is_active' => $request->status,
            'web' => $request->web,
            'logo' => $logoName,
            'short_logo' => $shortLogoName,
        ] );

        return response()->json( [
            'success' => true,
            'message' => 'Company updated successfully!',
            'branch'  => $Company,
        ] );
    }

    public function destroy( $id ) {
        $branch = Branch::where( 'unique_id', $id )->first();
        // get actual model
        if ( !$branch ) {
            return response()->json( [
                'success' => false,
                'message' => 'Company not found.'
            ] );
        }

        // Soft delete by setting is_delete flag
        $branch->is_delete = 1;
        $branch->save();

        return response()->json( [
            'success' => true,
            'message' => 'Deleted successfully!'
        ] );
    }
}
