<?php
// namespace telesalud\controllers;
// Dependecia de las globales del OpenEmr
$p = $_SERVER['DOCUMENT_ROOT'];
//
$telesalud_path = $p . '/telesalud';
//
require_once ($p . "/interface/globals.php");

/**
 * medic-set-attendance: El médico ingresa a la videoconsulta
 * medic-unset-attendance: El médico cierra la pantalla de videoconsulta
 * videoconsultation-started: Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
 * videoconsultation-finished: El médico presiona el botón Finalizar consulta
 * patient-set-attendance: El paciente anuncia su presencia
 * Enviar mail al medico y acitavar color de que el paciente esta presente
 */
/**
 * Show VC Patient link
 *
 * @param unknown $pc_aid            
 * @param unknown $pc_pid            
 * @param string $url_field_name            
 * @return string
 */
function vcLinks($pc_aid, $pc_pid, $url_field_name = 'data_medic_url')
{
    $r = '';
    // xlt("Iniciar Teleconsulta");
    $title = "Iniciar Teleconsulta";
    $pc_catid = 16;
    // xlt("Patient Teleconsultation");
    $patient_title = 'Patient Teleconsultation';
    // xlt("Medic Teleconsultation");
    $medic_title = 'Medic Teleconsultation';
    $sql = "
    -- mostrar teleconsulta activa
SELECT cal.pc_eid,
    cal.pc_aid,
    cal.pc_pid,
    cal.pc_title,
    pc_hometext,
    pc_startTime,
    pc_endTime,
    vcdata.*
FROM `openemr_postcalendar_events` as cal
    inner join tsalud_vc as vcdata on cal.pc_eid = vcdata.pc_eid
    INNER JOIN patient_data AS p ON cal.pc_pid = p.id
where pc_eventDate = current_date()
    and CURRENT_TIME BETWEEN cal.pc_startTime and cal.pc_endTime 
    and cal.pc_catid = $pc_catid
    and cal.pc_aid = $pc_aid
    and cal.pc_pid = $pc_pid;";
    // echo "$sql";
    $res = sqlStatement($sql);
    $data = sqlFetchArray($res);
    // print_r($data);
    if ($data) {
        // data_patient_url, data_medic_url
        $data_url = $data[$url_field_name];
        $tconsultation_link = "href=\"$data_url\" target=\"_blank\"";
        $r = " &nbsp  <a class=\"btn btn-primary\" $tconsultation_link title=\"$title\" >$title</a>";
    }
    return $r;
}

/**
 * * Crea una video consulta desde una cita
 * creada.
 * * Para esto usa el Servicio de creacion de video consulta (SCV)
 * La categoria de la cita debe estar entre * las cagetoiras de tipo
 * video consultas configuradas * dentro de la tabla de confguraciones de *
 * de video consultas * * @param integer $pc_eid
 */
