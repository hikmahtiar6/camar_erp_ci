<?php
/**
 * Model Master Len
 */
class Shift_model extends CI_Model {

	const TABLE = 'Factory.Shifts';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('IsActive', 1);

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
}

?>