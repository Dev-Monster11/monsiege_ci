
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Staff extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        
    }
    public function login_post(){
        $email = $this->post('email');
        $password = $this->post('password');
        $urls = ['https://kraainem.monsiegesocial.be', 'https://bruxelles.monsiegesocial.be', 'https://overijse.monsiegesocial.be'];
        $tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
        $staff = array();

        
        $errorFlag = true;
        for($i = 0; $i < 3; $i++){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urls[$i].'/api/staffs/search/'.$email);
            $headers = array(
                'Content-Type: application/json',
                'authtoken: '.$tokens[$i]
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if(!curl_errno($ch))
            {

            //     array_push($result, curl_errorno($ch));
            // } else {

                // check the HTTP status code of the request
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    // array_push($result, $resultStatus);

                    // die('Request failed: HTTP status code: ' . $resultStatus);
                    
                // }else {
                    $errorFlag = false;
                    
                    $staff = json_decode($response);
                    curl_close($ch);
                    break;
                }
            }
            curl_close($ch);
            
        }
        if ($errorFlag == true){
            $this->response([
                'error'     => $errorFlag,
                'message'   => 'This account does not exist' 
            ], 200);
            return;
        }
        $this->response($staff, 200);
        return;
        if (password_verify($password, $staff[0]["password"])) {
            $token =  substr(strrev($staff[0]["password"]),5,15). strrev(md5($staff[0]["staffid"]));
            $this->response([
                'error'     => false,
                'message'   => 'successful',
                'data'      => [
                    'staffId'   => $staff[0]['staffid'],
                    'email'     => $email
                ]
                ], 200);
            // echo '{ 
            //         "error" : false,
            //         "message" : "successful",
            //         "data" : { 
            //                 "staffId": '.$staff["staffid"].',
            //                 "email" : "'.$email.'",
            //                 "firstName" : "'.$staff["firstname"].'",
            //                 "lastName" : "'.$staff["lastname"].'",
            //                 "lastLogin" : "'.$staff["lastLogin"].'",
            //                 "lastActivity" : "'.$staff["lastActivity"].'",
            //                 "token" : "'.$token.'"
            //                 }
            //       }';
        }
    }
    
    
}
?>