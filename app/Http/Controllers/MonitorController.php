<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;

class MonitorController extends Controller
{
    // 1. NEW: Live Corporate Dashboard
    public function dashboard()
    {
        $today = date('Y-m-d');
        
        // Calculate KPI Metrics for today
        $kpi = [
            'total' => Patient::where('date', $today)->count(),
            'processing' => Patient::where('date', $today)->where('status', 'PROCESSING')->count(),
            'ready' => Patient::where('date', $today)->where('status', 'READY FOR COLLECTION')->count(),
            'completed' => Patient::where('date', $today)->whereIn('status', ['COMPLETED', 'COLLECTED BY PHARMACIST OR PPK/SN', 'COLLECTED BY STAFF NURSE/PPK'])->count(),
        ];

        // Fetch active queue (excluding completed orders)
        $activeQueue = Patient::where('date', $today)
            ->whereNotIn('status', ['COMPLETED', 'COLLECTED BY PHARMACIST OR PPK/SN', 'COLLECTED BY STAFF NURSE/PPK'])
            ->orderBy('time', 'desc')
            ->get();

        return view('dashboard', compact('kpi', 'activeQueue'));
    }

    // 2. Main Monitoring History (IPS)
    public function index(Request $request)
    {
        $query = Patient::query(); 

        // Initial page load defaults to today. If user submits with blank dates (e.g. clicks "All"), skip date filter.
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

        // Apply Ward Dropdown Filter (Ignore "ALL")
        if ($request->filled('ward') && $request->ward !== 'ALL') {
            $query->where('ward', $request->ward);
        }

        $patients = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(50)->withQueryString();

        return view('monitor.index', compact('patients'));
    }

    // Insert new patient record
    public function store(Request $request)
    {
        DB::table('patientlist')->insert([
            'date' => date('Y-m-d'),
            'time' => $request->input('time', date('H:i')) . ':00', 
            'ward' => $request->input('ward'),
            'patient_name' => $request->input('patient_name'),
            'mrn' => $request->input('mrn'),
            'total_item' => $request->input('total_item', 0),
            
            // Map the form input (e.g., "1 WEEK") to the correct 'supply' varchar column
            'supply' => $request->input('total_item2', '-'), 
            
            // Provide a strict integer for total_item2 to satisfy the DB schema
            'total_item2' => 0, 
            
            'status' => $request->input('status', 'PROCESSING'),
            'remarks' => $request->input('remarks') ?? '-',
            'takenby' => '-',
            
            // Provide default blank times to satisfy the strict schema requirements
            'statusready' => '00:00:00',
            'statuscollected' => '00:00:00',
        ]);

        return redirect()->back()->with('success', 'Patient order added successfully.');
    }

    public function update(Request $request, $id)
    {
        $status = $request->input('status');

        $updateData = [
            'status'  => $status,
            'takenby' => $request->input('takenby') ?? '-',
            'remarks' => $request->input('remarks') ?? '-',
        ];

        // Dynamically capture the time when the status is updated to READY
        if ($status === 'READY FOR COLLECTION') {
            $updateData['statusready'] = date('H:i:s');
        }

        // Dynamically capture the time when the status is updated to COLLECTED
        if ($status === 'COLLECTED BY PHARMACIST OR PPK/SN' || $status === 'COLLECTED BY STAFF NURSE/PPK') {
            $updateData['statuscollected'] = date('H:i:s');
        }

        DB::table('patientlist')->where('no', $id)->update($updateData);

        return redirect()->back()->with('success', 'Patient order updated successfully.');
    }

    // Delete patient record
    public function destroy($no)
    {
        $patient = Patient::findOrFail($no);
        $patient->delete();

        return redirect()->route('monitor.index')->with('success', 'Patient record deleted successfully.');
    }

    public function searchMrn(Request $request)
    {
        $mrn = $request->query('mrn');
        
        if (!$mrn) {
            return response()->json(['success' => false]);
        }

        $patient = \Illuminate\Support\Facades\DB::table('patientlist')
            ->where('mrn', $mrn)
            ->first();

        if ($patient) {
            return response()->json([
                'success' => true, 
                'patient_name' => $patient->patient_name 
            ]);
        }

        return response()->json(['success' => false]);
    }
}