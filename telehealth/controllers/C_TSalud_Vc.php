<?php

declare(strict_types=1);

/**
 * 
 * 
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
// Video consutlation strting constat
//Paths
$webroot = $_SERVER['DOCUMENT_ROOT'];
define('VC', 'Teleconsultas');
define('CLASS_DIR', "$webroot/library/classes/");
define('SRC_DIR', "$webroot/src/");
define('COMMON_DIR', "$webroot/src/common");
define('Csrf_DIR', "$webroot/src/common/Csrf");
define('Uuid_DIR', "$webroot/src/common/Uuid");
define('Core_DIR', "$webroot/src/Core");
define('Services_DIR', "$webroot/Services");
define('MAIN_DIR', "$webroot/");
define('PHP_MAILER_DIR', "$webroot/telehealth/controllers/PHPMailer/src/");
// Videoconsutlation API
define('VC_API_PORT', '31443');
define('VC_API_URL', 'https://meet.telesalud.iecs.org.ar:' . VC_API_PORT . '/api/videoconsultation?');
define('VC_API_DATA_URL', 'https://meet.telesalud.iecs.org.ar:31443/api/videoconsultation/data?');
//TOKEN PRODUCTION
// define('VC_API_TOKEN', "1|OB00LDC8eGEHCAhKMjtDRUXu9buxOm2SREHzQqPz");
//TOKEN DEVELOPMENT
define('VC_API_TOKEN', "1|dcX1XEqBOOtVC0ufo3RzyrOR96Jqajzr0Ly6tLmz");
define('VC_API_AUTH', "Authorization: Bearer " . VC_API_TOKEN);
//OpenEMR Classes
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Core\Header;
use OpenEMR\Services\ClinicalNotesService;
use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Services\CodeTypesService;
use OpenEMR\Services\EncounterService;
use OpenEMR\Services\FacilityService;
use OpenEMR\Services\ListService;
//some OpenEMR globals vars and requirements 
$_GET['site'] = 'default';
$ignoreAuth = true;
//
require_once(MAIN_DIR . "interface/globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");

/**
 * Simple autoloader, so we don't need Composer just for this.
 */
