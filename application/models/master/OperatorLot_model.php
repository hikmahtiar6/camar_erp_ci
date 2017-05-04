<?php
/**
 * Model Master Len
 */
class OperatorLot_model extends CI_Model {

	const TABLE = 'Operator';

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
		$sql->where('ShiftNo', $id);

		$get = $sql->get();
		return $get->row();
	}

	public function get_data_by($array)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where($array);

		$get = $sql->get();
		return $get->result();
	}
}

?>