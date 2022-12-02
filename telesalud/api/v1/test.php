<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as JitsiRequest;

$client = new Client();
$headers = [
  'Authorization' => 'Bearer 1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg',
  'Content-Type' => 'application/x-www-form-urlencoded'
];
$options = [
'form_params' => [
  'appointment_date' => '2022-10-24 12:30:00',
  'days_before_expiration' => '1',
  'medic_name' => 'medico 2',
  'patient_name' => 'paciente 3',
  'extra[]' => 'hola mundo'
]];
$request = new JitsiRequest('POST', 'https://srv3.integrandosalud.com/os-telesalud/api/videoconsultation?', $headers);
$res = $client->sendAsync($request, $options)->wait();
echo $res->getBody();

/*
"{
    success":true,
    "message":"",
    "data":{
        "id":"0b108dcb56a1e0f682f9389856fec400c99e218b",
        "valid_from":"2022-10-24T15:20:00.000000Z",
        "valid_to":"2022-10-25T15:30:00.000000Z",
        "patient_url":"https:\/\/srv3.integrandosalud.com\/os-telesalud\/videoconsultation?vc=0b108dcb56a1e0f682f9389856fec400c99e218b",
        "medic_url":"https:\/\/srv3.integrandosalud.com\/os-telesalud\/videoconsultation?vc=0b108dcb56a1e0f682f9389856fec400c99e218b&medic=YpxFAXAVri",
        "data_url":"https:\/\/srv3.integrandosalud.com\/os-telesalud\/api\/videoconsultation\/data?vc=0b108dcb56a1e0f682f9389856fec400c99e218b&medic=YpxFAXAVri"
    }
}
*/

/*
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as JitsiRequest;

$vcBaseUrl = 'https://srv3.integrandosalud.com/os-telesalud/api/';
$endpoint = 'videoconsultation';
$vcToken = '1|hqg8cSkfrmLVwq12jK6yAv03HHGyP6BYJNpH84Wg';
$request = [
    'appointment_date'=> '2022-10-24 12:30:00',
    'days_before_expiration' => 1,
    'medic_name' => 'Dr. Luis García',
    'patient_name' => 'Alejandro Benavides'
];
$client = new Client();
$headers = [
    'content-type' => 'application/json',
    'Accept' => 'application/json',
    'Authorization' => 'Bearer ' . $vcToken
];
$body = "{
    appointment_date: '{$request['appointment_date']}',
    'days_before_expiration': {$request['days_before_expiration']},
    'medic_name': '{$request['medic_name']}',
    'patient_name': '{$request['patient_name']}'
}";

$url = $vcBaseUrl . $endpoint;
$jitsiRequest = new JitsiRequest('POST', $url, $headers, $body);
$response = $client->sendAsync($jitsiRequest)->wait();

$data = $response->getBody();

print_r($data);
*/