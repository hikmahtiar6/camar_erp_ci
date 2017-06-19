<?php
/**
 * Model Master Len
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Shift_model extends CI_Model {

	const TABLE = 'Factory.Shifts';
	const SHIFT_TYPE = 'ShiftType';

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

	/**
	 * Get data advance
	 */
	public function get_data_advance($shift_id = '', $type_id = '')
	{
		$sql = $this->db;

		$sql->select('a.*, b.ShiftTypeName');
		$sql->from(static::TABLE.' a');
		$sql->join(static::SHIFT_TYPE.' b', 'a.ShiftTypeId = b.ShiftTypeId', 'inner');
		$sql->where('IsActive', 1);

		$get = $sql->get();
		return $get;
	}

	/**
	 * Get data Type Shift
	 */
	public function get_data_type()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::SHIFT_TYPE);

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