<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpareCreation;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SpareCreationController extends Controller
{
    // Show list page
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = DB::table('spare_creation as sc')
            //     ->leftJoin('spare_category as c', 'sc.spare_category', '=', 'c.unique_id')
            //     ->leftJoin('unit as u', 'sc.unit', '=', 'u.unique_id')
            //     ->select([
            //         'sc.id',
            //         'sc.spare_id',
            //         'sc.spare_name',
            //         'c.category_name as spare_category',
            //         'u.unit_name as unit',
            //         'sc.description',
            //         'sc.unique_id',
            //         'sc.is_active',
            //     ])
            //     ->where('sc.is_delete', 0)
            //     ->orderByDesc('sc.id')
            //     ->get();

            $data = DB::table('spare_creation as sc')
                ->select([
                    'sc.id',
                    'sc.spare_id',
                    'sc.spare_name',
                    DB::raw('get_spare_category_by_id(sc.spare_category) as spare_category'),
                    DB::raw('get_unit_by_id(sc.unit) as unit'),
                    'sc.unique_id',
                    'sc.is_active',
                ])
                ->where('sc.is_delete', 0)
                ->orderByDesc('sc.id')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', fn($row) => $row->is_active ? 'Active' : 'Inactive')
                ->addColumn('action', function ($row) {
                    $editUrl = route('spare_creation.edit', $row->unique_id);
                    return '
                    <a href="' . $editUrl . '"><i class="fas fa-edit text-primary"></i></a>
                    <a href="javascript:void(0);" onclick="deleteSpare(\'' . $row->unique_id . '\')">
                        <i class="fas fa-trash-alt text-danger ms-2"></i>
                    </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.spare_creation.list');
    }


    // Show the form for creating a new spare
    public function add()
    {
        return view('content.spare_creation.form');
    }

    // Store new spare
    public function store(Request $request)
    {
        $request->validate([
            'spare_group' => 'required|string|max:255',
            'spare_name' => 'required|string|max:255',
            'spare_category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        SpareCreation::create([
            'unique_id' => Str::random(18),
            'spare_id' => $this->generateSpareId(),
            'spare_name' => $request->spare_name,
            'spare_group' => $request->spare_group,
            'spare_category' => $request->spare_category,
            'unit' => $request->unit,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'is_delete' => 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Spare created successfully.']);
    }

    // Edit spare form
    public function edit($id)
    {
        $spare = SpareCreation::where('unique_id', $id)->where('is_delete', 0)->firstOrFail();

        // Convert comma-separated string to array
        $spare->compatible_product_ids = explode(',', $spare->compatible_product_ids);

        return view('content.spare_creation.form', compact('spare'));
    }

    // Update existing spare
    public function update(Request $request, $id)
    {
        $request->validate([
            'spare_name' => 'required|string|max:255',
            'spare_category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'is_active' => 'required|in:0,1',
        ]);

        $spare = SpareCreation::findOrFail($id);

        $spare->update([
            'spare_name' => $request->spare_name,
            'spare_category' => $request->spare_category,

            'unit' => $request->unit,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return response()->json(['success' => true, 'message' => 'Spare updated successfully.']);
    }

    public function destroy($id)
    {
        $spare = SpareCreation::where('unique_id', $id)->first();

        if (!$spare) {
            return response()->json(['success' => false, 'message' => 'Spare not found.']);
        }

        $spare->update(['is_delete' => 1]);

        return response()->json(['success' => true, 'message' => 'Spare deleted successfully.']);
    }

    public function getSpareCategoriesByGroup(Request $request)
    {
        $group_id = $request->input('group_id');

        $categories = DB::table('spare_category')
            ->where('group_id', $group_id)
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->orderBy('category_name')
            ->get();

        $options = '<option value="">Select Spare Category</option>';
        foreach ($categories as $category) {
            $options .= '<option value="' . $category->unique_id . '">' . $category->category_name . '</option>';
        }

        return response()->json(['options' => $options]);
    }


    // Helper function to generate spare_id
    // private function generateSpareId()
    // {
    //     $datePrefix = Carbon::now()->format('Ymd');
    //     $countToday = SpareCreation::where('created_at', Carbon::today())->count();
    //     $increment = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    //     return 'SP-' . $datePrefix . '-' . $increment;
    // }

    function generateSpareId()
    {
        // Get the latest non-deleted category
        $lastCategory = SpareCreation::where('is_delete', 0)
            ->select('spare_id')
            ->orderByDesc('id') // Or orderByDesc( 'category_id' ) if numeric
            ->first();
        // Corrected regex pattern: no spaces!
        if ($lastCategory && preg_match('/SPR-(\d+)/', $lastCategory->spare_id, $matches)) {
            $nextId = (int) $matches[1] + 1;
        } else {
            $nextId = 1;
        }

        // Format the next ID: CATG0001, CATG0002, etc.
        return 'SPR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }


    //     function category_id()
// {
//     // Get the latest non-deleted category
//     $lastCategory = spareCategory::where('is_delete', 0)
//         ->select('category_id')
//         ->orderByDesc('id') // Or orderByDesc( 'category_id' ) if numeric
//         ->first();
//     // Corrected regex pattern: no spaces!
//     if ($lastCategory && preg_match('/SP-CAT-(\d+)/', $lastCategory->category_id, $matches)) {
//         $nextId = (int) $matches[1] + 1;
//     } else {
//         $nextId = 1;
//     }

    //     // Format the next ID: CATG0001, CATG0002, etc.
//     return 'SP-CATG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
// }

}
