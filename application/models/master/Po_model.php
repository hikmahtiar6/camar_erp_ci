<?php
/**
 * @author HIkmahtiar <hikmahtiar.cool@gmail.com>
 */
class Po_model extends CI_Model {
	const PO_HEADER = 'DiePurchaseOrderHeader';
	const PO_DETAIL = 'DiePurchaseOrderDetail';
	const PR_HEADER = 'DiePurchaseRequestHeader';	
	const PR_DETAIL = 'DiePurchaseRequestDetail';
	const SECTIONS  = 'Inventory.Sections';	

	/**
	 * get last data by month year
	 */
	public function get_last_data_by_year_month($id = '', $key_year_month = '', $show = 'PurchaseOrderNo')
	{
		$sql = $this->db;

		$sql->select('ph.*');
		$sql->from(static::PO_HEADER .' ph');

		if($id != '')
		{
			$sql->where('ph.PurchaseOrderHeaderId', $id);
		}

		if($key_year_month != '')
		{
			$sql->like('ph.PurchaseOrderNo', $key_year_month);
		}

		$sql->order_by('ph.PurchaseOrderHeaderId', 'desc');

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

	public function get_data_header($po_id = '', $po_document = '')
	{
		$sql = $this->db;

		$sql->select('poh.*');
		$sql->from(static::PO_HEADER.' poh');

		if($po_id != '') 
		{
			$sql->where('poh.PurchaseOrderHeaderId', $po_id);
		}

		if($po_document != '')
		{
			$sql->where('poh.PurchaseOrderNo', $po_document);
		}

		$get = $sql->get();
		return $get;
	}

	/**
	 * Get data detail advance
	 */
	public function get_detail_advance($id = '', $po_no = '', $pr_detail_id = '')
	{
		$sql = $this->db;

		$sql->select('pod.*, sec.SectionDescription');
		$sql->from(static::PO_DETAIL.' pod');
		$sql->join(static::SECTIONS.' sec', 'pod.SectionId = sec.SectionId', 'inner');

		if($id != '') 
		{
			$sql->where('pod.PurchaseOrderDetailId', $id);
		}

		if($po_no != '')
		{
			$sql->where('pod.PurchaseOrderNo', $po_no);
		}

		$sql->order_by('DiesSeqNo');

		$get = $sql->get();
		return $get;
	}

	public function save_header($data)
	{
		return $this->db->insert(static::PO_HEADER, $data);
	}

	public function update_header($id, $data)
	{
		$this->db->where('PurchaseOrderHeaderId', $id);
		return $this->db->update(static::PO_HEADER, $data);
	}

	public function delete_header($id)
	{
		$this->db->where('PurchaseOrderHeaderId', $id);
		return $this->db->delete(static::PO_HEADER);
	}

	/**
	 * Delete data detail po
	 */
	public function delete_detail($id)
	{
		$this->db->where('PurchaseOrderDetailId', $id);
		return $this->db->delete(static::PO_DETAIL);
	}

	public function get_data_detail($id = '', $po_no = '', $dies_id = '')
	{
		$sql = $this->db;

		$sql->select('pd.*');
		$sql->from(static::PO_DETAIL.' pd');

		if($id != '') 
		{
			$sql->where('pd.PurchaseOrderDetailId', $id);
		}

		if($po_no != '')
		{
			$sql->where('pd.PurchaseOrderNo', $po_no);
		}

		if($dies_id != '')
		{
			$sql->where('pd.DiesId', $dies_id);
		}

		$get = $sql->get();
		return $get;
	}

	public function save_detail($data, $batch = false)
	{
		if($batch)
		{
			return $this->db->insert_batch(static::PO_DETAIL, $data);
		}
		return $this->db->insert(static::PO_DETAIL, $data);
	}

	public function update_detail($dies_id, $data)
	{
		$this->db->where('DiesId', $dies_id);
		return $this->db->update(static::PO_DETAIL, $data);
	}

}
?>