<?php
/**
 * Model Master Header
 */
class Header_model extends CI_Model {

	const TABLE = 'HeaderSpk';
	const TABLE_MACHINE = 'Factory.Machines';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data()
	{
		$sql = $this->db;

		$sql->select('a.*, b.MachineTypeId as machine_type_id');
		$sql->from(static::TABLE. ' a');
		$sql->join(static::TABLE_MACHINE. ' b', 'a.machine_id = b.MachineId', 'inner');

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

	public function get_data_by($array)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where($array);

		$get = $sql->get();
		return $get->row();
	}

	public function advance_search($machine_id)
	{
		$sql = $this->db;

		$sql->select('a.*, b.MachineTypeId as machine_type_id');
		$sql->from(static::TABLE. ' a');
		$sql->join(static::TABLE_MACHINE. ' b', 'a.machine_id = b.MachineId', 'inner');

		if($machine_id != '')
		{
			$sql->where('machine_id', $machine_id);
		}


		$get = $sql->get();
		return $get->result();
	}

	public function delete($id)
	{
		$this->db->where('header_id', $id);
		return $this->db->delete(static::TABLE);
	}

	public function update($id, $data)
	{
		$this->db->where('header_id', $id);
		return $this->db->update(static::TABLE, $data);
	}
}

?>