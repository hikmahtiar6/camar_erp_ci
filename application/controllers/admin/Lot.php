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
			$berat_billet = ($get_sum_ak->jml != NULL) ? (float) round($gmd->p_billet_aktual * $gmd->jumlah_billet * $billet_weight, 2) : '';

			$berat_hasil = $len * $get_sum_jml_btg->jml * $rata2_berat_ak;
			$recovery = ($berat_hasil > 0) ? round($get_sum_jml_btg->jml / $berat_hasil, 3) : 0;

			$response->rows[$i]['id']   = $gmd->lot_id;
			$response->rows[$i]['cell'] = array(
				$weight_standard,
				$gmd->berat_ak,
				$rata2_berat_ak,
				'#'.($i+1),
				$gmd->p_billet_aktual,
				$gmd->jumlah_billet,
				$gmd->billet_vendor_id,
				'<div class="berat-billet">'.$berat_billet.'</div>',
				'<div class="total-billet"></div>',
				$gmd->rak_btg,
				($gmd->jumlah_di_rak_btg > 0) ? $gmd->jumlah_di_rak_btg : '',
				$get_sum_jml_btg->jml,
				$berat_hasil,
				$recovery,
			);
			$i++;
		}

		$this->output->set_output(json_encode($response));
	}

	public function get_detail($master_detail_id)
	{
		$get_detail = $this->detail_model->get_data_by_id($master_detail_id);
		$get_detail_header = $this->lot_model->get_data_header_by_master_detail_id($master_detail_id);
		$get_operator = $this->operatorLot_model->get_data_by(array('grup' => 'operator'));
		$get_operator2 = $this->operatorLot_model->get_data_by(array('grup' => 'wakil'));
		$vendor_lot_data = list_vendor_lot();

		$machine_id = '';
		$section_id = '';
		if($get_detail)
		{
			$machine_id = $get_detail->MachineId;
			$section_id = $get_detail->section_id;
		}
		$get_master_query =  $this->query_model->get_master_advance($machine_id, $section_id)->row();

		$this->twiggy->set('vendor_lot_data', $vendor_lot_data);
		$this->twiggy->set('get_operator', $get_operator);
		$this->twiggy->set('get_operator2', $get_operator2);
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
		//$opr1 = $this->input->post('opr1');
		//$opr2 = $this->input->post('opr2');
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
			//'opr1'             => $opr1,
			//'opr2'             => $opr2,
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

	/**
	 * Check selisih waktu
	 */
	public function check_selisih_time()
	{
		$time1 = $this->input->post('time1');
		$time2 = $this->input->post('time2');

		$date1 = date('Y-m-d ').$time1.':00';
		$date2 = date('Y-m-d ').$time2.':00';

		$this->output->set_output(selisih_waktu($date2, $date1));
	}

	/**
	 * Get Lot Billet
	 */
	public function get_billet($master_detail_id = '')
	{
		$data = $this->lot_model->get_lot_billet($master_detail_id)->result();
		$response = [];

		if($data)
		{
			foreach($data as $row)
			{
				$response[] = array(
					'lotBilletId'   => $row->SpkLotBilletId,
					'pBilletActual' => to_decimal($row->PBilletActual),
					'jmlBillet'     => to_decimal($row->JumlahBillet),
					'billetVendorId'=> $row->BilletVendorId,
					'keterangan'    => $row->Keterangan,
				);
			}
			
		}

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Save Lot billet
	 */
	public function save_billet()
	{
		$master_detail_id = $this->input->post('master_detail_id');
		$p_billet_aktual = $this->input->post('p_billet_aktual');
		$jml_billet = $this->input->post('jml_billet');
		$vendor_id = $this->input->post('vendor_id');
		$lot_billet_id = $this->input->post('lot_billet_id');
		$keterangan = $this->input->post('keterangan');

		$data_save = array(
			'PBilletActual'  => $p_billet_aktual,
			'JumlahBillet'   => $jml_billet,
      		'BilletVendorId' => $vendor_id,
			'MasterDetailId' => $master_detail_id,
			'Keterangan'     => $keterangan
		);

		if($lot_billet_id == '')
		{
			$save = $this->lot_model->save_lot_billet($data_save);
			if($save)
			{
				$response = array(
					'message' => 'Berhasil menyimpan lot billet',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal menyimpan lot billet',
					'status'  => 'danger'
				);
			}
		}
		else
		{
			$update = $this->lot_model->update_lot_billet($lot_billet_id, $data_save);
			if($update)
			{
				$response = array(
					'message' => 'Berhasil update lot billet',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal update lot billet',
					'status'  => 'danger'
				);
			}
		}

		$get_lot_billet_id = $this->lot_model->get_lot_billet($master_detail_id, '', $p_billet_aktual, $jml_billet, $vendor_id)->row();

		$id = ($get_lot_billet_id) ? $get_lot_billet_id->SpkLotBilletId : '';

		$response['id'] = $id;		

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Delete Lot billet
	 */
	public function delete_billet()
	{
		$id = $this->input->post('id');
		$delete = $this->lot_model->delete_lot_billet($id);
		if($delete)
		{
			$response = array(
				'message' => 'Berhasil menghapus lot billet',
				'status'  => 'success'
			);
		}
		else
		{
			$response = array(
				'message' => 'Gagal menghapus lot billet',
				'status'  => 'danger'
			);
		}
		$this->output->set_output(json_encode($response));
	}


	/**
	 * Save Lot hasil
	 */
	public function save_hasil()
	{
		$master_detail_id = $this->input->post('master_detail_id');
		$rak = $this->input->post('rak');
		$jml_rak = $this->input->post('jml_rak');
		$lot_hasil_id = $this->input->post('lot_hasil_id');

		$data_save = array(
			'Rak'            => $rak,
			'JumlahBtgRak'   => $jml_rak,
			'MasterDetailId' => $master_detail_id
		);

		if($lot_hasil_id == '')
		{
			$save = $this->lot_model->save_lot_hasil($data_save);
			if($save)
			{
				$response = array(
					'message' => 'Berhasil menyimpan lot hasil',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal menyimpan lot hasil',
					'status'  => 'danger'
				);
			}
		}
		else
		{
			$update = $this->lot_model->update_lot_hasil($lot_hasil_id, $data_save);
			if($update)
			{
				$response = array(
					'message' => 'Berhasil update lot hasil',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal update lot hasil',
					'status'  => 'danger'
				);
			}
		}

		$get_lot_hasil_id = $this->lot_model->get_lot_hasil($master_detail_id, '', $rak, $jml_rak)->row();

		$id = ($get_lot_hasil_id) ? $get_lot_hasil_id->SpkLotHasilId : '';

		$response['id'] = $id;		

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Delete Lot hasil
	 */
	public function delete_hasil()
	{
		$id = $this->input->post('id');
		$delete = $this->lot_model->delete_lot_hasil($id);
		if($delete)
		{
			$response = array(
				'message' => 'Berhasil menghapus lot hasil',
				'status'  => 'success'
			);
		}
		else
		{
			$response = array(
				'message' => 'Gagal menghapus lot hasil',
				'status'  => 'danger'
			);
		}
		$this->output->set_output(json_encode($response));
	}

	/**
	 * Get Lot Hasil
	 */
	public function get_hasil($master_detail_id = '')
	{
		$data = $this->lot_model->get_lot_hasil($master_detail_id)->result();
		$response = [];

		if($data)
		{
			foreach($data as $row)
			{
				$response[] = array(
					'lotHasilId'   => $row->SpkLotHasilId,
					'rak'          => $row->Rak,
					'jmlRak'       => to_decimal($row->JumlahBtgRak),
				);
			}
			
		}

		$this->output->set_output(json_encode($response));
	}


	/**
	 * Save Lot Berat Actual
	 */
	public function save_berat_actual()
	{
		$master_detail_id = $this->input->post('master_detail_id');
		$berat_akt = $this->input->post('berat_akt');
		$lot_berat_actual_id = $this->input->post('lot_berat_actual_id');

		$data_save = array(
			'BeratAkt'       => $berat_akt,
			'MasterDetailId' => $master_detail_id
		);

		if($lot_berat_actual_id == '')
		{
			$save = $this->lot_model->save_lot_berat_actual($data_save);
			if($save)
			{
				$response = array(
					'message' => 'Berhasil menyimpan lot berat aktual',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal menyimpan lot berat aktual',
					'status'  => 'danger'
				);
			}
		}
		else
		{
			$update = $this->lot_model->update_lot_berat_actual($lot_berat_actual_id, $data_save);
			if($update)
			{
				$response = array(
					'message' => 'Berhasil update lot berat aktual',
					'status'  => 'success'
				);
			}
			else
			{
				$response = array(
					'message' => 'Gagal update lot berat aktual',
					'status'  => 'danger'
				);
			}
		}

		$get_lot_berat_actual_id = $this->lot_model->get_lot_berat_actual($master_detail_id, '', $berat_akt)->row();

		$id = ($get_lot_berat_actual_id) ? $get_lot_berat_actual_id->SpkLotBeratActualId : '';

		$response['id'] = $id;		

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Delete Lot Berat Actual
	 */
	public function delete_berat_actual()
	{
		$id = $this->input->post('id');
		$delete = $this->lot_model->delete_lot_berat_actual($id);
		if($delete)
		{
			$response = array(
				'message' => 'Berhasil menghapus lot berat aktual',
				'status'  => 'success'
			);
		}
		else
		{
			$response = array(
				'message' => 'Gagal menghapus lot berat aktual',
				'status'  => 'danger'
			);
		}
		$this->output->set_output(json_encode($response));
	}

	/**
	 * Get Lot Berat Actual
	 */
	public function get_berat_actual($master_detail_id = '')
	{
		$data = $this->lot_model->get_lot_berat_actual($master_detail_id)->result();
		$response = [];

		if($data)
		{
			foreach($data as $row)
			{
				$response[] = array(
					'lotBeratId' => $row->SpkLotBeratActualId,
					'beratAkt'         => to_decimal($row->BeratAkt),
					'beratStd'         => '',
					'rataAkt'          => '',
				);
			}
			
		}

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Cek isian lot
	 */
	public function cek_isian_lot($master_detail_id = '')
	{
		$exists = array();

		$cek_billet = $this->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'billet');
		$cek_berat_aktual = $this->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'berat_aktual');
		$cek_hasil = $this->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'hasil');

		if(count($cek_billet) > 0) {
			array_push($exists, 1);
		}

		if(count($cek_berat_aktual) > 0) {
			array_push($exists, 1);
		}

		if(count($cek_hasil) > 0) {
			array_push($exists, 1);
		}

		echo count($exists);
	}

	/**
	 * set posting
	 */
	public function set_posting_header($master_detail_id = '')
	{
		$data_update = array(
			'is_posted' => 1
		);
		return $this->lot_model->update_header($master_detail_id, $data_update);
	}

}

?>