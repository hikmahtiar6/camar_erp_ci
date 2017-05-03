<?php
/**
 * CLass Do
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class DeliveryOrder extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->twiggy->display('admin/do/index');
    }
}
?>