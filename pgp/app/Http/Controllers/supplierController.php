<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class supplierController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $supplier = Supplier::where('is_delete', 0)->get();

            return DataTables::of($supplier)
                ->addIndexColumn()
                ->addColumn(
                    'action',

                    function ($row) {
                        $editUrl = route('supplier.edit', $row->unique_id);
                        $btn = '<a href="' . $editUrl . '" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a> &nbsp;';
                        $btn .= '<a href="#" class="text-danger" title="Delete" onclick="confirmDelete(\'' . $row->unique_id . '\')" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    }
                )
                ->rawColumns(['action']) // Needed to render HTML
                ->make(true);
        }

        return view('content.supplier.list');
        // No need to pass $unit, it's loaded via AJAX
    }

    // Display the form to add a new center

    public function add()
    {
        return view('content.supplier.form');
    }

    // Handle form submission and store the data

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact' => 'required',
            'email' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'pincode' => 'required|string|max:255',
            'address' => 'required|string|max:100',
            'gstin' => 'required|string|size:40|alpha_num',
            'pan_no' => 'required|string|size:15|alpha_num',
        ]);

        // Save the validated data to the database

        Supplier::create([
            'unique_id' => unique_id(),
            'supplier_id' => supplier_id(),
            'supplier_name' => $request->supplier_name,
            'contact_person' => $request->contact_person,
            'contact' => $request->contact,
            'email' => $request->email,
            'gstin' => $request->gstin,
            'pan_no' => $request->pan_no,
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'ifsc_code' => $request->ifsc_code,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'delivery_address' => $request->delivery_address,
            'delivery_pincode' => $request->delivery_pin,
            'is_active' => $request->status

        ]);

        // Return a JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully!',
        ]);
    }

    public function edit($id)
    {
        $supplier_id = Supplier::where('unique_id', $id)
            ->where('is_delete', 0)
            ->first();

        if (!$supplier_id) {
            return redirect()->route('supplier')->with('error', 'Supplier not found.');
        }

        return view('content.supplier.form', compact('supplier_id'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact' => 'required',
            'email' => 'required|string|max:255',
            'gstin' => 'required|string|size:15|alpha_num',
            'pan_no' => 'required|string|size:15|alpha_num',
            'bank_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'address' => 'required|string|max:100',
            'pincode' => 'required|string|max:255',

        ]);

        $Supplier = Supplier::where('unique_id', $id);

        $Supplier->update([
            'supplier_name' => $request->supplier_name,
            'contact_person' => $request->contact_person,
            'contact' => $request->contact,
            'email' => $request->email,
            'gstin' => $request->gstin,
            'pan_no' => $request->pan_no,
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'ifsc_code' => $request->ifsc_code,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'delivery_address' => $request->delivery_address,
            'delivery_pincode' => $request->delivery_pin,
            'is_active' => $request->status
        ]);

        return response()->json(['Supplier Updated Sucessfully', 'Supplier' => $Supplier]);
    }

    public function destroy($id)
    {
        $Supplier = Supplier::where('unique_id', $id)->first();
        // get actual model

        if (!$Supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ]);
        }

        // Soft delete by setting is_delete flag
        $Supplier->is_delete = 1;
        $Supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}


function supplier_id()
{
    $lastSupplier = Supplier::where('is_delete', 0)
        ->select('supplier_id')
        ->orderByDesc('id')
        ->first();

    // ❌ No spaces in regex pattern
    if ($lastSupplier && preg_match('/SUP(\d+)/', $lastSupplier->supplier_id, $matches)) {
        $nextId = (int) $matches[1] + 1;
    } else {
        $nextId = 1;
    }

    return 'SUP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}
