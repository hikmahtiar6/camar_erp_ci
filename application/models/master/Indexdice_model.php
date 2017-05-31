<?php
/**
 * Model Master Index DIce
 *
 * @author Hikmahtiar <hikamhtiar.cool@gmail.com>
 */
class Indexdice_model extends CI_Model {

	const TABLE = 'Purchasing.DieReceivingDetail';
	const TABLE_SPK = 'dbo.SpkDetail';
	const TABLE_DIES_LOG = 'dbo.DiesHistoryCardLog';
	const TABLE_DIES_LOCATION = 'dbo.DiesLocation';
	const TABLE_DIES_STATUS = 'dbo.DiesStatus';
	const TABLE_HEAD = 'SpkHeader';
	const TABLE_BARANG = 'Inventory.Sections';
	const TABLE_PROBLEM = 'dbo.DiesProblem';
	const TABLE_LOT = 'dbo.SpkLot';
	const TABLE_BILLET = 'SpkLotBillet';
	const TABLE_BERAT_ACTUAL = 'SpkLotBeratActual';
	const TABLE_HASIL = 'SpkLotHasil';
	const TABLE_MACHINE = 'Factory.Machines';
	const TABLE_SHIFT = 'Factory.Shifts';

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

	public function get_data_by_machine_section($machinetypeid = '', $sectionid = '')
	{
		$sql = $this->db;

		$sql->select('DiesId');
		$sql->from(static::TABLE);

		if($sectionid != '')
		{
			$sql->where('SectionId', $sectionid);
		}

		if($machinetypeid != '')
		{
			if($machinetypeid == '0750T')
			{
				$sql->where_in('MachineTypeId', array('0750T', '0690T'));
			}
			else
			{
				$sql->where('MachineTypeId', $machinetypeid);			
			}
				//$sql->where('MachineTypeId', $machinetypeid);			
		}

		$sql->order_by('DiesYear, DiesSeqNo, DiesSuffix, DieTypeComponentId, DieTypeSubComponentId');

		//$sql->group_by('DiesId');

		$get = $sql->get();
		return $get;
	}

	public function get_data_by($array)
	{
		$sql = $this->db;

		$sql->select('DiesId');
		$sql->from(static::TABLE);
		$sql->where($array);

		$sql->group_by('DiesId');

		$get = $sql->get();
		return $get;
	}

	public function get_data2()
	{
		$sql = $this->db;

		$sql->select('DiesId');
		$sql->from(static::TABLE);
		//$sql->where($array);

		$sql->group_by('DiesId');

		$get = $sql->get();
		return $get;
	}

	public function filter_dies_departement($tgl, $shift, $machine = '')
	{
		$sql = $this->db;

		$sql->select('a.*, c.SectionDescription, d.machine_id as MachineId');
		$sql->from(static::TABLE_SPK. ' a');
		$sql->join(static::TABLE_BARANG. ' c', 'a.section_id = c.SectionId', 'left');
		$sql->join(static::TABLE_HEAD. ' d', 'a.header_id = d.header_id', 'inner');
		$sql->join(static::TABLE_SHIFT. ' s', 'a.shift = s.ShiftRefId', 'inner');

		$sql->where('a.index_dice IS NOT NULL');
		$sql->where('CONVERT(VARCHAR, a.index_dice) != ', '');
		$sql->where('a.tanggal', $tgl);
		$sql->where('s.ShiftNo', $shift);

		if($machine != '')
		{
			$sql->where('d.machine_id', $machine);
		}

		$get = $sql->get();
		return $get->result();

	}

	/**
	 * save log
	 */
	public function set_dies_log($data)
	{
		return $this->db->insert(static::TABLE_DIES_LOG, $data);
	}

	/**
	 * get log
	 */
	public function get_dies_log($date = '', $status = '', $location = '', $dies_id = '')
	{
		$sql = $this->db;

		$sql->select('DiesId');
		$sql->from(static::TABLE_DIES_LOG);
		
		if($date != '')
		{
			$sql->where('CONVERT(VARCHAR(10),LogTime,120)', $date);	
		}
		
		if($status != '')
		{
			$sql->where('DiesStatusId', $status);	
		}

		if($dies_id != '')
		{
			$sql->where('DiesId', $dies_id);	
		}

		if($location != '')
		{
			$sql->where('DiesLocationId', $location);
		}

		$sql->group_by('DiesId');

		$get = $sql->get();
		return $get;
	}

