<?php
namespace App\Http\Controllers;

use App\Models\ProductCreation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class productSpecificationType extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
    $query = DB::table('product_creation as pc')
        ->select([
            'pc.id',
            'pc.product_id',
            'pc.product_name',
            DB::raw('get_category_by_id(pc.product_category) as product_category'),
            DB::raw('get_group_name_by_id(pc.product_group) as group_name'),
            DB::raw('get_unit_by_id(pc.unit) as unit'),
            'pc.hsn_code',
            'pc.warranty_period',
            'pc.description',
            'pc.unique_id',
            'pc.is_active',
        ])
        ->where('pc.is_delete', 0);

    // Filter based on tab
    if ($request->type == 'completed') {
        $query->where('pc.is_active', 1); // example condition
    } else {
        $query->where('pc.is_active', 0); // example condition
    }

    $data = $query->get();

    return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('status', fn($row) => $row->is_active ? 'Active' : 'Inactive')
        ->addColumn('action', function ($row) use ($request) {
            if ($request->type == 'completed') {
                return '<a href="' . route('product_creation.view', $row->unique_id) . '">
                            <i class="fas fa-eye text-info"></i>
                        </a>';
            } else {
                return '
                    <a href="' . route('product_creation.edit', $row->unique_id) . '">
                        <i class="fas fa-edit text-primary"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deleteProduct(\'' . $row->unique_id . '\')">
                        <i class="fas fa-trash-alt text-danger ms-2"></i>
                    </a>';
            }
        })
        ->rawColumns(['action'])
        ->make(true);
}
        return view('content.product_specification_type.list');
    }

    public function add()
    {
        return view('content.product_specification_type.form');
    }

    public function edit()
{
    return view('content.cleaning.form'); // adjust path as needed
}
 public function view()
    {
       
        return view('content.cleaning.view');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_category' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        // $count = ProductCreation::count() + 1;
        // $product_id = date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
// dd($request);
        ProductCreation::create([
            'unique_id' => unique_id(),
            'product_id' => product_id(),
            'product_name' => $request->product_name,
            'product_category' => $request->product_category,
            'product_group' => $request->product_group,
            'unit' => $request->unit,
            'hsn_code' => $request->hsn_code,
            'warranty_period' => $request->warranty_period,
            'description' => $request->description,
            'is_active' => $request->status == 'active' ? 1 : 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Product created successfully!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_category' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $product = ProductCreation::findOrFail($id);

        $product->update([
            'product_name' => $request->product_name,
            'product_category' => $request->product_category,
            'product_group' => $request->product_group,
            'unit' => $request->unit,
            'hsn_code' => $request->hsn_code,
            'warranty_period' => $request->warranty_period,
            'description' => $request->description,
            'is_active' => $request->status == 'active' ? 1 : 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
    }

    public function destroy($id)
    {
        $product = ProductCreation::where('unique_id', $id)->firstOrFail();
        // dd($id);
        $product->update(['is_delete' => 1]);
        return response()->json(['success' => true, 'message' => 'Product deleted successfully!']);
    }

    public function getCategoriesByGroup(Request $request)
    {
        $group_id = $request->input('group_id');

        $categories = DB::table('category')
            ->where('group_id', $group_id)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('category_name')
            ->get();

        $options = '<option value="">Select Product Category</option>';
        foreach ($categories as $category) {
            $options .= '<option value="' . $category->unique_id . '">' . $category->category_name . '</option>';
        }

        return response()->json(['options' => $options]);
    }
}



function product_id()
{
    // Get the latest non-deleted category
    $lastCategory = ProductCreation::where('is_delete', 0)
        ->select('product_id')
        ->orderByDesc('id') // Or orderByDesc( 'category_id' ) if numeric
        ->first();
    // Corrected regex pattern: no spaces!
    if ($lastCategory && preg_match('/PRD-(\d+)/', $lastCategory->product_id, $matches)) {
        $nextId = (int) $matches[1] + 1;
    } else {
        $nextId = 1;
    }

    // Format the next ID: CATG0001, CATG0002, etc.
    return 'PRD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}