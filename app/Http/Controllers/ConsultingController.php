<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $request->validate([
            'patient_name' => 'required|string|max:100',
            'mrn'          => 'required|string|max:50',
            'ward'         => 'required|string',
            'bed'          => 'required|string|max:20',
            'consult_info' => 'required|string',
            'medstatus'    => 'required|string',
            'doc'          => 'required|string|max:100',
            'special_request' => 'nullable|string'
        ]);

        // Save using strtoupper just like the legacy code
        Consulting::create([
            'date'            => now()->toDateString(),
            'time'            => now()->toTimeString(),
            'ward'            => strtoupper($request->ward),
            'bed'             => strtoupper($request->bed),
            'patient_name'    => strtoupper($request->patient_name),
            'mrn'             => strtoupper($request->mrn),
            'consult_info'    => strtoupper($request->consult_info),
            'medstatus'       => strtoupper($request->medstatus),
            'status'          => 'REFERRED', // Default status from legacy
            'doc'             => strtoupper($request->doc),
            'special_request' => strtoupper($request->special_request ?? ''),
        ]);

        return back()->with('success', 'Consultation Order was successfully sent to Pharmacy Department. We will attend to the request as soon as possible.');
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
}