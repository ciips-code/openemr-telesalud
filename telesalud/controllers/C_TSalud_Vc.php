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
print_r($GLOBALS["pid"]);

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
    $patient_title = xla("Patient Teleconsultation");
    $medic_title = xla("Medic Teleconsultation");
    
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
    $data = sqlFetchArray($tres);
    // data_patient_url, data_medic_url
    $patinet_url = $data['data_patient_url'];
    $medic_url = $data['data_medic_url'];
    $link_patient = "&nbsp <a class=\"btn btn-primary\" href=\"$patinet_url\" title=\"$patient_title\" target=\"_blank \">$patient_title</a>&nbsp ";
    $link_medic = "&nbsp <a class=\"btn btn-primary\" href=\"$medic_url\" title=\"$medic_title\" target=\"_blank \">$medic_title</a> &nbsp ";
    return array(
        'patient_url' => $link_patient,
        'medic_url' => $link_medic
    );
}

/**
 * Create a VC
 *
 * @param unknown $pc_eid            
 */
function createVc($pc_eid)
{
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
	CONCAT_WS( m.fname, m.mname, m.lname ) AS medic_name 
FROM
	openemr_postcalendar_events AS c
	INNER JOIN patient_data AS p ON c.pc_pid = p.id
	INNER JOIN users AS m ON c.pc_aid = m.id 
WHERE
	c.pc_catid = 16 and c.pc_eid=$pc_eid;";
    $data = array();
    $tres = sqlStatement($sql);
    $trow = sqlFetchArray($tres);
    $data = array(
        "medic_name" => $trow['medic_name'],
        "patient_name" => $trow['patient_name'],
        "days_before_expiration" => '1',
        "appointment_date" => $trow['pc_eventDate'] . ' ' . $trow['pc_startTime'],
        "extra" => array(
            'saludo' => 'Hola'
        )
    );
    $vc_response = requestVc($data);
    if ($vc_response) {
        insertVc($pc_eid, $vc_response);
    }
}

/**
 *
 * @param unknown $result            
 * @return unknown
 */
function insertVc($pc_eid, $result)
{
    $json = json_decode($result, TRUE);
    //
    $success = $json['success'];
    $message = $json['message'];
    $id = $json['data']['id'];
    $valid_from = $json['data']['valid_from'];
    $valid_to = $json['data']['valid_to'];
    $patient_url = $json['data']['patient_url'];
    $medic_url = $json['data']['medic_url'];
    $data_url = $json['data']['data_url'];
    try {
        // Save new vc on Database
        $query = "INSERT INTO openemr.tsalud_vc ";
        $query .= "( pc_eid, success,message,data_id,data_valid_from,data_valid_to, data_patient_url, data_medic_url, data_data_url ) ";
        $query .= " VALUES ( $pc_eid, '$success','$message','$id', '$valid_from','$valid_to','$patient_url','$medic_url','$data_url' )";
        return sqlInsert($query);
    } catch (Exception $e) {
        // echo $e-// Error: Duplicate entry '1' for key 'PRIMARY'
        // return false;
    }
}

/**
 *
 * @param unknown $data            
 * @return unknown
 */
function requestVc($data)
{
    $bearToken = "1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg";
    $authorization = "Authorization: Bearer $bearToken";
    $url = 'https://srv3.integrandosalud.com/os-telesalud/api/videoconsultation?';
    // Create VC
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    // Returns the data/output as a string instead of raw data
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // Set your auth headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearToken
    ));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($curl);
    if (! $result) {
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}

