<?php
/**
 * Model Master Len
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Machine_model extends CI_Model {

	const TABLE = 'Factory.Machines';
	const MACHINE_TYPE = 'Factory.MasterMachineType';
	const BILLET_TYPE = 'Inventory.MasterBilletTypes';

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
		$sql->where('MachineId', $id);

		$get = $sql->get();
		return $get->row();
	}

	/**
	 * Get data machine type
	 */
	public function get_data_type()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::MACHINE_TYPE);

		$get = $sql->get();
		return $get->result();
	}

	/**
	 * get detail machine
	 */
	public function get_detail($machine_id = '')
	{
		$sql = $this->db;

		$sql->select('m.*, b.BilletDiameter, b.BilletWeight');
		$sql->from(static::TABLE.' m');
		$sql->join(static::BILLET_TYPE.' b', 'b.BilletTypeId = m.BilletTypeId', 'left');

		if($machine_id != '')
		{
			$sql->where('m.MachineId', $machine_id);
		}

		$get = $sql->get();
		return $get;
	}

	public function get_data_type_id($id = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::MACHINE_TYPE);

		if($id != '')
		{
			$sql->where('MachineTypeId', $id);
		}

		$get = $sql->get();
		return $get;
	}
}

?>