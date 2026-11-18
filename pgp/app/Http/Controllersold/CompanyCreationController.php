<?php

namespace App\Http\Controllers;

use App\Models\CompanyCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CompanyCreationController extends Controller
 {
    // Display a listing of the companies.

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $company = DB::table('company_creation')
                ->select('id', 'company_name', 'description', 'status')
                ->where('is_delete', '=', 0)
                ->where('is_active', '=', 1)
                ->get();
    
            return DataTables::of($company)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit_url = route('company_creation.edit', $row->id);
                    $btn = '<a href="' . $edit_url . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="delete-btn text-danger" data-id="' . $row->id . '" title="Delete" style="background:none; margin-left: 10px; border:none; padding:0;">';
                    $btn .= '<i class="fas fa-trash-alt"></i>';
                    $btn .= '</button>';
                    return $btn;
                })
                ->make(true);
        }
        return view('content.company_creation.index');
        // return response()->json(['status' => 'Company Created Successfully']);
    }
    
    //     // $companies = CompanyCreation::where( 'is_delete', 0 )->get();
    //     return view( 'content.company_creation.index', compact( 'companies' ) );
    // }

    // Show the form for creating a new company.

    public function add()
 {
        return view( 'content.company_creation.create' );
    }

    // Store a newly created company in storage.

    public function store( Request $request )
 {
        $request->validate( [
            'company_name' => 'required|string|max:255',
            'status' => 'required|string',
        ] );

        CompanyCreation::create( [
            'unique_id' => unique_id(),
            'company_name' => $request->company_name,
            'description' => $request->description,
            'status' => $request->status,
        ] );

        return redirect( '/company_creation' )->with( 'success', 'Company Created Successfully' );
    }

    // Show the form for editing the specified company.

    public function edit( $id )
 {
        $company = CompanyCreation::findOrFail( $id );
        return view( 'content.company_creation.edit', compact( 'company' ) );
    }

    // Update the specified company in storage.

    //     public function update( Request $request, $id )
    // {
    //         $request->validate( [
    //             'company_name' => 'required|string|max:255',
    //             'status' => 'required|string',
    // ] );

    //         $company = CompanyCreation::findOrFail( $id );

    //         if ( $company ) {
    //             $company->update( [
    //                 'company_name' => $request->company_name,
    //                 'description' => $request->description,
    //                 'status' => $request->status,
    // ] );
    //         }

    //         return redirect( '/company_creation' )->with( 'success', 'Company Updated Successfully' );
    //     }

    public function update( Request $request, $id ) {
        $request->validate( [
            'company_name' => 'required|string|max:255',
            'status' => 'required|string',
        ] );

        $company = CompanyCreation::findOrFail( $id );

        $company->update( [
            'company_name' => $request->company_name,
            'description' => $request->description,
            'status' => $request->status,
        ] );

        return response()->json( [ 'Department Updated Sucessfully', 'company' => $company ] );
    }

    // public function destroy( $id )
    // {

    //     $company = CompanyCreation::findOrFail( $id );
    //     $company->is_delete = 1;
    //     $company->save();

    //     return response()->json( [ 'status' => 'Company Deleted Successfully' ] );
    // }

//     public function destroy($id )
//  {
//         // Find the company by ID
//         $company = CompanyCreation::find( $id );

//         // Check if the company exists
//         if ( !$company ) {
//             return redirect( '/company_creation' )->with( 'error', 'Company not found.' );
//         }

//         // Soft delete by setting is_delete flag
//         $company->is_delete = 1;
//         $company->save();

//         // Redirect back to the list page with a success message
//         return redirect( '/company_creation' )->with( 'success', 'Deleted Successfully' );
//     }
public function destroy($id)
    {
        // dd($id);
        $CompanyCreation = CompanyCreation::find($id);
// dd( $CompanyCreation);
        if ($CompanyCreation) {
            $CompanyCreation->is_delete = 1; // Mark as deleted
            $CompanyCreation->save();

            return response()->json(['success' => true, 'message' => 'Category marked as deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    }

}
