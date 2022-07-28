
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
        $this->tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
    }

    private function search($val){

        for($i = 0; $i < 3; $i++){
            $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $this->urls[$i].'/api/staffs/search/'.$val);
            curl_setopt($ch, CURLOPT_URL, $this->urls[$i].$val);
            $headers = array(
                'Content-Type: application/json',
                'authtoken: '.$this->tokens[$i]
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
            curl_setopt($ch, CURLOPT_URL, $this->urls[$i].'/api/staffs/'.$id);
            $headers = array(
                'Content-Type: application/json',
                'authtoken: '.$this->tokens[$i]
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
        $staff = $this->search('/api/staffs/search/'.$email);
        echo (gettype($staff));
        return;
        if ($staff == false){
            $this->response([
                'error'     => true,
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
        $searchText = $this->post('searchText');
        $isOcr = $this->post('isOcr');

        $staff = searchStaffById($staffId);
        if(substr(strrev($staff[0]["password"]), 5, 15) == $token) {
            $indexes = [ "grensstraat", "limite", "mechelen", "malines", "empereur", "keizerslaan"];
            $search_text = strtolower($searchText);
            $company_name = "";
            if ($isOcr == true){
                $company_name = $this->post('companyName');
            }else{
                $company_name = $searchText;
            }
            $clients = $this->search('/api/customers/search/'.$company_name);
            if ($clients == false){
                $this->response([
                    'error'     =>  true,
                    'message'   =>  'This account doesn not exist'
                ]);
                return;
            }
            $contacts = $this->search('/api/contacts/search/'.$clients[0]['userid']);
            if ($contacts == false){
                $this->response([
                    'error'     => true,
                    'message'   => 'Contact does not exist'
                ]);
                return;
            }
            $result = array(
                'companyId'         => $clients[0]['userid'],
                'companyName'       => $clients[0]['company'],
                'companyPhone'      => $clients[0]['phonenumber'],
                'companyAddress'    => $clients[0]['address']
            );
            $existFlag = false;
            foreach($contacts as $item){
                if ($item['userid'] == $clients[0]['userid'] || $item['client'] == $client[0]['userid']){
                    $result['contactFirstName'] = $item['firstname'];
                    $result['contactLastname'] = $item['lastname'];
                    $result['contactEmail'] = $item['email'];
                    $result['contactPhone'] = $item['phonenumber'];
                    $result['dateFinContrat'] = date_format($item['dataend'], '%d/%m/%Y');
                    $existFlag = true;
                    break;
                }
            }
            if ($existFlag == true){
                $this->response($result, 200);
            }else{
                $this->response([
                    'error'     => true,
                    'message'   => 'Contact does not exist'
                ]);
            }
        }
        $this->response([
            'error'     => true,
            'message'   => 'Token is wrong'
        ], 200);

    }
}   
?>