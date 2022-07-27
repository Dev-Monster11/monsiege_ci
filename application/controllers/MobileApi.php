
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;
class MobileApi extends RestController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }
    public function users_post()
    {
        $email = $this->post('email');
        $pass = $this->post('password');
        

    }

}
?>