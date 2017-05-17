<?php
/**
 * Billet model
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Billet_model extends CI_Model {

	const BILLET_TYPE = 'Inventory.MasterBilletTypes';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_billet_type($billet_type_id = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::BILLET_TYPE);

		if($billet_type_id != '')
		{
			$sql->where('BilletTypeId', $billet_type_id);
		}

		$get = $sql->get();
		return $get;
	}
}
?>