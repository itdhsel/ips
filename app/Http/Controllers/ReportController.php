<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report', null);
        $year = $request->input('year', date('Y'));
        $data = [];
        
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        if ($reportType == 1) {
            // Report 1: Total patient monthly (Distinct MRN)
            $query = DB::table('patientlist')
                ->selectRaw('MONTH(date) as month, COUNT(DISTINCT mrn) as total')
                ->whereYear('date', $year)
                ->groupBy('month')
                ->pluck('total', 'month')->toArray();

            foreach ($months as $num => $name) {
                $data[$name] = $query[$num] ?? 0;
            }
        } 
        elseif ($reportType == 2) {
            // Report 2: Total item ordered monthly
            $query = DB::table('patientlist')
                ->selectRaw('MONTH(date) as month, SUM(total_item + total_item2) as total')
                ->whereYear('date', $year)
                ->groupBy('month')
                ->pluck('total', 'month')->toArray();

            foreach ($months as $num => $name) {
                $data[$name] = $query[$num] ?? 0;
            }
        } 
        elseif ($reportType == 3) {
            // Report 3: Total time frame order monthly
            $records = DB::table('patientlist')
                ->selectRaw('MONTH(date) as month, time')
                ->whereYear('date', $year)
                ->get();

            $frames = [
                '0000-0700', '0701-0800', '0801-1000', '1001-1200', 
                '1201-1400', '1401-1600', '1601-1700', '1701-1800', 
                '1801-1900', '1901-0000'
            ];

            // Initialize array
            foreach ($months as $name) {
                foreach ($frames as $f) $data[$name][$f] = 0;
            }

            foreach ($records as $row) {
                $mName = $months[$row->month];
                $t = strtotime($row->time);
                if ($t >= strtotime('00:00:00') && $t <= strtotime('07:00:00')) $data[$mName]['0000-0700']++;
                elseif ($t >= strtotime('07:01:00') && $t <= strtotime('08:00:00')) $data[$mName]['0701-0800']++;
                elseif ($t >= strtotime('08:01:00') && $t <= strtotime('10:00:00')) $data[$mName]['0801-1000']++;
                elseif ($t >= strtotime('10:01:00') && $t <= strtotime('12:00:00')) $data[$mName]['1001-1200']++;
                elseif ($t >= strtotime('12:01:00') && $t <= strtotime('14:00:00')) $data[$mName]['1201-1400']++;
                elseif ($t >= strtotime('14:01:00') && $t <= strtotime('16:00:00')) $data[$mName]['1401-1600']++;
                elseif ($t >= strtotime('16:01:00') && $t <= strtotime('17:00:00')) $data[$mName]['1601-1700']++;
                elseif ($t >= strtotime('17:01:00') && $t <= strtotime('18:00:00')) $data[$mName]['1701-1800']++;
                elseif ($t >= strtotime('18:01:00') && $t <= strtotime('19:00:00')) $data[$mName]['1801-1900']++;
                else $data[$mName]['1901-0000']++;
            }
        } 
        elseif ($reportType == 4) {
            // Report 4: Bedside dispensing workload by status monthly
            $records = DB::table('patientlist')
                ->selectRaw('MONTH(date) as month, status, COUNT(*) as total')
                ->whereYear('date', $year)
                ->where('status', '!=', 'CANCELLED')
                ->groupBy('month', 'status')
                ->get();

            $statuses = [
                'PROCESSING', 'READY FOR COLLECTION', 'COLLECTED BY PHARMACIST', 
                'COLLECTED BY STAFF NURSE/PPK', 'COMPLETED', 'PENDING'
            ];

            foreach ($months as $name) {
                foreach ($statuses as $s) $data[$name][$s] = 0;
            }

            foreach ($records as $row) {
                if (in_array($row->status, $statuses)) {
                    $data[$months[$row->month]][$row->status] = $row->total;
                }
            }
        } 
        elseif ($reportType == 5) {
            // Report 5: Time frame for Ready For Collection
            $records = DB::table('patientlist')
                ->selectRaw("
                    MONTH(date) as month,
                    CASE
                        WHEN statusready = '00:00:00' THEN 'Undefined'
                        WHEN TIME_TO_SEC(TIMEDIFF(statusready, time)) <= 1800 THEN 'Under 30min'
                        WHEN TIME_TO_SEC(TIMEDIFF(statusready, time)) <= 3600 THEN '30min-60min'
                        WHEN TIME_TO_SEC(TIMEDIFF(statusready, time)) <= 7200 THEN '60min-120min'
                        WHEN TIME_TO_SEC(TIMEDIFF(statusready, time)) <= 10800 THEN '120min-180min'
                        ELSE 'Above 180min'
                    END AS time_needed,
                    COUNT(*) AS frequency
                ")
                ->whereYear('date', $year)
                ->groupBy('month', 'time_needed')
                ->orderBy('month')
                ->get();

            $timeNeededLabels = ['Under 30min', '30min-60min', '60min-120min', '120min-180min', 'Above 180min', 'Undefined'];

            foreach ($months as $name) {
                foreach ($timeNeededLabels as $l) $data[$name][$l] = 0;
            }

            foreach ($records as $row) {
                $data[$months[$row->month]][$row->time_needed] = $row->frequency;
            }
        }

        return view('reports.index', compact('reportType', 'year', 'data', 'months'));
    }
}