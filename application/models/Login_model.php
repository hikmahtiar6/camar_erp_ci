<?php
/**
 * Class Login Model
 */
class Login_model extends CI_Model
{
	const TABLE = 'Shared.Users';

	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * get user by session id
	 */
	public function get_user_by_session_id()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('UserId', $this->session->userdata('user_id'));
		$get = $sql->get();
		$row = $get->row();

		if($row)
		{
			$data = array(
				'username' => $row->UserId,
				'password' => $row->Password,
				'realname' => $row->NickName,
				'role'     => $row->UserRole,
			);

			return $data;
		}
		else
		{
			return false;
		}
	}

	/**
	 * process login action
	 */
	public function get_account($username, $password)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('UserId', $username);
		$sql->where('Password', $password);

		$get = $sql->get();

		return $get->row();
	}

	/**
	 * insert account
	 */
	public function insert_account($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

}

?>