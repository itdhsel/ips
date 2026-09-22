<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Consulting;

class ConsultingController extends Controller
{
    public function index()
    {
        // List of wards for the dropdown
        $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
        
        return view('counselling.index', compact('wards'));
    }

    public function store(Request $request)
    {
        DB::table('consulting')->insert([
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
            'ward' => $request->ward,
            'bed' => $request->bed,
            'patient_name' => $request->patient_name,
            'mrn' => $request->mrn,
            'consult_info' => $request->consult_info,
            'medstatus' => $request->medstatus,
            'status' => 'REFERRED',
            'doc' => $request->doc,
            'special_request' => $request->special_request ?? '-',
            'remarks' => $request->remarks ?? '-', 
            
            // Bypass MySQL strict mode by explicitly providing a valid timestamp
            'patient_stamp' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Counselling Request submitted successfully!');
    }

    public function list(Request $request)
    {
        // 1. Check if any search parameter or show_all flag is provided
        $hasSearch = $request->filled('mrn') 
                  || $request->filled('patient_name') 
                  || $request->filled('start_date') 
                  || $request->filled('end_date')
                  || $request->filled('show_all');

        // 2. Return empty collection by default if no search has been initiated
        if (!$hasSearch) {
            $records = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);
            return view('counselling.list', compact('records'));
        }

        $query = \Illuminate\Support\Facades\DB::table('consulting');

        // 3. Search by MRN
        if ($request->filled('mrn')) {
            $query->where('mrn', 'like', '%' . trim($request->mrn) . '%');
        }

        // 4. Search by Patient Name
        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . trim($request->patient_name) . '%');
        }

        // 5. Unified Date Range Filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // 6. Paginate Results
        $records = $query->orderBy('date', 'desc')
                         ->orderBy('time', 'desc')
                         ->paginate(50)
                         ->withQueryString();

        return view('counselling.list', compact('records'));
    }

    public function update(Request $request, $id)
    {
        // Sanitize incoming data to bypass strict mode / null constraints
        $status = $request->input('status', 'PENDING');
        $remarks = $request->input('remarks') ?? '-';

        // Update the legacy consulting table using the Query Builder
        DB::table('consulting')
            ->where('no', $id) // The legacy primary key is 'no'
            ->update([
                'status' => $status,
                'remarks' => $remarks
            ]);

        return redirect()->back()->with('success', 'Counselling Order updated successfully.');
    }
}