<?php
/**
 * Performance Model
 */
class Performance_model extends CI_Model {

	var $spk_detail = 'SpkDetail';
	var $spk_header = 'SpkHeader';
	var $spk_lot = 'SpkHeaderLot';
	var $shift = 'Factory.Shifts';

	/**
	 * Get data
	 */
	public function get_data_advance($master_detail_id = '', $week = '', $mesin_id = '')
	{
		$sql = $this->db;

		$sql->select('detail.*, shift.ShiftStart, shift.ShiftEnd, lot.time_start, lot.time_finish');
		$sql->from($this->spk_detail.' detail');
		$sql->join($this->spk_header.' head', 'head.header_id = detail.header_id', 'inner');
		$sql->join($this->spk_lot.' lot', 'lot.master_detail_id = detail.master_detail_id', 'inner');
		$sql->join($this->shift.' shift', 'shift.ShiftRefId = detail.shift', 'inner');

		if($week != '')
		{
			$sql->where('head.week', trim($week));
		}

		if($mesin_id != '')
		{
			$sql->where('head.machine_id', trim($mesin_id));
		}

		$get = $sql->get();

		return $get;
	}
}

?>