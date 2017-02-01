<?php
/**
 * Transaction Controller
 */
class Transaction extends CI_Controller 
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// check session
		$this->auth->_is_authentication();

		// load model
		$this->load->model('section_model');
		$this->load->model('master_model');
	}

	/**
	 * Index Page
	 */
	public function index()
	{
		$this->twiggy->template('admin/transaction/index')->display();
	}

	/**
	 * Data Transacation
	 */
	public function data()
	{
		$data = array(); 
		$get_md = $this->section_model->get_data_detail();

		$sum = array();

		if($get_md)
		{
			$no = 1;
			foreach($get_md as $gmd)
			{
				/*$target_prod_btg = $gmd->target_prod;
				if($gmd->f2_estfg != NULL)
				{
					$target_prod_btg = $gmd->f2_estfg * $gmd->target_prod; 
				}

				array_push($sum, $gmd->WeightStandard * $gmd->target_prod * $gmd->Length);
				*/
				$data[] = array(
					'no'         => $no,
					'id'    => $gmd->master_detail_id,
					'machine_id'	=> $gmd->machine_id,
					'tanggal'    => '',//date('D, j/M/y', strtotime($gmd->tanggal)),
					'shift'      => $gmd->shift,
					'section_id' => $gmd->section_id,
					'section_name' => '',//$gmd->SectionDescription,
					'mesin'    => '',//$gmd->machine_type_id,
					'billet'    => '',//$gmd->billet_type_id,
					'len'    => '',//$gmd->Length,
					'finishing'    => '',//$gmd->finishing_name,
					'target_prod'    => '',//$gmd->target_prod,
					'index_dice'    => $gmd->index_dice,
					'ppic_note'    => $gmd->ppic_note,
					'target_prod_btg'    => '',//$target_prod_btg,
					'die_type'    => '',//$gmd->DieTypeId,
					'weight_standard'    => '',//$gmd->WeightStandard,
					'target_section'    => '',//$gmd->WeightStandard * $gmd->target_prod * $gmd->Length,
					'total_target'    => '',//array_sum($sum),
					'shift_start'    => '',//date('H:i:s', strtotime($gmd->ShiftStart)),
					'shift_end'    => '',//date('H:i:s', strtotime($gmd->ShiftStart) + time($gmd->actual_pressure_time * $gmd->target_prod)),
					'null'    => '-',
					'apt'     => '',//$gmd->actual_pressure_time,
					'action' => '',//
						//"<a class='btn btn-default' id='edit-transaksi-".$gmd->master_detail_id."' data-toggle='modal' data-target='#defaultModal' href='".site_url('admin/transaction/edit/'.$gmd->master_detail_id)."' onclick='window.TRANSACTION.handleEditModal(\"".$gmd->master_detail_id."\")'>Edit</a>".
						//"<a class='btn btn-danger' id='delete-transaksi-".$gmd->master_detail_id."' href='javascript:;' onclick='window.TRANSACTION.handleDelete(\"".$gmd->master_detail_id."\")'>Hapus</a>"
				);

				$no++;
			}
		}

		$response = array(
			'data' => $data,
			'recordsTotal' => count($data)
		);

		$this->output->set_output(json_encode($response));
	}

	/**
	 * Save action
	 */
	public function save()
	{
		$post = $this->input->post();

		$id = $post['id'];

		$data_for_insert_header = array(
			'machine_id'  => $post['mesin'],
			'date_start'  => $post['date_start'],
			'date_finish' => $post['date_finish'],
		);


		
		if($id == "")
		{
			$saving = $this->section_model->save('header', $data_for_insert_header);
			if($saving)
			{

				$data_for_insert_detail = array(
					'header_id'  => $this->section_model->get_last_insert_id(),
				);
				$saving_detail = $this->section_model->save('detail', $data_for_insert_detail);

				$response = array(
					'message' => 'Transaksi berhasil disimpan',
					'status'  => 'success',
				);
			}
			else
			{
				$response = array(
					'message' => 'Transaksi gagal disimpan',
					'status'  => 'danger',
				);
			}
			
		}
		else
		{
			/*$saving = $this->section_model->update($id, $data_for_insert);
			if($saving)
			{
				$response = array(
					'message' => 'Transaksi berhasil diupdate',
					'status'  => 'success',
				);
			}
			else
			{
				$response = array(
					'message' => 'Transaksi gagal diupdate',
					'status'  => 'danger',
				);
			}*/
		}
	
		return $this->output->set_output(json_encode($response));
	}

	/**
	 * edit save view
	 */
	public function edit($id)
	{

		if($id != 'new')
		{
			$get_detail = $this->section_model->get_detail_by_id($id);
			$this->twiggy->set('get_detail', $get_detail);	
		}

		$section_data = $this->section_model->get_data();
		$shift_data = $this->master_model->get_data_shift();
		$len_data = $this->master_model->get_data_len();
		$machine_data = $this->master_model->get_data_machine();
		$billet_data = $this->master_model->get_data_billet();
		$finishing_data = $this->master_model->get_data_finishing();

		$this->twiggy->set('section_data', $section_data);
		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('len_data', $len_data);
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('billet_data', $billet_data);
		$this->twiggy->set('finishing_data', $finishing_data);
		$this->twiggy->template('admin/transaction/edit')->display();
	}

	/**
	 * delete transaksi
	 */
	public function delete($id)
	{
		$del = $this->section_model->delete($id);
		if($del)
		{
			$response = array(
				'message' => 'Transaksi yg terpilih berhasil dihapus',
				'status'  => 'success',
			);
		}
		else
		{
			$response = array(
				'message' => 'Transaksi yg terpilih  gagal dihapus',
				'status'  => 'danger',
			);
		}

		$this->output->set_output(json_encode($response));
	}

	/**
	 * get new master by section id
	 */
	public function get_new_master()
	{
		$section_id = $this->input->post('section_id');
		$get_data = $this->master_model->get_master_by_section($section_id);
		$this->output->set_output(json_encode($get_data));
	}

	/**
	 * update inline
	 */
	public function update_inline()
	{

	}

}
?>