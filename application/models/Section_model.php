<?php
/**
 * Section Model
 */
class Section_model extends CI_Model {
	
	const TABLE = 'SpkDetail';
	const TABLE_BARANG = 'Inventory.Sections';
	const TABLE_MACHINE = 'Factory.Machines';
	const TABLE_LEN = 'Inventory.MasterDimensionLength';
	const TABLE_SHIFT = 'Factory.Shifts';
	const TABLE_DIMENSION = 'Inventory.SectionsDimension';
	const TABLE_FINISHING = 'Finishing';
	const TABLE_NEWMASTER = 'NewMaster';
	const TABLE_HEAD = 'SpkHeader';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * save to database
	 */
	public function save($type, $data)
	{
		switch ($type) {
			case 'detail':
				$tbl = static::TABLE;
				break;
			
			case 'header':
				$tbl = static::TABLE_HEAD;
				break;

			default:
				$tbl = static::TABLE_HEAD;
				break;
		}

		return $this->db->insert($tbl, $data);
	}

	/**
	 * get last id
	 */
	public function get_last_insert_id()
	{
		return $this->db->insert_id();
	}

	/**
	 * get data section
	 */
	public function get_data()
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_BARANG);
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get data section
	 */
	public function get_data_detail($date_start = '', $date_finish = '', $shift = 0, $machine_id = '', $section_id = '', $header_id = '')
	{
		$sql = $this->db;

		$sql->select('a.*, b.*, c.SectionDescription, d.MachineTypeId as machine_type, e.ShiftDescription, e.ShiftStart, g.finishing_name, i.*');
		$sql->from(static::TABLE.' a');
		$sql->join(static::TABLE_HEAD.' b', 'a.header_id = b.header_id', 'inner');
		$sql->join(static::TABLE_BARANG.' c', 'a.section_id = c.SectionId', 'left');
		$sql->join(static::TABLE_MACHINE.' d', 'b.machine_id = d.MachineId', 'inner');
		$sql->join(static::TABLE_SHIFT.' e', 'a.shift = e.ShiftNo', 'left');
		$sql->join(static::TABLE_FINISHING.' g', 'a.finishing = g.finishing_id', 'left');
		$sql->join(static::TABLE_LEN.' i', 'a.len = i.LengthId', 'left');

		if($date_start != '')
		{
			$sql->where('a.tanggal >=', "$date_start");
		}

		if($date_finish != '')
		{
			$sql->where('a.tanggal <=', "$date_finish");
		}

		if($shift != 0)
		{
			$sql->where('a.shift', $shift);
		}

		if($machine_id != '')
		{
			$sql->where('b.machine_id', $machine_id);
		}

		if($section_id != '')
		{
			$sql->where('a.section_id', $section_id);
		}

		if($header_id != '')
		{
			$sql->where('a.header_id', $header_id);
		}

		$sql->order_by('a.master_detail_id', 'DESC');
		$get = $sql->get();

		return $get->result();
	}

	/**
	 * get detail data
	 */
	public function get_detail_by_id($id)
	{
		$sql = $this->db;

		$sql->select('a.*, h.*, h.billet_id as billet_type_id');
		$sql->from(static::TABLE. ' a');
		$sql->join(static::TABLE_NEWMASTER.' h', 'a.master_id = h.master_id', 'left');
		$sql->where('a.master_detail_id', $id);
		$get = $sql->get();

		return $get->row();
	}

	/**
	 * get detail data
	 */
	public function get_data_by_id($id)
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE_BARANG);
		$sql->where('SectionId', $id);
		$get = $sql->get();

		return $get->row();
	}

	/**
	 * insert to master detail
	 */
	public function save_detail($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	/**
	 * update to master detail
	 */
	public function update($id, $data)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->update(static::TABLE, $data);
	}

	/**
	 * insert to master detail
	 */
	public function delete($id)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->delete(static::TABLE);
	}

	/**
	 * Searching detail
	 */
	public function search($date_start, $date_finish, $shift)
	{
		$sql = $this->db;

		$sql->select('a.*, b.SectionDescription, b.DieTypeId, c.Name as MachineName, d.Length, e.ShiftStart, h.weight_standard as WeightStandard, g.finishing_name, h.*, h.billet_id as billet_type_id');
		$sql->from(static::TABLE.' a');
		$sql->join(static::TABLE_BARANG.' b', 'a.section_id = b.SectionId', 'inner');
		$sql->join(static::TABLE_LEN.' d', 'a.len = d.LengthId', 'inner');
		$sql->join(static::TABLE_SHIFT.' e', 'a.shift = e.ShiftNo', 'inner');
		$sql->join(static::TABLE_FINISHING.' g', 'a.finishing = g.finishing_id', 'inner');
		$sql->join(static::TABLE_NEWMASTER.' h', 'a.master_id = h.master_id', 'inner');
		$sql->join(static::TABLE_MACHINE.' c', 'h.machine_type_id = c.MachineTypeId', 'inner');
		
		$sql->order_by('a.tanggal', 'DESC');

		if($date_start != '')
		{
			$sql->where('a.tanggal >=', "$date_start");
		}

		if($date_finish != '')
		{
			$sql->where('a.tanggal <=', "$date_finish");
		}

		if($shift != '')
		{
			$sql->where('a.shift', $shift);
		}

		$get = $sql->get();

		return $get->result();
	}
}
?>