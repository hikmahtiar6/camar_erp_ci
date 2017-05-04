<?php
/**
 * CLass Ro
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Ro extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->twiggy->display('admin/ro/index');
    }
}
?>