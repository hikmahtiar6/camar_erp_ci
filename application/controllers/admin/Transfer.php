<?php
/**
 * Class Transfer
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com
 * Camar Software, Juni 2017
 */
class Transfer extends CI_Controller {
	/**
	 * COnstructor COdeigniter
	 */
	public function __construct()
	{
		parent::__construct();

		// load model
		$this->load->model('master/lot_model');
	}

	/**
	 * Index Page
	 * 
	 * @return HTML
	 */
	public function index()
	{
		$lot_data = $this->lot_model->get_data_header();

		// load view with Twig
		$this->twiggy->set('lot_data', $lot_data);
		$this->twiggy->display('admin/transfer/index');
	}

	/**
	 * Get data
	 */
	public function get_data()
	{
		$response = array();

		$lot_id = $this->input->post('lot_id');

		$data = $this->lot_model->get_data_advance('', $lot_id)->result();
		if($data)
		{
			foreach ($data as $row) {

				$response[] = array(
					'hasil_id'      => $row->SpkLotHasilId,
					'jumlah_billet' => to_decimal($row->JumlahBtgRak),
					'rak'           => $row->Rak,
					'jumlah_aging'  => $row->JumlahBillet
				);
			}
		}

		output_json($response);
	}

	public function save_data()
	{
		$response = array();

		$hasil_id = $this->input->post('hasil_id');
		$jumlah_aging = $this->input->post('jumlah_aging');

		$data_save = array(
			'SpkLotHasilId' => $hasil_id,
			'JumlahBillet'  => $jumlah_aging
		);

		$data_update = array(
			'JumlahBillet'  => $jumlah_aging
		);

		$get_data = $this->db->query('select * from dbo.SpkLotAgingOven where SpkLotHasilId = '.$hasil_id.'')->row();
		if($get_data)
		{
			$this->db->where('SpkLotHasilId', $hasil_id);
			return $this->db->update('dbo.SpkLotAgingOven', $data_update);
		} else
		{
			return $this->db->insert('dbo.SpkLotAgingOven', $data_save);
		}

	}
}