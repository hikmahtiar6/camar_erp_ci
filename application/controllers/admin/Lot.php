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
	}

	public function json($master_detail_id)
	{
		$page  = ($this->input->post('page')) ? $this->input->post('page') : 1;
		$limit = ($this->input->post('rows')) ? $this->input->post('rows') : 10 ;
		$sidx  = ($this->input->post('sidx')) ? $this->input->post('sidx') : 'lot_id';
		$sord  = ($this->input->post('sord')) ? $this->input->post('sord') : 'desc';
		$search_get  = $this->input->post('_search');
		$sum = array();
		 
		if(!$sidx) $sidx=1;

		$get_md = $this->lot_model->get_data_by(array(
			'master_detail_id' => $master_detail_id
		))->result();
		 
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

		$total_pages = 0;
		if($count > 0) 
		{
			$total_pages = ceil($count/$limit);
		}


		if ($page > $total_pages) $page=$total_pages;
			$start = $limit*$page - $limit;
		if($start <0) $start = 0;		
		 
		$data1 = $get_md;
		//echo ($limit + $start).'-'.$start;
		
		$response = new stdClass();

		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;

		$i=0;
		foreach($data1 as $gmd)
		{
			$response->rows[$i]['id']   = $gmd->lot_id;
			$response->rows[$i]['cell'] = array(
				$gmd->dies_used,
				'',
				$gmd->berat_ak,
				'',
				'',
				$gmd->p_billet_aktual,
				$gmd->jumlah_billet,
				$gmd->billet_vendor_id,
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
			);
			$i++;
		}

		$this->output->set_output(json_encode($response));
	}

	public function get_detail($master_detail_id)
	{
		$get_detail = $this->detail_model->get_data_by_id($master_detail_id);

		$this->twiggy->set('get_detail', $get_detail);
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
				);
				$this->lot_model->update($id, $datanya);
			break;
			case 'del':
				$this->lot_model->delete($id);
			break;
		}	
	}

}

?>