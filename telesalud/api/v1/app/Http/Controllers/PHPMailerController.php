<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;
use App\Models\TsaludVcConfig;

class PHPMailerController extends Controller
{

    /**
     * 
     */
    public function composeEmail(Request $request) 
    {
        
        $emailsSent = false;
        $validate = $request->validate([
            'patientFullName'       => 'required|string|max:255',
            'patientEmail'          => 'required|string|email|max:255',
            'patientEncounterUrl'   => 'required|string',
            'doctorFullName'        => 'required|string|max:255',
            'doctorEmail'           => 'required|string|email|max:255',
            'doctorEncounterUrl'    => 'required|string',
            'encounterDate'         => 'required|string',
            'encounterTime'         => 'required|string'
        ]);
        
        $patientFullName     = $validate['patientFullName'];
        $patientEmail        = $validate['patientEmail'];
        $patientEncounterUrl = $validate['patientEncounterUrl'];

        $doctorFullName      = $validate['doctorFullName'];
        $doctorEmail         = $validate['doctorEmail'];
        $doctorEncounterUrl  = $validate['doctorEncounterUrl'];

        $encounterDate       = $validate['encounterDate'];
        $encounterTime       = $validate['encounterTime'];

        // We build the body to patient y doctor
        $patient = [
            'to' => $patientEmail,
            'subject' => "[All in One OPS ] - Usted tiene una video consulta para el {$encounterDate} a las {$encounterTime}",
            'body' => "Hola, {$patientFullName} usted tiene una video consulta médica con el médico {$doctorFullName} para el día {$encounterDate} a las {$encounterTime}. <br> <br> Para acceder a la video consulta médica ingrese al siguiente enlace: <br> <a href='{$patientEncounterUrl}' target='_blank'>{$patientEncounterUrl}</a>"
        ];
        
        $doctor = [
            'to' => $doctorEmail,
            'subject' => "[All in One OPS ] - Nueva video consulta con el Paciente {$patientFullName}",
            'body' => "Hola, se ha agendado una video consulta médica con el paciente {$patientFullName} el día {$encounterDate} a las {$encounterTime}. <br> <br> Para acceder a la video consulta ingrese al siguiente enlace: <br> <a href='{$doctorEncounterUrl}' target='_blank'>{$doctorEncounterUrl}</a>",
        ];
        
        $mailSetting = TsaludVcConfig::take(1)->get();
        if (!isset($mailSetting[0]->mail_smtp)) {
            return response()->json([
                'code' => 500,
                'message' => 'SMTP Server no exists' 
            ]);
        }

        foreach ([$patient, $doctor] as $addressee) {
            $mail = new PHPMailer(true);

            try {
 
                // Email server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = $mailSetting[0]->mail_smtp;              //  smtp host
                $mail->Port = $mailSetting[0]->mail_port;              // port - 587/465

                $mail->SMTPAuth = false;
                if ($mailSetting[0]->mail_password != '') {
                    $mail->SMTPAuth = true;
                    $mail->Username = $mailSetting[0]->mail_username;           //  sender username
                    $mail->Password = $mailSetting[0]->mail_password;           // sender password
                    $mail->SMTPSecure = $mailSetting[0]->mail_smtp_secure;      // encryption - ssl/tls
                }
                
                
                $mail->setFrom($mailSetting[0]->mail_from_email, $mailSetting[0]->mail_from_name);
                $mail->addAddress($addressee['to']);
     
                $mail->addReplyTo('noreply@openemr.orf', 'No-Replay');
     
                /*
                SOPORTE PARA EL ENVÍO DE ARCHIVO ADJUNTOS
                if (isset($_FILES['emailAttachments'])) {    
                    for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                        $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
                    } 
                }
                */
     
                $mail->isHTML(true);                // Set email content format to HTML
     
                $mail->Subject = $addressee['subject'];
                $mail->Body    = $addressee['body'];
     
                $emailsSent = false;
                if ($mail->send()) {
                    $emailsSent = true;
                } 
     
            } catch (Exception $e) {
                # return back()->with('error','Message could not be sent.');
                return response()->json([
                    'code' => 500,
                    'message' => $e->getMessage() 
                ]); 
            }
        }

        if ($emailsSent) {
            return response()->json([
                'code' => 200,
                'message' => "Email has been sent." 
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => $mail->ErrorInfo 
            ]);
        }

    }

}
