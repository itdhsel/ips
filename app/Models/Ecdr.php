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

        // Ward Monitoring Query
        $wardQuery = DB::table('ecdrs');
        
        if ($request->filled('wad')) {
            $wardQuery->where('ward', $request->wad);
            
            $dayView = $request->input('dayview', '0');
            if ($dayView === '0') {
                $wardQuery->whereDate('date_use', date('Y-m-d'));
            } elseif ($dayView === '1') {
                $wardQuery->whereBetween('date_use', [date('Y-m-d', strtotime('-1 day')), date('Y-m-d')]);
            } elseif ($dayView === '2') {
                $wardQuery->whereBetween('date_use', [date('Y-m-d', strtotime('-2 days')), date('Y-m-d')]);
            }
        } else {
            // Default blank state until filtered
            $wardQuery->whereRaw('1 = 0');
        }

        $wardOrders = $wardQuery->orderBy('id', 'desc')->get();

        // Personal Staff Orders Query (My Orders)
        $userStaffId = auth()->user()->login_username ?? auth()->user()->name;
        $myOrders = DB::table('ecdrs')
            ->where('prepared_by', $userStaffId)
            ->whereBetween('date_use', [date('Y-m-d', strtotime('-30 days')), date('Y-m-d', strtotime('+30 days'))])
            ->orderBy('date_use', 'desc')
            ->get();

        return view('ecdr.index', compact('wards', 'wardOrders', 'myOrders'));
    }

    public function cancel($id)
    {
        DB::table('ecdrs')
            ->where('id', $id)
            ->where('status', 'ORDER RECEIVED')
            ->update(['status' => 'CANCELLED']);

        return redirect()->back()->with('success', 'CDR order cancelled successfully.');
    }
}