<?php
/**
 * Model Master Finishing
 */
class Finishing_model extends CI_Model {

	const TABLE = 'Finishing';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);

		$get = $sql->get();
		return $get->result();
	}

	public function get_data_by_id($id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('header_id', $id);

		$get = $sql->get();
		return $get->row();
	}
}

?>