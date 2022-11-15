<?php
use PHPMailer\PHPMailer\PHPMailer;

// C_TSalud_Vc.php
/**
 * - Mostrar link iniciar teleconsutla en encabezado de resumen de paciente.
 * - Mostrar opciones de teleconsulta en el momento y hora correctas
 * - corregir el error sql de traducciones.
 * - Traducciones del excel
 * ----
 * API OpenEmr
 * - Envio de email a paciente y medico
 * - Recibir notificaciones :
 * - medic-set-attendance: El médico ingresa a la videoconsulta
 * - medic-unset-attendance: El médico cierra la pantalla de videoconsulta
 * - videoconsultation-started: Se da por iniciada la videoconsulta, esto se da cuando tanto el médico como el paciente están presentes
 * - videoconsultation-finished: El médico presiona el botón Finalizar consulta
 * - patient-set-attendance: El paciente anuncia su presencia
 * -Enviar mail al medico y acitavar color de que el paciente esta presente
 */
require_once ($p = $_SERVER['DOCUMENT_ROOT'] . "/telesalud/globals.php");

/**
 *
 * @return NULL|mysqli
 */
function dbConn()
{
    $servername = "localhost";
    $username = "admin_devopenemr";
    $password = "BxX7vZb27z";
    $database = "admin_devopenemr";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        $conn = null;
    }
    // echo "Connected successfully";
    return $conn;
}

/**
 *
 * @param unknown $conn            
 * @param unknown $sql            
 * @return array|NULL[]
 */
function sqlS($sql)
{
    $r = array();
    try {
        $conn = dbConn();
        if ($conn) {
            // echo $sql;
            $result = $conn->query($sql) or trigger_error($conn->error . " " . $sql);
            if ($result !== false && $result->num_rows > 0) {
                $r = $result->fetch_assoc();
            }
            $conn->close();
        }
    } catch (Exception $e) {
        $r = array(
            'error' => $e->getMessage()
        );
    }
    return $r;
}

/**
 * Show VC HTML Button link
 *
 * @param integer $authUserID            
 * @param integer $patientID            
 * @param string $url_field_name            
 * @return string
 */
