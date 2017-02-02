<?php
/**
 * Model Master Header
 */
class Header_model extends CI_Model {

	const TABLE = 'HeaderSpk';

	public function __construct()
	{
		parent::__construct();
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