	/**
	 * get last status log
	 */
	public function get_last_status_log($dies_id)
	{
		$sql = $this->db;

		$sql->select('b.DiesStatus');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_DIES_STATUS.' b', 'a.DiesStatusId = b.DiesCode', 'inner');
		$sql->where('a.DiesId', $dies_id);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}

	/**
	 * get last status log
	 */
	public function get_last_location_log($dies_id)
	{
		$sql = $this->db;

		$sql->select('c.Location');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_DIES_LOCATION.' c', 'a.DiesLocationId = c.DiesLocationId', 'inner');
		$sql->where('a.DiesId', $dies_id);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}
	
	/**
	 * get last log
	 */
	public function get_last_problem_log($dies_id)
	{
		$sql = $this->db;

		$sql->select('c.DiesProblemId, c.Problem, a.DiesStatusId, a.Koreksi, a.Korektor');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_PROBLEM.' c', 'a.DiesProblemId = c.DiesProblemId', 'left');
		$sql->where('a.DiesId', $dies_id);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}
	
	/**
	 * get last log
	 */
	public function get_last_datetime_log($dies_id)
	{
		$sql = $this->db;

		$sql->select('a.LogTime');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->where('a.DiesId', $dies_id);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}
	
	/**
	 * get last log
	 */
	public function get_last_log_by_dies($dies)
	{
		$sql = $this->db;

		$sql->select('a.*, b.DiesStatus, c.Location');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_DIES_STATUS.' b', 'a.DiesStatusId = b.DiesCode', 'inner');
		$sql->join(static::TABLE_DIES_LOCATION.' c', 'a.DiesLocationId = c.DiesLocationId', 'inner');
		$sql->where('a.DiesId', $dies);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}
	
	/**
	 * Get data BPLEmr
	 */
	 public function get_data_problem()
	 {
		 $sql = $this->db;

		 $sql->select('*');
		 $sql->from(static::TABLE_PROBLEM);

		 $get = $sql->get();
		 return $get->result();
	 }
	 
	 /**
	  * Update log
	  */
	 public function update_log($data, $id)
	 {
		 $this->db->where('DiesHistoryCardLogId', $id);
		 return $this->db->update(static::TABLE_DIES_LOG, $data);
	 }

	 /**
	  * Get history card filter
	  */
	 public function filter_history_card($section_id = '', $dice = '', $tgl = '')
	 {
	 	$sql = $this->db;

	 	$sql->select('a.*, b.PBilletActual, b.JumlahBillet');
	 	$sql->from(static::TABLE_SPK.' a');
		$sql->join(static::TABLE_BILLET.' b', 'a.master_detail_id = b.MasterDetailId', 'left');

	 	if($section_id != '')
	 	{
	 		$sql->where('a.section_id', $section_id);
	 	}

	 	if($dice != '')
	 	{
	 		$sql->like('a.index_dice', $dice);
	 	}
		
		if($tgl != '')
		{
			$sql->where('a.tanggal', change_format_date($tgl));
		}

	 	$get = $sql->get();

	 	return $get->result_array();
	 }

	 /**
	  * Get history card filter fix
	  */
	 public function filter_history_card_fix($section_id = '', $dice = '', $tgl = '')
	 {
	 	$sql = $this->db;

	 	$sql->select('a.*, b.DiesStatus, c.Location, p.Problem');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_DIES_STATUS.' b', 'a.DiesStatusId = b.DiesCode', 'inner');
		$sql->join(static::TABLE_DIES_LOCATION.' c', 'a.DiesLocationId = c.DiesLocationId', 'inner');
		$sql->join(static::TABLE_PROBLEM.' p', 'a.DiesProblemId = p.DiesProblemId', 'left');

	 	/*if($section_id != '')
	 	{
	 		$sql->where('a.section_id', $section_id);
	 	}*/

	 	if($dice != '')
	 	{
	 		$sql->like('a.DiesId', $dice);
	 	}
		
		if($tgl != '')
		{
			$sql->where('a.LogTime <=', change_format_date($tgl). ' 23:59:59');
		}

		$sql->order_by('a.LogTime', 'asc');

	 	$get = $sql->get();

	 	return $get->result_array();
	 }

	/**
	 * get data status
	 */
	public function get_dice_status()
	{
		$sql = $this->db;

	 	$sql->select('*');
	 	$sql->from(static::TABLE_DIES_STATUS);
	 	$sql->order_by('DiesCode', 'asc');
	 	$get = $sql->get();

	 	return $get->result();
	}

	/**
	 * get data by id
	 */
	public function get_log_by_id($card_log_id)
	{
		$sql = $this->db;

		$sql->select('a.*, b.DiesStatus, c.Location');
		$sql->from(static::TABLE_DIES_LOG.' a');
		$sql->join(static::TABLE_DIES_STATUS.' b', 'a.DiesStatusId = b.DiesCode', 'inner');
		$sql->join(static::TABLE_DIES_LOCATION.' c', 'a.DiesLocationId = c.DiesLocationId', 'inner');
		$sql->where('a.DiesHistoryCardLogId', $card_log_id);
		$sql->order_by('a.LogTime', 'desc');

		$get = $sql->get();
		return $get->row();
	}
}

?>