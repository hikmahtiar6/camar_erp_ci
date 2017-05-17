<?php
class Po_model extends CI_Model {
	const PO_HEADER = 'DiePurchaseOrderHeader';
	const PO_DETAIL = 'DiePurchaseOrderDetail';
	const PR_HEADER = 'DiePurchaseRequestHeader';	

	/**
	 * get last data by month year
	 */
	public function get_last_data_by_year_month($id = '', $key_year_month = '', $show = 'PurchaseOrderNo')
	{
		$sql = $this->db;

		$sql->select('ph.*, prh.PurchaseRequestNo');
		$sql->from(static::PO_HEADER .' ph');
		$sql->join(static::PR_HEADER.' prh', 'prh.PurchaseRequestHeaderId = ph.PurchaseRequestHeaderId', 'inner');

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

	public function get_data_header()
	{
		$sql = $this->db;

		$sql->select('poh.*, prh.PurchaseRequestNo');
		$sql->from(static::PO_HEADER.' poh');
		$sql->join(static::PR_HEADER.' prh', 'prh.PurchaseRequestHeaderId = poh.PurchaseRequestHeaderId', 'inner');

		$get = $sql->get();
		return $get->result();
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

}
?>