<?php
/**
 * Model Master Spk Lot
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Lot_model extends CI_Model {

	const TABLE = 'SpkLot';
	const TABLE_HEAD_LOT = 'SpkHeaderLot';
	const TABLE_DETAIL = 'SpkDetail';
	const TABLE_LEN = 'Inventory.MasterDimensionLength';
	const TABLE_HEAD = 'SpkHeader';
	const TABLE_BARANG = 'Inventory.Sections';
	const TABLE_MACHINE = 'Factory.Machines';
	const TABLE_SHIFT = 'Factory.Shifts';
	const TABLE_FINISHING = 'Finishing';
	const TABLE_BILLET = 'SpkLotBillet';
	const TABLE_BERAT_ACTUAL = 'SpkLotBeratActual';
	const TABLE_HASIL = 'SpkLotHasil';
	const TABLE_AGING_OVEN = 'SpkLotAgingOven';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);

		$get = $sql->get();
		return $get->result();
	}

	/**
	 * get lot advance
	 */
	public function get_data_advance($master_detail_id = '', $lot_id = '', $tanggal = '')
	{
		$sql = $this->db;

		$sql->select('hsl.*, aging.JumlahBillet');
		$sql->from(static::TABLE_HASIL .' hsl');
		$sql->join(static::TABLE_HEAD_LOT .' lot', 'hsl.MasterDetailId = lot.master_detail_id', 'inner');
		$sql->join(static::TABLE_DETAIL .' detail', 'detail.master_detail_id = lot.master_detail_id', 'inner');
		$sql->join(static::TABLE_AGING_OVEN .' aging', 'aging.SpkLotHasilId = hsl.SpkLotHasilId', 'left');

		if($master_detail_id != '')
		{
			$sql->where('lot.master_detail_id', $master_detail_id);
		}

		if($lot_id != '')
		{
			$sql->where('lot.header_lot_id', $lot_id);
		}

		if($tanggal != '')
		{
			$sql->where('detail.tanggal', $tanggal);
		}

		$get = $sql->get();
		return $get;
	}

	/**
	 * Get data header lot
	 */
	public function get_data_header()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_HEAD_LOT);

		$sql->order_by('header_lot_id', 'asc');

		$get = $sql->get();
		return $get->result();
	}

	public function get_data_by($array)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where($array);

		$get = $sql->get();
		return $get;
	}

	public function suming($sum = 'a.berat_ak', $shift = 0, $master_detail_id = '', $machine_id = '', $section_id = '')
	{
		$sql = "
			SELECT * 
			FROM
			 (
			 	SELECT SUM($sum) as jml

				FROM
					".static::TABLE." a
				LEFT JOIN 
					".static::TABLE_DETAIL." b ON a.master_detail_id = b.master_detail_id
				INNER JOIN 
					".static::TABLE_HEAD." c ON b.header_id = c.header_id ";

		if($machine_id != '')
		{
			$sql .= "AND c.machine_id = '$machine_id' ";
		}

		if($section_id != '')
		{
			$sql .= "AND b.section_id = '$section_id' ";
		}

		if($master_detail_id != '')
		{
			$sql .= "AND b.master_detail_id = '$master_detail_id' ";
		}

		$sql .= " ) AS t ";

		$sql = str_replace("c.header_id AND", "c.header_id WHERE", $sql);

		$sql = str_replace("t AND", "t WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}	

	public function counting($count = 'a.berat_ak', $shift = 0, $master_detail_id = '', $machine_id = '', $section_id = '')
	{
		$sql = "
			SELECT * 
			FROM
			 (
			 	SELECT COUNT($count) as jml

				FROM
					".static::TABLE." a
				LEFT JOIN 
					".static::TABLE_DETAIL." b ON a.master_detail_id = b.master_detail_id
				INNER JOIN 
					".static::TABLE_HEAD." c ON b.header_id = c.header_id ";

		if($machine_id != '')
		{
			$sql .= "AND c.machine_id = '$machine_id' ";
		}

		if($section_id != '')
		{
			$sql .= "AND b.section_id = '$section_id' ";
		}

		if($master_detail_id != '')
		{
			$sql .= "AND b.master_detail_id = '$master_detail_id' ";
		}

		$sql .= " ) AS t ";

		$sql = str_replace("c.header_id AND", "c.header_id WHERE", $sql);

		$sql = str_replace("t AND", "t WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

	public function advance_search($shift = 0, $master_detail_id = '', $machine_id = '', $section_id = '', $limit = '', $start = '', $order = 'tanggal', $type_order = 'ASC')
	{

		
		$sql = "
			SELECT * 
			FROM
			 (
			 	SELECT 
					a.*, 
					b.section_id AS SectionId,
					c.machine_id AS MachineId,
					ROW_NUMBER() OVER(ORDER BY a.lot_id DESC) as RowNum
				FROM
					".static::TABLE." a
				LEFT JOIN 
					".static::TABLE_DETAIL." b ON a.master_detail_id = b.master_detail_id
				INNER JOIN 
					".static::TABLE_HEAD." c ON b.header_id = c.header_id ";

		if($machine_id != '')
		{
			$sql .= "AND c.machine_id = '$machine_id' ";
		}

		if($section_id != '')
		{
			$sql .= "AND b.section_id = '$section_id' ";
		}

		if($master_detail_id != '')
		{
			$sql .= "AND b.master_detail_id = '$master_detail_id' ";
		}

		$sql .= " ) AS t ";

		$sql = str_replace("c.header_id AND", "c.header_id WHERE", $sql);

		if($limit != '')
		{
			$sql .= "AND RowNum <= '$limit' ";
		}

		if($start != '')
		{
			$sql .= "AND RowNum > '".$start."' ";
		}

		$sql .= " ORDER BY $order $type_order ";


		$sql = str_replace("t AND", "t WHERE", $sql);

		$query = $this->db->query($sql);
		return $query;
	}

	public function get_data_header_by_master_detail_id($master_detail_id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_HEAD_LOT);
		$sql->where('master_detail_id', $master_detail_id);

		$get = $sql->get();
		return $get->row();
	}

	public function get_last_data($master_detail_id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);
		$sql->where('master_detail_id', $master_detail_id);
		$sql->order_by('lot_id', 'desc');

		$get = $sql->get();
		return $get->row();	
	}

	public function save($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	public function save_header($data)
	{
		return $this->db->insert(static::TABLE_HEAD_LOT, $data);
	}

	public function update($id, $data)
	{
		$this->db->where('lot_id', $id);
		return $this->db->update(static::TABLE, $data);
	}

	public function update_header($id, $data)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->update(static::TABLE_HEAD_LOT, $data);
	}

	public function delete($id)
	{
		$this->db->where('lot_id', $id);
		return $this->db->delete(static::TABLE);
	}

	/**
	 * Delete head lot
	 */
	public function delete_head_lot($master_detail_id)
	{
		$this->db->where('master_detail_id', $master_detail_id);
		return $this->db->delete(static::TABLE_HEAD_LOT);
	}

	/**
	 * Delete lot
	 */
	public function delete_lot($master_detail_id)
	{
		$this->db->where('master_detail_id', $master_detail_id);
		return $this->db->delete(static::TABLE);
	}

	/**
	 * Save lot billet, berat_actual, hasil
	 */
	public function save_lot_billet($data)
	{
		return $this->db->insert(static::TABLE_BILLET, $data);
	}

	/**
	 * Update lot billet
	 */
	public function update_lot_billet($id, $data)
	{
		$this->db->where('SpkLotBilletId', $id);
		return $this->db->update(static::TABLE_BILLET, $data);
	}

	/**
	 * Delete lot billet
	 */
	public function delete_lot_billet($id)
	{
		$this->db->where('SpkLotBilletId', $id);
		return $this->db->delete(static::TABLE_BILLET);
	}

	/**
	 * get lot billet
	 */
	public function get_lot_billet($master_detail_id = '', $id = '', $billet_actual = '', $jml_billet = '', $vendor_id = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_BILLET);

		if($master_detail_id != '')
		{
			$sql->where('MasterDetailId', $master_detail_id);
		}

		if($id != '')
		{
			$sql->where('SpkLotBilletId', $id);
		}

		if($billet_actual != '')
		{
			$sql->where('PBilletActual', $billet_actual);
		}

		if($jml_billet != '')
		{
			$sql->where('JumlahBillet', $jml_billet);
		}

		if($vendor_id != '')
		{
			$sql->where('BilletVendorId', $vendor_id);
		}

		$sql->order_by('SpkLotBilletId', 'ASC');

		$get = $sql->get();
		return $get;	
	}

	/**
	 * Get last billet actual
	 */
	public function get_last_billet_actual($master_detail_id = '', $machine_id = '', $section_id = '')
	{
		$sql = $this->db;

		$sql->select('a.*');
		$sql->from(static::TABLE_BILLET. ' a');
		$sql->join(static::TABLE_DETAIL. ' b', 'a.MasterDetailId = b.master_detail_id', 'inner');
		$sql->join(static::TABLE_HEAD. ' c', 'b.header_id = c.header_id', 'inner');

		if($master_detail_id != '')
		{
			$sql->where('a.MasterDetailId', $master_detail_id);
		}

		if($section_id != '')
		{
			$sql->where('b.section_id', $section_id);
		}
		
		if($machine_id != '')
		{
			$sql->where('c.machine_id', $machine_id);
		}

		$sql->order_by('a.SpkLotBilletId', 'DESC');

		$get = $sql->get();

		$row = $get->row();

		if($row)
		{
			return $row->PBilletActual;
		}
		return '';
	}


	/**
	 * Save lot hasil
	 */
	public function save_lot_hasil($data)
	{
		return $this->db->insert(static::TABLE_HASIL, $data);
	}

	/**
	 * Update lot hasil
	 */
	public function update_lot_hasil($id, $data)
	{
		$this->db->where('SpkLotHasilId', $id);
		return $this->db->update(static::TABLE_HASIL, $data);
	}

	/**
	 * Delete lot hasil
	 */
	public function delete_lot_hasil($id)
	{
		$this->db->where('SpkLotHasilId', $id);
		return $this->db->delete(static::TABLE_HASIL);
	}

	/**
	 * get lot hasil
	 */
	public function get_lot_hasil($master_detail_id = '', $id = '', $rak = '', $jml_rak = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_HASIL);

		if($master_detail_id != '')
		{
			$sql->where('MasterDetailId', $master_detail_id);
		}

		if($id != '')
		{
			$sql->where('SpkLotHasilId', $id);
		}

		if($rak != '')
		{
			$sql->where('Rak', $rak);
		}

		if($jml_rak != '')
		{
			$sql->where('JumlahBtgRak', $jml_rak);
		}

		$sql->order_by('SpkLotHasilId', 'ASC');

		$get = $sql->get();
		return $get;	
	}

	/**
	 * Save lot berat actual
	 */
	public function save_lot_berat_actual($data)
	{
		return $this->db->insert(static::TABLE_BERAT_ACTUAL, $data);
	}

	/**
	 * Update lot berat actual
	 */
	public function update_lot_berat_actual($id, $data)
	{
		$this->db->where('SpkLotBeratActualId', $id);
		return $this->db->update(static::TABLE_BERAT_ACTUAL, $data);
	}

	/**
	 * Delete lot berat actual
	 */
	public function delete_lot_berat_actual($id)
	{
		$this->db->where('SpkLotBeratActualId', $id);
		return $this->db->delete(static::TABLE_BERAT_ACTUAL);
	}

	/**
	 * get lot berat actual
	 */
	public function get_lot_berat_actual($master_detail_id = '', $id = '', $akt50 = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_BERAT_ACTUAL);

		if($master_detail_id != '')
		{
			$sql->where('MasterDetailId', $master_detail_id);
		}

		if($id != '')
		{
			$sql->where('SpkLotBeratActualId', $id);
		}

		if($akt50 != '')
		{
			$sql->where('BeratAkt', $akt50);
		}

		$sql->order_by('SpkLotBeratActualId', 'ASC');

		$get = $sql->get();
		return $get;	
	}

	/**
	 * Get Hasil Prod (btg)
	 */
	public function get_hasil_prod_btg($mesin_id = '', $section_id = '', $shift = '', $tgl = '', $master_detail_id = '')
	{
		$sql = $this->db;

		$sql->select('SUM(CONVERT(DECIMAL(18, 3), a.JumlahBtgRak)) AS HasilProd');
		$sql->from(static::TABLE_HASIL.' a');
		$sql->join(static::TABLE_DETAIL.' b', 'a.MasterDetailId = b.master_detail_id', 'inner');
		$sql->join(static::TABLE_HEAD.' c', 'b.header_id = c.header_id', 'inner');
		$sql->join(static::TABLE_SHIFT.' s', 'b.shift = s.ShiftRefId', 'inner');

		if($mesin_id != '')
		{
			$sql->where('c.machine_id', $mesin_id);
		}

		if($section_id != '')
		{
			$sql->where('b.section_id', $section_id);
		}

		if($shift != '')
		{
			$sql->where('s.ShiftNo', $shift);
		}

		if($tgl != '')
		{
			$sql->where('b.tanggal', date('Y-m-d', strtotime($tgl)));
		}

		if($shift != '')
		{
			$sql->where('s.ShiftNo', $shift);
		}

		if($master_detail_id != '')
		{
			$sql->where('b.master_detail_id', $master_detail_id);
		}

		$get = $sql->get();
		$row = $get->row();

		if($row)
		{
			return $row->HasilProd;
		}
		
		return '';
	}

	/**
	 * [get_isian_billet_by_master_detail_id description]
	 */
	public function get_isian_billet_by_master_detail_id($master_detail_id, $type)
	{
		$sql = $this->db;
		
		switch ($type) {
			case 'billet':
			 	$sql->select('*');
			 	$sql->from(static::TABLE_BILLET);
			 	$sql->where('MasterDetailId', $master_detail_id);
			 	$get = $sql->get();

			 	return $get->result();

				break;

			case 'berat_aktual':
			 	$sql->select('*');
			 	$sql->from(static::TABLE_BERAT_ACTUAL);
			 	$sql->where('MasterDetailId', $master_detail_id);
			 	$get = $sql->get();

			 	return $get->result();

				break;

			case 'hasil':
			 	$sql->select('*');
			 	$sql->from(static::TABLE_HASIL);
			 	$sql->where('MasterDetailId', $master_detail_id);
			 	$get = $sql->get();

			 	return $get->result();

				break;
			
			default:
				# code...
				break;
		}
	}
}

?>