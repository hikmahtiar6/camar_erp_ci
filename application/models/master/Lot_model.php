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
}

?>