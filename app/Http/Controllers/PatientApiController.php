<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientApiController extends Controller
{
    public function searchImonitorMrn(Request $request)
    {
        $mrn = $request->query('mrn');
        
        if (!$mrn) {
            return response()->json(['success' => false]);
        }

        // Explicitly target the 'imonitor' DB connection and 'patientlist' table
        $patient = \Illuminate\Support\Facades\DB::connection('imonitor')
            ->table('patientlist')
            ->where('mrn', $mrn)
            ->orderBy('date', 'desc') // Ensures it pulls the most recently recorded name
            ->first();

        if ($patient) {
            return response()->json([
                'success' => true, 
                'patient_name' => $patient->patient_name 
            ]);
        }

        return response()->json(['success' => false]);
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