
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Staff extends REST_Controller {
    protected $urls = [];
    protected $tokens = [];
    protected $index = -1;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->urls = ['https://bruxelles.monsiegesocial.be', 'https://kraainem.monsiegesocial.be', 'https://overijse.monsiegesocial.be'];
        $this->tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
        // $this->urls = ['https://bruxelles.monsiegesocial.be'];
        // $this->tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc'];
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
                        
                        $staff = json_decode($response);
                        curl_close($ch);
                        // $this->index = $i;
                        return $staff;
                    }
                }
                curl_close($ch);
            }        
            return false;
        // }else{
        //     $ch = curl_init();
        //     // curl_setopt($ch, CURLOPT_URL, $this->urls[$i].'/api/staffs/search/'.$val);
        //     curl_setopt($ch, CURLOPT_URL, $this->urls[$this->index].$val);
        //     $headers = array(
        //         'Content-Type: application/json',
        //         'authtoken: '.$this->tokens[$this->index]
        //     );
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     $response = curl_exec($ch);
        //     if(!curl_errno($ch))
        //     {
        //         $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         if ($resultStatus == 200) {
                    
        //             $staff = json_decode($response);
        //             curl_close($ch);
        //             return $staff;
        //         }
        //     }
        //     curl_close($ch);
    
        //     return false;            
        // }
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
                    $staff = json_decode($response);
                    curl_close($ch);
                    return $staff;
                }
            }
            curl_close($ch);
        }        
        return false;        
    }


    public function login_post(){
        $email = $this->post('email');
        $password = $this->post('password');
        $staff = $this->search('/api/staffs/search/'.$email);

        if ($staff == false){
            $this->response([
                'error'     => true,
                'message'   => 'This account does not exist' 
            ], 200);
            return;
        }

        if (password_verify($password, $staff[0]->password)) {
            $token =  substr(strrev($staff[0]->password),5,15). strrev(md5($staff[0]->staffid));
            $this->response([
                'error'     => false,
                'message'   => 'successful',
                'data'      => [
                    'staffId'       => intval($staff[0]->staffid),
                    'email'         => $email,
                    'firstName'     => $staff[0]->firstname,
                    'lastName'      => $staff[0]->lastname,
                    'lastLogin'     => $staff[0]->last_login,
                    'lastActivity'  => $staff[0]->last_activity,
                    'token'         => $token,
                ]
                ], 200);
        }
    }
    
    public function search_post(){
        $token = substr($this->input->request_headers()['Token'], 0, 15);
        $staffId = $this->post('staffId');
        $searchText = $this->post('searchText');
        $isOcr = $this->post('isOcr');
        $staff = $this->searchStaffById($staffId);

        if(substr(strrev($staff->password), 5, 15) == $token)
         {
            $search_text = strtolower($searchText);
            $company_name = $search_text;

            $clients = $this->search('/api/customers/search/'.$company_name);
            if ($clients == false){
                $this->response([
                    'error'     =>  true,
                    'message'   =>  'Client Search Failed'
                ]);
                return;
            }
            $rClients = array();
            if (is_array($clients)){
                foreach($clients as $client){
                    
                    // if (strpos($client->company, $company_name) !== false){
                    if (str_contains(strtolower($client->company), $company_name)){
                    // if (strtolower($client->company) $company_name){
                        array_push($rClients, $client);
                    }

                }
            }

            $contacts = $this->search('/api/contacts/'.$rClients[0]->userid);
            if ($contacts == false){
                $this->response([
                    'error'     => true,
                    'message'   => 'Contact does not exist'
                ]);
                return;
            }
            $result = array(
                'companyId'         => $rClients[0]->userid,
                'companyName'       => $rClients[0]->company,
                'companyPhone'      => $rClients[0]->phonenumber,
                'companyAddress'    => $rClients[0]->address
            );
            $existFlag = false;
            $item = $contacts[0];
            var_dump($item);
            return;
            // foreach($contacts as $item){
                if (property_exists($item, 'userid')){
                    if ($item->userid == $rClients[0]->userid){
                        $result['contactFirstName'] = $item->firstname;
                        $result['contactLastname'] = $item->lastname;
                        $result['contactEmail'] = $item->email;
                        $result['contactPhone'] = $item->phonenumber;
                        // $result['dateFinContrat'] = date_format($item->dataend, '%d/%m/%Y');
                        $existFlag = true;
                        // break;
                    }
                }
                if (property_exists($item, 'client')){
                    if ($item->userid == $rClients[0]->userid){
                        $result['contactFirstName'] = $item->firstname;
                        $result['contactLastname'] = $item->lastname;
                        $result['contactEmail'] = $item->email;
                        $result['contactPhone'] = $item->phonenumber;
                        // $result['dateFinContrat'] = date_format($item->dataend, '%d/%m/%Y');
                        $existFlag = true;
                        // break;                        
                    }
                }
                // if ($item->userid == $clients[0]->userid || $item->client == $clients[0]->userid){
                //     $result['contactFirstName'] = $item->firstname;
                //     $result['contactLastname'] = $item->lastname;
                //     $result['contactEmail'] = $item->email;
                //     $result['contactPhone'] = $item->phonenumber;
                //     $result['dateFinContrat'] = date_format($item->dataend, '%d/%m/%Y');
                //     $existFlag = true;
                //     break;
                // }
            // }
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

    public function autologin_post(){
        $token = substr($this->input->request_headers()['Token'], 0, 15);
        $staff_id = $this->post('staffId');
        $staffs = $this->search('/api/staffs/'.$staff_id);
        foreach($staffs as $staff){
            if (substr(strrev($staff->password), 5, 15) == $token){
                $this->response([
                    'error'         => false,
                    'message'       => 'successful',
                    'data'          => [
                        'staffId'       => $staff->staffid,
                        'email'         => $staff->email,
                        'firstName'     => $staff->firstname,
                        'lastName'      => $staff->lastname,
                        'lastLogin'     => $staff->last_login,
                        'lastActivity'  => $staff->last_activity,
                        'token'         => $token,

                        ]
                    ], 200);
                return;
            }
        }
        $this->response([
            'error'     => true,
            'message'   => 'This account does not exist'
        ], 200);
    }

    public function scanqr_post(){
        $token = substr($this->input->request_headers()['Token'], 0, 15);
        $staff_id = $this->post('staffId');
        $qrCode = $this->post('qrCode');
        $staff = $this->search('/api/staffs/'.$staff_id);
        if ($staff == false){
            $this->response([
                'error'     => true,
                'message'   => 'This Staff does not exist'], 200);
            return;
        }
        if (substr(strrev($staff->password), 5, 15) == $token){
            $array = ["#", "%", "d", "!", "?", "*", "^", "$", "S", "Z"];
            $chars = str_split($qrCode);
            $companyId = "";
            foreach ($chars as $char) {
                foreach ($array as $key => $a) {
                    if ($a == $char){
                        $companyId.= $key;
                    }
                }
            }
            $companyId = $companyId / 255;
            $userid = 0;
            $client = array();
            $clients = $this->search('/api/customers/search/'.$companyId);
            if ($clients == false){
                $this->response([
                    'error'         => true,
                    'message'       => 'Client Search Failed'
                ]);
                return;
            }
            foreach($clients as $item){
                if ($item->userid == $companyId){
                    $client['userid'] = $companyId;
                    $client['company'] = $item->company;
                    $client['companyPhone'] = $item->phonenumber;
                    $client['companyAddress'] = $item->address;
                    break;
                }
            }
            $contacts = $this->search('/api/contacts/search/'.$companyId);
            if ($contacts == false){
                $this->response([
                    'error'         => true,
                    'message'       => 'Contacts Search Failed'
                ]);
                return;
            }
            foreach($contacts as $item){
                if ($item->userid == $companyId || $item->client == $companyId){
                    $client['contactFirstname'] = $item->firstname;
                    $client['contactLastname'] = $item->lastname;
                    $client['contactEmail'] = $item->email;
                    $client['contactPhone'] = $item->phonenumber;
                    // $client['ContratdateFin'] = date_format($item->dataend, '%d/%m/%Y');
                    break;
                }
            }
            $tickets = $this->search('/api/tickets/search/'.$companyId);
            $tts = array();
            $existFlag = false;
            foreach($tickets as $item){
                if ($item->status == 0) continue;
                $result = array(
                    'ticketid'      => $item->ticketid,
                    'subject'       => $item->subject,
                    'DateTicket'    => date_format($item->date, '%d/%m/%Y %H:%i:%s'),
                    'file_name'     => $item->attachments
                );
                // $result['ticketid'] = $item->ticketid;
                // $result['subject']  = $item->subject;
                // $result['DateTicket'] = date_format($item->date, '%d/%m/%Y %H:%i:%s');
                // $result['file_name'] = $item->attachments;
                array_push($tts, $result);
                $existFlag = true;
                break;
            }
            if ($existFlag == true){
                $this->response([
                    'error'         => false,
                    'message'       => 'successful',
                    'data'          => [
                        'client'    => json_encode($client), 
                        'tickets'   => json_encode($tts)
                    ]
                ], 200);
                return;
            }else{
                $this->response([
                    'error'         => false,
                    'message'       => 'successful',
                    'data'          => [
                        'client' =>  json_encode($client),
                        'tickets' => []
                    ]                    
                ], 200);
            }
        }
    }

    public function createticket_post(){

    }


    public function test_post(){
        $staff = $this->search('/api/staffs/1583907178');
        $this->response($staff, 200);
    }

}   
?>