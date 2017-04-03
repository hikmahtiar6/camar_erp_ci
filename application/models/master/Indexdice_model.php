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

	public function filter_dies_departement($tgl, $shift)
	{
		$sql = $this->db;

		$sql->select('index_dice');
		$sql->from(static::TABLE_SPK);
		$sql->where('index_dice IS NOT NULL');
		$sql->where('tanggal', $tgl);
		$sql->where('shift', $shift);

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

		$sql->select('DiesId, 
			(SELECT top 1 b.DiesStatus FROM DiesHistoryCardLog a INNER JOIN DiesStatus b ON a.DiesStatusId = b.DiesCode order by a.LogTime DESC ) as DiesStatus,
			(SELECT top 1 c.Location FROM DiesHistoryCardLog a INNER JOIN DiesLocation c ON a.DiesLocationId = c.DiesLocationId order by a.LogTime DESC ) as DiesLocation');
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
}

?>