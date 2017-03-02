<?php
/**
 * Model Master Spk Lot
 */
class Lot_model extends CI_Model {

	const TABLE = 'SpkLot';
	const TABLE_HEAD_LOT = 'SpkHeaderLot';

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

	public function get_data_by($array)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where($array);

		$get = $sql->get();
		return $get;
	}

	public function get_data_header_by_master_detail_id($master_detail_id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_HEAD_LOT);
		$sql->where('master_detail_id', $master_detail_id);

		$get = $sql->get();
		return $get->row();
	}

	public function get_last_data($master_detail_id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('master_detail_id', $master_detail_id);
		$sql->order_by('lot_id', 'desc');

		$get = $sql->get();
		return $get->row();	
	}

	public function save($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	public function save_header($data)
	{
		return $this->db->insert(static::TABLE_HEAD_LOT, $data);
	}

	public function update($id, $data)
	{
		$this->db->where('lot_id', $id);
		return $this->db->update(static::TABLE, $data);
	}

	public function update_header($id, $data)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->update(static::TABLE_HEAD_LOT, $data);
	}

	public function delete($id)
	{
		$this->db->where('lot_id', $id);
		return $this->db->delete(static::TABLE);
	}
}

?>