<?php
/**
 * Model Master Detail
 */
class Detail_model extends CI_Model {

	const TABLE = 'MasterDetail';

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