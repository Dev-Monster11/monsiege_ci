
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Staff extends REST_Controller {
    protected $urls = [];
    protected $tokens = [];
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->urls = ['https://kraainem.monsiegesocial.be', 'https://bruxelles.monsiegesocial.be', 'https://overijse.monsiegesocial.be'];
        $this->$tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
    }

    private function searchStaff($val){

        for($i = 0; $i < 3; $i++){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->$urls[$i].'/api/staffs/search/'.$email);
            $headers = array(
                'Content-Type: application/json',
                'authtoken: '.$this->$tokens[$i]
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if(!curl_errno($ch))
            {
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    $errorFlag = false;
                    
                    $staff = (array)json_decode($response);
                    curl_close($ch);
                    return $staff[0];
                }
            }
            curl_close($ch);
        }        
        return false;
    }

    private function searchStaffById($id){

        for($i = 0; $i < 3; $i++){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->$urls[$i].'/api/staffs/'.$id);
            $headers = array(
                'Content-Type: application/json',
                'authtoken: '.$this->$tokens[$i]
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if(!curl_errno($ch))
            {
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    $errorFlag = false;
                    
                    $staff = (array)json_decode($response);
                    curl_close($ch);
                    return $staff[0];
                }
            }
            curl_close($ch);
        }        
        return false;        
    }
    public function login_post(){
        $email = $this->post('email');
        $password = $this->post('password');
        // $urls = ['https://kraainem.monsiegesocial.be', 'https://bruxelles.monsiegesocial.be', 'https://overijse.monsiegesocial.be'];
        // $tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
        // $staff = array();

        
        // $errorFlag = true;
        // for($i = 0; $i < 3; $i++){
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $urls[$i].'/api/staffs/search/'.$email);
        //     $headers = array(
        //         'Content-Type: application/json',
        //         'authtoken: '.$tokens[$i]
        //     );
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     $response = curl_exec($ch);
        //     if(!curl_errno($ch))
        //     {
        //         $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         if ($resultStatus == 200) {
        //             $errorFlag = false;
                    
        //             $staff = (array)json_decode($response);
        //             curl_close($ch);
        //             break;
        //         }
        //     }
        //     curl_close($ch);
            
        // }
        $staff = searchStaff($email);
        
        if ($staff == false){
            $this->response([
                'error'     => $errorFlag,
                'message'   => 'This account does not exist' 
            ], 200);
            return;
        }

        if (password_verify($password, $staff[0]["password"])) {
            $token =  substr(strrev($staff[0]["password"]),5,15). strrev(md5($staff[0]["staffid"]));
            $this->response([
                'error'     => false,
                'message'   => 'successful',
                'data'      => [
                    'staffId'       => $staff[0]['staffid'],
                    'email'         => $email,
                    'firstName'     => $staff[0]['firstName'],
                    'lastName'      => $staff[0]['lastName'],
                    'lastLogin'     => $staff[0]['lastLogin'],
                    'lastActivity'  => $staff[0]['lastActivity'],
                    'token'         => $token,
                ]
                ], 200);
        }
    }
    
    public function search_post(){
        $token = $this->post('token');
        $staffId = $this->post('staffId');
        $staff = searchStaffById($staffId);
        
    }
}   
?>