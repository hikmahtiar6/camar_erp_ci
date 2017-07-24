<?php
/**
 * Model Master Detail
 */
class Detail_model extends CI_Model {

	const TABLE = 'SpkDetail';
	const TABLE_LEN = 'Inventory.MasterDimensionLength';
	const TABLE_HEAD = 'SpkHeader';
	const TABLE_BARANG = 'Inventory.Sections';
	const TABLE_MACHINE = 'Factory.Machines';
	const TABLE_SHIFT = 'Factory.Shifts';
	const TABLE_FINISHING = 'Finishing';
	const TABLE_LOT = 'dbo.SpkLot';
	const FINISHING = 'Inventory.MasterFinishing';
	const FINISHING_TYPE = 'Inventory.MasterFinishingType';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_data_by_id($id)
	{
		$sql = $this->db;

		$sql->select('a.*, b.Length, c.SectionDescription, d.machine_id as MachineId, s.ShiftNo, ft.FinishingType');
		$sql->from(static::TABLE. ' a');
		$sql->join(static::TABLE_LEN. ' b', 'a.len = b.LengthId', 'left');
		$sql->join(static::TABLE_BARANG. ' c', 'a.section_id = c.SectionId', 'left');
		$sql->join(static::TABLE_HEAD. ' d', 'a.header_id = d.header_id', 'inner');
		$sql->join(static::TABLE_SHIFT. ' s', 'a.shift = s.ShiftRefId', 'inner');
		$sql->join(static::FINISHING. ' fi', 'fi.FinishingId = a.finishing', 'inner');
		$sql->join(static::FINISHING_TYPE. ' ft', 'fi.FinishingType = ft.FinishingType', 'inner');
		$sql->where('a.master_detail_id', $id);

		$get = $sql->get();
		return $get->row();
	}