function showVCButtonlink($authUserID, $patientID, $url_field_name = 'medic_url', $vcCatList = '16')
{
    try {
        $r = '';
        $sql = "
            
SELECT cal.pc_eid,
    cal.pc_aid,
    cal.pc_pid,
    cal.pc_title,
    pc_hometext,
    pc_startTime,
    pc_endTime,
    vcdata.*
FROM `openemr_postcalendar_events` as cal
    INNER join tsalud_vc as vcdata on cal.pc_eid = vcdata.pc_eid
    INNER JOIN patient_data AS p ON cal.pc_pid = p.id
where pc_eventDate = current_date()
    and CURRENT_TIME BETWEEN cal.pc_startTime and cal.pc_endTime
    and cal.pc_catid IN ($vcCatList)
    and cal.pc_aid = $authUserID
    and cal.pc_pid = $patientID";
        
        $row = sqlS($sql);
        if (isset($row[$url_field_name])) {
            $url = $row[$url_field_name];
            $r .= vcButton($url, $url_field_name);
        }
        // }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    return $r;
}

/**
 *
 * @param unknown $url            
 * @param unknown $url_field_name            
 * @return string
 */
function vcButton($url, $url_field_name)
{
    $button = '';
    if ($url_field_name == 'medic_url') {
        // xlt("Medic Teleconsultation");
        $title = 'Start video consultation';
    } else {
        // xlt("Patient Teleconsultation");
        $title = 'Patient Teleconsultation';
    }
    // echo $url;
    $link_element = "href=\"$url\" target=\"_blank\"";
    $button = " &nbsp  <a class=\"btn btn-primary\" $link_element title=\"$title\" >$title</a>";
    return $button;
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
    $sql_vc_calender = "
SELECT c.pc_eid, c.pc_catid, c.pc_aid, c.pc_pid,
c.pc_title, c.pc_time, c.pc_eventDate as encounterDate,
c.pc_endDate, c.pc_startTime as encounterTime,
c.pc_endTime, c.pc_duration, 
CONCAT_WS( p.fname, p.mname, p.lname ) AS patientFullName, 
CONCAT_WS( m.fname, m.mname, m.lname ) AS medicFullName
, p.email as patientEmail
,  vc.patient_url as patientEncounterUrl
,  vc.medic_url as medicEncounterUrl
, m.email as medicEmail

FROM
openemr_postcalendar_events AS c 
INNER JOIN patient_data AS p ON
c.pc_pid = p.id 
INNER JOIN users AS m ON c.pc_aid = m.id 
left join tsalud_vc as vc on c.pc_eid =vc.pc_eid

WHERE
c.pc_catid IN ($vc_category_list) and c.pc_eid=$pc_eid;";
    // echo $sql_vc_calender;
    try {
        $res = sqlStatement($sql_vc_calender);
        $calendar_data = sqlFetchArray($res);
        $extra_data = array(
            'saludo' => 'Hola'
        );
        
        // preparar datos a enviar al SCV
        $appoinment_date = $calendar_data['encounterDate'] . ' ' . $calendar_data['encounterTime'];
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
            "medic_name" => $calendar_data['medicFullName'],
            "patient_name" => $calendar_data['patientFullName'],
            "days_before_expiration" => '1',
            "appointment_date" => "$appoinment_date",
            "extra" => $extra_data
        );
        /**
         *
         * @var string $vc_response -
         *      respuesta de SCV
         */
        $svc_response = requestAPI($data, CURLOPT_POST);
        /**
         * * * @var array $vc_data datos devueltos por el SCV
         */
        $vc_data = json_decode($svc_response, TRUE);
        // si hay respuesta
        if ($vc_data['success']) {
            // agregar video consulta a la bd
            insertVc($pc_eid, $vc_data);
            // actualizar links de acceso a video consulta en evento
            updateLinksToAgenda($pc_eid, $vc_data);
            // enviar email de la video consulta al medico
            // sendEmail($calendar_data);
        } else {
            echo "Errores en respuesta API Datos devueltos: " . print_r($vc_data, true);
        }
    } catch (Exception $e) {
        // ehco $e
    }
}

/**
 *
 * @param unknown $calendar_data            
 */
function sendEmail($calendar_data)
{
    echo 'enviando mail...';
    $mailData = emailMessageFor($calendar_data);
    $to = $mailData['to'];
    print_r($mailData);
    //
    if (empty($to)) {
        echo "Email could not be
        sent, the address supplied: '$to' was empty or invalid.";
        return false;
    } else {
        // $mail->Host = $mailHost;
        // $mail->Mailer = "mail";
        $body = $mailData['body'];
        $subject = $mailData['subject'];
        $from = 'yois@zoomtecnologias.com';
        $fromName = 'All in One OPS';
        $mailHost = "lugaronline.com";
        //
        $mail = new PHPMailer();
        /**
         * Username:
         * yois@zoomtecnologias.com
         * Password:
         * OntnnVus9c
         * IMAP hostname:
         * s1031.lugaronline.com
         * IMAP port:
         * 143
         * IMAP security:
         * STARTTLS
         * IMAP auth method:
         * Normal password
         * SMTP hostname:
         * s1031.lugaronline.com
         * SMTP port:
         * 587
         * SMTP security:
         * STARTTLS
         * SMTP auth method:
         * Normal password
         * Webmail URL:
         * /webmail/
         *
         * @var Ambiguous $mailData
         */
        // mail server
        // $mail->IsSMTP();
        // $mail->Host = "s1031.lugaronline.com";
        // $mail->SMTPAuth = true;
        // $mail->Username = 'mails.sending@lugaÏronline.com';
        // $mail->Password = 'OntnnVus9c';
        // // this is a temporary config item until the rest of the per practice billing settings make their way in
        // $mail->From = $fromEmail;
        // $mail->FromName = $fromName;
        // $mail->isMail();
        // $mail->Body = $body;
        // $mail->Subject = $subject;
        // $mail->AddAddress($to);
        // //
        // if ($mail->Send()) {
        // echo "Email was successfully sent to: " . $email;
        // return false;
        // } else {
        // echo "There has been a mail error sending to " . $to . " " . $mail->ErrorInfo;
        // return true;
        // }
        $headers = array(
            'From' => $from,
            'Reply-To' => $from,
            'X-Mailer' => 'PHP/' . phpversion()
        );
        if (xxmail($from, $to, $subject, $body, $headers)) {
            echo 'todo ok';
        } else {
            echo 'no  ok';
        }
        // ,
        // array|string $additional_headers = [],
        // string $additional_params = ""
        ;
    }
}

