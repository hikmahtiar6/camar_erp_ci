<?php
/**
 * Class Lot
 */

class Lot extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		$this->load->model('master/query_model');
		$this->load->model('master/detail_model');
		$this->load->model('master/lot_model');
		$this->load->model('master/operatorLot_model');
	}

	public function json($master_detail_id)
	{
		$page  = ($this->input->post('page')) ? $this->input->post('page') : 1;
		$sidx  = ($this->input->post('sidx')) ? $this->input->post('sidx') : 'lot_id';
		$sord  = ($this->input->post('sord')) ? $this->input->post('sord') : 'desc';
		$search_get  = $this->input->post('_search');
		$sum = array();
		 
		if(!$sidx) $sidx=1;

		$get_detail = $this->detail_model->get_data_by_id($master_detail_id);
		$machine_id = '';
		$section_id = '';

		if($get_detail)
		{
			$machine_id = $get_detail->MachineId;
			$section_id = $get_detail->section_id;
		}

		$get_md = $this->lot_model->advance_search($shift = 0, $master_detail_id, $machine_id, $section_id, $limit = '', $start = '', $order = 'lot_id', $type_order = 'DESC')->result();

		/*$get_md = $this->lot_model->get_data_by(array(
			'master_detail_id' => $master_detail_id
		))->result();
		*/
		# Untuk Single Searchingnya #
		/*$where = ""; //if there is no search request sent by jqgrid, $where should be empty
			$searchField = isset($_GET['searchField']) ? $_GET['searchField'] : false;
		$searchString = isset($_GET['searchString']) ? $_GET['searchString'] : false;
		if ($search_get == 'true') 
		{
			$where = array($searchField => $searchString);
		}*/
		# End #
		 
		//$count = $this->section_model->count_master_advance($where);
		
		$count = count($get_md);

		$limit = ($this->input->post('rows')) ? $this->input->post('rows') : 10;

		$total_pages = 0;
		if($count > 0) 
		{
			$total_pages = ceil($count/$limit);
		}


		if ($page > $total_pages) $page=$total_pages;
			$start = $limit*$page - $limit;
		if($start <0) $start = 0;		
		 
		$data1 = $get_md = $this->lot_model->advance_search($shift = 0, $master_detail_id, $machine_id, $section_id, $limit + $start, $start, $order = 'lot_id', $type_order = 'DESC')->result();
		//echo ($limit + $start).'-'.$start;
		
		$response = new stdClass();

		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;

		$i=0;
		foreach($data1 as $gmd)
		{
			$get_master_query =  $this->query_model->get_master_advance($machine_id, $section_id)->row();
			$weight_standard = ($get_master_query) ? (float) round($get_master_query->WeightStandard, 3) : '';
			$billet_weight = ($get_master_query) ? (float) round($get_master_query->BilletWeight, 3) : '';
			$len = ($get_master_query) ? (float) round($get_master_query->Length, 3) : '';
			
			$get_sum_ak = $this->lot_model->suming('a.berat_ak', $shift = 0, $master_detail_id, $machine_id, $section_id)->row();
			$get_sum_jml_btg = $this->lot_model->suming('a.jumlah_di_rak_btg', $shift = 0, $master_detail_id, $machine_id, $section_id)->row();
			$get_counting_ak = $this->lot_model->counting('a.berat_ak', $shift = 0, $master_detail_id, $machine_id, $section_id)->row();

			$rata2_berat_ak = ($get_sum_ak->jml != NULL) ? (float) round($get_sum_ak->jml / $get_counting_ak->jml * 2 / 1000, 3) : '';
			$berat_billet = ($get_sum_ak->jml != NULL) ? (float) round((($gmd->p_billet_aktual * 2) / 100) * $billet_weight, 3) : '';

			$response->rows[$i]['id']   = $gmd->lot_id;
			$response->rows[$i]['cell'] = array(
				$weight_standard,
				$gmd->berat_ak,
				$rata2_berat_ak,
				'#'.($i+1),
				$gmd->p_billet_aktual,
				($gmd->p_billet_aktual * 2) / 100,
				$gmd->billet_vendor_id,
				$berat_billet,
				'',
				$gmd->rak_btg,
				($gmd->jumlah_di_rak_btg > 0) ? $gmd->jumlah_di_rak_btg : '',
				$get_sum_jml_btg->jml,
				$len * $get_sum_jml_btg->jml * $rata2_berat_ak,
				$len * $get_sum_jml_btg->jml * $rata2_berat_ak,
			);
			$i++;
		}

		$this->output->set_output(json_encode($response));
	}

	public function get_detail($master_detail_id)
	{
		$get_detail = $this->detail_model->get_data_by_id($master_detail_id);
		$get_detail_header = $this->lot_model->get_data_header_by_master_detail_id($master_detail_id);
		$get_operator = $this->operatorLot_model->get_data();

		$machine_id = '';
		$section_id = '';
		if($get_detail)
		{
			$machine_id = $get_detail->MachineId;
			$section_id = $get_detail->section_id;
		}
		$get_master_query =  $this->query_model->get_master_advance($machine_id, $section_id)->row();

		$this->twiggy->set('get_operator', $get_operator);
		$this->twiggy->set('get_detail', $get_detail);
		$this->twiggy->set('get_master_query', $get_master_query);
		$this->twiggy->set('get_detail_header', $get_detail_header);
		$this->twiggy->set('master_detail_id', $master_detail_id);
		$this->twiggy->template('admin/lot/edit')->display();
	}

	public function add_row_data($master_detail_id)
	{
		$data_inserted = array(
			'master_detail_id' => $master_detail_id,
			'billet_vendor_id' => 'Vendor1',
		);

		$get_last_data = $this->lot_model->get_last_data($master_detail_id);

		if($get_last_data)
		{
			$data_inserted = array(
				'master_detail_id' => $master_detail_id,
				'dies_used'        => $get_last_data->dies_used,
				'berat_ak'         => $get_last_data->berat_ak,
				'p_billet_aktual'  => $get_last_data->p_billet_aktual,
				'jumlah_billet'    => $get_last_data->jumlah_billet,
				'billet_vendor_id' => $get_last_data->billet_vendor_id,
			);

		}

		$save = $this->lot_model->save($data_inserted);

		$response = array(
			'status' => 'success'
		);
		$this->output->set_output(json_encode($response));
	}

	public function crud()
	{
		$oper = $this->input->post('oper');
		$id = $this->input->post('id');
		$dies_used = $this->input->post('dies_used');
		$berat_ak = $this->input->post('berat_ak');
		$p_billet_aktual = $this->input->post('p_billet_aktual');
		$jumlah_billet = $this->input->post('jumlah_billet');
		$billet_vendor_id = $this->input->post('billet_vendor_id');
		$rak_btg = $this->input->post('rak_btg');
		$jumlah_di_rak_btg = $this->input->post('jumlah_di_rak_btg');

		switch ($oper) {
			case 'add':
			break;
			case 'edit':
				$datanya=array(
					'dies_used'        => $dies_used,
					'berat_ak'         => $berat_ak,
					'p_billet_aktual'  => $p_billet_aktual,
					'jumlah_billet'    => $jumlah_billet,
					'billet_vendor_id' => $billet_vendor_id,
					'rak_btg'          => $rak_btg,
					'jumlah_di_rak_btg'          => $jumlah_di_rak_btg,
				);
				$this->lot_model->update($id, $datanya);
			break;
			case 'del':
				$this->lot_model->delete($id);
			break;
		}	
	}

	public function save_header()
	{
		$master_detail_id = $this->input->post('master_detail_id');
		$opr1 = $this->input->post('opr1');
		$opr2 = $this->input->post('opr2');
		$scrap = $this->input->post('scrap');
		$pot_end_butt = $this->input->post('potendbutt');
		$time_start = $this->input->post('time_start');
		$time_finish = $this->input->post('time_finish');
		$downtime = $this->input->post('downtime');
		$deadcycle = $this->input->post('deadcycle');
		$ram_speed = $this->input->post('ram_speed');
		$pressure_bar = $this->input->post('pressure_bar');
		$keterangan = $this->input->post('keterangan');
		$blkg_actual = $this->input->post('blkg_actual');
		$pull_awal_actual = $this->input->post('pull_awal_actual');

		$data = array(
			'master_detail_id' => $master_detail_id,
			'opr1'             => $opr1,
			'opr2'             => $opr2,
			'scrap'            => $scrap,
			'pot_end_butt'     => $pot_end_butt,
			'time_start'       => $time_start,
			'time_finish'      => $time_finish,
			'downtime'         => $downtime,
			'deadcycle'        => $deadcycle,
			'ram_speed'        => $ram_speed,
			'pressure_bar'     => $pressure_bar,
			'keterangan'       => $keterangan,
			'blkg_actual'      => $blkg_actual,
			'pull_awal_actual' => $pull_awal_actual,
		);
		$index_dice = $this->input->post('index_dice');
		if($index_dice)
		{
			$this->detail_model->update($master_detail_id, array('index_dice' => $this->set_idxdice($index_dice)));
		}

		$get_header_lot = $this->lot_model->get_data_header_by_master_detail_id($master_detail_id);
		if($get_header_lot)
		{
			$this->lot_model->update_header($master_detail_id, $data);
		}
		else
		{
			$this->lot_model->save_header($data);
		}
	}

	private function set_idxdice($array)
	{
		$str = '';
		foreach($array as $row)
		{
			$str .= $row.', ';
		}

		return rtrim($str, ', ');
	}

}

?>