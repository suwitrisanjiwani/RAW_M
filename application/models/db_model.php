<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* model for database sql queries */
class Db_model extends CI_Model {

	function __construct(){
		parent::__construct(); //call the model constructor
	}

	function getShareInformation(){
		$sql = "SELECT * from shareinformation";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getPercentageJCI(){
		$sql = "SELECT percentage FROM percentage_JCI";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getPercentageBMRI(){
		$sql = "SELECT * FROM percentage_BMRI";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}

/* End of file db_model.php */
/* Location: ./application/models/db_model.php */