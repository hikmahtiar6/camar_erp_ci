<?php
/**
 * CLass Po
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Po extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('master/pr_model');
        $this->load->model('master/po_model');
    }
    
    public function index()
    {
    	$data_po_headers = $this->po_model->get_data_header();

		$this->twiggy->set('data_headers', $data_po_headers);
        $this->twiggy->display('admin/po/index');
    }

    public function edit($id = 'new')
    {
    	$pr_header = $this->pr_model->get_headers('po')->result();
		$document_no = $this->generate_document_number(date('Y-m-d'));
		$document_pr = '';
		$tgl = date('d/m/Y');

		if($id != 'new')
		{
			$get_last_data_pr_date = $this->po_model->get_last_data_by_year_month($id);
			$document_no = $get_last_data_pr_date;
			$document_pr = $this->po_model->get_last_data_by_year_month($id, '', 'PurchaseRequestNo');
			$tgl = $this->po_model->get_last_data_by_year_month($id, '', 'TransactionDate');
			$tgl = date('d/m/Y', strtotime($tgl));
		}

		$this->twiggy->set('id', $id);
		$this->twiggy->set('tgl', $tgl);
		$this->twiggy->set('document_pr', $document_pr);
		$this->twiggy->set('document_no', $document_no);
    	$this->twiggy->set('pr_header', $pr_header);
		$this->twiggy->display('admin/po/edit');
    }

    public function save_header()
	{
		$id = 'new';
		$response = 'error';
		$id = $this->input->post('id');
		$tgl = $this->input->post('tgl');
		$document_no = $this->input->post('document_no');
		$document_pr = $this->input->post('document_pr');

		$data_save = array(
			'PurchaseRequestHeaderId' => $document_pr,
			'PurchaseOrderNo'         => $document_no,
			'TransactionDate'         => change_format_date($tgl),
		);

		return 0;

		if($id != 'new')
		{
			$save = $this->po_model->update_header($id, $data_save);
			if($save)
			{
				$response = 'success';
				$id = $id;
			}
		}
		else
		{
			$data_save['DocumentDate'] = date('Y-m-d H:i:s');
			$save = $this->po_model->save_header($data_save);
			if($save)
			{
				$response = 'success';
				$id = $this->db->insert_id();
			}
		}

		$json = array(
			'status' => $response,
			'id'     => $id
		);

		$this->output->set_output(json_encode($json));

	}

	public function delete_header()
	{
		$id = $this->input->post('id');
		$del = $this->po_model->delete_header($id);
		$response = array(
			'message' => 'Gagal dihapus',
			'status'  => 'error',
		);	

		if($del)
		{
			$response = array(
				'message' => 'Berhasil dihapus',
				'status'  => 'success',
			);
		}

		output_json($response);
	}

    private function generate_document_number($date = '')
	{
		$id = 'PO';
		$date_now = date('y/m');
		$date_from_data = date('y/m', strtotime($date));
		$numbering = add_zero(1, 4);

		// cek jika tahun bulan sama
		if($date_now == $date_from_data)
		{
			// cek data terakhir
			$get_last_data_pr = $this->po_model->get_last_data_by_year_month('', 'PO-'.$date_now);
			if($get_last_data_pr)
			{
				// cek jika number sama
				if($numbering == $this->get_number_document_no($get_last_data_pr))
				{
					$numb = $numbering + 1;
					return $id.'-'.$date_now.'-'.add_zero($numb, 4);
				}
				else
				{
					$numb = $this->get_number_document_no($get_last_data_pr) + 1;
					return $id.'-'.$date_now.'-'.add_zero($numb, 4);
				}
			}
			else
			{
				return $id.'-'.$date_now.'-'.$numbering;
			}
		}

		return $id.'-'.$date_now.'-'.$numbering;
	}

	private function get_number_document_no($document_no)
	{
		$expl = explode('-', $document_no);
		return $expl[2];
	}
}
?>