function createVc($pc_eid)
{
    /**
     * * Categorias de video consultas
     *
     * @var integer $vc_category_id
     */
    $vc_category_list = '16';
    /**
     *
     * @var string $sql_vc_calender -
     *      Consulta cita de tipo video consulta
     *      devuelve:
     *      - datos a enviar al SCV
     *      - emails del paciente y del medico
     */
    $sql_vc_calender = "SELECT c.pc_eid, c.pc_catid, c.pc_aid, c.pc_pid,
c.pc_title, c.pc_time, c.pc_eventDate, c.pc_endDate, c.pc_startTime,
c.pc_endTime, c.pc_duration, CONCAT_WS( p.fname, p.mname, p.lname ) AS
patient_name, CONCAT_WS( m.fname, m.mname, m.lname ) AS medic_name FROM
openemr_postcalendar_events AS c INNER JOIN patient_data AS p ON
c.pc_pid = p.id INNER JOIN users AS m ON c.pc_aid = m.id WHERE
c.pc_catid IN ($vc_category_list) and c.pc_eid=$pc_eid;";
    
    try {
        $res = sqlStatement($sql_vc_calender);
        $calendar_data = sqlFetchArray($res);
        $extra_data = array(
            'saludo' => 'Hola'
        );
        
        // preparar datos a enviar al SCV
        $appoinment_date = $calendar_data['pc_eventDate'] . ' ' . $calendar_data['pc_startTime'];
        /**
         *
         * @var array $vc_data -
         *      datos a enviar al SCV
         *      Ejemplo:
         *      appointment_date:2022-10-31 14:30:00
         *      days_before_expiration:1
         *      medic_name:medico-yois
         *      patient_name:paciente-yois
         *      extra[]:hola
         */
        $data = array(
            "medic_name" => $calendar_data['medic_name'],
            "patient_name" => $calendar_data['patient_name'],
            "days_before_expiration" => '1',
            "appointment_date" => "$appoinment_date",
            "extra" => $extra_data
        );
        /**
         *
         * @var string $vc_response -
         *      respuesta de SCV
         */
        $svc_response = requestSCV($data);
        /**
         * * * @var array $vc_data datos devueltos por el SCV
         */
        $vc_data = json_decode($svc_response, TRUE);
        // si hay respuesta
        if ($vc_data['success']) {
            /**
             * - Actualizacion de liks dentro de consulta despues de generar consutla *
             * - Envio de email a paciente y medico
             * - Recibir notificaciones
             * - Mostrar opciones de teleconsulta en el momento y hora correctas
             * - Traducciones del excel
             */
            // agregar video consulta a la bd
            insertVc($pc_eid, $vc_data);
            updateLinksToAgenda($pc_eid, $vc_data);
            // enviar email de la video consulta al medico
            // sendVcMedicEmail
            // enviar email a paciente // sendVcPatientEmail
        } else {
            echo "Errores en respuesta API Datos devueltos: " . print_r($vc_data, true);
        }
    } catch (Exception $e) {
        // ehco $e
    }
}

/**
 * * * @param unknown
 * $p * @param unknown $email
 */
function sendEmail($p, $email)
{
    // //
    // if
    // (empty($email)) {
    // // $this->assign("process_result", "Email could not be
    // sent, the address supplied: '$email' was empty or invalid."); // return;
    // // } // $mail->From = $from; // $mail = new PHPMailer(); // // this is a
    // temporary config item until the rest of the per practice billing
    // settings make their way in // $from =
    // $GLOBALS['practice_return_email_path']; // $mail->FromName =
    // $p->provider->get_name_display(); // $mail->isMail(); // $mail->Host =
    // "localhost"; // $mail->Mailer = "mail"; // $text_body =
    // $p->get_prescription_display(); // $mail->Body = $text_body; //
    // $mail->Subject = "Prescription for: " . $p->patient->get_name_display();
    // // $mail->AddAddress($email); // if ($mail->Send()) { //
    // $this->assign("process_result", "Email was successfully sent to: " .
    // $email); // return; // } else { // $this->assign("process_result",
    // "There has been a mail error sending to " . $_POST['email_to'] . " " .
    // $mail->ErrorInfo); // return; // } }
}

/**
 * * Actualizacion de liks dentro
 * de consulta despues de generar consutla * guardar datos de acceso a la
 * video consulta de comentarios de la cita.
 * * * @param unknown $vc_data *
 *
 * @return recordset
 */
function updateLinksToAgenda($pc_eid, $vc_data)
{
    $patient_url = $vc_data['data']['patient_url'];
    $medic_url = $vc_data['data']['medic_url'];
    $pc_hometext = "Accesos a la video consulta:
<ul>
<li>Profesional: <a href=\"{$medic_url}\" target=\"_blank\">{$medic_url}</a></li>
<li>Paciente: <a href=\"{$patient_url}\" target=\"_blank\">{$patient_url}</a></li>
</ul>
";
    $sql_update_pc_hometext = "update openemr_postcalendar_events set
pc_hometext='$pc_hometext' where pc_eid=$pc_eid;"; // echo
    $sql_update_pc_hometext;
    return sqlStatement($sql_update_pc_hometext);
}

