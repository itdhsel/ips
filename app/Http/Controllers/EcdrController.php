<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcdrController extends Controller
{
    public function create(Request $request) // <-- Note the added Request $request
    {
        $wards = [
            "4A","4B","4C","5A","5B","5C","6B","7A","7B","7C","7D","8A","8D","9A","9B","9C","9D","10A","10B","11C",
            "Klinik Pakar - Rheumatology","Klinik Pakar - Nephrology","Klinik Pakar - Dermatology","Klinik Pakar - Ophthalmology",
            "Daycare- Rheumatology","Day Care - Nephrology","Day Care- Dermatology","Day Care - Ophthalmology","Angio Room (X Ray)",
            "NICU","ICU","Others"
        ];
        
        $reorderMaster = null;
        $reorderDrugs = collect();

        // Detect if this is a Re-Order request
        if ($request->has('reorder_id')) {
            $reorderMaster = DB::connection('ecdr')->table('cdr')->where('cdr_id', $request->reorder_id)->first();
            if ($reorderMaster) {
                // Pull the legacy drugs exactly as they were entered before
                $reorderDrugs = DB::connection('ecdr')->table('cdr_drugs')->where('cdrid', $request->reorder_id)->get();
            }
        }

        return view('ecdr.create', compact('wards', 'reorderMaster', 'reorderDrugs'));
    }
    

    public function wardList(Request $request)
    {
        $wards = [
            "4A","4B","4C","5A","5B","5C","6B","7A","7B","7C","7D","8A","8D","9A","9B","9C","9D","10A","10B","11C",
            "Klinik Pakar - Rheumatology","Klinik Pakar - Nephrology","Klinik Pakar - Dermatology","Klinik Pakar - Ophthalmology",
            "Daycare- Rheumatology","Day Care - Nephrology","Day Care- Dermatology","Day Care - Ophthalmology","Angio Room (X Ray)",
            "NICU","ICU","Others"
        ];
        
        if ($request->filled('wad')) {
            $query = DB::connection('ecdr')->table('cdr')
                ->join('cdr_orders', 'cdr.cdr_id', '=', 'cdr_orders.order_id')
                ->select('cdr.cdr_id as id', 'cdr.date_order as date', 'cdr_orders.date_use', 'cdr.ward', 'cdr.name as patient_name', 'cdr.mrn', 'cdr_orders.orderstatus as status')
                ->where('cdr.ward', $request->wad);

            $dayView = $request->input('dayview', '0');
            if ($dayView === '0') $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) = 0');
            elseif ($dayView === '1') $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) BETWEEN 0 AND 1');
            elseif ($dayView === '2') $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) BETWEEN 0 AND 2');

            $wardOrders = $query->orderBy('cdr.cdr_id', 'desc')->get();
        } else {
            $wardOrders = collect();
        }

        return view('ecdr.ward_list', compact('wards', 'wardOrders'));
    }

    public function history()
    {
        $userId = auth()->user()->login_id ?? auth()->id();
        
        $myOrders = DB::connection('ecdr')->table('cdr')
            ->join('cdr_orders', 'cdr.cdr_id', '=', 'cdr_orders.order_id')
            ->select('cdr.cdr_id as id', 'cdr.date_order as date', 'cdr_orders.date_use', 'cdr.ward', 'cdr.name as patient_name', 'cdr.mrn', 'cdr_orders.orderstatus as status')
            ->where('cdr.orderedbyid', $userId)
            ->whereBetween('cdr_orders.date_use', [date('Y-m-d', strtotime('-30 days')), date('Y-m-d', strtotime('+30 days'))])
            ->orderBy('cdr_orders.date_use', 'desc')
            ->get();

        return view('ecdr.history', compact('myOrders'));
    }

    public function store(Request $request)
    {
        $userId = auth()->user()->login_id ?? auth()->id();
        $userName = auth()->user()->name ?? 'Staff';

        // Perform transaction on the ecdr connection
        DB::connection('ecdr')->transaction(function () use ($request, $userId, $userName) {
            
            // Get arrays from the request
            $dateUses = $request->input('date_use', []);
            $drugNames = $request->input('drug_name', []);
            $doses = $request->input('dose', []);
            $catatans = $request->input('catatan', []); 

            // 1. Insert Master CDR Record (Stores all patient demographics)
            $cdrId = DB::connection('ecdr')->table('cdr')->insertGetId([
                'date_order'      => date('Y-m-d'),
                'date_expected'   => $dateUses[0] ?? date('Y-m-d'), 
                'name'            => $request->input('patient_name'),
                'mrn'             => $request->input('mrn'),
                'ward'            => $request->input('ward'),
                'age'             => $request->input('age'),
                'sex'             => $request->input('sex'),
                'weight'          => $request->input('weight'),
                'height'          => $request->input('height'),
                'bsa'             => $request->input('bsa'),
                'diagnosis'       => $request->input('diagnosis'),
                'protocol'        => $request->input('protocol'),
                'orderedby'       => $userName,
                'orderedbyid'     => $userId,
                'nota'            => $request->input('remarks', '-'),
            ]);

            // 2. Insert Tracking CDR Orders (Only stores cycle date and status)
            foreach ($dateUses as $dateUse) {
                if (!empty($dateUse)) {
                    DB::connection('ecdr')->table('cdr_orders')->insert([
                        'order_id'    => $cdrId,
                        'date_use'    => $dateUse,
                        'orderstatus' => 'ORDER RECEIVED',
                    ]);
                }
            }

            // 3. Insert Cytotoxic Drug Details
            foreach ($drugNames as $index => $drugName) {
                if (!empty($drugName)) {
                    DB::connection('ecdr')->table('cdr_drugs')->insert([
                        'cdr_id'  => $cdrId, // Changed from cdrid back to cdr_id
                        'mrn'     => $request->input('mrn'), 
                        'ubat'    => $drugName,
                        'dos'     => $doses[$index] ?? '-',
                        'catatan' => $catatans[$index] ?? '-',
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Cytotoxic Drug order placed successfully.');
    }

    public function cancel(Request $request, $id)
    {
        $cancelRemarks = $request->input('cancel_remarks', 'No remarks provided');

        // Append the cancel reason to the master record's 'nota' (remarks)
        DB::connection('ecdr')->table('cdr')
            ->where('cdr_id', $id)
            ->update([
                'nota' => DB::raw("CONCAT(IFNULL(nota, ''), ' [CANCEL REASON: ', '" . addslashes($cancelRemarks) . "', ']')")
            ]);

        // Update the status of all active cycles to CANCELLED
        DB::connection('ecdr')->table('cdr_orders')
            ->where('order_id', $id)
            ->whereNotIn('orderstatus', ['COMPLETED', 'DISPOSED']) // Prevent canceling finished orders
            ->update([
                'orderstatus' => 'CANCELLED',
                'remarks' => DB::raw("CONCAT(IFNULL(remarks, ''), ' [Cancelled]')")
            ]);

        return redirect()->back()->with('success', 'CDR order cancelled successfully.');
    }

    public function show($id)
    {
        $master = DB::connection('ecdr')->table('cdr')->where('cdr_id', $id)->first();
        
        if (!$master) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }

        $cycles = DB::connection('ecdr')->table('cdr_orders')->where('order_id', $id)->orderBy('date_use', 'asc')->get();
        
        // Querying cdrid to match legacy structure
        $drugs = DB::connection('ecdr')->table('cdr_drugs')->where('cdrid', $id)->get(); 

        return response()->json([
            'success' => true,
            'master'  => $master,
            'cycles'  => $cycles,
            'drugs'   => $drugs
        ]);
    }

    public function showOrder($id)
    {
        $master = DB::connection('ecdr')->table('cdr')->where('cdr_id', $id)->first();
        
        if (!$master) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $cycles = DB::connection('ecdr')->table('cdr_orders')
            ->where('order_id', $id)
            ->orderBy('date_use', 'asc')
            ->get();
            
        $drugs = DB::connection('ecdr')->table('cdr_drugs')
            ->where('cdr_id', $id)
            ->get(); 

        return view('ecdr.show', compact('master', 'cycles', 'drugs'));
    }

    public function edit($id)
    {
        $wards = [
            "4A","4B","4C","5A","5B","5C","6B","7A","7B","7C","7D","8A","8D","9A","9B","9C","9D","10A","10B","11C",
            "Klinik Pakar - Rheumatology","Klinik Pakar - Nephrology","Klinik Pakar - Dermatology","Klinik Pakar - Ophthalmology",
            "Daycare- Rheumatology","Day Care - Nephrology","Day Care- Dermatology","Day Care - Ophthalmology","Angio Room (X Ray)",
            "NICU","ICU","Others"
        ];
        
        $master = DB::connection('ecdr')->table('cdr')->where('cdr_id', $id)->first();
        if (!$master) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $cycles = DB::connection('ecdr')->table('cdr_orders')->where('order_id', $id)->orderBy('date_use', 'asc')->get();
        $drugs = DB::connection('ecdr')->table('cdr_drugs')->where('cdrid', $id)->get();

        return view('ecdr.edit', compact('wards', 'master', 'cycles', 'drugs'));
    }

    public function update(Request $request, $id)
    {
        DB::connection('ecdr')->transaction(function () use ($request, $id) {
            
            // 1. Update Master Demographics
            DB::connection('ecdr')->table('cdr')->where('cdr_id', $id)->update([
                'date_expected'   => $request->input('date_use')[0] ?? date('Y-m-d'), 
                'name'            => $request->input('patient_name'),
                'mrn'             => $request->input('mrn'),
                'ward'            => $request->input('ward'),
                'age'             => $request->input('age'),
                'sex'             => $request->input('sex'),
                'weight'          => $request->input('weight'),
                'height'          => $request->input('height'),
                'bsa'             => $request->input('bsa'),
                'diagnosis'       => $request->input('diagnosis'),
                'protocol'        => $request->input('protocol'),
                'nota'            => $request->input('remarks', '-'),
            ]);

            // 2. Refresh Tracking CDR Orders
            DB::connection('ecdr')->table('cdr_orders')->where('order_id', $id)->delete();
            $dateUses = $request->input('date_use', []);
            foreach ($dateUses as $dateUse) {
                if (!empty($dateUse)) {
                    DB::connection('ecdr')->table('cdr_orders')->insert([
                        'order_id'    => $id,
                        'date_use'    => $dateUse,
                        'orderstatus' => 'ORDER RECEIVED',
                    ]);
                }
            }

            // 3. Refresh Cytotoxic Drug Details
            DB::connection('ecdr')->table('cdr_drugs')->where('cdrid', $id)->delete();
            $drugNames = $request->input('drug_name', []);
            $doses = $request->input('dose', []);
            $catatans = $request->input('catatan', []); 
            foreach ($drugNames as $index => $drugName) {
                if (!empty($drugName)) {
                    DB::connection('ecdr')->table('cdr_drugs')->insert([
                        'cdrid'   => $id, 
                        'mrn'     => $request->input('mrn'), 
                        'ubat'    => $drugName,
                        'dos'     => $doses[$index] ?? '-',
                        'catatan' => $catatans[$index] ?? '-',
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Order updated successfully.');
    }
}