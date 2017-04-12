<?php
/**
 * Scrap Model
 */
class Scrap_model extends CI_Model {
	
	const TABLE = 'dbo.LotScrap';

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
}
?>