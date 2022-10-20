<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;


/**
 * @OA\Info(title="OpenEMR TeleSalud API", version="1.0")
 */
class PatientController extends Controller
{
    
    /**
     * Obtener las información de todos los pacientes
     * 
     * @OA\Get(
     *     path="/api/patients",
     *     tags={"Patient"},
     *     operationId="showAllPatients",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function showAllPatients()
    {
        $response = [];
        $patients = Patient::with('encounters')->get();
        
        foreach ($patients as $patient) {
            
            $patientEncounters = [];
            foreach ($patient->encounters as $encounter) {
                $provider = $encounter->provider()->get();
                $patientEncounters[] = [
                    'date' => explode(' ', $encounter->date)[0],
                    'time' => explode(' ', $encounter->date)[1],
                    'reason' => $encounter->reason,
                    'end_date' => $encounter->date_end != '' ? explode(' ', $encounter->date_end)[0] : NULL,
                    'end_time' => isset(explode(' ', $encounter->date_end)[1]) ? explode(' ', $encounter->date_end)[1] : NULL,
                    'providerName' => $provider[0]->suffix . ' ' . $provider[0]->fname . ' ' . $provider[0]->lname,
                    'providerSpecialty' => $provider[0]->specialty
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
     * Obtener las información de un paciente por su número de identificación
     * 
     * @OA\Get(
     *     path="/api/patient/{id}",
     *     tags={"Patient"},
     *     operationId="showOnePatient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paciente",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function showOnePatient($id)
    {
        $response = [];
        $patient = Patient::with('encounters')
            ->where('pid', $id)
            ->first();

        if ($patient) {
            $patientEncounters = [];

            foreach ($patient->encounters as $encounter) {
                $provider = $encounter->provider()->get();
                $patientEncounters[] = [
                    'date' => explode(' ', $encounter->date)[0],
                    'time' => explode(' ', $encounter->date)[1],
                    'reason' => $encounter->reason,
                    'end_date' => $encounter->date_end != '' ? explode(' ', $encounter->date_end)[0] : NULL,
                    'end_time' => isset(explode(' ', $encounter->date_end)[1]) ? explode(' ', $encounter->date_end)[1] : NULL,
                    'providerName' => $provider[0]->suffix . ' ' . $provider[0]->fname . ' ' . $provider[0]->lname,
                    'providerSpecialty' => $provider[0]->specialty
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
     * Obtener las información de todas las consultas filtrado por identificación del paciente
     * 
     * @OA\Get(
     *     path="/api/patient/{id}/encounters",
     *     tags={"Patient"},
     *     operationId="showAllEncountersByPatient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paciente",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function showAllEncountersByPatient($id)
    {
        $response = [];
        $patient = Patient::with('encounters')
            ->where('pid', $id)
            ->first();

        if ($patient) {
            $patientEncounters = [];

            foreach ($patient->encounters as $encounter) {
                $provider = $encounter->provider()->get();
                $patientEncounters[] = [
                    'date' => explode(' ', $encounter->date)[0],
                    'time' => explode(' ', $encounter->date)[1],
                    'reason' => $encounter->reason,
                    'end_date' => $encounter->date_end != '' ? explode(' ', $encounter->date_end)[0] : NULL,
                    'end_time' => isset(explode(' ', $encounter->date_end)[1]) ? explode(' ', $encounter->date_end)[1] : NULL,
                    'providerName' => $provider[0]->suffix . ' ' . $provider[0]->fname . ' ' . $provider[0]->lname,
                    'providerSpecialty' => $provider[0]->specialty
                ];
            }

            $response = ['encounters' => $patientEncounters];

        }

        return response()->json($response); 
    }


    /**
     * Obtener las información de un consulta por id filtrado por identificación del paciente
     * 
     * @OA\Get(
     *     path="/api/patient/{id}/encounters/{encounterId}",
     *     tags={"Patient"},
     *     operationId="showPatientEncounterById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paciente",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="encounterId",
     *         in="path",
     *         description="ID de la consulta",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function showPatientEncounterById($id, $encounterId)
    {
        $response = [];
        $patient = Patient::with('encounters')
            ->where('pid', $id)
            ->whereRelation('encounters', 'id', $encounterId)
            ->first();

        if ($patient) {
            $patientEncounters = [];

            foreach ($patient->encounters as $encounter) {
                $provider = $encounter->provider()->get();
                $patientEncounters[] = [
                    'date' => explode(' ', $encounter->date)[0],
                    'time' => explode(' ', $encounter->date)[1],
                    'reason' => $encounter->reason,
                    'end_date' => $encounter->date_end != '' ? explode(' ', $encounter->date_end)[0] : NULL,
                    'end_time' => isset(explode(' ', $encounter->date_end)[1]) ? explode(' ', $encounter->date_end)[1] : NULL,
                    'providerName' => $provider[0]->suffix . ' ' . $provider[0]->fname . ' ' . $provider[0]->lname,
                    'providerSpecialty' => $provider[0]->specialty
                ];
            }

            $response = ['encounters' => $patientEncounters];

        }

        return response()->json($response); 
    }

    

}
