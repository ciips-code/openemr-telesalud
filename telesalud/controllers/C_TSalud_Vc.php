<?php
// namespace telesalud\controllers;
// Dependecia de las globales del OpenEmr
$p = $_SERVER['DOCUMENT_ROOT'];
//
$telesalud_path = $p . '/telesalud';
//
require_once ($p . "/interface/globals.php");
// include_once '../globals.php';

/**
 * Only for demo
 */
if (isset($_GET['action'])) {
    
    switch ($_GET['action']) {
        case 'link':
            echo "generate link";
            break;
        case 'insertEvent':
            createVc($_GET['pc_eid']);
            break;
        default:
            ;
            break;
    }
    ;
}

// print_r($GLOBALS["pid"]);

/**
 * Show VC Patient link
 *
 * @param unknown $pac_id            
 * @param number $pc_catid            
 * @return string
 *
 */
function vcLinks($pc_aid, $pc_pid)
{
    $pc_catid = 16;
    $patient_title = 'Patient Teleconsultation'; // xlt("Patient Teleconsultation");
    $medic_title = 'Medic Teleconsultation'; // xlt("Medic Teleconsultation");
    
    $sql = "SELECT
	c.pc_eid,
	c.pc_catid,
	c.pc_aid,
	c.pc_pid,
	c.pc_title,
	c.pc_time,
	c.pc_eventDate,
	c.pc_endDate,
	c.pc_startTime,
	c.pc_endTime,
	c.pc_duration,
	CONCAT_WS( p.fname, p.mname, p.lname ) AS patient_name,
	CONCAT_WS( m.fname, m.mname, m.lname ) AS medic_name,
	vc.* 
FROM
	openemr_postcalendar_events AS c
	INNER JOIN patient_data AS p ON c.pc_pid = p.id
	INNER JOIN users AS m ON c.pc_aid = m.id
	INNER JOIN tsalud_vc AS vc ON c.pc_eid = vc.pc_eid 
WHERE
	c.pc_catid = $pc_catid
and c.pc_aid=$pc_aid
and c.pc_pid=$pc_pid";
    
    $res = sqlStatement($sql);
    $data = sqlFetchArray($res);
    // data_patient_url, data_medic_url
    $medic_url = $data['data_medic_url'];
    $link_medic = "href=\"$medic_url\" target=\"_blank\"";
    //
    $patinet_url = $data['data_patient_url'];
    $link_patient = "href=\"$patinet_url\" target=\"_blank\"";
    //
    $link_medic_btn = "&nbsp <a class=\"btn btn-primary\" $link_medic title=\"$medic_title\" >$medic_title</a> &nbsp ";
    $link_patient_btn = "&nbsp <a class=\"btn btn-primary\" $link_patient title=\"$patient_title\" >$patient_title</a>&nbsp ";
    
    // <li><a href="#">Link Medico</a></li>
    // <li><a href="#">Link Paciente</a></li>
    //
    /**
     * medic-set-attendance: El médico ingresa a la videoconsulta
     * medic-unset-attendance: El médico cierra la pantalla de videoconsulta
     * videoconsultation-started: Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
     * videoconsultation-finished: El médico presiona el botón Finalizar consulta
     * patient-set-attendance: El paciente anuncia su presencia
     * Enviar mail al medico y acitavar color de que el paciente esta presente
     */
    return array(
        'patient_url' => $link_patient,
        'medic_url' => $link_medic
    );
}

/**
 * Crea una video consulta desde una cita creada.
 * Para esto usa el Servicio de creacion de video consulta (SCV)
 * La categoria de la cita debe estar entre
 * las cagetoiras de tipo video consultas configuradas
 * dentro de la tabla de confguraciones de
 * de video consultas
 *
 * @param integer $pc_eid            
 */
