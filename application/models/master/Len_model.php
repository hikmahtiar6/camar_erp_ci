<?php
/**
 * Model Master Len
 */
class Len_model extends CI_Model {

	const TABLE = 'Inventory.MasterDimensionLength';

	public function __construct()
	{
		parent::__construct();
	}



	public function get_data($machine_id = '', $section_id = '')
	{
		$sql = "
		SELECT DISTINCT d.LengthId, d.Length
		FROM Extrusion.ExtrusionGuideFinal2() d ";

		/*if($machine_id != '')
		{
			$sql .= "AND MACHINEID='".$machine_id."' ";
		}*/

		if($machine_id != '')
		{
			if($machine_id == 'SN0750.01')
			{
				$sql .= "AND MachineId IN ('SN0690.01', 'SN0690.02', 'SN0690.03', '".$machine_id."') ";
			}
			else
			{
				$sql .= "AND MACHINEID='".$machine_id."' ";
			}

		}

		if($section_id != '')
		{
			$sql .= "AND d.sectionid='".$section_id."' ";
		}

		$sql .= "AND d.Length IS NOT NULL ";

		$sql = str_replace("d AND", "d WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

	public function get_data_by_id($id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('LengthId', $id);

		$get = $sql->get();
		return $get->row();
	}
}

?>