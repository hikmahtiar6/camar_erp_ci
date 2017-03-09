<?php
/**
 * Model Master Index DIce
 */
class Indexdice_model extends CI_Model {

	const TABLE = 'Purchasing.DieReceivingDetail';

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

		$sql->select('DiesId');
		$sql->from(static::TABLE);
		$sql->where($array);

		$sql->group_by('DiesId');

		$get = $sql->get();
		return $get;
	}
}

?>