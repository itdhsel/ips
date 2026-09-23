<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcdrController extends Controller
{
    public function create()
    {
        $wards = [
            "4A","4B","4C","5A","5B","5C","6B","7A","7B","7C","7D","8A","8D","9A","9B","9C","9D","10A","10B","11C",
            "Klinik Pakar - Rheumatology","Klinik Pakar - Nephrology","Klinik Pakar - Dermatology","Klinik Pakar - Ophthalmology",
            "Daycare- Rheumatology","Day Care - Nephrology","Day Care- Dermatology","Day Care - Ophthalmology","Angio Room (X Ray)",
            "NICU","ICU","Others"
        ];
        return view('ecdr.create', compact('wards'));
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
            $diluents = $request->input('diluent', []);
            $volumes = $request->input('volume', []);

            // 1. Insert Master CDR Record
            // We use the first cycle's date as the master 'date_expected'
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

            // 2. Insert Tracking CDR Orders (Multiple Cycles)
            foreach ($dateUses as $dateUse) {
                if (!empty($dateUse)) {
                    DB::connection('ecdr')->table('cdr_orders')->insert([
                        'order_id'    => $cdrId,
                        'date_order'  => date('Y-m-d'),
                        'date_use'    => $dateUse,
                        'ward'        => $request->input('ward'),
                        'name'        => $request->input('patient_name'),
                        'mrn'         => $request->input('mrn'),
                        'orderstatus' => 'ORDER RECEIVED',
                        'remarks'     => $request->input('remarks', '-'),
                    ]);
                }
            }

            // 3. Insert Cytotoxic Drug Details (Multiple Drugs)
            foreach ($drugNames as $index => $drugName) {
                if (!empty($drugName)) {
                    DB::connection('ecdr')->table('cdr_drugs')->insert([
                        'cdr_id'    => $cdrId,
                        'drug_name' => $drugName,
                        'dose'      => $doses[$index] ?? '-',
                        'diluent'   => $diluents[$index] ?? '-',
                        'volume'    => $volumes[$index] ?? '-',
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Cytotoxic Drug order placed successfully.');
    }

    public function cancel($id)
    {
        DB::connection('ecdr')->table('cdr_orders')
            ->where('order_id', $id)
            ->where('orderstatus', 'ORDER RECEIVED')
            ->update(['orderstatus' => 'CANCELLED']);

        return redirect()->back()->with('success', 'CDR order cancelled successfully.');
    }

    public function show($id)
    {
        $master = DB::connection('ecdr')->table('cdr')->where('cdr_id', $id)->first();
        
        if (!$master) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }

        $cycles = DB::connection('ecdr')->table('cdr_orders')->where('order_id', $id)->orderBy('date_use', 'asc')->get();
        $drugs = DB::connection('ecdr')->table('cdr_drugs')->where('cdr_id', $id)->get();

        return response()->json([
            'success' => true,
            'master'  => $master,
            'cycles'  => $cycles,
            'drugs'   => $drugs
        ]);
    }
}