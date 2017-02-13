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

	public function get_report_advance($machine_id = '', $tanggal = '', $shift = '')
	{
		$sql = "
		SELECT d.*,
			h.machine_id as machine_id2,
			f.finishing_name,
			g.SectionDescription,
			g.ThicknessStandard,
			g.ThicknessLowerLimit,
			g.ThicknessUpperLimit,
			g.HoleCount,
			g.WeightStandard,
			g.BolsterTypeId,
			g.InitialPullingLength,
			g.BadEndLength,
			g.F2_PullingLength,
			g.F2_EstFG,
			g.F2_EstBilletLengthMax,
			g.F2_FreqBillet,
			g.F2_FreqCut	
			
		FROM dbo.SpkDetail d
		INNER JOIN dbo.SpkHeader h ON h.header_id=d.header_id
		LEFT JOIN dbo.Finishing f ON d.finishing=f.finishing_id
		LEFT JOIN 
			(SELECT *,
				RowNo=ROW_NUMBER() OVER (PARTITION BY SectionId, MachineId, LengthId ORDER BY SectionId)
			 FROM Extrusion.ExtrusionGuideFinal2())
				 g ON g.SectionId=d.section_id
				AND g.MachineId=h.machine_id
				AND g.[LengthId]=d.Len
				AND g.RowNo=1 ";

		if($machine_id != '')
		{
			$sql .= "AND h.machine_id ='".$machine_id."' ";
		}

		if($tanggal != '')
		{
			$sql .= "AND d.tanggal ='".$tanggal."' ";
		}

		if($shift != '')
		{
			$sql .= "AND d.shift ='".$shift."' ";
		}

		$sql = str_replace("g.RowNo=1 AND", "g.RowNo=1 WHERE", $sql);
		
		$sql .= $sql." ORDER BY d.shift ASC";

		$query = $this->db->query($sql);
		return $query;
	}

}