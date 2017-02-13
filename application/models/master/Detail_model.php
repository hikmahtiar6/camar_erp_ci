<?php
/**
 * Model Master Detail
 */
class Detail_model extends CI_Model {

	const TABLE = 'SpkDetail';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data_by_id($id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('master_detail_id', $id);

		$get = $sql->get();
		return $get->row();
	}

	public function get_date_start($header_id)
	{
		$sql = $this->db;

		$sql->select('MIN(tanggal) as date_start_header');
		$sql->from(static::TABLE);
		$sql->where('header_id', $header_id);
		$sql->where('tanggal !=', NULL);
		$sql->where('tanggal !=', '');
		$sql->where('tanggal !=', ' ');

		$get = $sql->get();
		return $get->row();
	}

	public function get_date_finish($header_id)
	{
		$sql = $this->db;

		$sql->select('MAX(tanggal) as date_finish_header');
		$sql->from(static::TABLE);
		$sql->where('header_id', $header_id);
		$sql->where('tanggal !=', NULL);
		$sql->where('tanggal !=', '');
		$sql->where('tanggal !=', ' ');

		$get = $sql->get();
		return $get->row();
	}

	public function save($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	public function delete($id)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->delete(static::TABLE);
	}
}

?>