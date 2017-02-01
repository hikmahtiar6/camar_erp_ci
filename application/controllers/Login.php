<?php
/**
 * Class login
 */
class Login extends CI_Controller
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('login_model');
	}

	/**
	 * index page
	 */
	public function index()
	{
		$this->twiggy->set('test', 'sample a');
		$this->twiggy->display('default/login/layout');
	}

	/**
	 * Process login
	 */
	public function process_login()
	{
		$username = $this->input->post('uname');
		$password = $this->input->post('upass');

		$get_data_user = $this->login_model->get_account($username, $password);
		if($get_data_user)
		{
			$this->session->set_userdata('user_id', $get_data_user->UserId);
			$response = array(
				'status'  => 'success',
				'message' => 'Anda berhasil login'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'Username atau password salah, silahkan coba kembali'
			);	
		}

		$this->output->set_output(json_encode($response));
	}

	public function insert_admin()
	{
		$data = array(
			'username' => 'admin',
			'password' => md5('admin'),
			'realname' => 'admin',
			'role' => 1,
		);

		if(!$this->login_model->get_account('admin', 'admin'))
		{
			if($this->login_model->insert_account($data))
			{
				echo "sukses tambah admin";
			} 
			else 
			{
				echo "gagal tambah admin";
			}
		}
		else
		{
			echo "admin sudah ada";
		}

		
	}
}
?>