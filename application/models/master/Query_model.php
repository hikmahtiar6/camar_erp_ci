<?php
/**
 * Model Query Master
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Query_model extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_master_section($machine_id = '', $section_id = '')
	{
		$sql = "
		SELECT DISTINCT d.SectionId, d.SectionDescription
		FROM Extrusion.ExtrusionGuideFinal2() d
		LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
		LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId ";

		if($machine_id != '')
		{
			/*if($machine_id == 'SN0750.01')
			{
				$sql .= "AND MachineId IN ('SN0690.02', 'SN0690.03', '".$machine_id."') ";
			}
			else
			{
				if($machine_id == 'SN0690.02' || $machine_id == 'SN0690.01' || $machine_id == 'SN0690.03')
				{
					$sql .= "AND MachineId IN ('SN0750.01', '".$machine_id."') ";
				}
				else
				{
					$sql .= "AND MACHINEID='".$machine_id."' ";	
				}
			}*/
					$sql .= "AND MACHINEID='".$machine_id."' ";	

		}

		if($section_id != '')
		{
			$sql .= "AND d.SectionId='".$section_id."' ";
		}

		$sql = str_replace("s.DieTypeId AND", "s.DieTypeId WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

	public function get_master_advance($machine_id = '', $section_id = '', $length_id = '')
	{
		$sql = "
		SELECT DISTINCT d.*, mdt.DieTypeName , s.SectionDescription
		FROM Extrusion.ExtrusionGuideFinal2() d
		LEFT JOIN Inventory.Sections s ON s.SectionId=d.SectionId
		LEFT JOIN Inventory.MasterDieTypes mdt ON mdt.DieTypeId=s.DieTypeId ";

		/*if($machine_id != '')
		{
			$sql .= "AND MACHINEID='".$machine_id."' ";
		}*/

		if($machine_id != '')
		{
			/*if($machine_id == 'SN0750.01')
			{
				$sql .= "AND MachineId IN ('SN0690.02', 'SN0690.03', '".$machine_id."') ";
			}
			else
			{
				if($machine_id == 'SN0690.02' || $machine_id == 'SN0690.01' || $machine_id == 'SN0690.03')
				{
					$sql .= "AND MachineId IN ('SN0750.01', '".$machine_id."') ";
				}
				else
				{
					$sql .= "AND MACHINEID='".$machine_id."' ";
				}
			}*/
					$sql .= "AND MACHINEID='".$machine_id."' ";
		}

		if($section_id != '')
		{
			$sql .= "AND d.sectionid='".$section_id."' ";
		}

		if($length_id != '')
		{
			$sql .= "AND d.LengthId='".$length_id."' ";
		}

		$sql = str_replace("s.DieTypeId AND", "s.DieTypeId WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

	public function count_master_advance($machine_id = '', $section_id = '')
	{
		return $this->get_master_advance($machine_id, $section_id)->num_rows();
	}

	public function get_report_advance($machine_id = '', $tanggal = '', $shift = 0, $district = false)
	{
		if($district)
		{
			$sql = "
			SELECT  DISTINCT s.SectionDescription, sh.ShiftNo	
				
			FROM dbo.SpkDetail d
			INNER JOIN dbo.SpkHeader h ON h.header_id=d.header_id
			LEFT JOIN Inventory.MasterFinishing f ON d.finishing=f.FinishingId
			LEFT JOIN Inventory.Sections s ON d.section_id=s.SectionId
			INNER JOIN Factory.Shifts sh ON d.shift = sh.ShiftRefId
			LEFT JOIN 
				(SELECT *,
					RowNo=ROW_NUMBER() OVER (PARTITION BY SectionId, MachineId, LengthId ORDER BY SectionId)
				 FROM Extrusion.ExtrusionGuideFinal2())
					 g ON g.SectionId=d.section_id
					AND g.MachineId=h.machine_id
					AND g.[LengthId]=d.Len
					AND g.RowNo=1 ";
		} 
		else 
		{
			$sql = "
			SELECT d.*,
				sh.ShiftNo,
				h.machine_id as machine_id2,
				f.FinishingId,
				s.SectionDescription,
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
				F2_EstBilletLengthMin,
				g.F2_FreqBillet,
				g.F2_FreqCut,
				g.WeightUpperLimit,	
				g.WeightLowerLimit
				
			FROM dbo.SpkDetail d
			INNER JOIN dbo.SpkHeader h ON h.header_id=d.header_id
			LEFT JOIN Inventory.MasterFinishing f ON d.finishing=f.FinishingId
			LEFT JOIN Inventory.Sections s ON d.section_id=s.SectionId
			INNER JOIN Factory.Shifts sh ON d.shift = sh.ShiftRefId
			LEFT JOIN 
				(SELECT *,
					RowNo=ROW_NUMBER() OVER (PARTITION BY SectionId, MachineId, LengthId ORDER BY SectionId)
				 FROM Extrusion.ExtrusionGuideFinal2())
					 g ON g.SectionId=d.section_id
					AND g.MachineId=h.machine_id
					AND g.[LengthId]=d.Len
					AND g.RowNo=1 ";
		}
		
		if($machine_id != '')
		{
			$sql .= "AND h.machine_id ='".$machine_id."' ";
		}

		if($tanggal != '')
		{
			$sql .= "AND d.tanggal ='".$tanggal."' ";
		}

		if($shift > 0)
		{
			$sql .= "AND sh.shiftNo ='".$shift."' ";
		}

		$sql = str_replace("g.RowNo=1 AND", "g.RowNo=1 WHERE", $sql);
		
		$fix = $sql." ORDER BY d.tanggal, sh.ShiftNo ASC,  d.master_detail_id DESC ";

		$query = $this->db->query($fix);
		return $query;
	}

	public function get_report_advance_lot($machine_id = '', $tanggal = '', $shift = 0)
	{
		$sql = "
		SELECT d.*,
			h.machine_id as machine_id2,
			f.FinishingId,
			s.SectionDescription,
			hl.pot_end_butt,
			lent.Length
			
		FROM dbo.SpkDetail d
		INNER JOIN dbo.SpkHeader h ON h.header_id=d.header_id
		INNER JOIN Inventory.MasterDimensionLength lent ON d.len = lent.LengthId
		LEFT JOIN Inventory.MasterFinishing f ON d.finishing=f.FinishingId
		LEFT JOIN Inventory.Sections s ON d.section_id=s.SectionId
		INNER JOIN Factory.Shifts sh ON d.shift = sh.ShiftRefId
		LEFT JOIN dbo.SpkHeaderLot hl ON d.master_detail_id = hl.master_detail_id ";
		
		
		if($machine_id != '')
		{
			$sql .= "AND h.machine_id ='".$machine_id."' ";
		}

		if($tanggal != '')
		{
			$sql .= "AND d.tanggal ='".$tanggal."' ";
		}

		if($shift > 0)
		{
			$sql .= "AND sh.ShiftNo ='".$shift."' ";
		}

		$sql = str_replace("hl.master_detail_id AND", "hl.master_detail_id WHERE", $sql);
		
		$sql .= $sql." ORDER BY sh.ShiftNo DESC";

		$query = $this->db->query($sql);
		return $query;
	}

	/**
	 * Get detail effective item dimension
	 */
	public function get_effective_item_dimension($section_id = '')
	{
		$sql = "
		SELECT
			*
		FROM
			Inventory.ViewEffectiveItemDimension ";

		if($section_id != '')
		{
			$sql .= "WHERE SectionId ='".$section_id."' ";
		}

		$query = $this->db->query($sql);
		return $query;
	}

	/**
	 * get report pr
	 */
	public function get_report_pr($header_id)
	{
		$sql = "
		SELECT 
			DISTINCT pd.SectionId, 
			s.SectionDescription, 
			pd.HoleCount, 
			pd.DieTypeId, 
			dt.DieTypeName, 
			pd.MachineTypeId, 
			bt.BilletDiameter,
			pd.PurchaseRequestHeaderId,
			(
				SELECT MIN(CONVERT(VARCHAR, DiesId)) FROM DiePurchaseRequestDetail 
				WHERE SectionId = pd.SectionId
				AND DieTypeId = pd.DieTypeId
				AND MachineTypeId = pd.MachineTypeId
				AND HoleCount = pd.HoleCount
				AND BilletTypeId = pd.BilletTypeId
				AND PurchaseRequestHeaderId = pd.PurchaseRequestHeaderId
			) AS FirstDies,
			(
				SELECT MIN(DiesYear) FROM DiePurchaseRequestDetail 
				WHERE SectionId = pd.SectionId
				AND DieTypeId = pd.DieTypeId
				AND MachineTypeId = pd.MachineTypeId
				AND HoleCount = pd.HoleCount
				AND BilletTypeId = pd.BilletTypeId
				AND PurchaseRequestHeaderId = pd.PurchaseRequestHeaderId
			) AS FirstDiesYear,
			(
				SELECT MAX(CONVERT(VARCHAR, DiesId)) FROM DiePurchaseRequestDetail 
				WHERE SectionId = pd.SectionId
				AND DieTypeId = pd.DieTypeId
				AND MachineTypeId = pd.MachineTypeId
				AND HoleCount = pd.HoleCount
				AND BilletTypeId = pd.BilletTypeId
				AND PurchaseRequestHeaderId = pd.PurchaseRequestHeaderId
			) AS LastDies,
			(
				SELECT MAX(DiesYear) FROM DiePurchaseRequestDetail 
				WHERE SectionId = pd.SectionId
				AND DieTypeId = pd.DieTypeId
				AND MachineTypeId = pd.MachineTypeId
				AND HoleCount = pd.HoleCount
				AND BilletTypeId = pd.BilletTypeId
				AND PurchaseRequestHeaderId = pd.PurchaseRequestHeaderId
			) AS LastDiesYear,
			(
				SELECT MAX(DiesSeqNo) FROM DiePurchaseRequestDetail 
				WHERE SectionId = pd.SectionId
				AND DieTypeId = pd.DieTypeId
				AND MachineTypeId = pd.MachineTypeId
				AND HoleCount = pd.HoleCount
				AND BilletTypeId = pd.BilletTypeId
				AND PurchaseRequestHeaderId = pd.PurchaseRequestHeaderId
			) AS LastDiesSeqNo
		FROM dbo.DiePurchaseRequestDetail pd
		INNER JOIN Inventory.Sections s ON s.SectionId = pd.SectionId
		INNER JOIN Inventory.MasterDieTypes dt ON dt.DieTypeId = pd.DieTypeId
		INNER JOIN Inventory.MasterBilletTypes bt ON bt.BilletTypeId = pd.BilletTypeId
		WHERE pd.PurchaseRequestHeaderId = '".$header_id."'
		";

		$query = $this->db->query($sql);
		return $query;
	}

	/**
	 * qty per section
	 */
	public function qty_pr_per_section($header_id, $section_id)
	{
		$sql = "
		SELECT COUNT(DiePurchaseRequestDetailId) AS total
		FROM dbo.DiePurchaseRequestDetail
		WHERE PurchaseRequestHeaderId = '".$header_id."'
		AND SectionId = '".$section_id."'
		";

		$query = $this->db->query($sql);
		$row = $query->row();
		if($row)
		{
			return $row->total;
		}

		return 0;
	}

	public function get_max_min_dies_pr($header_id, $section_id , $die_type_id, $type = 'min')
	{
		$sql = "
		SELECT $type(CONVERT(INT, DiesSeqNo)) AS seqno 
		FROM dbo.DiePurchaseRequestDetail
		WHERE SectionId = '035'
		AND PurchaseRequestHeaderId = '".$header_id."'
		AND DieTypeId = '".$die_type_id."'
		";

		$query = $this->db->query($sql);
		$row = $query->row();
		if($row)
		{
			if($row->seqno != null)
			{
				return $row->seqno;
			}
			return 0;
		}

		return 0;
	}

	public function get_seq_no_by_section_year($section_id, $year)
	{
		$sql = "
		SELECT DISTINCT DiesSeqNo FROM Purchasing.DieReceivingDetail
		WHERE DiesYear = '".$year."'
		AND SectionId = '".$section_id."'
		ORDER BY DiesSeqNo
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_dies_year_by_section($section_id)
	{
		$sql = "
		SELECT DISTINCT DiesYear 
		FROM Purchasing.DieReceivingDetail
		WHERE SectionId = '".$section_id."'
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_dies_history_card2($section_id, $year, $seqno)
	{
		$sql = "
		SELECT d.DiesId from Purchasing.DieReceivingDetail d
		LEFT JOIN Purchasing.DieReceivingHeader h on h.DieReceivingNo = d.DieReceivingNo
		WHERE SectionId = '".$section_id."'
		AND DiesYear = '".$year."'
		AND DiesSeqNo = '".$seqno."'
		ORDER BY d.DiesYear, d.DiesSeqNo, d.DiesSuffix, d.DieTypeComponentId, d.DieTypeSubComponentId
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_machine_in_spk_detail($master_detail_id)
	{
		$sql = "
		SELECT h.machine_id 
		FROM dbo.SpkDetail d
		INNER JOIN dbo.SpkHeader h ON d.header_id = h.header_id
		WHERE d.master_detail_id = '".$master_detail_id."'
		";

		$query = $this->db->query($sql);
		return $query->row();
	}

}