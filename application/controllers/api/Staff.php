
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Staff extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library('curl');
        
    }
    public function login_post()
    {
        $email = $this->post('email');
        $pass = $this->post('password');
        $urls = ['https://kraainem.monsiegesocial.be', 'https://bruxelles.monsiegesocial.be', 'https://overijse.monsiegesocial.be'];
        $tokens = ['eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NDA3fQ.XpRi1xqMhRltL4b4iReVboqGYME8JZdpESvFZrfaUsQ', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic3RhZmZfbW9iaWxlIiwibmFtZSI6InN0YWZmX21vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODYxNDIwfQ.AWRB9c1Uqy2fVk0dIkf_qPKQZBu3y8Ql-OuiwnRSDgc', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoibW9iaWxlIiwibmFtZSI6Im1vYmlsZSIsIkFQSV9USU1FIjoxNjU4ODU3NjMzfQ.JLACceWKpUmkHWZ94YkVuuEy4N28dai0l88dByhF0xI'];
        $result = array();
        for($i = 0; $i < 3; $i++){
            $this->curl->set_url($url[$i].'/api/staffs/search/'.$email);
            $this->curl->set_method('get');
            $this->curl->set_token($tokens[$i]);
            $result = $this->curl->result();
            array_push($result, $result);
        }
        $this->response($result, 200);
    }
    public function index_get()
    {
        $this->response([
            'email' => 'softdrink1991@gmail.com',
            'password' => "asdasfd"
        ], 200);
    }
    public function test_get()
    {
        $this->response([
            'status' => false,
            'message' => "Success"
        ], 200);
    }
}
?>