function xxmail($from, $to, $subject, $body, $headers)
{
    
    // Login email and password
    $login = "yois@zoomtecnologias.com";
    $pass = "2vIXKmPKEz";
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
    stream_context_set_option($ctx, 'ssl', 'verify_peer_name', false);
    try {
        // echo $socket = stream_socket_client('ssl://s1031.lugaronline.com:587', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        echo $socket = stream_socket_client('tcp://s1031.lugaronline.com:587', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (! $socket) {
            print "Failed to connect $err $errstr\n";
            return;
        } else {
            // Http
            // fwrite($socket, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
            // Smtp
            $buffer = 8192;
            $host = 'mac-yois.lugaronline.com';
            echo fread($socket, $buffer);
            echo fwrite($socket, "EHLO $host\r\n");
            echo fread($socket, $buffer);
            
            // Start tls connection
            echo fwrite($socket, "STARTTLS\r\n");
            echo fread($socket, $buffer);
            
            echo stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
            
            // Send ehlo
            echo fwrite($socket, "EHLO $host\r\n");
            echo fread($socket, $buffer);
            
            // echo fwrite($socket, "MAIL FROM: <hello@cool.com>\r\n");
            // echo fread($socket,8192);
            
            echo fwrite($socket, "AUTH LOGIN\r\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, base64_encode($login) . "\r\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, base64_encode($pass) . "\r\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, "rcpt to: <$to>\r\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, "DATA\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, "Date: " . time() . "\r\nTo: <$to>\r\nFrom:<$from\r\nSubject:$subject\r\n.\r\n");
            echo fread($socket, $buffer);
            
            echo fwrite($socket, "quit \n");
            echo fread($socket, $buffer);
            
            /* Turn off encryption for the rest */
            // stream_socket_enable_crypto($fp, false);
            
            fclose($socket);
        }
    } catch (Exception $e) {
        echo $e;
    }
}

/**
 * returns subjetc, body and sender email to send
 *
 * @param array $consultationData            
 * @param string $for            
 * @return string[]|unknown[]
 */
