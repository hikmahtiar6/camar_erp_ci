<?php
/**
 * Master model
 */

class Master_model extends CI_Model {

	const TABLE_SHIFT = 'Factory.Shifts';
	const TABLE_LEN = 'Inventory.MasterDimensionLength';
	const TABLE_MACHINE = 'Factory.Machines';
	const TABLE_BILLET = 'Inventory.MasterBilletTypes';
	const TABLE_FINISHING = 'Finishing';
	const TABLE_SECTION = 'Inventory.Sections';

	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 query

	 SELECT DISTINCT d.sectionid,d.sectiondescription
FROM Extrusion.ExtrusionGuideFinal2() d
LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId
WHERE MACHINEID='SN0690.02' and d.sectionid='1826'

SELECT DISTINCT d.SectionId, d.LengthId, d.LEngth
FROM Extrusion.ExtrusionGuideFinal2() d
LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId
WHERE MACHINEID='SN0690.02' and d.sectionid='1826'

SELECT *
FROM Purchasing.DieReceivingDetail d
WHERE d.SectionId='1826'
AND d.MachineTypeId='0690T'



	 */
	
	public function get_section_by_machine($machine_id)
	{
		$sql = "
		SELECT DISTINCT d.sectionid,d.sectiondescription
		FROM Extrusion.ExtrusionGuideFinal2() d
		LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
		LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId
		WHERE MACHINEID = '".$machine_id."'
		";

		$query = $this->db->query($sql);

		return $query;
	}

	/**
	 * sinkronisasi data view ke newmaster
	 */
	public function get_data_view()
	{
		$sql = "
			SELECT d.*, mdt.DieTypeName						
			FROM Extrusion.ExtrusionGuideFinal2() d						
			LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId						
			LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId						
			ORDER BY MachineTypeId, SectionId, [Length]
		";

		$get1 = $this->db->query($sql);

		return $get1->result();
	}

	/**
	 * get data shift
	 */
	public function get_data_shift()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_SHIFT);
		//$sql->where('IsActive', true);
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get data shift
	 */
	public function get_data_len()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_LEN);
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get data shift
	 */
	public function get_data_machine()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_MACHINE);
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get data shift
	 */
	public function get_data_billet()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_BILLET);
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get data finishing
	 */
	public function get_data_finishing()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_FINISHING);
		$get = $sql->get();

		return $get->result();
	}
}

?>