class Autoloader
{
    public static function register()
    {
        // Add your class dir to include path
        set_include_path(get_include_path() . PATH_SEPARATOR . CLASS_DIR);
        // You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
        spl_autoload_extensions('.class.php');
        // Use default autoload implementation
        spl_autoload_register();
        spl_autoload_register(function ($class) {
            //
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            echo  "- After replace bar : Clase: $class : Archivo: $file  \n";
            //comon dir
            $file = str_replace("OpenEMR" . DIRECTORY_SEPARATOR . "Common", COMMON_DIR, $file);
            echo  "$class : $file - After OpenEMR\Common \n";
            //
            $file = str_replace("OpenEMR" . DIRECTORY_SEPARATOR . "Common" . DIRECTORY_SEPARATOR . "Csrf", Csrf_DIR, $file);
            echo  "$class : $file - OpenEMR\Common\Csrf\n";
            $file = str_replace("OpenEMR" . DIRECTORY_SEPARATOR . "Common" . DIRECTORY_SEPARATOR . "Uuid", Uuid_DIR, $file);
            echo  "$class : $file - OpenEMR\Common\Uuid\n";
            $file = str_replace("OpenEMR" . DIRECTORY_SEPARATOR . "Services", Services_DIR, $file);
            echo  "$class : $file - After OpenEMR\Services\n";
            //PHPMAILER
            if (strpos($file, "PHPMailer")) {
                $file = str_replace("PHPMailer" . DIRECTORY_SEPARATOR, PHP_MAILER_DIR, $file);
                $file = PHP_MAILER_DIR . $file;
            }
            if (file_exists($file)) {
                echo  "$class : $file";
                require_once $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();


/**
 * For testing
 */


/**
 * For unit testing
 */
// get from get vars
$pc_aid = isset($_GET['pc_aid']) ? $_GET['pc_aid'] : 0;
$pc_pid = isset($_GET['pc_pid']) ? $_GET['pc_pid'] : 8;
$pc_eid = isset($_GET['pc_eid']) ? $_GET['pc_eid'] : 0;
$encounter = isset($_GET['encounter']) ? $_GET['encounter'] : 0;
//
$medic_secret = isset($_GET['medic_secret']) ? $_GET['medic_secret'] : 'YjWUyBTp7W';
$data_id = isset($_GET['data_id']) ? $_GET['data_id'] : 'd8a40f11bbe3978fe511cea661feb5e651391a21';
// hardcoded values
$base64_content = '';
// $pid = 8;
// $encounter = 1;
$formid = 25;
//
/**
 * 
 */
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'insertEvent':
            createVc($pc_eid);
            // echo createVideoConsultationTest();
            break;
        case 'vcButton': // echo "generate link"; //
            // $links =
            echo showVCButtonlink($pc_aid, $pc_pid);
            break;
        case 'vcNotify':
            // asve notification test
            saveNotify();
            break;
        case 'vcGetFiles':
            //Get files
            getVcFiles($data_id, $medic_secret);
            break;
        case 'saveFile':
            // save document file
            $r = saveDocument($base64_content, $pid, $encounter, $formid);
            break;

        case 'getEvolution':
            // save document file
            echo getEvolution($encounter);
            break;
        case 'createEcounter':
            // save document file
            //function createEcounter($pid, $provider_id, $userauthorized, $facility_id, $reason = 'Teleconsulta', $pc_catid = 16)
            $provider_id = "8";
            $userauthorized = "8";
            $facility_id = "3";
            $r = createEcounter($data_id, "$pid", $provider_id, $userauthorized, $facility_id);
            break;
        default:
            break;
    }
}

/**
 * Undocumented function
 *
 * @param [type] $pid
 * @param [type] $provider_id
 * @param [type] $userauthorized
 * @param [type] $facility_id
 * @param string $reason
 * @param integer $pc_catid
 * @return void
 */
function createEcounter($data_id, $pid, $provider_id, $userauthorized, $facility_id, $reason = 'Teleconsulta', $pc_catid = 16)
{
    $facilityService = new FacilityService();
    $date = DateTimeToYYYYMMDDHHMMSS(date('Y-m-d H:i:s'));
    $onset_date = null;
    // $onset_date = isset($_POST['form_onset_date']) ? DateTimeToYYYYMMDDHHMMSS($_POST['form_onset_date']) : null;
    $sensitivity = null;
    // $pc_catid = $_POST['pc_catid'] ?? null;
    // $facility_id = $_POST['facility_id'] ?? null;
    $billing_facility = '';
    // $reason = $_POST['reason'] ?? null;
    $mode = null;
    $referral_source =  '';
    $class_code =  '';
    $pos_code =  null;
    $parent_enc_id =  null;
    $encounter_provider =  null;
    $referring_provider_id =  null;
    //save therapy group if exist in external_id column
    $external_id = isset($_POST['form_gid']) ? $_POST['form_gid'] : '';

    $discharge_disposition = null;
    $discharge_disposition = null;

    $facilityresult = $facilityService->getById($facility_id);
    $facility = $facilityresult['name'];

    // $normalurl = "patient_file/encounter/encounter_top.php";

    // $nexturl = $normalurl;

    // $provider_id = $_SESSION['authUserID'] ? $_SESSION['authUserID'] : 0;
    // $provider_id = $encounter_provider ? $encounter_provider : $provider_id;

    // $encounter_type = $_POST['encounter_type'] ?? '';
    $encounter_type_code = null;
    $encounter_type_description = null;
    // // we need to lookup the codetype and the description from this if we have one
    if (!empty($encounter_type)) {
        $listService = new ListService();
        $option = $listService->getListOption('encounter-types', $encounter_type);
        $encounter_type_code = $option['codes'] ?? null;
        if (!empty($encounter_type_code)) {
            $codeService = new CodeTypesService();
            $encounter_type_description = $codeService->lookup_code_description($encounter_type_code) ?? null;
        } else {
            // we don't have any codes installed here so we will just use the encounter_type
            $encounter_type_code = $encounter_type;
            $encounter_type_description = $option['title'];
        }
    }
    // // TSALUD- ISSUE-#16: Campos obligatorios nueva visita
    // // Desde globales se ocultan todos los campos del formulario nueva visita. 
    // // A continuación se asignan los valores obligatorios para que se pueda generar una nueva visita. 

    // // facility_id=3
    // // pc_catid=5
    // // class_code='AMB'
    // // date=fecha actual

    // if ($pc_catid==null) {
    //     $pc_catid=5;
    // }
    // if ($facility_id==null) {
    //     $facility_id=3;
    // }
    // if ($class_code==null) {
    //     $class_code='ABM';
    // }
    // if ($date==null) {
    $date = date('Y-m-d h:i:s', time()); // DateTimeToYYYYMMDDHHMMSS());
    // }

    echo $date;

    // if (
    $mode = 'new';
    // ) {
    $encounter = generate_id();
    // print_r($result);
    $query = "update telehealth_vc set encounter=$encounter where data_id='$data_id'";
    $conn = dbConn();
    $return = $conn->query($query) or trigger_error($conn->error . " " . $query);
    $result = addForm(
        $encounter,
        xlt('Telehealth Videoconstations Encounter'),
        sqlInsert(
            "INSERT INTO form_encounter SET
                date = ?,
                onset_date = ?,
                reason = ?,
                facility = ?,
                pc_catid = ?,
                facility_id = ?,
                billing_facility = ?,
                sensitivity = ?,
                referral_source = ?,
                pid = ?,
                encounter = ?,
                pos_code = ?,
                class_code = ?,
                external_id = ?,
                parent_encounter_id = ?,
                provider_id = ?,
                discharge_disposition = ?,
                referring_provider_id = ?,
                encounter_type_code = ?,
                encounter_type_description = ?",
            [
                $date,
                $onset_date,
                $reason,
                $facility,
                $pc_catid,
                "$facility_id",
                $billing_facility,
                $sensitivity,
                $referral_source,
                $pid,
                $encounter,
                $pos_code,
                $class_code,
                $external_id,
                $parent_enc_id,
                $provider_id,
                $discharge_disposition,
                $referring_provider_id,
                $encounter_type_code,
                $encounter_type_description
            ]
        ),
        "newpatient",
        $pid,
        $userauthorized,
        $date
    );

    return $return;
}


/**
 *
 * @return NULL|mysqli
 */
function dbConn()
{
    // localserver
    $servername = "ops-openemr-mysql";
    $username = "openemr";
    $password = "openemr";
    $database = "openemr";
    // dev server
    $servername = "localhost";
    $username = "admin_devopenemr";
    $password = "BxX7vZb27z";
    $database = "admin_devopenemr";
    // production server
    // $servername = "localhost";
    // $username = "admin_devopenemr";
    // $password = "BxX7vZb27z";
    // $database = "admin_devopenemr";
    //
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
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
    INNER join telehealth_vc as vcdata on cal.pc_eid = vcdata.pc_eid
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
 */
function getEvolution($encounter)
{
    $sql = "SELECT * FROM `telehealth_vc` where encounter=$encounter";
    $r = sqlS($sql);
    // echo $sql;
    return isset($r['evolution']) ? $r['evolution'] : '';
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
, m.email as medicEmail,
c.pc_facility

FROM
openemr_postcalendar_events AS c 
INNER JOIN patient_data AS p ON
c.pc_pid = p.id 
INNER JOIN users AS m ON c.pc_aid = m.id 
left join telehealth_vc as vc on c.pc_eid =vc.pc_eid

WHERE
c.pc_catid IN ($vc_category_list) and c.pc_eid=$pc_eid;";
    $br = '<br>';
    // echo $sql_vc_calender . $br;
    try {
        // $res = sqlStatement($sql_vc_calender);
        // $calendar_data = sqlFetchArray($res);
        $calendar_data = sqlS($sql_vc_calender);
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
        // print_r($data);
        /**
         *
         * @var string $vc_response -
         *      respuesta de SCV
         */
        $svc_response = requestAPI($data, CURLOPT_POST);
        // $svc_response = createVideoConsultationTest(VC_API_URL, $data, VC_API_TOKEN);
        // echo $svc_response;
        /**
         * * * @var array $vc_data datos devueltos por el SCV
         */
        $vc_data = json_decode($svc_response, TRUE);
        // print_r($vc_data);
        // si hay respuesta
        if ($vc_data['success']) {
            //save log
            logVc($vc_data['data']['id'], 'Scheduled', $svc_response);
            // agregar video consulta a la bd
            insertVc($pc_eid, $vc_data);
            // actualizar links de acceso a video consulta en evento
            updateLinksToAgenda($pc_eid, $vc_data);
            //agregar encuentro
            //function createEcounter($pid, $provider_id, $userauthorized, $facility_id, $reason = 'Teleconsulta', $pc_catid = 16)
            $pid = $calendar_data['pc_pid'];
            $provider_id = $calendar_data['pc_aid'];
            $userauthorized = $calendar_data['pc_aid'];
            $facility_id = $calendar_data['pc_facility'];
            // $provider_id=, $userauthorized, $facility_id;
            $data_id = $vc_data['data']['id'];
            createEcounter($data_id, $pid, $provider_id, $userauthorized, $facility_id);
            // enviar email de la video consulta al medico
            // sendEmail($calendar_data);
        } else {
            $error = "$br Errores en respuesta API Datos devueltos: " . print_r($vc_data, true);
            logVc('0', 'Error', "$error");
        }
    } catch (Exception $e) {

        logVc('0', 'Error', "{$e->getMessage()}");
    }
}
/**
 * Undocumented function
 *
 * @return void
 */
function addEcounter()
{
    //
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
    //
    try {
        if (empty($to)) {
            echo "Email could not be
           sent, the address supplied: '$to' was empty or invalid.";
            return false;
        } else {
            $contents = print_r($mailData, true);
            // saveFile($content);
            $fullPath = getcwd();
            $file = 'log.txt';
            file_force_contents("$fullPath/$file", $contents);
            // Login email and password
            $login = "sending@lugaronline.com";
            $pass = "cxWXKk35mc";
            $body = $mailData['body'];
            $subject = $mailData['subject'];
            $from = 'sending@lugaronline.com';
            $fromName = 'All in One OPS';
            $mailHost = "mail.lugaronline.com";
            //
            $mail = new PHPMailer();
            //
            // mail server
            $mail->IsSMTP();
            $mail->Host = $mailHost;
            $mail->SMTPAuth = true;
            $mail->Username = $from;
            $mail->Password = $pass;
            // // this is a temporary config item until the rest of the per practice billing settings make their way in
            $mail->From = $from;
            $mail->FromName = $fromName;
            $mail->isMail();
            $mail->Body = $body;
            $mail->Subject = $subject;
            $mail->AddAddress($to);
            // //
            if ($mail->Send()) {
                echo "Email was successfully sent to: " . $to;
                return false;
            } else {
                echo "There has been a mail error sending to " . $to . " " . $mail->ErrorInfo;
                return true;
            }
            // $headers = array(
            //     'From' => $from,
            //     'Reply-To' => $from,
            //     'X-Mailer' => 'PHP/' . phpversion()
            // );
            // if (xxmail($from, $to, $subject, $body, $headers)) {
            //     echo 'todo ok';
            // } else {
            //     echo 'no  ok';
            // }
            // mail($to, $subject, $body, $headers);
            //     ,
            //     array|string $additional_headers = [],
            //     string $additional_params = ""
            // ;
        }
    } catch (Exception $e) {
        $contents = $e->getMessage();
    }

    // saveFile($content);
}

// function saveFile($content, )
// {

// // Open the file to get existing content
// $current = file_get_contents($file);
// // Append a new person to the file
// $current .= $content;
// // Write the contents back to the file
// file_put_contents($file, $current);
// }
function file_force_contents($fullPath, $contents, $flags = 0)
{
    $parts = explode('/', $fullPath);
    array_pop($parts);
    $dir = implode('/', $parts);

    if (!is_dir($dir))
        mkdir($dir, 0777, true);

    file_put_contents($fullPath, $contents, $flags);
}

function xxmail($from, $to, $subject, $body, $headers)
{

    // Login email and password
    $login = "sending@lugaronline.com";
    $pass = "cxWXKk35mc";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
    stream_context_set_option($ctx, 'ssl', 'verify_peer_name', false);
    try {
        // echo $socket = stream_socket_client('ssl://s1031.lugaronline.com:587', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        $socket = stream_socket_client('tcp://s1031.lugaronline.com:587', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$socket) {
            print "Failed to connect $err $errstr\n";
            return;
        } else {
            // Http
            // fwrite($socket, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
            // Smtp
            $buffer = 8192;
            $host = 'mac-yois';
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

            // echo fwrite($socket, "MAIL FROM: <$from>\r\n");
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
    $query = "update openemr_postcalendar_events set
pc_hometext='$pc_hometext' where pc_eid=$pc_eid;";
    // echo
    // $query;
    // return sqlStatement($sql_update_pc_hometext);
    $conn = dbConn();
    return $conn->query($query) or trigger_error($conn->error . " " . $query);
}
/**
 * Undocumented function
 *
 * @param [type] $data_id
 * @param [type] $status
 * @param [type] $json_response
 * @return void
 */
function logVc($data_id, $status, $json_response)
{
    $query = "insert into telehealth_vc_log (data_id,status,response) VALUES('$data_id','$status','$json_response')";
    $conn = dbConn();
    return $conn->query($query) or trigger_error($conn->error . " " . $query);
}
/**
 * Undocumented function
 *
 * @param [type] $pc_eid
 * @param [type] $data_id
 * @param [type] $topic
 * @param [type] $medic_secret
 * @param [type] $appstatus
 * @param [type] $json_response
 * @return void
 */
function updateAppStatus($pc_eid, $data_id, $topic, $medic_secret, $appstatus, $json_response)
{
    $conn = dbConn();
    $result = false;
    if ($conn) {
        $query = "update openemr_postcalendar_events set pc_apptstatus='$appstatus' where pc_eid=$pc_eid;";
        $result = $conn->query($query) or trigger_error($conn->error . " " . $query);
        //save log
        logVc($data_id, $topic, $json_response);
        //si el medico cierran la videoconsulta
        if ($topic == 'videoconsultation-finished') {
            // echo "Status ok getting files..";
            //Obtener y guardar adjuntos
            getVcFiles($data_id, $medic_secret);
        }
        $conn->close();
    }
    return $result;
}
/**
 * Undocumented function
 *
 * @param [type] $data_id
 * @param [type] $medic_secret
 * @return void
 */
function getVcFiles($data_id, $medic_secret)
{
    $result = false;
    try {
        $data = array(
            "vc" => $data_id,
            "medic" => $medic_secret
        );
        // echo "Requesting API...";
        // Api json response
        $api_response = requestAPI($data, '', true);
        // $api_response = file_get_contents('ExampleResponse.json');
        // print_r($api_response);
        //get api response
        $response_data = json_decode($api_response, true);
        //db conn
        $conn = dbConn();
        //if connected and files inside answer
        if ($conn && isset($response_data['data']['files'])) {
            // print_r($response_data);            
            // echo "Getting Files...";
            //get files
            $files = $response_data['data']['files'];
            //files types list
            $types = array('medic', 'patient', 'paciente');
            // loop files types
            foreach ($types as $key => $type) {
                // echo "<br>Type: $type";
                //if type files
                if (isset($files[$type])) {
                    // echo "<br>Getting files from $type...";
                    //get content file
                    $context_files = $files[$type];
                    //loop type files
                    foreach ($context_files as $key => $file) {
                        //if have file content
                        if (!empty($file['file'])) {
                            // echo "<br>Saving files $type...";
                            $filetext = $file['file'];
                            $fileName = strtolower(trim(preg_replace('#\W+#', '_', $file['description']), '_'));
                            //get event data
                            $event_data = getEventData($data_id);
                            if (isset($event_data['pc_pid'])) {
                                //
                                $patient_id = $event_data['pc_pid'];
                                $encounter = $event_data['encounter'];
                                //Save file
                                saveDocument($filetext, $patient_id, $encounter, $fileName);
                            }
                        } else {
                            // echo "<br>$type have not file";
                        }
                    }
                    //end loop type files
                }
            }
            //Guardar Evolucion
            $evolution = isset($response_data['data']['evolution']) ? $response_data['data']['evolution'] : '';
            $query = "UPDATE telehealth_vc set evolution='$evolution' where data_id='$data_id'";
            $result = $conn->query($query) or trigger_error($conn->error . " " . $query);
            //  end loop types
            //save log
            logVc($data_id, 'Download attached files', $api_response);
        }
        $result = true;
    } catch (Exception $e) {
        //
        echo $e;
        // Error: Duplicate entry '1' for key 'PRIMARY' //
    }
    return $result;
    // print_r();
}


/**
 * save the document file
 *
 * @param [type] $base64_content
 * @param [type] $pid patient id
 * @param [type] $encounter encounter id
 * @param [type] $formid encounter form id
 * @return void
 */
function saveDocument($filetext, $patient_id, $encounter, $fileName)
{
    $d = null;
    // try {
    //decode base64 string
    $imgdata = base64_decode($filetext);
    //file info
    $f = finfo_open();
    $mimetype = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
    //file extentension
    $type = explode('/', $mimetype)[1];
    //
    if ($type !== 'x-empty') {
        $fileName = check_file_dir_name($fileName) . ".$type";
        // get catid 
        $query = "SELECT id FROM categories where name ='" . VC . "'";
        $result = sqlS($query);
        $category_id = $result['id'];

        $error = '';
        $d = new Document();
        $d->encounter_id = $encounter;
        $rc = $d->createDocument(
            $patient_id,
            $category_id,
            $fileName,
            $mimetype,
            $imgdata,
            $_GET['higher_level_path'] ?? '',
            $_POST['path_depth'] ?? '',
        );

        if ($rc) {
            echo  $rc . "\n";
        } else {
            // $this->assign("upload_success", "true");
            // print_r($rc);
        }
    }
    return $d;
    // print_r($d);
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
        $query = "INSERT INTO telehealth_vc ";
        $query .= "( pc_eid,
success,message,
data_id,
valid_from,
valid_to,
patient_url,
medic_url,
url,medic_secret,created) ";
        $query .= " VALUES (
$pc_eid, '$success','$message','$id',
'$valid_from','$valid_to','$patient_url',
'$medic_url','$data_url','$medic_secret',
current_timestamp())";
        // $return = sqlInsert($query);
        // echo $sql;
        $conn = dbConn();
        $return = $conn->query($query); // or trigger_error($conn->error . " " . $query);
    } catch (Exception $e) {
        // echo $e;
        logVc('0', 'Error', "{$e->getMessage()}");
    }
    //save log
    logVc('0', 'Saved', 'Telehalth saved on DB ok');
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
/**
 * Undocumented function
 *
 * @param [type] $data
 * @param string $method
 * @param boolean $files
 * @return string
 */
function requestAPI($data, $method = CURLOPT_POST, $files = false)

{
    try {
        // echo "<br>Init curl...";
        // Create VC
        $curl = curl_init();
        if ($files) {
            // echo "<br>Requesting Files URL: " . VC_API_DATA_URL;
            // $qry_str='';
            curl_setopt($curl, CURLOPT_URL, VC_API_DATA_URL . http_build_query($data));
            // curl_setopt($curl, CURLOPT_HEADER, 1); 
        } else {
            echo "<br>Requesting new teleconsultation: " . VC_API_DATA_URL;
            curl_setopt($curl, CURLOPT_URL, VC_API_URL);
        }

        // Returns the data/output as a string instead of raw data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Set your auth headers
        /**
         * --header 'Content-Type: application/x-www-form-urlencoded'
         */
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            VC_API_AUTH
        ));
        if (!empty($method)) {
            $jsdata = json_encode($data);
            // echo "<br>Using post $method with  $jsdata</br>";
            curl_setopt($curl, $method, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsdata);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        // echo "<br>Calling API...";
        $result = curl_exec($curl);
        if (!$result) {
            $error = "API $curl - Connection Failure";
            logVc('0', 'Error', "$error");
            die($error);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        logVc('0', 'Error', "{$e->getMessage()}");
    } finally {
        curl_close($curl);
    }
    // print_r($result);
    return $result;
}

/**
 * Undocumented function
 *
 * @param [type] $api_url
 * @param [type] $data
 * @param [type] $token
 * @return void
 */
function createVideoConsultationTest($api_url, $data, $token)
{


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => VC_API_URL, //'https://meet.telesalud.iecs.org.ar:31443/api/videoconsultation?',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'appointment_date=2022-12-6%2007%3A00%3A00&days_before_expiration=1&medic_name=medico-yois&patient_name=paciente-yois&extra%5B%5D=hola',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 1|dcX1XEqBOOtVC0ufo3RzyrOR96Jqajzr0Ly6tLmz',
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}

/**
 * Capture VC notifications
 */
function saveNotify()
{
    try {
        $r = array(
            'success' => 'nada'
        );
        // echo  "start saving notification...";
        //getting POS from API
        $json_response = file_get_contents('php://input');
        $data = json_decode($json_response, true);
        // echo  "<br>POST Data: " . print_r($data, true);
        //if have topic is a notification
        if (isset($data['topic'])) {
            // echo  "<br>getting status from stautus table...";
            $topic = $data['topic'];
            $appstatus = getappStatus($topic);
            // echo  "satus ok and is $appstatus ";
            //
            $data_id = $data['vc']['secret'];
            $sql = "SELECT * FROM telehealth_vc where data_id='$data_id';";
            // echo $sql;
            $records = sqlS($sql);
            // print_r($records);
            // if exists
            if ($records) {
                // echo  "updating appointment status...";
                $pc_eid = $records['pc_eid'];
                $medic_secret = $records['medic_secret'];
                //save appointment state
                updateAppStatus($pc_eid, $data_id, $topic, $medic_secret, $appstatus, $json_response);
            }
            $r = array(
                'success' => 'ok'
            );
        }
    } catch (Exception $e) {
        echo "ups... an erorr ocurred. Is about this {$e->getMessage()}";
    }
    return json_encode($r);
}
/**
 * retrieve patient id, calendar event id and form id 
 *
 * @param [type] $data_id
 * @return void
 */
function getEventData($data_id)
{
    $query = "SELECT 
    vc.data_id,
    e.pc_eid,
    e.pc_pid,
    vc.encounter,
    IFNULL((SELECT 
                    f.id
                FROM
                    forms AS f
                WHERE
                    f.pid = e.pc_pid 
                    and f.encounter=vc.encounter
                LIMIT 1),
            0) AS formId
FROM
    telehealth_vc AS vc
        INNER JOIN
    openemr_postcalendar_events e ON vc.pc_eid = e.pc_eid
WHERE
    data_id = '$data_id'
;";
    // echo "<br>$query";
    return  sqlS($query);
}
/**
 * get topic appointment status from list_options
 *
 * @param unknown $topic            
 * @return NULL
 */
function getappStatus($topic)
{
    $sql_appstatus = "SELECT * FROM telehealth_vc_topic where topic='$topic';";
    $records_appstatus = sqlS($sql_appstatus);
    return $records_appstatus['value'];
}
