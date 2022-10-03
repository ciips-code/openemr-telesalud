<?php

namespace App\Http\Controllers;

use App\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Devolvemos todos los pacientes
     * REVIEW: 
     * Este recurso debería solo para test, 
     * no se recomienda obtener el pull 
     * completo de los datos
     */
    public function showAllPatients()
    {
        $response = [];
        $patients = Patient::with('encounters')->get();
        
        foreach ($patients as $patient) {
            
            $patientEncounters = [];
            foreach ($patient->encounters as $encounter) {
                $patientEncounters[] = [
                    'date' => explode(' ', $encounter->date)[0],
                    'time' => explode(' ', $encounter->date)[1],
                    'reason' => $encounter->reason,
                    'providerName' => '',
                    'providerSpecialty' => ''
                ];
            }
            $response[] = [
                'patientId' => $patient->id,
                'firstName' => $patient->fname,
                'lastName' => $patient->lname,
                'dob' => $patient->DOB,
                'identifications' => [
                    [
                        'number' => $patient->pid,
                        'type' => 'pid'
                    ],
                    [
                        'number' => $patient->ss,
                        'type' => 'ss'
                    ],
                    [
                        'number' => $patient->drivers_license,
                        'type' => 'driverLicense'
                    ]
                ],
                'encounters' => $patientEncounters
                
            ];
            
        }
        
        return response()->json($response);
    }

    /**
     * Devolvemos el registro del paciente
     * que se encuentre por su id
     */
    public function showOnePatientById($id): array
    {
        return response()->json(Patient::find($id));
    }

    /**
     * Devolvemos el regsitro del paciente
     * que se encuentre por su número de 
     * identificación
     */
    public function showOnePatientByIdentification($identification): array
    {

    }

}