function emailMessageFor($consultationData, $for = 'pac')
{
    $patientFullName = $consultationData['patientFullName'];
    $medicFullName = $consultationData['medicFullName'];
    //
    $encounterDate = $consultationData['encounterDate'];
    $encounterTime = $consultationData['encounterTime'];
    //
    $medicEncounterUrl = $consultationData['medicEncounterUrl'];
    $patientEncounterUrl = $consultationData['patientEncounterUrl'];
    //
    // We build the body to patient y doctor
    if ($for == 'doc') {
        $result = [
            'to' => $consultationData['medicEmail'],
            'subject' => "[All in One OPS ] - Nueva video consulta con el Paciente {$patientFullName}",
            'body' => "Hola, se ha agendado una video consulta médica con el paciente {$patientFullName} el día {$encounterDate} a las {$encounterTime}. <br> <br> Para acceder a la video consulta ingrese al siguiente enlace: <br> <a href='{$medicEncounterUrl}' target='_blank'>{$medicEncounterUrl}</a>"
        ];
    } else {
        $result = [
            'to' => $consultationData['patientEmail'],
            'subject' => "[All in One OPS ] - Nueva video consulta para el {$encounterDate} a las {$encounterTime}",
            'body' => "Hola, {$patientFullName} usted tiene una video consulta médica con el médico {$medicFullName} para el día {$encounterDate} a las {$encounterTime}. <br> <br> Para acceder a la video consulta médica ingrese al siguiente enlace: <br> <a href='{$patientEncounterUrl}' target='_blank'>{$patientEncounterUrl}</a>"
        ];
    }
    return $result;
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
    $conn = dbConn();
    $pc_hometext = mysqli_real_escape_string($conn, "Accesos a la video consulta:
<ul>
<li>Profesional: <a href=\"{$medic_url}\" target=\"_blank\" id=\"medicButton\">{$medic_url}</a></li>
<li>Paciente: <a href=\"{$patient_url}\" target=\"_blank\" id=\"patientButton\">{$patient_url}</a></li>
</ul>
");
    $sql_update_pc_hometext = "update openemr_postcalendar_events set
pc_hometext='$pc_hometext' where pc_eid=$pc_eid;";
    // echo
    $sql_update_pc_hometext;
    return sqlStatement($sql_update_pc_hometext);
}

/**
 *
 * @param unknown $pc_eid            
 * @param unknown $status            
 * @return recordset
 */
function updateScheduleStatus($pc_eid, $status)
{
    $conn = dbConn();
    $sql = "update openemr_postcalendar_events set pc_apptstatus='$status' where pc_eid=$pc_eid;";
    // echo $sql;
    return sqlStatement($sql);
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
success,message,
data_id,
valid_from,
valid_to,
patient_url,
medic_url,
url,medic_secret ) ";
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
 * @param string $method            
 * @param string $bearToken            
 * @param unknown $authorization            
 * @param string $api_url            
 * @return string -
 *         respuesta del servicio de video consulta
 */
function requestAPI($data, $method, $api_url = 'https://srv3.integrandosalud.com/os-telesalud/api/videoconsultation?')

{
    $bearToken = "1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg";
    $authorization = "Authorization: Bearer $bearToken";
    
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
        
        curl_setopt($curl, $method, 1);
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
}

// include_once

/**
 * Capture VC notifications
 */
function getNotinfy()
{
    // try {
    $r = array(
        'success' => 'nada'
    );
    // echo 'recibiendo notificaciones';
    // get POST
    // $post_json = '{"vc":{"secret":"172a73071f4418badd8852de5d38547bd37028e0",
    // "medic_secret":"dBk55KtSpz",
    // "status":"Valid",
    // "medic_attendance_date":"2022-11-15T13:46:29.000000Z",
    // "patient_attendance_date":null,
    // "start_date":null,
    // "finish_date":null,
    // "extra":{"saludo":"Hola"}},
    // "topic":"medic-set-attendance"}';
    // $data = json_decode($post_json);
    //
    $data = json_decode($_POST);
    if (isset($data->topic)) {
        $topic = $data->topic;
        // $data_id = 'd39b03a01ab5aa5d7bb36487c840638ac435d36c';
        $data_id = $data->vc->secret;
        $appstatus = getappStatus($topic);
        //
        $sql = "SELECT * FROM openemr.tsalud_vc where data_id='$data_id';";
        $records = sqlS($sql);
        //
        // print_r($records);
        if ($records) {
            $pc_eid = $records['pc_eid'];
            updateScheduleStatus($pc_eid, $appstatus);
        }
        $r = array(
            'success' => 'ok'
        );
    }
    // } catch (Exception $e) {
    // $r = array(
    // 'error' => $e->getMessage()
    // );
    // }
    return $r;
}

/**
 * get topic appointment status from list_options
 *
 * @param unknown $topic            
 * @return NULL
 */
function getappStatus($topic)
{
    $sql_appstatus = "SELECT * FROM openemr.tsalud_vc_topic where topic='$topic';";
    $records_appstatus = sqlS($sql_appstatus);
    $appstatus = $records_appstatus['value'];
    return $appstatus;
}

'../globals.php';
// print_r($GLOBALS["pid"]); /** * Only for demo */
$pc_aid = 5;
$pc_pid = 1;
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'insertEvent':
            createVc(1);
            break;
        case 'vcButton': // echo "generate link"; //
            $pc_aid = $_GET['pc_aid'];
            $pc_pid = $_GET['pc_pid'];
            // $links =
            echo showVCButtonlink($pc_aid, $pc_pid);
            break;
        case 'vcNotify':
            getNotinfy();
        default:
            break;
    }
}
