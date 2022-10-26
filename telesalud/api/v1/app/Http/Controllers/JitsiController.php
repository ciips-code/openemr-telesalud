<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as JitsiRequest;

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

        $data = $response->getBody();

        return response($data);
    }

    /**
     * Iniciamos una Teleconsulta
     */
    public function startVC(Request $request)
    {

    }

    /**
     * Verificamos si un paciente inició una videoconsulta
     */
    public function verifyIfPatientStartVC(Request $request)
    {

    }
}