	public function advance_search($date_start = '', $date_finish = '', $shift = 0, $machine_id = '', $section_id = '', $header_id = '', $limit = '', $start = '', $order = 'tanggal', $type_order = 'ASC')
	{
		$sql = "
			SELECT * 
			FROM
			 (
			 	SELECT 
					a.*, 
					b.machine_id AS machine_id_header, 
					c.SectionDescription, 
					d.MachineTypeId as machine_type, 
					e.ShiftDescription, 
					e.ShiftStart, 
					g.finishing_name, 
					i.*,
					ROW_NUMBER() OVER(ORDER BY a.master_detail_id DESC) as RowNum
				FROM
					".static::TABLE." a
				INNER JOIN 
					".static::TABLE_HEAD." b ON a.header_id = b.header_id
				LEFT JOIN
					".static::TABLE_BARANG." c ON a.section_id = c.SectionId
				INNER JOIN
					".static::TABLE_MACHINE." d ON b.machine_id = d.MachineId
				LEFT JOIN
					".static::TABLE_SHIFT." e ON a.shift = e.ShiftRefId
				LEFT JOIN
					".static::TABLE_FINISHING." g ON a.finishing = g.finishing_id
				LEFT JOIN
					".static::TABLE_LEN." i ON a.len = i.LengthId ";

		if($date_start != '')
		{
			$sql .= "AND a.tanggal >= '$date_start' ";
		}

		if($date_finish != '')
		{
			$sql .= "AND a.tanggal <= '$date_finish' ";
		}

		if($shift != 0)
		{
			$sql .= "AND a.shift = '$shift' ";
		}

		if($machine_id != '')
		{
			$sql .= "AND b.machine_id = '$machine_id' ";
		}

		if($section_id != '')
		{
			$sql .= "AND a.section_id = '$section_id' ";
		}

		if($header_id != '')
		{
			$sql .= "AND b.header_id = '$header_id' ";
		}

		$sql .= " ) AS t ";

		$sql = str_replace("i.LengthId AND", "i.LengthId WHERE", $sql);

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

	public function count_record_by_header_shift($header_id, $shift = 0)
	{
		$sql = $this->db;

		$sql->select('COUNT(*) as jml');
		$sql->from(static::TABLE);
		$sql->where('header_id', $header_id);

		if($shift > 0)
		{
			$sql->where('shift', $shift);		
		}

		$get = $sql->get();
		$row = $get->row();
		if($row)
		{
			return $row->jml;
		}
		return '0';
	}

	public function get_data_for_grid_dinamic($header_id = '', $shift = 0, $group = true, $machine_id = 'no', $tanggal = '')
	{
		$sql = $this->db;

		if($group)
		{
			$sql->select('a.header_id, d.MachineId, s.ShiftNo');
		}
		else
		{
			$sql->select('a.section_id, a.target_prod, b.Length, d.MachineId, s.ShiftNo');
		}

		$sql->from(static::TABLE. ' a');
		$sql->join(static::TABLE_LEN. ' b', 'a.len = b.LengthId', 'left');
		$sql->join(static::TABLE_HEAD. ' c', 'a.header_id = c.header_id', 'inner');
		$sql->join(static::TABLE_MACHINE. ' d', 'c.machine_id = d.MachineId', 'inner');
		$sql->join(static::TABLE_SHIFT. ' s', 's.ShiftRefId = a.shift', 'inner');

		if($header_id != '')
		{
			$sql->where('c.header_id', $header_id);
		}

		if($machine_id != 'no')
		{
			$sql->where('c.machine_id', $machine_id);
		}

		if($tanggal != '')
		{
			$sql->where('a.tanggal', date('Y-m-d', strtotime($tanggal)));
		}

		if($shift > 0)
		{
			$sql->where('s.ShiftNo', $shift);
		}

		if($group)
		{
			$sql->group_by('s.ShiftNo');
			$sql->group_by('a.header_id');
			$sql->group_by('d.MachineId');	
		}


		$get = $sql->get();
		return $get->result();
	}


	public function get_date_start($header_id)
	{
		$sql = $this->db;

		$sql->select('MIN(tanggal) as date_start_header');
		$sql->from(static::TABLE);
		$sql->where('header_id', $header_id);
		$sql->where('tanggal !=', NULL);
		$sql->where('tanggal !=', '');
		$sql->where('tanggal !=', ' ');

		$get = $sql->get();
		return $get->row();
	}

	public function get_date_finish($header_id)
	{
		$sql = $this->db;

		$sql->select('MAX(tanggal) as date_finish_header');
		$sql->from(static::TABLE);
		$sql->where('header_id', $header_id);
		$sql->where('tanggal !=', NULL);
		$sql->where('tanggal !=', '');
		$sql->where('tanggal !=', ' ');

		$get = $sql->get();
		return $get->row();
	}

	/**
	 * get data detail dinamis
	 */
	public function get_data_advance($id = '', $header_id = '')
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from(static::TABLE);

		if($id != '')
		{
			$sql->where('master_detail_id', $id);
		}

		if($header_id != '')
		{
			$sql->where('header_id', $header_id);
		}


		$get = $sql->get();
		return $get;
	}

	public function save($data)
	{
		return $this->db->insert(static::TABLE, $data);
	}

	public function update($id, $data)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->update(static::TABLE, $data);
	}

	public function delete($id)
	{
		$this->db->where('master_detail_id', $id);
		return $this->db->delete(static::TABLE);
	}

	/**
	 * delete header
	 */
	public function delete_header($id)
	{
		$this->db->where('header_id', $id);
		return $this->db->delete(static::TABLE_HEAD);
	}

	/**
	 * delete detail by header
	 */
	public function delete_detail_by_header($header_id)
	{
		$this->db->where('header_id', $header_id);
		return $this->db->delete(static::TABLE);
	}

	/**
	 * sum for lot
	 */
	public function suming_lot($field, $master_detail_id)
	{
		$sql = $this->db;

		$sql->select('SUM(CONVERT(INT, '.$field.')) as field');
		$sql->from(static::TABLE_LOT);
		$sql->where('master_detail_id', $master_detail_id);

		$get = $sql->get();
		$row =  $get->row();

		if($row != NULL)
		{
			return $row->field;
		}

		return '';
	}
}

?>