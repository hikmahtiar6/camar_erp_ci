<?php
/**
 * Die Model
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Die_model extends CI_Model {

	const DIE_TYPE = 'Inventory.MasterDieTypes';
	const DIE_COMPONENT = 'Inventory.MasterDieComponent';
	const R_DETAIL = 'Purchasing.DieReceivingDetail';
	const R_HEADER = 'Purchasing.DieReceivingHeader';

	public function __construct()
	{
		parent::__construct();
	} 

	public function get_data_type()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::DIE_TYPE);

		$get = $sql->get();
		return $get->result();
	}

	public function get_data_component($component_id = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::DIE_COMPONENT);

		if($component_id != '')
		{
			$sql->where('ComponentId', $component_id);
		}

		$get = $sql->get();
		return $get->result();
	}

	public function get_component_parent($die_type)
	{
		$sql = $this->db;

		$sql->select('ComponentId');
		$sql->from(static::DIE_COMPONENT);
		$sql->where('DieTypeId', $die_type);
		$sql->group_by('ComponentId', 'asc');

		$get = $sql->get();
		$row = $get->row();

		if($row)
		{
			return $row->ComponentId;
		}

		return '';
	}

	public function get_detail($dies_id, $show = 'TransactionDate')
	{
		$sql = $this->db;

		$sql->select('rh.*, rd.*');
		$sql->from(static::R_HEADER.' rh');
		$sql->join(static::R_DETAIL.' rd', 'rh.DieReceivingNo = rd.DieReceivingNo', 'inner');

		$sql->where('DiesId', $dies_id);

		$get = $sql->get();
		$row = $get->row_array();

		if($row)
		{
			if(array_key_exists($show, $row))
			{
				return $row[$show];
			}

			return '';
		}

		return '';
	}

}