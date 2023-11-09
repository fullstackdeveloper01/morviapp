<?php 
namespace App\Helpers;

use App\User;

class Helper
{

    /* Send Email */
    public static function send_email($email,$subject,$message)
    {   

        $params = array(

            'to'        => $email,   

            'subject'   => $subject,

            'html'      => $message,

            'from'      => 'support@html.manageprojects.in',
            
            'fromname'  => 'Morvi'

        );

        $request =  'https://api.sendgrid.com/api/mail.send.json';

        $headr = array();

        // $pass = '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';

        // $pass = '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';

        // $pass = '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';
        $pass = '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';

        // set authorization header

        $headr[] = 'Authorization: Bearer '.$pass;
    
        $session = curl_init($request);

        curl_setopt ($session, CURLOPT_POST, true);

        curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

        curl_setopt($session, CURLOPT_HEADER, false);

        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // add authorization header

        curl_setopt($session, CURLOPT_HTTPHEADER,$headr);

        $response = curl_exec($session);

        curl_close($session);

        return true;

    }
    /*** Send notification **/
function sendNotification($fcm_token, $title, $message, $url = null,$redirection_url = '')
{
    if($fcm_token!='')
    {
        $registrationDeviceIds = array($fcm_token);
        $msg = array
        (
            'title'         => $message['title'],
            'body'          => $message['message']
        );
        $msg2 = array
        (
            'id'                => $message['id'],
            'user_id'           => $message['user_id'],
            'notify_type'       => $message['notify_type'],
            'redirection'       => @$message['redirection'],
            'title'             => $message['title'],
            'message'           => $message['message'],
            'image'             => $message['image'],    
        );
         
        $fields = array
        (
            'registration_ids'  => $registrationDeviceIds,
            'notification'      => $msg,
            'data'              => $msg2
        );
      
        $headers = array
        (
            'Authorization: key=AAAAEGuSohw:APA91bG9gNFuomhcq6mAqx8I-ER60Knc7WJ8MopHuA0Z4Am2OndTukxUk8E2c7OIEvIMEfQmD3cp3-QRAQZfcJ8RiwwbZD9A6lcxbTOFlZh25Wa2nECR_xM_Nvi4JWWI7mu894MHJG3j',
            'Content-Type: application/json'
        );
         
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        //var_dump($result);//
        curl_close( $ch );
        //echo $result;
        //die;
        return $result;
    }else{
        return true;
    }
}

}