function createVc($pc_eid)
{
    /**
     * Categorias de video consultas
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
    $sql_vc_calender = "SELECT
	c.pc_eid,
	c.pc_catid,
	c.pc_aid,
	c.pc_pid,
	c.pc_title,
	c.pc_time,
	c.pc_eventDate,
	c.pc_endDate,
	c.pc_startTime,
	c.pc_endTime,
	c.pc_duration,
	CONCAT_WS( p.fname, p.mname, p.lname ) AS patient_name,
	CONCAT_WS( m.fname, m.mname, m.lname ) AS medic_name 
FROM
	openemr_postcalendar_events AS c
	INNER JOIN patient_data AS p ON c.pc_pid = p.id
	INNER JOIN users AS m ON c.pc_aid = m.id 
WHERE
	c.pc_catid in ($vc_category_list)  and c.pc_eid=$pc_eid;";
    /**
     *
     * @var array $vc_data -
     *      datos a enviar al SCV
     */
    $vc_data = array();
    $res = sqlStatement($sql_vc_calender);
    $calendar_data = sqlFetchArray($res);
    $extra_data = array(
        'saludo' => 'Hola'
    );
    //preparar datos a enviar al SCV
    $vc_data = array(
        "medic_name" => $calendar_data['medic_name'],
        "patient_name" => $calendar_data['patient_name'],
        "days_before_expiration" => '1',
        "appointment_date" => $calendar_data['pc_eventDate'] . ' ' . $calendar_data['pc_startTime'],
        "extra" => $extra_data
    );
    /**
     *
     * @var string $vc_response respuesta de SCV
     */
    $svc_response = requestSCV($vc_data);
    // si hay respuesta
    if ($svc_response) {
        /**
         *
         * @var array $vc_data datos devueltos por el SCV
         */
        $vc_data = json_decode($svc_response, TRUE);
        // agregar video consulta a la bd
        insertVc($pc_eid, $vc_data);
        // guardar datos de acceso a la video consulta de comentarios de la cita.
        // saveVcLinks
        // enviar email de la video consulta al medico
        // sendVcMedicEmail
        // enviar email a paciente
        // sendVcPatientEmail
    }
}

/**
 * agrega una nueva video consulta a la Base de datos
 *
 * @param integer $pc_eid            
 * @param string $vc_response
 *            datos devueltos por el servicio de video consulta
 * @return number
 */
/**
 *
 * @param unknown $pc_eid            
 * @param unknown $vc_data            
 * @return boolean|number
 */
function insertVc($pc_eid, $vc_data)
{
    /**
     *
     * @var boolean $response valor de retorno verdadero/false
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
    $posevent_exists = false;
    //
    try {
        if (! $posevent_exists) {
            // Save new vc on Database
            $query = "INSERT INTO openemr.tsalud_vc ";
            $query .= "( pc_eid, success,message,data_id,data_valid_from,data_valid_to, data_patient_url, data_medic_url, data_data_url,medic_secret ) ";
            $query .= " VALUES ( $pc_eid, '$success','$message','$id', '$valid_from','$valid_to','$patient_url','$medic_url','$data_url','$medic_secret' )";
            $return = sqlInsert($query);
        } else {
            // save;
        }
    } catch (Exception $e) {
        // echo $e-// Error: Duplicate entry '1' for key 'PRIMARY'
        // return false;
    }
    return $return;
}

/**
 * solcita servicio de video consulta
 *
 * @param array $vc_data            
 * @return string respuesta del servicio de video consulta
 */
function requestSCV($vc_data)
{
    $bearToken = "1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg";
    $authorization = "Authorization: Bearer $bearToken";
    $api_url = 'https://srv3.integrandosalud.com/os-telesalud/api/videoconsultation?';
    // Create VC
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    // Returns the data/output as a string instead of raw data
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // Set your auth headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearToken
    ));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($vc_data));
    $result = curl_exec($curl);
    if (! $result) {
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}

function saveNotify($response)
{
    $json = json_decode($response, TRUE);
/**
 * `pc_eid` int(11) unsigned NOT NULL,
 * `vc_secret` varchar(1024) DEFAULT NULL,
 * `vc_medic_secret` varchar(1024) DEFAULT NULL,
 * `vc_status` varchar(1024) DEFAULT NULL,
 * `vc_medic_attendance_date` varchar(1024) DEFAULT NULL,
 * `vc_patient_attendance_date` varchar(1024) DEFAULT NULL,
 * `vc_start_date` varchar(1024) DEFAULT NULL,
 * `vc_finish_date` varchar(1024) DEFAULT NULL,
 * `vc_extra` varchar(1024) DEFAULT NULL,
 * `topic` varchar(1024) DEFAULT NULL
 */
}

$links = vcLinks(1, 2);
$patient_l = $links['patient_url'];
?>

<ul>
	<li><a
		href="http://localhost:8390/telesalud/controllers/C_TSalud_Vc.php?action=insertEvent&pc_eid=1">Crear</a>
	</li>
	<li><a href="#">Link Medico</a></li>
	<li><a href="#">Link Paciente</a></li>
</ul>
<p>&nbsp;</p>
<p>
	<strong>Lista de Notificaciones</strong>
</p>
