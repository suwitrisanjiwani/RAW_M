<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function shareInformationController(){
		$this->load->model('db_model');

		$data['title'] = 'Wealth Management Automated Report';
		$data['page_header'] = 'Share Information';
		$types = $this->db_model->getTypes();
		foreach($types as $t){

		}
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */