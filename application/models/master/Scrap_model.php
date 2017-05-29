<?php
/**
 * Scrap Model
 */
class Scrap_model extends CI_Model {
	
	const TABLE = 'dbo.LotScrap';
	const TABLE_HEADER = 'dbo.SpkHeader';
	const TABLE_SHIFT = 'Factory.Shifts';

	public function __construct()
	{
		parent::__construct();
	}

	public function save($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	public function update($id, $data)
	{
		$this->db->where('LotScrapId', $id);
		return $this->db->update(static::TABLE, $data);
	}

	public function update2($header_id, $tgl, $data)
	{
		$this->db->where('SpkHeaderId', $header_id);
		$this->db->where('Tanggal', $tgl);
		return $this->db->update(static::TABLE, $data);
	}

	public function get_data_tgl_header($header_id, $tgl, $shift = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		if($shift != '')
		{
			$sql->where('Shift', $shift);		
		}
		$sql->where('SpkHeaderId', $header_id);
		$sql->where('Tanggal', date('Y-m-d', strtotime($tgl)));

		$get = $sql->get();

		return $get->row();
	}
	
	/**
	 * Suming for Reporting Lot Harian
	 */
	public function sum_field($field, $machine, $shift, $tanggal)
	{
		switch ($field) {
			case 'scrap':
				$field = 'Scrap';
				break;
				
			case 'lost':
				$field = 'Lost';
				break;

			case 'endbutt':
				$field = 'EndButt';
				break;
			
			default:
				$field = 'Scrap';
				break;
		}
		
		$sql = $this->db;

		$sql->select('SUM(CONVERT(DECIMAL(18, 3), a.'.$field.')) as field');
		$sql->from(static::TABLE.' a');
		$sql->join(static::TABLE_HEADER.' b', 'a.SpkHeaderId = b.header_id', 'inner');
		$sql->where('b.machine_id', $machine);
		$sql->where('a.Shift', $shift);
		$sql->where('a.Tanggal', date('Y-m-d', strtotime($tanggal)));
		
		$get = $sql->get();
		$row = $get->row();
		
		if($row)
		{
			if($row->field != NULL)
			{
				return $row->field;
			}
		}
		
		return '0';
	}
}
?>