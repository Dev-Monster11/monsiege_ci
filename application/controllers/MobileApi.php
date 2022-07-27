
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;
class MobileApi extends RestController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }
    public function login_post()
    {
        $email = $this->post('email');
        $pass = $this->post('password');
        $this->respnse([
            'email' => $email,
            'password' => $pass
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