<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class escalationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $q = DB::table('escalation as e')
                ->join('department as d', 'd.unique_id', '=', 'e.department')
                ->where('e.is_delete', 0)
                ->select([
                    'e.unique_id as escalation_id',
                    'd.department as department_name',
                    'e.stage_count',
                    'e.is_active',
                ]);

            // ✅ apply filter from Go
            if ($request->filled('department')) {
                $q->where('e.department', $request->get('department'));
            }

            return DataTables::of($q)
                ->addIndexColumn()
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">ACTIVE</span>'
                        : '<span class="badge bg-secondary">INACTIVE</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('escalation.edit', $row->escalation_id);
                    return
                        '<a href="'.$editUrl.'" class="text-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;'.
                        '<button type="button" class="btn btn-link p-0 text-danger" title="Delete" onclick="confirmDelete(\''.$row->escalation_id.'\')"><i class="fas fa-trash-alt"></i></button>';
                })
                ->rawColumns(['is_active','action'])
                ->make(true);
        }

        $departments = DB::table('department')->where('is_delete',0)->orderBy('department')->get(['unique_id','department']);
        return view('content.escalation.list', compact('departments'));
    }

    public function add()
    {
        return view('content.escalation.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department'   => 'required|string|max:255',
            'stage_count'  => 'required|int',
            'is_active'    => 'required|int',
        ]);

        try {
            DB::statement("CALL sp_manage_escalation(?, ?, ?, ?, ?, ?)", [
                'INSERT',
                NULL,
                unique_id(),
                $request->department,
                $request->stage_count,
                $request->is_active ?? 1
            ]);

            return response()->json(['success' => true, 'message' => 'Escalation created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function edit($id)
    {
        $escalation = DB::table('escalation')
            ->where('is_delete', 0)
            ->where('unique_id', $id)
            ->first();

        if (!$escalation) {
            return redirect()->back()->with('error', 'Escalation not found.');
        }

        return view('content.escalation.form', compact('escalation'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department'   => ['required','string','max:255'],
            'stage_count'  => ['required','integer','min:1'],
            'is_active'    => ['required','in:0,1'],
        ]);

        try {
            $escalation = DB::table('escalation')->where('unique_id', $id)->first();
            if (!$escalation) {
                return response()->json(['success' => false, 'message' => 'Escalation not found.'], 404);
            }

            DB::statement('CALL pgp.sp_manage_escalation(?, ?, ?, ?, ?, ?)', [
                'UPDATE',
                $escalation->id,
                $id,
                $request->department,
                (int) $request->stage_count,
                (int) $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Escalation updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $row = DB::table('escalation')->where('unique_id', $id)->first();
            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Escalation not found.'], 404);
            }

            DB::statement('CALL pgp.sp_manage_escalation(?, ?, ?, ?, ?, ?)', [
                'DELETE',
                $row->id,
                $id,
                null,
                null,
                0
            ]);

            return response()->json(['success' => true, 'message' => 'Escalation deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
