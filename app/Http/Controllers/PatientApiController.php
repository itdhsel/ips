<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientApiController extends Controller
{
    public function searchImonitorMrn(Request $request)
    {
        $mrn = trim($request->query('mrn'));

        if (!$mrn) {
            return response()->json(['success' => false, 'message' => 'MRN is required.']);
        }

        if (Schema::hasTable('patientlist')) {
            $record = DB::table('patientlist')
                ->where('mrn', $mrn)
                ->orderBy('no', 'desc')
                ->first();

            if ($record) {
                return response()->json([
                    'success' => true,
                    'patient' => [
                        'patient_name' => $record->patient_name ?? '',
                        'ward'         => $record->ward ?? '',
                    ]
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No iMonitor record found for this MRN.']);
    }

    public function searchEcdrMrn(Request $request)
    {
        $mrn = trim($request->query('mrn'));

        if (!$mrn) {
            return response()->json(['success' => false, 'message' => 'MRN is required.']);
        }

        if (Schema::connection('ecdr')->hasTable('cdr')) {
            $record = DB::connection('ecdr')->table('cdr')
                ->where('mrn', $mrn)
                ->orderBy('cdr_id', 'desc')
                ->first();

            if ($record) {
                return response()->json([
                    'success' => true,
                    'patient' => [
                        'patient_name' => $record->name ?? '',
                        'ward'         => $record->ward ?? '',
                        'age'          => $record->age ?? '',
                        'sex'          => $record->sex ?? '',
                        'weight'       => $record->weight ?? '',
                        'height'       => $record->height ?? '',
                        'bsa'          => $record->bsa ?? '',
                        'diagnosis'    => $record->diagnosis ?? '',
                        'protocol'     => $record->protocol ?? '',
                    ]
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No eCDR record found for this MRN.']);
    }
}