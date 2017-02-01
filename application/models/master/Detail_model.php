<?php
/**
 * Model Master Detail
 */
class Detail_model extends CI_Model {

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
		$sql->where('header_id');

		$get = $sql->get();
		return $get->row();
	}
}

?>