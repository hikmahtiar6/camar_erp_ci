<?php
/**
 * Model Query Master
 */
class Query_model extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_master_advance($machine_id = '', $section_id = '')
	{
		$sql = "
		SELECT DISTINCT d.*, mdt.DieTypeName
		FROM Extrusion.ExtrusionGuideFinal2() d
		LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
		LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId ";

		if($machine_id != '')
		{
			$sql .= "AND MACHINEID='".$machine_id."' ";
		}

		if($section_id != '')
		{
			$sql .= "AND d.sectionid='".$section_id."' ";
		}

		$sql = str_replace("s.DieTypeId AND", "s.DieTypeId WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

}