/**
 * * agrega una nueva video consulta a la Base de datos * * @param
 * integer $pc_eid * @param string $vc_response * datos devueltos por el
 * servicio de video consulta * * @return boolean|number
 */
function insertVc($pc_eid, $vc_data)
{
    /**
     * * * @var boolean $response valor de
     * retorno verdadero/false
     */
    $return = false;
    //
    $success = $vc_data['success'];
    $message = $vc_data['message'];
    $id = $vc_data['data']['id'];
    $valid_from = $vc_data['data']['valid_from'];
    $valid_to = $vc_data['data']['valid_to'];
    $patient_url = $vc_data['data']['patient_url'];
    $medic_url = $vc_data['data']['medic_url'];
    $data_url = $vc_data['data']['data_url'];
    $medic_secret = $vc_data['data']['medic_secret'];
    //
    try {
        // Save new vc on Database
        $query = "INSERT INTO tsalud_vc ";
        $query .= "( pc_eid,
success,message,data_id,data_valid_from,data_valid_to, data_patient_url,
data_medic_url, data_data_url,medic_secret ) ";
        $query .= " VALUES (
$pc_eid, '$success','$message','$id',
'$valid_from','$valid_to','$patient_url','$medic_url','$data_url','$medic_secret')";
        $return = sqlInsert($query);
    } catch (Exception $e) {
        //
        echo $e;
        // Error: Duplicate entry '1' for key 'PRIMARY' //
        return false;
    }
    return $return;
}

/**
 * solcita servicio de video consulta
 *
 * @param array $data            
 * @return string -
 *         respuesta del servicio de video consulta
 */
function requestSCV($data)
{
    $bearToken = "1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg";
    $authorization = "Authorization: Bearer $bearToken";
    $api_url = 'https://srv3.integrandosalud.com/os-telesalud/api/videoconsultation?';
    try {
        // Create VC
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        // Returns the data/output as a string instead of raw data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Set your auth headers
        /**
         * --header 'Content-Type: application/x-www-form-urlencoded'
         */
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $bearToken
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        //
        $result = curl_exec($curl);
        if (! $result) {
            die("API $curl - Connection Failure");
        }
    } catch (Exception $e) {
        echo $e;
    } finally {
        curl_close($curl);
    }
    
    return $result;
}

function saveNotify($response)
{
    $json = json_decode($response, TRUE);
/**
 * * `pc_eid` int(11) unsigned
 * NOT NULL, * `vc_secret` varchar(1024) DEFAULT NULL, * `vc_medic_secret`
 * varchar(1024) DEFAULT NULL, * `vc_status` varchar(1024) DEFAULT NULL, *
 * `vc_medic_attendance_date` varchar(1024) DEFAULT NULL, *
 * `vc_patient_attendance_date` varchar(1024) DEFAULT NULL, *
 * `vc_start_date` varchar(1024) DEFAULT NULL, * `vc_finish_date`
 * varchar(1024) DEFAULT NULL, * `vc_extra` varchar(1024) DEFAULT NULL, *
 * `topic` varchar(1024) DEFAULT NULL
 */
} // include_once
'../globals.php'; // print_r($GLOBALS["pid"]); /** * Only for demo */
$pc_aid = 5;
$pc_pid = 1;
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'insertEvent':
            createVc(1);
            break;
        case 'generateLinks': // echo "generate link"; //
            $pc_aid = $_GET['$pc_aid']; // $pc_pid=$_GET['pc_pid']; $links =
            vcLinks($pc_aid, $pc_pid); // print_r($links); // $patient_l =
            $links['patient_url'];
            echo $links['medic_url'];
            break;
        default:
            break;
    }
}
