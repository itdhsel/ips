<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcdrController extends Controller
{
    public function index(Request $request)
    {
        $wards = [
            "4A","4B","4C","5A","5B","5C","6B","7A","7B","7C","7D","8A","8D","9A","9B","9C","9D","10A","10B","11C",
            "Klinik Pakar - Rheumatology","Klinik Pakar - Nephrology","Klinik Pakar - Dermatology","Klinik Pakar - Ophthalmology",
            "Daycare- Rheumatology","Day Care - Nephrology","Day Care- Dermatology","Day Care - Ophthalmology","Angio Room (X Ray)",
            "NICU","ICU","Others"
        ];

        // Ward Monitoring Query (Using ecdr connection)
        if ($request->filled('wad')) {
            $query = DB::connection('ecdr')->table('cdr')
                ->join('cdr_orders', 'cdr.cdr_id', '=', 'cdr_orders.order_id')
                ->select(
                    'cdr.cdr_id as id',
                    'cdr.date_order as date',
                    'cdr_orders.date_use',
                    'cdr.ward',
                    'cdr.name as patient_name',
                    'cdr.mrn',
                    'cdr_orders.orderstatus as status',
                    'cdr_orders.collectedby as takenby',
                    'cdr_orders.remarks'
                )
                ->where('cdr.ward', $request->wad);

            $dayView = $request->input('dayview', '0');
            if ($dayView === '0') {
                $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) = 0');
            } elseif ($dayView === '1') {
                $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) BETWEEN 0 AND 1');
            } elseif ($dayView === '2') {
                $query->whereRaw('DATEDIFF(CURDATE(), cdr_orders.date_use) BETWEEN 0 AND 2');
            }

            $wardOrders = $query->orderBy('cdr.cdr_id', 'desc')->get();
        } else {
            $wardOrders = collect();
        }

        // My Orders Query (Using ecdr connection)
        $userId = auth()->user()->login_id ?? auth()->id();
        $myOrders = DB::connection('ecdr')->table('cdr')
            ->join('cdr_orders', 'cdr.cdr_id', '=', 'cdr_orders.order_id')
            ->select(
                'cdr.cdr_id as id',
                'cdr.date_order as date',
                'cdr_orders.date_use',
                'cdr.ward',
                'cdr.name as patient_name',
                'cdr.mrn',
                'cdr_orders.orderstatus as status',
                'cdr_orders.collectedby as takenby',
                'cdr_orders.remarks'
            )
            ->where('cdr.orderedbyid', $userId)
            ->whereBetween('cdr_orders.date_use', [date('Y-m-d', strtotime('-30 days')), date('Y-m-d', strtotime('+30 days'))])
            ->orderBy('cdr_orders.date_use', 'desc')
            ->get();

        return view('ecdr.index', compact('wards', 'wardOrders', 'myOrders'));
    }

    public function store(Request $request)
    {
        $userId = auth()->user()->login_id ?? auth()->id();
        $userName = auth()->user()->name ?? 'Staff';

        // Perform transaction on the ecdr connection
        DB::connection('ecdr')->transaction(function () use ($request, $userId, $userName) {
            // 1. Insert Master CDR Record
            $cdrId = DB::connection('ecdr')->table('cdr')->insertGetId([
                'date_order'      => date('Y-m-d'),
                'date_expected'   => $request->input('date_use'),
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

            // 2. Insert Tracking CDR Order
            DB::connection('ecdr')->table('cdr_orders')->insert([
                'order_id'    => $cdrId,
                'date_order'  => date('Y-m-d'),
                'date_use'    => $request->input('date_use'),
                'ward'        => $request->input('ward'),
                'name'        => $request->input('patient_name'),
                'mrn'         => $request->input('mrn'),
                'orderstatus' => 'ORDER RECEIVED',
                'remarks'     => $request->input('remarks', '-'),
            ]);

            // 3. Insert Cytotoxic Drug Details
            DB::connection('ecdr')->table('cdr_drugs')->insert([
                'cdr_id'    => $cdrId,
                'drug_name' => $request->input('drug_name'),
                'dose'      => $request->input('dose'),
                'diluent'   => $request->input('diluent', '-'),
                'volume'    => $request->input('volume', '-'),
            ]);
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
}