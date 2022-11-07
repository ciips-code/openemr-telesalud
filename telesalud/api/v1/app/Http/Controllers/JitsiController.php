<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as JitsiRequest;
use App\Models\CalendarEvents;

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
     * Show VC HTML Button link
     */
    public function getVcLink(Request $request)
    {
        $title = 'Patient Teleconsultation';
        $currentTime = date('h:i:s', time());
        $currentDate = date('Y-m-d', time());
        $data = [];
        $response = [];
        
        if ($request['url_field_name'] == 'data_medic_url') {
            $title = 'Start video consultation';
        }

        $data = CalendarEvents::with('patient', 'videoCall')
        ->whereRaw("? BETWEEN pc_startTime AND pc_endTime", ["'". $currentTime . "'"])
        ->whereIn('pc_catid', [16])
        ->where([
            ['pc_eventDate', '=', $currentDate],
            ['pc_aid', '=', $request['pc_aid']],
            ['pc_pid', '=', $request['pc_pid']]
        ])->get();

        if (isset($data[0])) {
            $href = 'href="' . $data[0]->videoCall->data_url;
            $response = [
                'data_url' => $request['url_field_name'],
                'href' => $href,
                'title' => $title,
                'html_button' => "&nbsp <a class=\"btn btn-primary\" $href title=\"$title\"> $title </a>" 
            ];
        }

        return response()->json($response, 200, [
            'Content-Type' => 'application/json;charset=UTF-8', 
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Actualizacion de liks dentro
     * de consulta despues de generar consutla * guardar datos de acceso a la
     * video consulta de comentarios de la cita.
     * @param unknown $vc_data *
     */
    public function updateLinksToAgenda(Request $request)
    {
        $patientUrl = $request['patient_url'];
        $medicUrl = $request['medic_url'];
        $response = [
            'message' => 'Los enlaces no se pudieron actualizar'
        ];
        $pcHometext = "Accesos a la video consulta:
        <ul>
            <li>Profesional: <a href=\"{$medicUrl}\" target=\"_blank\">{$medicUrl}</a></li>
            <li>Paciente: <a href=\"{$patientUrl}\" target=\"_blank\">{$patientUrl}</a></li>
        </ul>";
        
        $updatePcHometext = CalendarEvents::where('pc_eid', $request['pc_eid'])
        ->update([
            'pc_hometext' => $pcHometext
        ]);

        if ($updatePcHometext) {
            $response = [
                'message' => 'Enlaces actualizados'
            ];
        }

        return response()->json($response);

    }
}
