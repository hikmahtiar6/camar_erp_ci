<?php
class Pr_model extends CI_Model {
	
	const PR_HEADER = 'DiePurchaseRequestHeader';
	const R_HEADER = 'Purchasing.DieReceivingHeader';
	const PR_DETAIL = 'DiePurchaseRequestDetail';
	const R_DETAIL = 'Purchasing.DieReceivingDetail';
	const VENDOR = 'Shared.BusinessPartners';
	const SECTION = 'Inventory.Sections';
	const DIE_TYPE = 'Inventory.MasterDieTypes';
	const BILLET_TYPE = 'Inventory.MasterBilletTypes';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * get data headers
	 */
	public function get_headers()
	{
		$sql = $this->db;

		$sql->select('ph.*, v.*');
		$sql->from(static::PR_HEADER. ' ph');
		$sql->join(static::VENDOR .' v', 'ph.VendorId = v.BusinessPartnerId', 'left');
		$sql->order_by('ph.PurchaseRequestNo', 'asc');

		$get = $sql->get();
		return $get;
	}

	/**
	 * get last data by month year
	 */
	public function get_last_data_by_year_month($id = '', $key_year_month = '', $show = 'PurchaseRequestNo')
	{
		$sql = $this->db;

		$sql->select('ph.*, v.Initial, v.BusinessPartnerId');
		$sql->from(static::PR_HEADER .' ph');
		$sql->join(static::VENDOR .' v', 'ph.VendorId = v.BusinessPartnerId', 'left');

		if($id != '')
		{
			$sql->where('ph.PurchaseRequestHeaderId', $id);
		}

		if($key_year_month != '')
		{
			$sql->like('ph.PurchaseRequestNo', $key_year_month);
		}

		$sql->order_by('ph.PurchaseRequestHeaderId', 'desc');

		$get = $sql->get();
		$row = $get->row_array();
		if($row)
		{
			if(array_key_exists($show, $row))
			{
				return $row[$show];
			}
			return '';
		}

		return '';
	}

	public function get_last_data_r($vendor_id, $section_id, $machine_type_id, $die_type, $billet_type_id, $dies_year = '')
	{
		$sql = $this->db;

		$sql->select('ph.*, pd.*');
		$sql->from(static::R_HEADER.' ph');
		$sql->join(static::R_DETAIL.' pd', 'pd.DieReceivingNo = ph.DieReceivingNo', 'inner');

		$sql->where('ph.VendorId',$vendor_id);
		$sql->where('pd.SectionId',$section_id);
		$sql->where('pd.MachineTypeId',$machine_type_id);
		$sql->where('pd.DieTypeId',$die_type);
		$sql->where('pd.BilletTypeId',$billet_type_id);

		if($dies_year != '')
		{
			$sql->where('pd.DiesYear', $dies_year);
		}

		$sql->order_by('ph.DieReceivingNo', 'desc');

		$get = $sql->get();
		return $get;
	}

	public function get_last_data($vendor_id, $section_id, $machine_type_id, $die_type, $billet_type_id, $dies_year = '')
	{
		$sql = $this->db;

		$sql->select('ph.*, pd.*');
		$sql->from(static::PR_HEADER.' ph');
		$sql->join(static::PR_DETAIL.' pd', 'pd.PurchaseRequestHeaderId = ph.PurchaseRequestHeaderId', 'inner');

		$sql->where('ph.VendorId',$vendor_id);
		$sql->where('pd.SectionId',$section_id);
		$sql->where('pd.MachineTypeId',$machine_type_id);
		$sql->where('pd.DieTypeId',$die_type);
		$sql->where('pd.BilletTypeId',$billet_type_id);

		if($dies_year != '')
		{
			$sql->where('pd.DiesYear', $dies_year);
		}

		$sql->order_by('pd.DiesSeqNo', 'desc');

		$get = $sql->get();
		return $get;
	}

	public function save_header($data)
	{
		return $this->db->insert(static::PR_HEADER, $data);
	}

	public function save_detail($data, $batch = false)
	{
		if($batch)
		{
			return $this->db->insert_batch(static::PR_DETAIL, $data);		
		}
		return $this->db->insert(static::PR_DETAIL, $data);
	}

	public function delete_detail($id, $header_id = '')
	{
		if($header_id != '')
		{
			$this->db->where('PurchaseRequestHeaderId', $header_id);		
		}
		else
		{
			$this->db->where('DiePurchaseRequestDetailId', $id);
		}
		return $this->db->delete(static::PR_DETAIL);
	}

	public function delete_header($id)
	{
		$this->db->where('PurchaseRequestHeaderId', $id);
		return $this->db->delete(static::PR_HEADER);
	}

	public function update_header($id, $data)
	{
		$this->db->where('PurchaseRequestHeaderId', $id);
		return $this->db->update(static::PR_HEADER, $data);
	}

	public function update_detail($id, $data)
	{
		$this->db->where('DiePurchaseRequestDetailId', $id);
		return $this->db->update(static::PR_DETAIL, $data);
	}

	public function get_detail_by_header($header_id)
	{
		$sql = $this->db;

		$sql->select('ph.*, pd.*, s.SectionDescription, dt.DieTypeName, bt.BilletDiameter');
		$sql->from(static::PR_HEADER.' ph');
		$sql->join(static::PR_DETAIL.' pd', 'pd.PurchaseRequestHeaderId = ph.PurchaseRequestHeaderId', 'inner');
		$sql->join(static::SECTION.' s', 's.SectionId = pd.SectionId', 'inner');
		$sql->join(static::DIE_TYPE.' dt', 'dt.DieTypeId = pd.DieTypeId', 'inner');
		$sql->join(static::BILLET_TYPE.' bt', 'bt.BilletTypeId = pd.BilletTypeId', 'inner');

		$sql->where('ph.PurchaseRequestHeaderId',$header_id);
		$sql->order_by('pd.DiesSeqNo', 'asc');

		$get = $sql->get();
		return $get->result();
	}

	public function get_detail($id) 
	{
		$sql = $this->db;

		$sql->select('ph.*, pd.*, s.SectionDescription, dt.DieTypeName, bt.BilletDiameter');
		$sql->from(static::PR_HEADER.' ph');
		$sql->join(static::PR_DETAIL.' pd', 'pd.PurchaseRequestHeaderId = ph.PurchaseRequestHeaderId', 'inner');
		$sql->join(static::SECTION.' s', 's.SectionId = pd.SectionId', 'inner');
		$sql->join(static::DIE_TYPE.' dt', 'dt.DieTypeId = pd.DieTypeId', 'inner');
		$sql->join(static::BILLET_TYPE.' bt', 'bt.BilletTypeId = pd.BilletTypeId', 'inner');

		$sql->where('pd.DiePurchaseRequestDetailId',$id);
		$sql->order_by('pd.DiesSeqNo', 'asc');

		$get = $sql->get();
		return $get->row();
	}

	public function count_die_by_header($header_id)
	{
		$sql = $this->db;

		$sql->select('COUNT(DiePurchaseRequestDetailId) as total');
		$sql->from(static::PR_DETAIL);

		$sql->where('PurchaseRequestHeaderId',$header_id);

		$get = $sql->get();
		$row = $get->row();
		if($row)
		{
			return $row->total;
		}

		return 0;
	}

}
?>