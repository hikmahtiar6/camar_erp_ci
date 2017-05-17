<?php
/**
 * Vendor model
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Vendor_model extends CI_Model {

	const TABLE = 'Shared.BusinessPartners';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data($id = '', $initial = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);

		if($id != '')
		{
			$sql->where('BusinessPartnerId', $id);
		}

		if($initial != '')
		{
			$sql->where('Initial', $initial);
		}

		$get = $sql->get();
		return $get;
	}
}
?>