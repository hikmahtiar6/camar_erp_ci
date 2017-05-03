<?php
/**
 * CLass Po
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Po extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->twiggy->display();
    }
}
?>