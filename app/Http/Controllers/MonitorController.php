<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;

class MonitorController extends Controller
{
    // 1. NEW: Live Corporate Dashboard (Kept for fallback, though we use DashboardController now)
    public function dashboard()
    {
        $today = date('Y-m-d');
        
        $kpi = [
            'total' => Patient::where('date', $today)->count(),
            'processing' => Patient::where('date', $today)->where('status', 'PROCESSING')->count(),
            'ready' => Patient::where('date', $today)->where('status', 'READY FOR COLLECTION')->count(),
            'completed' => Patient::where('date', $today)->whereIn('status', ['COMPLETED', 'COLLECTED BY PHARMACIST OR PPK/SN', 'COLLECTED BY STAFF NURSE/PPK'])->count(),
        ];

        $activeQueue = Patient::where('date', $today)
            ->whereNotIn('status', ['COMPLETED', 'COLLECTED BY PHARMACIST OR PPK/SN', 'COLLECTED BY STAFF NURSE/PPK'])
            ->orderBy('time', 'desc')
            ->get();

        return view('dashboard', compact('kpi', 'activeQueue'));
    }

    // 2. Main Monitoring History (Status Queue)
    public function index(Request $request)
    {
        $query = \App\Models\Patient::query(); 

        // 1. Detect if a search is active. 
        // A fresh click from the sidebar has no parameters. 
        // A form submission ALWAYS has 'ward', even if it is stripped of empty dates.
        $isSearch = $request->has('ward');

        if (!$isSearch) {
            // Fresh load: default to today
            $query->whereDate('date', date('Y-m-d'));
        } else {
            // Filter applied: apply date range only if they have values
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }
            // If they are empty, it intentionally skips this block to show "All Time"
        }

        // 2. MRN Logic
        if ($request->filled('mrn')) {
            $query->where('mrn', 'like', '%' . trim($request->mrn) . '%');
        }

        // 3. Name Logic
        if ($request->filled('name')) {
            $query->where('patient_name', 'like', '%' . trim($request->name) . '%');
        }

        // 4. Ward Logic
        if ($request->filled('ward') && $request->ward !== 'ALL') {
            $query->where('ward', $request->ward);
        }

        // 5. Paginate with the query string
        $patients = $query->orderBy('date', 'desc')
                          ->orderBy('time', 'desc')
                          ->paginate(50)
                          ->withQueryString(); 

        return view('imonitor.status', compact('patients'));
    }

    // 3. NEW: Show the standalone New Order Form
    public function create()
    {
        return view('imonitor.create');
    }

    // 4. Insert new patient record
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
            
            'status' => $request->input('status', 'ORDER RECEIVED'), // Changed default to ORDER RECEIVED
            'remarks' => $request->input('remarks') ?? '-',
            'takenby' => '-',
            
            'statusready' => '00:00:00',
            'statuscollected' => '00:00:00',
        ]);

        // Redirect to the status queue instead of back to the form
        return redirect()->route('monitor.index')->with('success', 'Patient order added successfully.');
    }

    // 5. Update patient record
    public function update(Request $request, $id)
    {
        $status = $request->input('status');

        $updateData = [
            'status'  => $status,
            'takenby' => $request->input('takenby') ?? '-',
            'remarks' => $request->input('remarks') ?? '-',
        ];

        if ($status === 'READY FOR COLLECTION') {
            $updateData['statusready'] = date('H:i:s');
        }

        if ($status === 'COLLECTED BY PHARMACIST OR PPK/SN' || $status === 'COLLECTED BY STAFF NURSE/PPK') {
            $updateData['statuscollected'] = date('H:i:s');
        }

        DB::table('patientlist')->where('no', $id)->update($updateData);

        return redirect()->back()->with('success', 'Patient order updated successfully.');
    }

    // 6. Delete patient record
    public function destroy($no)
    {
        $patient = Patient::findOrFail($no);
        $patient->delete();

        // Fixed bad redirect route
        return redirect()->route('monitor.index')->with('success', 'Patient record deleted successfully.');
    }

    // 7. API Search
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