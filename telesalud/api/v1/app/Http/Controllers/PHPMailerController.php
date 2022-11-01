<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;

class PHPMailerController extends Controller
{

    // ========== [ Compose Email ] ================
    public function composeEmail(Request $request) 
    {
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
 
        try {
 
            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = '192.168.68.50';              //  smtp host
            $mail->SMTPAuth = false;
            /*
            $mail->Username = 'user@example.com';       //  sender username
            $mail->Password = '**********';             // sender password
            $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
            */
            $mail->Port = 1025;                          // port - 587/465
 
            $mail->setFrom('administrator@openemr.org', 'Admin OpenEMR');
            $mail->addAddress($request->emailRecipient);
            $mail->addCC($request->emailCc);
            $mail->addBCC($request->emailBcc);
 
            $mail->addReplyTo('sender@example.com', 'SenderReplyName');
 
            if (isset($_FILES['emailAttachments'])) {    
                for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                    $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
                } 
            }
 
 
            $mail->isHTML(true);                // Set email content format to HTML
 
            $mail->Subject = $request->emailSubject;
            $mail->Body    = $request->emailBody;
 
            // $mail->AltBody = plain text version of email body;
 
            if (!$mail->send() ) {
                // return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
                return response()->json([
                    'code' => 500,
                    'message' => $mail->ErrorInfo 
                ]);
            } else {
                # return back()->with("success", "Email has been sent.");
                return response()->json([
                    'code' => 200,
                    'message' => "Email has been sent." 
                ]);
            }
 
        } catch (Exception $e) {
            # return back()->with('error','Message could not be sent.');
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() 
            ]); 
        }
    }

}
