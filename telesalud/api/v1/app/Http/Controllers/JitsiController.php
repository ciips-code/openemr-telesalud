<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as JitsiRequest;
use App\Models\{OpenemrPostCalendarEvents, VideoEncounter};

/**
 * medic-set-attendance: El médico ingresa a la videoconsulta
 * medic-unset-attendance: El médico cierra la pantalla de videoconsulta
 * videoconsultation-started: Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
 * videoconsultation-finished: El médico presiona el botón Finalizar consulta
 * patient-set-attendance: El paciente anuncia su presencia
 * 
 * Enviar mail al medico y activar color de que el paciente esta presente
 */
class JitsiController extends Controller
{
    
    private string $vcBaseUrl = 'https://srv3.integrandosalud.com/os-telesalud/api/';
    private string $vcToken = '1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg';
    
    /**
     * Creamos una Teleconsulta
     */
    public function createVC(Request $request)
    {
        $endpoint = 'videoconsultation';

        $request->validate([
            'appointment_date'       => 'required',
            'days_before_expiration' => 'required',
            'medic_name'             => 'required|string',
            'patient_name'           => 'required|string'
        ]);

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $this->vcToken,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8;'
        ];
        $options = [
            'form_params' => [
                'appointment_date' => $request['appointment_date'],
                'days_before_expiration' => $request['days_before_expiration'],
                'medic_name' => $request['medic_name'],
                'patient_name' => $request['patient_name']
            ]
        ];

        $url = $this->vcBaseUrl . $endpoint;
        $request = new JitsiRequest('POST', $url, $headers);
        $response = $client->sendAsync($request, $options)->wait();

        $data = json_decode($response->getBody());

        return response($data);
    }


    /**
     * Show VC Patient link
     */
    public function getVcLink(Request $request)
    {

    }
    
}
