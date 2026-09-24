<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. General Pharmacy (iMonitor) - 5 Workflow Stages
        $kpi = [
            'received'   => DB::connection('imonitor')->table('patientlist')->where('date', $today)->where('status', 'ORDER RECEIVED')->count(),
            'processing' => DB::connection('imonitor')->table('patientlist')->where('date', $today)->where('status', 'PROCESSING')->count(),
            'ready'      => DB::connection('imonitor')->table('patientlist')->where('date', $today)->where('status', 'READY FOR COLLECTION')->count(),
            'collected'  => DB::connection('imonitor')->table('patientlist')->where('date', $today)->where('status', 'COLLECTED BY PHARMACIST OR PPK/SN')->count(),
            'completed'  => DB::connection('imonitor')->table('patientlist')->where('date', $today)->where('status', 'COMPLETED')->count(),
        ];

        // 2. General Pharmacy Active Queue
        $activeQueue = DB::connection('imonitor')->table('patientlist')
            ->where('date', $today)
            ->whereNotIn('status', ['DISPENSED', 'COMPLETED', 'CANCELLED'])
            ->orderBy('time', 'desc')
            ->limit(15)
            ->get();

        // 3. Cytotoxic (eCDR) - 5 Workflow Stages (NOW FILTERED BY TODAY)
        $ecdrKpi = [
            'total'     => DB::connection('ecdr')->table('cdr_orders')->where('date_use', $today)->count(),
            'received'  => DB::connection('ecdr')->table('cdr_orders')->where('date_use', $today)->where('orderstatus', 'ORDER RECEIVED')->count(),
            'preparing' => DB::connection('ecdr')->table('cdr_orders')->where('date_use', $today)->where('orderstatus', 'PREPARATION')->count(),
            'ready'     => DB::connection('ecdr')->table('cdr_orders')->where('date_use', $today)->where('orderstatus', 'READY')->count(),
            'dispensed' => DB::connection('ecdr')->table('cdr_orders')->where('date_use', $today)->where('orderstatus', 'DISPENSED')->count(),
        ];

        // 4. Cytotoxic (eCDR) Active Queue (NOW FILTERED BY TODAY)
        $ecdrQueue = DB::connection('ecdr')->table('cdr')
            ->join('cdr_orders', 'cdr.cdr_id', '=', 'cdr_orders.order_id')
            ->select('cdr.name as patient_name', 'cdr.ward', 'cdr_orders.date_use', 'cdr_orders.orderstatus as status')
            ->where('cdr_orders.date_use', $today) // Added Date Filter
            ->whereNotIn('cdr_orders.orderstatus', ['DISPENSED', 'CANCELLED'])
            ->orderBy('cdr_orders.date_use', 'asc')
            ->limit(15)
            ->get();

        return view('dashboard', compact('kpi', 'activeQueue', 'ecdrKpi', 'ecdrQueue'));
    }
}