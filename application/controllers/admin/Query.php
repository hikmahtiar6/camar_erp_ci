<?php
/**
 * Query Controller
 */
class Query extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	}

	public function sinkron_week()
	{

		foreach(week_in_year() as $row) {
			echo $row['no'].' <br>';

			$query = $this->db->query("select * from SpkHeader where week = '".$row['no']."'")->row();
			if($query)
			{
				$data = array(
					'date_start'  => change_format_date($row['date_start']),
					'date_finish' => change_format_date($row['date_finish'])
				);
				$this->db->where('week', $row['no']);
				$this->db->update('SpkHeader', $data);
			}


		}
			//$this->db->update('');
	}
}