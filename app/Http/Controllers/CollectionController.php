<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $wards = ["2C","4A","4B","4C","4D","5A","5B","5C","5D","6A","6B","6C","6D","7A","7B","7C","7D","8A","8B","8C","8D","9A","9B","9C","9D","10A","10B","10C","10D","11B","11C","NICU","HDW","BURN UNIT","LABOUR ROOM","ICU","ED","OTHERS"];
        
        $query = Patient::query();

        // 1. Initial load defaults to today. If user submits blank dates (e.g. clicks "All"), skip date filter.
        if (!$request->has('start_date')) {
            $query->where('date', date('Y-m-d'));
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('date', '<=', $request->end_date);
            }
        }

        // 2. Apply Ward Dropdown Filter (Ignore "ALL" string)
        if ($request->filled('ward') && $request->ward !== 'ALL') {
            $query->where('ward', $request->ward);
        }

        // 3. Limit to 50 items per page and preserve query strings
        $patients = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(50)->withQueryString();

        // Pass clean variables back to the view
        $selectedWard = $request->input('ward', 'ALL');
        $startDate = $request->has('start_date') ? $request->start_date : date('Y-m-d');
        $endDate = $request->has('end_date') ? $request->end_date : date('Y-m-d');

        return view('imonitor.collection', compact('wards', 'selectedWard', 'startDate', 'endDate', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'selectedData' => 'required|array',
            'nurseName' => 'required|string|max:100'
        ]);

        // Bulk update the selected patients
        Patient::whereIn('no', $request->selectedData)->update([
            'takenby' => strtoupper($request->nurseName),
            'status' => 'COLLECTED BY STAFF NURSE/PPK'
        ]);

        return back()->with('success', 'Medications successfully collected by ' . strtoupper($request->nurseName));
    }
}