<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gudang extends CI_Controller {

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
	public function __construct(){
		parent::__construct();
		$this->load->model('bas_model');
		if ($this->session->userdata('is_logged_in') == FALSE) {
			redirect(base_url());
		}
		
	}
	public function terimapogreige() {
		$data['title'] = "Terima PO Greige";
		show('_apps/gudang/terimapogreige',$data,'gudang');
	}
	public function ajaxgudangterimapogreige() {
		$this->load->view('_datatables/ajax_gudangterimapogreige');
	}
	public function terimapocelupan() {
		$data['title'] = "Terima PO Celupan";
		show('_apps/gudang/terimapocelupan',$data,'gudang');
	}
	public function ajaxgudangterimapocelupan() {
		$this->load->view('_datatables/ajax_gudangterimapocelupan');
	}
	public function ajaxgudangterimapocelupan_sjmaklon() {
		$this->load->view('_datatables/ajax_gudangterimapocelupan_sjmaklon');
	}
	public function terimapomatang() {
		$data['title'] = "Terima PO Matang";
		show('_apps/gudang/terimapomatang',$data,'gudang');
	}
	public function ajaxgudangterimapomatang() {
		$this->load->view('_datatables/ajax_gudangterimapomatang');
	}
	public function terimabarang() { // surat jalan greige, celupan, n matang
		// get kodebarang from uri segment 3
		$kodebarang = $this->uri->segment(3);
		// get nopo from uri segment 4
		$nopo = $this->uri->segment(4);

	    if ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) { 
	    	$nama = 'PO Matang'; 
	    	$tbl = 'po';
	    	$uri = 'terimapomatang';
	    	$data['barang'] = $this->bas_model->detailbarang($kodebarang);
	    } elseif ((substr($nopo,0,2) == 'PC') || (substr($nopo,0,3) == 'PCL')) { 
	    	$nama = 'PO Celupan'; 
	    	$tbl = 'po';
	    	$uri = 'terimapocelupan';
	    	$data['barang'] = $this->bas_model->detailbarang($kodebarang);
	    } else { 
	    	$nama = 'PO Greige'; 
	    	$tbl = 'po'; 
	    	$uri = 'terimapogreige';
	    	$data['barang'] = $this->bas_model->detailbaranggreige($kodebarang);
	    }
		$data['jenisterima'] = $nama;
		$data['kodebarang'] = $kodebarang;
		$data['supplier'] = $this->bas_model->getsupplieridname($nopo,$tbl);
		
		show('_apps/gudang/frmterimabarang',$data,'gudang');
	} // fungsi save nya di function saveterimabarangbaru()

	// buat surat jalan celupan dari surat jalan maklon
	public function sjcelupanfrommaklon() {
		// get kode barang dan no PO dari URL
		$kodebarang = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);

		$nama = 'PO Celupan'; 
    	$tbl = 'po';
    	$uri = 'terimapocelupan';
    	$data['barang'] = $this->bas_model->detailbarang($kodebarang);

		$data['jenisterima'] = $nama;
		$data['kodebarang'] = $kodebarang;
		$data['supplier'] = $this->bas_model->getsupplieridname($nopo,$tbl);
		
		show('_apps/gudang/frmterimabarang',$data,'gudang');
	}
	
	public function cetakfaktur() {
		$nofaktur = $this->uri->segment(3);
		
		$data['detail'] = $this->bas_model->getdetailcetakfaktur($nofaktur);
	
		show('_apps/gudang/cetakfakturpenjualan',$data,'gudang');
	}

	public function editfaktur() {
		$nofaktur = $this->uri->segment(3);
		//$kodebarang = $this->uri->segment(4);
		$data['tipe'] = 'Edit Faktur Penjualan';
		$data['detail'] = $this->bas_model->getdetailcetakfaktur($nofaktur);
		//$data['barang'] = $this->bas_model->fakturdetailkodebarang($kodebarang,$nofaktur);
		show('_apps/gudang/editfakturpenjualan', $data, 'gudang');
	}
	
	public function updatefaktur(){
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$berat = $this->input->post('berat');
		$harga = $this->input->post('harga');
		$id = $this->input->post('id');
		$itemid = $this->input->post('itemid');
		$fakturid = $this->input->post('fakturid');
		foreach($check as $i){
			$fakturpenjualanitem = array(
				'qty' => $jumlah[$i],
				'satuan_kg' => $berat[$i]
			);
			$orderitemdetail = array(
				'harga_customer' => $harga[$i]
			);
			$this->db->where('id', $id[$i]);
			$this->db->update('OrderItemDetail', $orderitemdetail);

			$this->db->where('itemid', $itemid[$i]);
			$this->db->where('fakturid', $fakturid[$i]);
			$this->db->update('FakturPenjualanItem', $fakturpenjualanitem);
		}	
		redirect(base_url('laporan/laporanfakturpenjualan'));
	}

	public function cetaksuratjalan() {
		$nosj = $this->uri->segment(3);
		
		$data['detail'] = $this->bas_model->editsuratjalancustomeritem($nosj);
		
		show('_apps/gudang/cetaksuratjalancustomer',$data,'gudang');
	}

	public function editsuratjalan(){
		$nosj = $this->uri->segment(3);
		$data['title'] = "Edit Surat Jalan Customer";
		$data['barang'] = $this->bas_model->editsuratjalancustomeritem($nosj);
		show('_apps/gudang/editsuratjalancustomer',$data,'gudang');	
	}

	public function updatesuratjalan(){
		$fakturid = $this->input->post('fakturid');
		$id = $this->input->post('id');
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		$harga = $this->input->post('harga');
		$invoicedetailid = $this->input->post('invoicedetailid');
		
		foreach ($kilo as $index => $id) {
			if(in_array($index, $check)){
				$invoicedetail = array(
					'quantity' => $jumlah[$index],
					'weight'   => $kilo[$index],
				);

				$this->db->where('invoicedetailid', $invoicedetailid[$index]);
				$this->db->update('invoicedetail', $invoicedetail);
				
				$pocustomerdetail = array(
					'pricecustomer' => $harga[$index],
				);

				$this->db->where('itemid', $id[$index]);
				$this->db->update('pocustomerdetail', $pocustomerdetail);
			}
		}

		redirect(base_url('laporan/laporansuratjalancustomer'));
	}
	
	
	/********** 18/07/2013 ****************/
	
	public function returcustomer() {
		$data['title'] = "Retur Customer";
		
		show('_apps/gudang/returcustomer',$data,'gudang');
	}
	public function ajaxreturcustomer() {
		$this->load->view('_datatables/ajax_gudangreturcustomer');
	}
	public function buatreturcustomer() {
		$this->load->library('controller_utility');
		
		$noretur = $this->bas_model->getnoreturcustomer();
		$noorder = $this->uri->segment(5);
		$ivid = $this->uri->segment(6);
		$data['noorder'] = $noorder;
		$kodebarang = urldecode($this->uri->segment(4));
		$nofaktur = $this->uri->segment(3);
		$customer = $this->bas_model->getcustomerbynoorder($noorder);
		$data['namacustomer'] = $customer->namaCustomer;
		$data['noretur'] = "RC".$this->controller_utility->generatereturnumber($noretur);
		
		$details[$kodebarang] = $this->bas_model->showorderitem($kodebarang);
		//$detailbarang[$kodebarang] = $this->bas_model->fakturdetailkodebarang($kodebarang, $nofaktur);
		$detailbarang[$kodebarang] = $this->bas_model->fakturdetail_byivid($ivid);
		
		$data['detail'] = $details;
		$data['detailbarang'] = $detailbarang;
		show('_apps/gudang/frmreturcustomer',$data,'gudang_returclaim');
	}
	public function savereturcustomer() {
		$t = explode('-',$this->input->post('tanggal'));
		$nofaktur = $this->input->post('nofaktur');
		$kodebarang = $this->input->post('kodebarang');
		$nopo = '';
		$ivid = $this->input->post('fakturid');

		$count =  $this->input->post('check');
		$itemid = $this->input->post('itemid');
		$jumlah = $this->input->post('jumlah');
		$satuankg = $this->input->post('satuankg');

		/*foreach ($count as $i) {		   
	   	
		   	$fakturdetail = $this->bas_model->fakturdetailkodebarang($kodebarang, $nofaktur);

		   	foreach ($fakturdetail as $detail) {		   		
		   		if($detail['id'] == $itemid[$i]) {
		   			$nopo = $this->bas_model->nopobykodebarang($detail['kodebarang']);

		   			$existingreturbarangdetail = $this->bas_model->detailreturbarang_groupbyitemid($detail['id']);
		   			
		   			if(($detail['qty'] - $jumlah[$i]) < 0) {
		   				$jumlah[$i] = (($detail['qty'] - $existingreturbarangdetail[0]->qty) == 0)?$detail['qty']:($detail['qty'] - $existingreturbarangdetail[0]->qty);
		   			}
		   			if(($detail['jumlahkg'] - $satuankg[$i]) < 0) {
		   				$satuankg[$i] = (($detail['jumlahkg'] - $existingreturbarangdetail[0]->satuan_kg) == 0)?$detail['jumlahkg']:($detail['jumlahkg'] - $existingreturbarangdetail[0]->satuan_kg);
		   			}
			   		
		//INSERT RETUR CUSTOMER ITEM
					$this->db->query("INSERT INTO returcustomeritem VALUES ('".$this->input->post('noretur')."',".$itemid[$i].",".$jumlah[$i].",".$satuankg[$i].")");
				}
		   	}
		}*/

		//INSERT RETUR CUSTOMER
		/*$data = array('noretur'=>$this->input->post('noretur'),
					  'tanggal'=>$t[2].'-'.$t[1].'-'.$t[0],
					  'nopo'=>$nopo,
					  'nofaktur'=>$nofaktur,
					  'kodebarang'=>$kodebarang,
					  'status'=>0,
					  'keterangan'=>$this->input->post('keterangan'),
					  'createdby'=>$this->session->userdata('username'),
					  'createddate'=>date('Y-m-d h:i:s')
					  );
		$this->db->insert('returcustomer',$data);*/

		$data = array('crno'=>$this->input->post('noretur'),
					  'crdate'=>$t[2].'-'.$t[1].'-'.$t[0],
					  'invoiceid'=>$ivid,
					  'notes'=>$this->input->post('keterangan'),
					  'createdby'=>$this->session->userdata('username'),
					  'createddate'=>date('Y-m-d h:i:s')
					  );
		$this->db->insert('customerreturn',$data);
		$crid = $this->db->insert_id();

		foreach ($count as $i) {	
			$datacrd = array('crid'=>$crid,
						  'itemid'=>$itemid[$i],
						  'quantity'=>$jumlah[$i],
						  'weight'=>$satuankg[$i],
						  );
			$this->db->insert('customerreturndetail',$datacrd);
		}

		/*$data = array('statusorder'=>'Retur Customer');
		$this->db->where('kodebarang',$kodebarang);
		$this->db->update('OrderCustomer',$data);*/
		$datastatus = array('pocstatus'=>'Retur Customer');
		$this->db->where('ordercode',$kodebarang);
		$this->db->update('pocustomer',$datastatus);
		
		//DISPLAY RETUR REPORT
		/*$data['detail'] = $this->bas_model->detailreturpenjualan($this->input->post('noretur'),$this->input->post('nofaktur'));
		$data['barang'] = $this->bas_model->detailreturbarang($this->input->post('noretur'));*/
		$data['detail'] = $this->bas_model->detailreturpenjualan_bycrid($crid);
		$data['barang'] = $this->bas_model->detailreturbarang_bycrid($crid);
		$data['nofaktur'] = $this->input->post('nofaktur');
		
		show('_apps/gudang/cetakreturpenjualan',$data,'gudang');
		
	}
	public function cetakreturpenjualan() {
		$noretur = $this->uri->segment(3);
		$nofaktur = $this->uri->segment(4);
		//DISPLAY RETUR REPORT
		$data['detail'] = $this->bas_model->newdetailreturpenjualan($noretur,$nofaktur);
		$data['barang'] = $this->bas_model->newdetailreturbarang($noretur);
		$data['nofaktur'] = $nofaktur;
		
		show('_apps/gudang/cetakreturpenjualan',$data,'gudang');
	}
	
	public function editreturcustomer() {
		$noretur = $this->uri->segment(3);
		$nofaktur = $this->uri->segment(4);
		$data['detail'] = $this->bas_model->newdetailreturpenjualan($noretur,$nofaktur);
		$data['barang'] = $this->bas_model->newdetailreturbarang($noretur);
		$data['nofaktur'] = $nofaktur;
		$data['tipe'] = 'Edit Retur Penjualan';
		show('_apps/gudang/editreturcustomer',$data,'gudang');
	}

public function updatereturpenjualan(){
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		$harga = $this->input->post('harga');
		$id = $this->input->post('id');
		$crdid = $this->input->post('crdid');
		foreach ($check as $i) {
				$returpenjualan = array(
					'pricecustomer' => $harga[$i]
					);
				$returitem = array(
					'quantity' => $jumlah[$i],
					'weight' => $kilo[$i]
					);
				$this->db->where('itemid', $id[$i]);
				$this->db->update('pocustomerdetail', $returpenjualan);

				$this->db->where('crdid', $crdid[$i]);
				$this->db->update('customerreturndetail', $returitem);
		}
		redirect(base_url('laporan/laporanreturcustomer'));
}

	/*----------- 23/07/2013 --------------------*/
	
	public function saveterimabarangbaru() {
		$nopo = $this->input->post('nopo');
		
		if((substr($nopo,0,2) == 'PG') || (substr($nopo,0,3) == 'POG')) {
			$link = 'terimapogreige';
			$db = 'purchaseorder';
			$type = 'PO Greige';
		} elseif ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) {
		 
		    $link = 'terimapomatang';
		    $db = 'purchaseorder';
		    $type = 'PO Matang';
		} else {
			$link = 'terimapocelupan';
			$db = 'purchaseorder';
			$type = 'PO Celupan';
		}
		$tgl = explode('-', $this->input->post('tanggal'));
		$tanggal = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
		$today = date('Y-m-d');
		
		$count = count($this->input->post('itemid'));
		$kode = $this->input->post('kodewarna');
		$typebarang = $this->input->post('typebarang');
		$jumlah = $this->input->post('jumlah');
		$itemid = $this->input->post('itemid');
		$satuankg = $this->input->post('satuankg');
		$satuankgori = $this->input->post('satuankgori');

		
		// insert sj header
		$poid = $this->bas_model->getpoid($nopo);
		$insertsj = array(
			'sjno'=>$this->input->post('nosuratjalan'),
			'poid'=>$poid,
			'sjtype'=>$type,
			'sjdate'=>$tanggal,
			'createddate'=>$today,
		  	'createdby'=>$this->session->userdata('username')
		);
		$this->db->insert('suratjalan', $insertsj);

		// get last inserted id (sj header id)
		$sjid = $this->db->insert_id();
		
		for ($i=0;$i<$count;$i++) {
			echo $jumlah[$i];
			if($jumlah[$i] == 0) continue;

			if((substr($nopo,0,2) == 'PG') || (substr($nopo,0,3) == 'POG')) {
				$detailbarang = $this->bas_model->detailbaranggreige($this->input->post('kodebarang'));
				foreach ($detailbarang as $detail) {
					if($typebarang[$i] == $detail['typebarang'] && $jumlah[$i] > $detail['jumlah'])
						$jumlah[$i] = $detail['jumlah'];
				}
			} else {
				$detailbarang = $this->bas_model->detailbarangbyitemid($itemid[$i]);
				
				if($jumlah[$i] > $detailbarang[0]['quantity'])
					$jumlah[$i] = $detailbarang[0]['quantity'];
			}

			$insertsjd = array(
  				'sjid'=>$sjid,
  				'itemid'=>$itemid[$i],
  				'quantity'=>$jumlah[$i],
  				'weight'=>$satuankg[$i]
			);
			 
			$this->db->insert('suratjalandetail', $insertsjd);

			//$penyusutanberat = $satuankgori[$i] - $satuankg[$i];		
			//$this->db->query("UPDATE OrderItemDetail SET tanggalterima = '$tanggal', penyusutanberat = '$penyusutanberat' WHERE id = $gudangid[$i]" );
		}
		//$data_status = array('statuspo'=>'Terima Gudang'); 
		//$this->db->where('nopo',$nopo);
		//$this->db->update($db,$data_status);
		redirect(base_url('gudang/'.$link));
	}

	/* 31/10/2013 */
	public function editsuratjalanpabrik() {
		$data['nosuratjalan'] = urldecode(preg_replace('/~/', '/', $this->uri->segment(3)));
		$data['jenisterima'] = "PO ".$this->uri->segment(4);
		$data['title'] = 'Edit Terima Barang Pabrik ' . $data['jenisterima'];

		$kodebarang = $this->uri->segment(5);
		$nopo = $this->uri->segment(6);

	    $data['barang'] = $this->bas_model->detailsuratjalan_byid_sj($this->uri->segment(7));

		$data['kodebarang'] = $kodebarang;
		$data['nopo'] = $nopo;
		$data['supplier'] = $this->bas_model->getsupplieridname($nopo,'po');

		show('_apps/gudang/editsuratjalanpabrik', $data, 'gudang');
	}

	public function updatesuratjalanpabrik() {
		$nosuratjalan = $this->input->post('nosuratjalan');
		$tanggalArr = explode('-', $this->input->post('tanggal'));
		$id_sj = $this->input->post('id_sj');
		$jumlah = $this->input->post('jumlah');
		$satuankg = $this->input->post('satuankg');
		$check = $this->input->post('check');

		$i = 0;
		foreach ($id_sj as $index => $id) {
			if(in_array($index, $check)){
				    //find sjid
					$sql = "SELECT sjd.sjid from suratjalandetail sjd where sjd.sjdid = $id";

				    $query = $this->db->query($sql);

				    $param = $query->result_array();
				    $sjid = $param[0]['sjid'];
				    
				    $sj = array(
				    	'sjno' => $nosuratjalan,
				    	'sjdate' => $tanggalArr[2].'-'.$tanggalArr[1].'-'.$tanggalArr[0],
		                'createddate' => date('Y-m-d H:i:s', time()),
						'createdby' => $this->session->userdata('username')
				    );

				    $this->db->where('sjid', $sjid);
				    $this->db->update('suratjalan', $sj);

				    $sjd = array(
	                    'quantity' => $jumlah[$index],
	                    'weight' => $satuankg[$index]
				    );

				    $this->db->where('sjdid', $id);
				    $this->db->update('suratjalandetail', $sjd);	
			}
		}

		if($this->input->post('jenisterima') == "PO Greige")
			redirect(base_url('laporan/laporanterimabarangpabrikgreige'));
		else if($this->input->post('jenisterima') == "PO Celupan")
			redirect(base_url('laporan/laporanterimabarangpabrikcelup'));
		else if($this->input->post('jenisterima') == "PO Matang")
			redirect(base_url('laporan/laporanterimabarangpabrikmatang'));
		
	}

	public function updateclaimpembelian() {
		$noclaim = $this->input->post('no_claim');
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		$id = $this->input->post('itemid');
		foreach ($check as $i) {
				$claimpembelian = array(
					'quantity' => $jumlah[$i],
					'weight' =>$kilo[$i]
					);
				$this->db->where('cdid', $id[$i]);
				$this->db->update('claimdetail', $claimpembelian);
		}
		redirect(base_url('laporan/laporanclaimpembelian'));	
	}
	
	/* 16/08/2013 */
	
	public function terimabarangpo() {
		$data['title'] = 'Terima Barang Gudang';
		
		show('_apps/gudang/terimabarangpo',$data,'gudang');
	}
	public function ajaxgudangterimabarangpo() {
		$this->load->view('_datatables/ajax_gudangterimabarangpo');
	}
	public function terimabaranggudang() {
		$kodebarang = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);
		$data['poc'] = $this->uri->segment(5);
		$sjid = $this->uri->segment(6);
		
		if ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) { 
	    	$nama = 'PO Matang'; 
	    	$tbl = 'po';
	    	$uri = 'terimapomatang';
	    	//$data['barang'] = $this->bas_model->detailterimabaranggudang($kodebarang, $nopo);
	    	$data['barang'] = $this->bas_model->detailterimabaranggudangbysjid($sjid);
	    } elseif ((substr($nopo,0,2) == 'PC') || (substr($nopo,0,3) == 'PCL')) { 
	    	$nama = 'PO Celupan'; 
	    	$tbl = 'po';
	    	$uri = 'terimapocelupan';
	    	//$data['barang'] = $this->bas_model->detailterimabaranggudang($kodebarang, $nopo);
	    	$data['barang'] = $this->bas_model->detailterimabaranggudangbysjid($sjid);
	    } else { 
	    	$nama = 'PO Greige'; 
	    	$tbl = 'po'; 
	    	$uri = 'terimapogreige';
	    	//$data['barang'] = $this->bas_model->detailterimabaranggudang($kodebarang, $nopo);
	    	$data['barang'] = $this->bas_model->detailterimabaranggudangbysjid($sjid);
	    }
		$data['jenisterima'] = $nama;
		$data['kodebarang'] = $kodebarang;
		$data['jenisbahan'] = $this->bas_model->jenisbahan($kodebarang);
		$data['supplier'] = $this->bas_model->getsupplieridname($nopo,$tbl);
		$data['gudangid'] = $this->bas_model->getgudangid()+1;
		/*
		foreach ($data['barang'] as $key => $value) {
			echo $value['jumlah'];
		} */
		show('_apps/gudang/frmterimabaranggudang',$data,'gudang');
	}
	public function saveterimabaranggudang() {
		$idsupplier = $this->input->post('idsupplier');
		$nopo = $this->input->post('nopo');
		$gudangid = $this->input->post('gudangid');
		$kodebarang = $this->input->post('kode_barang');
		$sjid = $this->input->post('sjid');
		$t = explode('-',$this->input->post('tanggal'));
		$today = date('Y-m-d');
		/*if ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) { 
			$type = 'Matang'; 
		} elseif ((substr($nopo,0,2) == 'PC') || (substr($nopo,0,3) == 'PCL')) { 
			$type='Celupan'; 
		} else { 
			$type='Greige'; 
		}*/
		$datagdg = array(
					'whid'=>$gudangid,
					'sjid'=>$sjid,
					'whdate'=>$t[2].'-'.$t[1].'-'.$t[0],
					'whtype'=>'in',
					'createdby'=>$this->session->userdata('username'),
					'createddate'=>$today
					);
		$this->db->insert('warehouse',$datagdg);
		
		$count = count($this->input->post('itemid'));
		
		$jumlah = $this->input->post('jumlah');
		$itemid = $this->input->post('itemid');
		$satuankg = $this->input->post('satuankg');
		
		for ($i=0;$i<$count;$i++) {
			if($jumlah[$i]) {
				$dataterima = array(
	  				'whid'=>$gudangid,
	  				'itemid'=>$itemid[$i],
	  				'quantity'=>$jumlah[$i],
	  				'weight'=>$satuankg[$i]
	  				);
							 
				$this->db->insert('warehousedetail',$dataterima);

				$tanggal = $t[2].'-'.$t[1].'-'.$t[0];
				//$kalkulasi = $this->bas_model->penyusutanberat($itemid[$i], $kodebarang);
				$kalkulasi = $this->bas_model->weightshrinking($itemid[$i], $kodebarang);
				if($kalkulasi[0]->jumlahblmditerima == 0) {
					$penyusutanberat = $kalkulasi[0]->susut_satuan_kg;
					$this->db->query("UPDATE pocustomerdetail SET weightshrinkage = $penyusutanberat WHERE itemid = $itemid[$i]" );
				}
			}
		}
		
		$data_status = array('postatus'=>'Terima Gudang'); 
		$this->db->where('pono',$nopo);
		$this->db->update('purchaseorder',$data_status);

		// status po untuk po greige
		$nopos = $this->bas_model->getpabrikpobykodebarang($kodebarang);
		foreach ($nopos as $value) {
			if(substr($value['nopo'], 0, 3) == 'POG') {
				$data_status = array('postatus'=>'Terima Gudang');
				$this->db->where('pono', $value['nopo']);
				$this->db->where('cancellation IS NULL');
				$this->db->update('purchaseorder',$data_status);
			}
		}
		
		redirect(base_url('gudang/terimabarangpo'));
	}
	
	public function suratjalanmaklon() {
		$data['title'] = 'Surat Jalan Maklon';
		
		show('_apps/gudang/suratjalanmaklon',$data,'gudang');
	}
	public function ajaxsuratjalanmaklon() {
		$this->load->view('_datatables/ajax_gudangsuratjalanmaklon');
	}
	
	public function statusperbaikan() {
		$data['title'] = 'Status Perbaikan';
		
		show('_apps/gudang/statusperbaikan',$data,'gudang');
	}
	public function ajaxstatusperbaikan() {
		$this->load->view('_datatables/ajax_gudangstatusperbaikan');
	}
	public function editstatusperbaikan() {
		$data = array('status'=>'1');
		$this->db->where('noretur',$this->input->post('noretur'));
		$this->db->update('returpabrik',$data);
	}
	/* 22/11/2013 */
	public function buatsuratjalanperbaikan() {
		$data['noretur'] = $this->uri->segment(3);
		$kodebarang = $this->uri->segment(4);
		$nopo = $this->uri->segment(5);
		$frid = $this->uri->segment(6);
		$data['nopo'] = $nopo;
	    if ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) { 
	    	$nama = 'PO Matang'; 
	    	$tbl = 'po';
	    	$uri = 'terimapomatang';
	    } elseif ((substr($nopo,0,2) == 'PC') || (substr($nopo,0,3) == 'PCL')) { 
	    	$nama = 'PO Celupan'; 
	    	$tbl = 'po';
	    	$uri = 'terimapocelupan';
	    } else { 
	    	$nama = 'PO Greige'; 
	    	$tbl = 'po'; 
	    	$uri = 'terimapogreige';
	    }
	    $data['detail'] = $this->bas_model->getdetailsuratjalanperbaikan_byfrid($frid);
		$data['barang'] = $this->bas_model->getdetailbarangsuratjalanperbaikan_byfrid($frid);
		$data['frid'] = $frid;
		$data['jenisterima'] = $nama;
		$data['kodebarang'] = $kodebarang;
		$data['supplier'] = $this->bas_model->getsupplieridname($nopo,$tbl);
		
		show('_apps/gudang/frmsuratjalanperbaikan',$data,'gudang');
	}

	public function savesuratjalanperbaikan() {
		$nopo = $this->input->post('nopo');
		$frid = $this->input->post('frid');
		
		if((substr($nopo,0,2) == 'PG') || (substr($nopo,0,3) == 'POG')) {
			$db = 'po';
			$type = 'PO Greige';
		} elseif ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) {
		    $db = 'po';
		    $type = 'PO Matang';
		} else {
			$db = 'po';
			$type = 'PO Celupan';
		}
		$tgl = explode('-', $this->input->post('tanggal'));
		$tanggal = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
		$today = date('Y-m-d');
		
		$count = count($this->input->post('itemid'));
		$kode = $this->input->post('kodewarna');
		
		$jumlah = $this->input->post('jumlah');
		$itemid = $this->input->post('itemid');
		$satuankg = $this->input->post('satuankg');
		//$satuankgori = $this->input->post('satuankgori');
		
		//cek fromtable value for certain noretur in returpabrikitem table
		//if retur was originated from stock gudang,
		//it will be saved into gudang and stock gudang table
		//else if retur was originated from orderitemterima.
		//it will be saved into orderitemterima table
		//$detailretur = $this->bas_model->getdetailbarangsuratjalanperbaikan($this->input->post('noretur'));
		$detailretur = $this->bas_model->getdetailbarangsuratjalanperbaikan_byfrid($frid);
		//echo $detailretur[0]['fromtable'];
		// **save barang retur ke table gudang

		/* COMMENT 29JAN2014 -dio
		if($detailretur[0]['fromtable'] == "stockgudang" || $detailretur[0]['fromtable'] == "returcustomeritem" || $detailretur[0]['fromtable'] == "orderitemterima") {
			$supplier = $this->bas_model->getsupplieridname($nopo, "po");
			if((substr($nopo,0,2) == 'PG') || (substr($nopo,0,3) == 'POG')) {
				$typestock = 'Greige';
			} elseif ((substr($nopo,0,2) == 'PM') || (substr($nopo,0,3) == 'POM')) {
			    $typestock = 'Matang';
			} else {
				$typestock = 'Celupan';
			}

			$datagdg = array(
				'kodebarang'=>$this->input->post('kode_barang'),
				'tanggal'=>$tanggal,
				'idSupplier'=>$supplier[0]['idsupplier'],
				'nopo'=>$nopo,
				'createddate'=>$today,
				'createdby'=>$this->session->userdata('username')
				);
			$this->db->insert('gudang',$datagdg);
			$lastid = $this->db->insert_id();
		} */
		
		$datasjr =  array(
			'sjid'=>$this->bas_model->getsjidbyfrid($frid),
			'sjrno'=>$this->input->post('nosuratjalan'),
			'frid'=>$frid,
			'sjrdate'=>$tanggal,
			'createddate'=>$today,
			'createdby'=>$this->session->userdata('username')
			);
		$this->db->insert('suratjalanretur',$datasjr);
		$sjrid = $this->db->insert_id();
		
		$datawh =  array(
			'sjid'=>$this->bas_model->getsjidbyfrid($frid),
			'whdate'=>$tanggal,
			'whtype'=>'in',
			'createddate'=>$today,
			'createdby'=>$this->session->userdata('username')
			);
		$this->db->insert('warehouse',$datawh);
		$whid = $this->db->insert_id();

		for($i=0;$i<$count;$i++){
			$datasjrd =  array(
				'sjrid'=>$sjrid,
				'itemid'=>$itemid[$i],
				'quantity'=>$jumlah[$i],
				'weight'=>$satuankg[$i]
				);
			$this->db->insert('suratjalanreturdetail',$datasjrd);

			$datawhd =  array(
				'whid'=>$whid,
				'itemid'=>$itemid[$i],
				'quantity'=>$jumlah[$i],
				'weight'=>$satuankg[$i]
				);
			$this->db->insert('warehousedetail',$datawhd);
		}

		if($this->bas_model->checksuratjalanreturn($frid) == $this->bas_model->checkfactoryreturn($frid)){
			$datafr =  array(
				'frstatus'=>'1'
				);
			$this->db->where('frid',$frid);
			$this->db->update('factoryreturn',$datafr);
		}

		/* $dataupdate = array('statusorder' => $type );
		$this->db->where('kodebarang',$this->input->post('kode_barang'));
		$this->db->update('OrderCustomer',$dataupdate);

		for ($i=0;$i<$count;$i++) {
			// jika jumlah = 0, tidak perlu disimpan
			if($jumlah[$i] == 0) continue;

			// get detail barang dari table orderitemdetail by id
			$detailbarang = $this->bas_model->detailbarangbyitemid($itemid[$i]);
			
			//	jika jumlah > jumlah di orderitemdetail <detail di po>
			//	maka dilakukan preventif untuk membatasi jumlah max = jumlah di orderitemdetail
			if($jumlah[$i] > $detailbarang[0]['jumlah'])
				$jumlah[$i] = $detailbarang[0]['jumlah'];

			if($detailretur[0]['fromtable'] == "orderitemterima"){
				//	if value kolom fromtable = "orderitemterima",
				//	barang retur cukup disimpan di table orderitemterima_retur

				//save the data to table orderitemterima_retur
				$dataterima = array(
				  				'nosuratjalan'=>$this->input->post('nosuratjalan'),
				  				'noretur'=>$this->input->post('noretur'),
				  				'fromtable' => $detailretur[0]['fromtable'],
				  				'typeterima'=>$type,
				  				'tanggal'=>$tanggal,
				  				'itemid'=>$itemid[$i],
				  				'kodebarang'=>$this->input->post('kode_barang'),
				  				'qty'=>$jumlah[$i],
				  				'satuan_kg'=>$satuankg[$i],
				  				'createddate'=>$today,
				  				'createdby'=>$this->session->userdata('username')
				  				);
				 
				$this->db->insert('OrderItemTerima_retur',$dataterima);

				$dataterima = array(
	  				'gudangid'=>$lastid,
	  				'itemid'=>$itemid[$i],
	  				'kodebarang'=>$this->input->post('kode_barang'),
	  				'tanggal'=>$tanggal,
	  				'jenisbarang'=>$typestock,
	  				'jumlah'=>$jumlah[$i],
	  				'satuan_kg'=>$satuankg[$i],
	  				'createddate'=>$today,
	  				'createdby'=>$this->session->userdata('username')
	  				);

				$this->db->insert('stockgudang',$dataterima);
			} else if($detailretur[0]['fromtable'] == "stockgudang" || $detailretur[0]['fromtable'] == "returcustomeritem") {
				//	if value kolom fromtable = "stockgudang",
				//	barang retur selain disimpan di table orderitemterima_retur
				//	juga perlu disimpan di table stockgudang dan gudang (code ada di atas, lihat **)
				//	barang retur cukup disimpan di table orderitemterima_retur

				//save the data to orderitemterima_retur
				$dataterima = array(
				  				'nosuratjalan'=>$this->input->post('nosuratjalan'),
				  				'noretur'=>$this->input->post('noretur'),
				  				'fromtable' => $detailretur[0]['fromtable'],
				  				'typeterima'=>$type,
				  				'tanggal'=>$tanggal,
				  				'itemid'=>$itemid[$i],
				  				'kodebarang'=>$this->input->post('kode_barang'),
				  				'qty'=>$jumlah[$i],
				  				'satuan_kg'=>$satuankg[$i],
				  				'createddate'=>$today,
				  				'createdby'=>$this->session->userdata('username')
				  				);
				 
				$this->db->insert('OrderItemTerima_retur',$dataterima);

				// insert jumlah n satuan kg barang yg selesai diretur ke stock gudang
				// dengan gudangid yg baru
				$dataterima = array(
	  				'gudangid'=>$lastid,
	  				'itemid'=>$itemid[$i],
	  				'kodebarang'=>$this->input->post('kode_barang'),
	  				'tanggal'=>$tanggal,
	  				'jenisbarang'=>$typestock,
	  				'jumlah'=>$jumlah[$i],
	  				'satuan_kg'=>$satuankg[$i],
	  				'createddate'=>$today,
	  				'createdby'=>$this->session->userdata('username')
	  				);

				$this->db->insert('stockgudang',$dataterima);
			}
			//$penyusutanberat = $satuankgori[$i] - $satuankg[$i];		
			//$this->db->query("UPDATE OrderItemDetail SET tanggalterima = '$tanggal', penyusutanberat = '$penyusutanberat' WHERE id = $gudangid[$i]" );
		}
		END COMMENT */

		//$data_status = array('statuspo'=>'Terima Gudang'); 
		//$this->db->where('nopo',$nopo);
		//$this->db->update($db,$data_status);

		//	update status di table returpabrik
		//	from 0 <baru> to 1 <selesai>
		
		/*$data = array('status'=>'1');
		$this->db->where('noretur',$this->input->post('noretur'));
		$this->db->update('returpabrik',$data);*/

		redirect(base_url('gudang/statusperbaikan'));
	}
	
	/* 03/09/2013 */

	public function getlastkodefaktursuratjalancustomer() {
		$kodeawal = $this->input->get('kodeawal');
		$this->load->library('controller_utility');
		echo json_encode($kodeawal . $this->controller_utility->generatefakturnumber($this->bas_model->getnofakturpenjualan($kodeawal)));
	}
	
	public function buatfaktursuratjalancustomer() {
		$this->load->library('controller_utility');

		$nosuratjalan = $this->bas_model->getnosuratjalancust();
		$data['nosuratjalan'] = "SJC".$this->controller_utility->generateordernumber($nosuratjalan);
		
		$nofakturpenjualan = $this->bas_model->getnofakturpenjualan('F');
		$data['nofakturpenjualan'] = "F".$this->controller_utility->generatefakturnumber($nofakturpenjualan);
		
		$sales = $this->bas_model->getsales();
		foreach ($sales as $row) {
		        //$row_sales[] = ($row['nama']); //build an array
		        $sales_row['id']=htmlentities(stripslashes($row['namaKaryawan']));
		        $sales_row['name']=htmlentities(stripslashes($row['namaKaryawan']));
		        $row_sales[] = $sales_row;
		}
		$data['sales'] = json_encode($row_sales);

		$customer = $this->bas_model->getcustomer();
		foreach ($customer as $row){
		        //$row_sales[] = ($row['nama']); //build an array
		        $customer_row['id']=htmlentities(stripslashes($row['idCustomer']));
		        $customer_row['name']=htmlentities(stripslashes($row['namaCustomer']));
		        $row_customer[] = $customer_row;
		      }
		$data['customer'] = json_encode($row_customer);
		
		show('_apps/gudang/frmfaktursuratjalancustomer',$data,'gudang_faktur');
		
	}
	public function ajaxfakturselectpoc() {
		$this->load->view('_datatables/ajax_fakturselectpoc');
	}
	public function ajaxfakturselectretur() {
		$this->load->view('_datatables/ajax_fakturselectretur');
	}
	public function loadpoc() 
	{
		$nosuratjalan = $this->input->post('nosuratjalan');
		$data['nosuratjalan'] = $nosuratjalan;

		$kodebarang = $this->bas_model->getkodeitem($nosuratjalan);
		$data['kodebarang'] = $kodebarang;

		$noorder = $this->bas_model->getnoorder($kodebarang);
		$data['noorder'] = $noorder;

		
		foreach (explode(",", $this->bas_model->getnamasales($noorder)) as $key => $value)
		{
			$sales_row['id']	= htmlentities(stripslashes($value));
	        $sales_row['name']	= htmlentities(stripslashes($value));
	        $row_sales[] = $sales_row;
		}

		$data['sales'] = json_encode($row_sales);

		//=== hanjoyo version ================================================================
		//$noorder = $this->uri->segment(3);
		
		foreach (explode(",", $this->bas_model->getnamasales($noorder)) as $key => $value) {
			$sales_row['id']=htmlentities(stripslashes($value));
	        $sales_row['name']=htmlentities(stripslashes($value));
	        $row_sales[] = $sales_row;
		}		
		$data['sales'] = json_encode($row_sales);

		$customerval = $this->bas_model->getcustomerbynoorder($noorder);
		foreach ($customerval as $key => $value) {
			if($key == 'idCustomer')
				$customerval_row['id']=htmlentities(stripslashes($value));
			elseif($key == 'namaCustomer')
	        	$customerval_row['name']=htmlentities(stripslashes($value));	        
		}
		$row_customerval[] = $customerval_row;	
		$data['customerval'] = json_encode($row_customerval);
		//===================================================================================
		
		//$data['details'] = $this->bas_model->fakturdetailpoc($noorder);
		$data['details'] = $this->bas_model->fakturheaderpocsuratjalan($noorder);
		$data['suratjalan'] = $this->bas_model->fakturdetailpocsuratjalan($nosuratjalan);
		
		echo json_encode($data);
	}
	
	public function loadretur() 
	{
		$nosuratjalan = $this->input->post('nosuratjalan');
		$data['nosuratjalan'] = $nosuratjalan;


		
		$kodebarang = $this->bas_model->getkodeitemretur($nosuratjalan);
		$data['kodebarang'] = $kodebarang;

		$noorder = $this->bas_model->getnoorder($kodebarang);
		$data['noorder'] = $noorder;

		
		foreach (explode(",", $this->bas_model->getnamasales($noorder)) as $key => $value)
		{
			$sales_row['id']	= htmlentities(stripslashes($value));
	        $sales_row['name']	= htmlentities(stripslashes($value));
	        $row_sales[] = $sales_row;
		}

		$data['sales'] = json_encode($row_sales);

		//=== hanjoyo version ================================================================
		//$noorder = $this->uri->segment(3);
		
		foreach (explode(",", $this->bas_model->getnamasales($noorder)) as $key => $value) {
			$sales_row['id']=htmlentities(stripslashes($value));
	        $sales_row['name']=htmlentities(stripslashes($value));
	        $row_sales[] = $sales_row;
		}		
		$data['sales'] = json_encode($row_sales);

		$customerval = $this->bas_model->getcustomerbynoorder($noorder);
		foreach ($customerval as $key => $value) {
			if($key == 'idCustomer')
				$customerval_row['id']=htmlentities(stripslashes($value));
			elseif($key == 'namaCustomer')
	        	$customerval_row['name']=htmlentities(stripslashes($value));	        
		}
		$row_customerval[] = $customerval_row;	
		$data['customerval'] = json_encode($row_customerval);
		//===================================================================================
		
		//$data['details'] = $this->bas_model->fakturdetailpoc($noorder);
		$data['details'] = $this->bas_model->fakturheaderpocsuratjalan($noorder);
		$data['suratjalan'] = $this->bas_model->fakturdetailretursuratjalan($nosuratjalan);
		
		echo json_encode($data);	
	}


	public function ajaxfakturloadbaranggudang() {
		$this->load->view('_datatables/ajax_fakturloadbaranggudang');
	}
	public function loadbaranggudang() {
		$this->load->library('controller_utility');

		$nopo = $this->bas_model->getnopo();
		$data['noorder'] = "POC".$this->controller_utility->generateordernumber($nopo);
		
		//$noorder = $this->uri->segment(3);
		$sales = $this->bas_model->getsales();
		foreach ($sales as $row){
		        //$row_sales[] = ($row['nama']); //build an array
		        $sales_row['id']=htmlentities(stripslashes($row['namaKaryawan']));
		        $sales_row['name']=htmlentities(stripslashes($row['namaKaryawan']));
		        $row_sales[] = $sales_row;
		      }
		$data['sales'] = json_encode($row_sales);
		//$data['details'] = $this->bas_model->fakturdetailpoc($noorder);
		//echo '{"noorder":"'.$noorder.'","sales":"'.$sales.'"}';	
		echo json_encode($data);	
	}

	public function allowedhppperbarang() {
		$id = $this->input->post('id');
		$noorder = $this->input->post('noorder');
		echo json_encode($this->bas_model->allowedhppperbarang($id, $noorder));
	}

	public function allowedhppperpo() {
		$noorder = $this->input->post('noorder');
		echo json_encode($this->bas_model->allowedhppperpo($noorder));
	}

	public function savefaktursuratjalancustomer()
	{	
		echo "===============check kodebarang=================" . "<br>";
		$kodebarang = $this->input->post('kodebarang');
		$unique_kodebarang = array_unique($kodebarang);
		$sKodebarang = '';
		foreach ($unique_kodebarang as $kode) 
		{
			$sKodebarang .= $kode . ',';
		}
		$sKodebarang = rtrim($sKodebarang, ',');

		echo "sKodebarang : " . $sKodebarang  . "<br>";



		echo "=============check order customer==================" . "<br>";
		//ORDER CUSTOMER
		$noorderbataldata = $this->bas_model->getnoorderbataldata($unique_kodebarang);
		$sGramasimatang = '';
		$sGramasigreige = '';
		foreach ($noorderbataldata as $key => $noorderbataldatum) {
				// concat gramasi matang
				$sGramasimatang .= $noorderbataldatum['gramasimatang'] . ',';
						
				// concat gramasi greige			
				$sGramasigreige .= $noorderbataldatum['gramasigreige'] . ',';			
		}
		$sGramasimatang = rtrim($sGramasimatang, ',');
		$sGramasigreige = rtrim($sGramasigreige, ',');
		
		$aTanggal = explode('-', $this->input->post('tanggal'));
		$tanggal = $aTanggal[2] . '-' . $aTanggal[1] . '-' . $aTanggal[0];

		//cek if noorder exists?
		$found = $this->bas_model->noorderexists($this->input->post('noorderpoc'));
		
		echo "found 			: " . $found . "<br>";
		echo "idCustomer 		: " . $this->input->post('customer') . "<br>";
		echo "idSales 			: " . $this->input->post('namasales') . "<br>";
		echo "diskon 			: " . $this->input->post('diskon') . "<br>";
		echo "jenispembayaran 	: " . $this->input->post('jenispembayaran') . "<br>";
		echo "lamabayar 		: " . $this->input->post('termin') . "<br>";
		echo "perusahaan 		: " . $this->input->post('perusahaan') . "<br>";

		
		if($found > 0) { // is exists
			$existingorderdata = array(
				'idCustomer' => $this->input->post('customer'), //tambahkan field bila click Buat POC
				'idSales' => $this->input->post('namasales'),
				'diskon' => $this->input->post('diskon'),
				'jenispembayaran' => $this->input->post('jenispembayaran'),
				'lamabayar' => $this->input->post('termin'),
				'perusahaan' => $this->input->post('perusahaan'),
				'statusorder' => 'Faktur Penjualan', 
				'createddate' => date('Y-m-d')
				);
			$this->db->where('noorder', $this->input->post('noorderpoc'));
			$this->db->update('OrderCustomer', $existingorderdata);
			echo "<br>=================== update order customer ===========================<br>";
			print_r($existingorderdata);
			echo "<br>=====================================================================<br><br>";

			// ALL PO 	[related to POC] update status="Faktur Penjualan"
			$nopos = $this->bas_model->getpabrikpo($this->input->post('noorderpoc'));

			$statuspoupdate = array('statuspo'=>'Faktur Penjualan');
			foreach ($nopos as $nopo) {
				$this->db->where('nopo', $nopo['nopo']);
				$this->db->update('po', $statuspoupdate);
			}
		} else { // not exists		
			
			$neworderdata = array(
				'noorder' => $this->input->post('noorderpoc'), 
				'idCustomer' => $this->input->post('customer'), //tambahkan field bila click Buat POC
				'idSales' => $this->input->post('namasales'), 
				'gramasimatang' => $sGramasimatang, 
				'gramasigreige' => $sGramasigreige, 
				'tanggal' => $tanggal, 
				'kodebarang' => $sKodebarang, 
				'jenispembayaran' => $this->input->post('jenispembayaran'), 
				'diskon' => $this->input->post('diskon'), 
				'lamabayar' => $this->input->post('termin'), 
				'mengetahui' => 'YS', 
				'catatan' => $this->input->post('catatan'), 
				'statusorder' => 'Faktur Penjualan', 
				'perusahaan' => $this->input->post('perusahaan'), 
				'createddate' => date('Y-m-d'), 
				'createdby' => $this->session->userdata('username')
				);

				echo "=================== update order customer ===========================<br>";
				print_r($neworderdata);
				echo "=====================================================================<br><br>";
				$this->db->insert('OrderCustomer', $neworderdata);			
		}
		

		echo "=============check Faktur & stock gudang==================" . "<br>";
		
		//FAKTUR & STOCK GUDANG
		$lastid = $this->bas_model->getlastfakturid();
			//faktur general data
		$faktursuratjalan = array(
			'id' => ($lastid + 1),
			'nofaktur' => $this->input->post('nofaktur'), 
			'tanggal' => $tanggal,
			'noorder' => $this->input->post('noorderpoc'),
			'status' => $this->input->post('status'),
			'tanggalterima' => null,
			'nosuratjalan' => $this->input->post('nosuratjalancustomer'),
			'namasupir' => $this->input->post('namasupir'),
			'nokendaraan' => $this->input->post('nokendaraan'),
			'catatan' => $this->input->post('catatan'),
			'createddate' => date('Y-m-d h:i:s'),
			'createdby' => $this->session->userdata('createdby'),
			'statuspembayaran' => null
			);
		$this->db->insert('fakturpenjualan',$faktursuratjalan);
		

		echo "id 				: " . ($this->bas_model->getlastfakturid() + 1) . "<br>";
		echo "nofaktur 			: " . $this->input->post('nofaktur') . "<br>";
		echo "tanggal 			: " . $tanggal . "<br>";
		echo "noorder 			: " . $this->input->post('noorderpoc') . "<br>";
		echo "status 			: " . $this->input->post('status') . "<br>";
		echo "nosuratjalan 		: " . $this->input->post('nosuratjalancustomer') . "<br>";
		echo "namasupir 		: " . $this->input->post('namasupir') . "<br>";
		echo "nokendaraan 		: " . $this->input->post('nokendaraan') . "<br>";
		echo "catatan 			: " . $this->input->post('catatan') . "<br>";
		echo "createdby 		: " . $this->input->post('createdby') . "<br>";

		//faktur details data
		$check = $this->input->post('check');
		$itemids = $this->input->post('itemid');
		/// declaration of $kodebarang = $this->input->post('kodebarang');
		/// has been moved above
		$typebarang = $this->input->post('typebarang');
		$kodewarna = $this->input->post('kodewarna');
		$warna = $this->input->post('warna');
		$jumlah = $this->input->post('jumlah');
		$satuan = $this->input->post('satuan');
		$satuankg = $this->input->post('jumlahkg');
		$harga_customer = $this->input->post('harga_customer');

		echo "=============check Faktur & stock gudang Detail ==============" . "<br>";
		
		echo "check : ";
		print_r($this->input->post('check'));
		echo "<br>";

		echo "itemids : ";
		print_r($this->input->post('itemid'));
		echo "<br>";

		echo "typebarang : ";
		print_r($this->input->post('typebarang'));
		echo "<br>";

		echo "kodewarna : ";
		print_r($this->input->post('kodewarna'));
		echo "<br>";

		echo "warna : ";
		print_r($this->input->post('warna'));
		echo "<br>";

		echo "jumlah : ";
		print_r($this->input->post('jumlah'));
		echo "<br>";

		echo "satuan : ";
		print_r($this->input->post('satuan'));
		echo "<br>";

		echo "jumlahkg : ";
		print_r($this->input->post('jumlahkg'));
		echo "<br>";

		echo "harga_customer : ";
		print_r($this->input->post('harga_customer'));
		echo "<br>";


		foreach ($check as $value) 
		{
			$orderitemdetail = array(
				'harga_customer' => $harga_customer[$value]
				);

			$this->db->where('id', $itemids[$value]);
			$this->db->update('OrderItemDetail', $orderitemdetail);
			echo "===============================================================<br>";
			echo "harga_customer 			: " . $harga_customer[$value] . "<br>";

			//STOCK GUDANG berkurang
			$existingstockgudang = $this->bas_model->getdetailstockgudang_sjcustomer($itemids[$value]);
			$qtysekarang = $existingstockgudang[0]->satuan_kg - $satuankg[$value];
			$qtystockgudang = array(
				'satuan_kg' => $qtysekarang
				);
			$this->db->where('itemid', $itemids[$value]);
			$this->db->where('gudangid', $existingstockgudang[0]->gudangid);
			$this->db->update('stockgudang', $qtystockgudang);


			echo print_r($qtystockgudang);
			echo "<br>";
			echo "itemids		:" .  $itemids[$value] . "<br>";
			echo "gudangid 		:" .  $existingstockgudang[0]->gudangid . "<br>";
			echo "qty sebelum 	:" .  $existingstockgudang[0]->satuan_kg . "<br>";
			echo "qty input 	:" .  $satuankg[$value] . "<br>";
			echo "qty sekarang	:" .  $qtysekarang . "<br>";


			/*
			$existingstockgudang = $this->bas_model->getdetailstockgudang($itemids[$value]);

			if(($existingstockgudang[0]->jumlah - $jumlah[$value]) <= 0) 
			{
				$this->db->where('kodebarang', $kodebarang[$value]);
				$this->db->where('itemid', $itemids[$value]);
				$this->db->where('gudangid', $existingstockgudang[0]->gudangid);
				$this->db->delete('stockgudang');

				$this->db->where('kodebarang', $kodebarang[$value]);
				$this->db->delete('gudang');
			} else {
				//prevent negative satuan_kg
				if(($existingstockgudang[0]->satuan_kg - $satuankg[$value]) < 0)
					$stockgudang = array(
						'jumlah' => $existingstockgudang[0]->jumlah - $jumlah[$value],
						'satuan_kg' => $existingstockgudang[0]->satuan_kg
						);
				else
					$stockgudang = array(
						'jumlah' => $existingstockgudang[0]->jumlah - $jumlah[$value],
						'satuan_kg' => $existingstockgudang[0]->satuan_kg - $satuankg[$value]
						);
				$this->db->where('kodebarang', $kodebarang[$value]);
				$this->db->where('itemid', $itemids[$value]);
				$this->db->where('gudangid', $existingstockgudang[0]->gudangid);
				$this->db->update('stockgudang', $stockgudang);
			}
			*/
			

			//$existingstockgudang = $this->bas_model->getdetailstockgudang($itemids[$value]);
			
			//FAKTUR
			//prevent negative qty n satuan_kg
			/*
			if(($existingstockgudang[0]->jumlah - $jumlah[$value]) < 0)
				$jumlah[$value] = $existingstockgudang[0]->jumlah;
			if(($existingstockgudang[0]->satuan_kg - $satuankg[$value]) < 0)
				$satuankg[$value] = $existingstockgudang[0]->satuan_kg;
				*/
			$faktursuratjalandetail = array(
				'fakturid' => ($lastid + 1), 
				'itemid' => $itemids[$value], 
				'kodebarang' => $sKodebarang, 
				'qty' => $jumlah[$value], 
				'satuan_kg' => $satuankg[$value]
				);
			
			$this->db->insert('fakturpenjualanitem',$faktursuratjalandetail);
			

			echo "fakturid 			: " . ($lastid + 1) . "<br>";
			echo "itemid 			: " . $itemids[$value] . "<br>";
			echo "kodebarang 		: " . $sKodebarang . "<br>";
			echo "qty 				: " . $jumlah[$value] . "<br>";
			echo "satuan_kg 		: " . $satuankg[$value] . "<br>";
			
		}
		
		redirect(base_url('gudang/buatfaktursuratjalancustomer'));
	}

	public function returpabrikbyfugreige() {
		$data['title'] = "Daftar Retur Pabrik by Fu Greige";
		show('_apps/gudang/returpabrikbyfugreige', $data, 'gudang_returclaim');
	}		
	public function ajaxreturpabrikbyfugreige() {
		$this->load->view('_datatables/ajax_gudangreturpabrikbyfugreige');
	}
	public function ajaxdaftarprosesreturbyfugreige() {
		$this->load->view('_datatables/ajax_gudangdaftarprosesreturbyfugreige');
	}
	public function returpabrikbyfucelupan() {
		$data['title'] = "Daftar Retur Pabrik by Fu Celupan";
		show('_apps/gudang/returpabrikbyfucelupan', $data, 'gudang_returclaim');
	}
	public function ajaxreturpabrikbyfucelupan() {
		$this->load->view('_datatables/ajax_gudangreturpabrikbyfucelupan');
	}

	public function returpabrikbyfugudang() {
		$data['title'] = "Daftar Retur Pabrik by Fu Gudang";
		show('_apps/gudang/returpabrikbyfugudang', $data, 'gudang_returclaim');
	}
	public function ajaxreturpabrikfromstockgudangbyfugudang() {
		$this->load->view('_datatables/ajax_gudangreturpabrikfromstockgudangbyfugudang');
	}
	public function ajaxreturpabrikfromreturcustomerbyfugudang() {
		$this->load->view('_datatables/ajax_gudangreturpabrikfromreturcustomerbyfugudang');
	}

	public function buatreturpabrikbyfugreige() {
		$data['title'] = "Input Retur Pabrik by Fu Greige";
		$nosuratjalan = urldecode(preg_replace('/~/', '/', $this->uri->segment(3)));
		$nopo = $this->uri->segment(4);
		if(substr($nopo, 0, 3) == 'POG') {
			$data['tipepo'] = "PO Greige";
			$data['poitem'] = $this->bas_model->getpoitemgreige($nopo);
			$data['detail'] = $this->bas_model->detailsuratjalan_bynosj2($nosuratjalan, $data['tipepo']);
			// print_r($data['detailbarang']);

			// $data['copydetailbarang'] = $data['detailbarang'];

			$data['detailbarang'] = array();
			foreach($data['detail'] as $row) {
				$totalsent = $this->bas_model->getmaklonsenttotalitems($row['kodebarang'], $row['id']);
				$row['jumlah'] = $row['jumlah'] - $totalsent[0]['qty'];
				$row['satuan_kg'] = $row['satuan_kg'] - $totalsent[0]['satuan_kg'];

				array_push($data['detailbarang'], $row);
			}
		}
		else if(substr($nopo, 0, 3) == "POM") {
			$data['tipepo'] = "PO Matang";
			$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);
			$data['detailbarang'] = $this->bas_model->detailsuratjalan_bynosj($nosuratjalan, $data['tipepo']);
		}
		$data['tipe'] = 'Claim';

		show('_apps/gudang/frmreturpabrikbyfugreige',$data,'gudang_returclaim');
	}

	public function buatreturpabrikbyfucelupan() {
		$data['title'] = "Input Retur Pabrik by Fu Celupan";
		$nosuratjalan = urldecode(preg_replace('/~/', '/', $this->uri->segment(3)));
		$nopo = $this->uri->segment(4);
		$sjid = $this->uri->segment(5);
				
		$data['tipepo'] = "PO Celupan";
		$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);
		//$data['detailbarang'] = $this->bas_model->detailsuratjalan_bynosj($nosuratjalan, $data['tipepo']);
		$data['detailbarang'] = $this->bas_model->getdetailsuratjalancelupan_bysjid($sjid);

		$data['tipe'] = 'Claim';

		show('_apps/gudang/frmreturpabrikbyfucelupan',$data,'gudang_returclaim');
	}

	public function buatreturpabrikbyfugudang() {
		$data['title'] = "Input Retur Pabrik by Fu Gudang";
		if(substr($this->uri->segment(3), 0, 2) == "RC") {
			$returno = $this->uri->segment(3);
			$data['noreturcustomer'] = $returno;
			$nopo = $this->uri->segment(4);
			//$data['tipepo'] = $this->uri->segment(5);
			$data['fromtable'] = 'returcustomeritem';

			if(substr($nopo, 0, 3) == 'POG') {
				$data['poitem'] = $this->bas_model->getpoitemgreige($nopo);	
				$data['tipepo'] = "Greige";
			}
			else if(substr($nopo, 0, 3) == "POM") {
				$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);	
				$data['tipepo'] = "Matang";		
			}
			else if(substr($nopo, 0, 3) == "PCL") {
				$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);
				$data['tipepo'] = "Celupan";
			}

			$data['detailbarang'] = $this->bas_model->getdetailreturcustomer($returno);
			
			$data['tipe'] = 'Claim';
		} else {
			$kodebarang = $this->uri->segment(3);
			$nopo = $this->uri->segment(4);
			//$data['tipepo'] = $this->uri->segment(5);
			$sjid = $this->uri->segment(7);
			$data['fromtable'] = 'stockgudang';
			
			if(substr($nopo, 0, 3) == 'POG') {
				$data['poitem'] = $this->bas_model->getpoitemgreige($nopo);	
				$data['tipepo'] = "Greige";		
			}
			else if(substr($nopo, 0, 3) == "POM") {
				$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);
				$data['tipepo'] = "Celupan";			
			}
			else if(substr($nopo, 0, 3) == "PCL") {
				$data['poitem'] = $this->bas_model->getpoitemmatangcelupan($nopo);	
				$data['tipepo'] = "Matang";		
			}

			//$data['detailbarang'] = $this->bas_model->getdetailstockgudang_bygudangid($kodebarang, $data['tipepo']);
			$data['detailbarang'] = $this->bas_model->getdetailstockgudang_bysjid($sjid);
			$data['tipe'] = 'Claim';
		}
		
		show('_apps/gudang/frmreturpabrikbyfugudang',$data,'gudang_returclaim');
	}

	public function generatereturnumber() {
		$tipe = $this->input->post('tipe');
		$this->load->library('Controller_utility');
		if($tipe == 'Claim') {
			$existingnoretur = $this->bas_model->getnoclaimpembelian('CP');
			$noretur = "CP".$this->controller_utility->generatereturnumber($existingnoretur);
		} else if($tipe == 'Perbaikan') {
		    $existingnoretur = $this->bas_model->getnoreturpabrik('PP');
		    $noretur = "PP".$this->controller_utility->generatereturnumber($existingnoretur);
		}
		echo json_encode($noretur);
	}

	public function savereturbyfugreige() {
		$arr = explode("-", $this->input->post('tanggal'));
		$tgl = $arr[2] . "-" .$arr[1]. "-" .$arr[0];

		if($this->input->post('status') == 'Claim') {
			$status = 2;

			$claimpembelian = array(
				'noclaim' => $this->input->post('noretur'),
				'tanggal' => $tgl,
				'nopo' => $this->input->post('nopo'),
				'kodebarang' => $this->input->post('kode_barang'),
				'type' => 'Claim',
				'status' => 1,
				'keterangan' => $this->input->post('keterangan'),
				'createdby' => $this->session->userdata('username'),
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('claimpembelian', $claimpembelian);

			$check = $this->input->post('check');
			$id_sj = $this->input->post('id_sj');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				$claimdetails = array(
					'noclaim' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => 'orderitemterima'
					);
				$this->db->insert('claimpembelianitem', $claimdetails);

				//	retur yg digenerate from orderitemterima
				//	tidak mengurangi nilai di orderitemterima
				
				//	$detailorderitemterima = $this->bas_model->detailsuratjalan_byid_sj($id_sj[$index]);
				//	$orderitemterima = array(
				//		'qty' => $detailorderitemterima[0]['qty'] - $jumlah[$index], 
				//		'satuan_kg' => floatval($detailorderitemterima[0]['satuan_kg'] - $satuankg[$index])
				//		);
				//	$this->db->where('id_sj', $id_sj[$index]);
				//	$this->db->update('orderitemterima', $orderitemterima);
			}

		  	$data['tipe'] = 'Claim Pembelian';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			$data['detail'] = $this->bas_model->detailclaimpembelian($this->input->post('noretur')); 
			
			$data['barang'] = $this->bas_model->detailclaimbarangpembelian($this->input->post('noretur'));

			$data['eachpoitemhpp'] = $this->bas_model->hppinclaimpembelian($this->input->post('nopo'));

			show('_apps/gudang/cetakclaimpembelian',$data,'gudang');

		} else {
			$status = 0;
		
			$returgeneral = array(
				'noretur' => $this->input->post('noretur'), 
				'tanggal' => $tgl, 
				'nopo' => $this->input->post('nopo'), 
				'kodebarang' => $this->input->post('kode_barang'), 
				'status' => $status, 
				'keterangan' => $this->input->post('keterangan'), 
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('returpabrik', $returgeneral);

			$check = $this->input->post('check');
			$id_sj = $this->input->post('id_sj');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				$returdetails = array(
					'noretur' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => 'orderitemterima'
					);
				$this->db->insert('returpabrikitem', $returdetails);

				//	retur yg digenerate from orderitemterima
				//	tidak mengurangi nilai di orderitemterima

				//	$detailorderitemterima = $this->bas_model->detailsuratjalan_byid_sj($id_sj[$index]);
				//	$orderitemterima = array(
				//		'qty' => $detailorderitemterima[0]['qty'] - $jumlah[$index], 
				//		'satuan_kg' => floatval($detailorderitemterima[0]['satuan_kg'] - $satuankg[$index])
				//		);
				//	$this->db->where('id_sj', $id_sj[$index]);
				//	$this->db->update('orderitemterima', $orderitemterima);
			}

		  	$data['tipe'] = 'Perbaikan Pabrik';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			$data['detail'] = $this->bas_model->detailreturpembelian($this->input->post('noretur')); 
			
			$data['barang'] = $this->bas_model->detailreturbarangpembelian($this->input->post('noretur'));

			show('_apps/gudang/cetakreturpembelian',$data,'gudang');
		}
		
	}
	public function savereturbyfucelupan() {
		$arr = explode("-", $this->input->post('tanggal'));
		$tgl = $arr[2] . "-" .$arr[1]. "-" .$arr[0];

		if($this->input->post('status') == 'Claim') {
			$status = 2;

			$claimpembelian = array(
				'noclaim' => $this->input->post('noretur'),
				'tanggal' => $tgl,
				'nopo' => $this->input->post('nopo'),
				'kodebarang' => $this->input->post('kode_barang'),
				'type' => 'Claim',
				'status' => 1,
				'keterangan' => $this->input->post('keterangan'),
				'createdby' => $this->session->userdata('username'),
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('claimpembelian', $claimpembelian);

			$check = $this->input->post('check');
			$id_sj = $this->input->post('id_sj');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				$claimdetails = array(
					'noclaim' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => 'orderitemterima'
					);
				$this->db->insert('claimpembelianitem', $claimdetails);

				//	retur yg digenerate from orderitemterima
				//	tidak mengurangi nilai di orderitemterima
				//	$detailorderitemterima = $this->bas_model->detailsuratjalan_byid_sj($id_sj[$index]);
				//	$orderitemterima = array(
				//		'qty' => $detailorderitemterima[0]['qty'] - $jumlah[$index], 
				//		'satuan_kg' => floatval($detailorderitemterima[0]['satuan_kg'] - $satuankg[$index])
				//		);
				//	$this->db->where('id_sj', $id_sj[$index]);
				//	$this->db->update('orderitemterima', $orderitemterima);
			}

		  	$data['tipe'] = 'Claim Pembelian';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			$data['detail'] = $this->bas_model->detailclaimpembelian($this->input->post('noretur')); 
			
			$data['barang'] = $this->bas_model->detailclaimbarangpembelian($this->input->post('noretur'));

			$data['eachpoitemhpp'] = $this->bas_model->hppinclaimpembelian($this->input->post('nopo'));

			show('_apps/gudang/cetakclaimpembelian',$data,'gudang');

		} else {
			$status = 0;
		
			/*$returgeneral = array(
				'noretur' => $this->input->post('noretur'), 
				'tanggal' => $tgl, 
				'nopo' => $this->input->post('nopo'), 
				'kodebarang' => $this->input->post('kode_barang'), 
				'status' => $status, 
				'keterangan' => $this->input->post('keterangan'), 
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('returpabrik', $returgeneral);*/

			$returgeneral = array(
				'frno' => $this->input->post('noretur'),
				'sjid' => $this->input->post('sjid'),
				'frstatus' => $status, 
				'notes' => $this->input->post('keterangan'), 
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('factoryreturn', $returgeneral);
			$frid = $this->db->insert_id();

			$check = $this->input->post('check');
			$id_sj = $this->input->post('id_sj');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				/*$returdetails = array(
					'noretur' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => 'orderitemterima'
					);
				$this->db->insert('returpabrikitem', $returdetails);*/

				$returdetails = array(
					'frid' => $frid,
					'itemid' => $itemid[$index], 
					'quantity' =>$jumlah[$index] , 
					'weight' => $satuankg[$index]
					);
				$this->db->insert('factoryreturndetail', $returdetails);

				//	retur yg digenerate from orderitemterima
				//	tidak mengurangi nilai di orderitemterima

				//	$detailorderitemterima = $this->bas_model->detailsuratjalan_byid_sj($id_sj[$index]);
				//	$orderitemterima = array(
				//		'qty' => $detailorderitemterima[0]['qty'] - $jumlah[$index], 
				//		'satuan_kg' => floatval($detailorderitemterima[0]['satuan_kg'] - $satuankg[$index])
				//		);
				//	$this->db->where('id_sj', $id_sj[$index]);
				//	$this->db->update('orderitemterima', $orderitemterima);
			}

		  	$data['tipe'] = 'Perbaikan Pabrik';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			//$data['detail'] = $this->bas_model->detailreturpembelian($this->input->post('noretur'));
			$data['detail'] = $this->bas_model->detailfactoryreturn($frid); 
			
			//$data['barang'] = $this->bas_model->detailreturbarangpembelian($this->input->post('noretur'));
			$data['barang'] = $this->bas_model->detailitemfactoryreturn($frid);

			show('_apps/gudang/cetakreturpembelian',$data,'gudang');
		}
	}
	public function savereturbyfugudang() {
		$arr = explode("-", $this->input->post('tanggal'));
		$tgl = $arr[2] . "-" .$arr[1]. "-" .$arr[0];
					
		if($this->input->post('status') == 'Claim') {
			$status = 2;

			$claimpembelian = array(
				'noclaim' => $this->input->post('noretur'),
				'noreturpabrik' => $this->input->post('noreturpabrik'),
				'noreturcustomer' => $this->input->post('noreturcustomer'),
				'tanggal' => $tgl,
				'nopo' => $this->input->post('nopo'),
				'kodebarang' => $this->input->post('kode_barang'),
				'type' => 'Claim',
				'status' => 1,
				'keterangan' => $this->input->post('keterangan'),
				'createdby' => $this->session->userdata('username'),
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('claimpembelian', $claimpembelian);

			$check = $this->input->post('check');
			if(isset($_POST['gudangid']))
				$gudangid = $this->input->post('gudangid');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				$claimdetails = array(
					'noclaim' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => $this->input->post('fromtable')
					);
				$this->db->insert('claimpembelianitem', $claimdetails);

				if(isset($_POST['gudangid'])) {
					$detailstockgudang = $this->bas_model->detailstockgudangbygudangid_itemid($gudangid[$index], $itemid[$index]);
					if(($detailstockgudang[0]['jumlah'] - $jumlah[$index]) > 0) {
						$stockgudang = array(
							'jumlah' => $detailstockgudang[0]['jumlah'] - $jumlah[$index], 
							'satuan_kg' => floatval($detailstockgudang[0]['satuan_kg'] - $satuankg[$index])
							);
						$this->db->where('gudangid', $gudangid[$index]);
						$this->db->where('itemid', $itemid[$index]);
						$this->db->update('stockgudang', $stockgudang);
					} else {
						$this->db->where('gudangid', $gudangid[$index]);
						$this->db->where('itemid', $itemid[$index]);
						$this->db->delete('stockgudang');

						$this->db->select('itemid');
						$this->db->from('stockgudang');
						$this->db->where('gudangid', $gudangid[$index]);
						if($this->db->count_all_results() == 0) {
							$this->db->where('id', $gudangid[$index]);
							$this->db->delete('gudang');
						}
					}
				}
			}

		  	$data['tipe'] = 'Claim Pembelian';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			$data['detail'] = $this->bas_model->detailclaimpembelian($this->input->post('noretur')); 
			
			$data['barang'] = $this->bas_model->detailclaimbarangpembelian($this->input->post('noretur'));

			$data['eachpoitemhpp'] = $this->bas_model->hppinclaimpembelian($this->input->post('nopo'));

			show('_apps/gudang/cetakclaimpembelian',$data,'gudang');

		} else {
			$status = 0;
		
			/*$returgeneral = array(
				'noretur' => $this->input->post('noretur'),
				'noreturcustomer' => $this->input->post('noreturcustomer'),
				'tanggal' => $tgl, 
				'nopo' => $this->input->post('nopo'), 
				'kodebarang' => $this->input->post('kode_barang'), 
				'status' => $status, 
				'keterangan' => $this->input->post('keterangan'), 
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);*/
			$returgeneral = array(
				'frno' => $this->input->post('noretur'),
				'sjid' => $this->input->post('sjid'),
				'frstatus' => $status, 
				'notes' => $this->input->post('keterangan'), 
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('factoryreturn', $returgeneral);
			$frid = $this->db->insert_id();

			$insertwh = array(
				'sjid' => $this->input->post('sjid'), 
				'whdate' => date('Y-m-d'),
				'whtype' => 'out',  
				'createdby' => $this->session->userdata('username'), 
				'createddate' => date('Y-m-d h:i:s')
				);
			$this->db->insert('warehouse', $insertwh);
			$whid = $this->db->insert_id();

			$check = $this->input->post('check');
			$gudangid = $this->input->post('gudangid');
			$itemid = $this->input->post('itemid');
			$jumlah = $this->input->post('jumlah');
			$satuankg = $this->input->post('satuankg');
			foreach ($check as $index) {
				/*$returdetails = array(
					'noretur' => $this->input->post('noretur'), 
					'itemid' => $itemid[$index], 
					'qty' =>$jumlah[$index] , 
					'satuan_kg' => $satuankg[$index],
					'fromtable' => $this->input->post('fromtable')
					);*/
				$returdetails = array(
					'frid' => $frid,
					'itemid' => $itemid[$index], 
					'quantity' =>$jumlah[$index] , 
					'weight' => $satuankg[$index]
					);
				$this->db->insert('factoryreturndetail', $returdetails);

				/*if(isset($_POST['gudangid'])) {
					$detailstockgudang = $this->bas_model->detailstockgudangbygudangid_itemid($gudangid[$index], $itemid[$index]);
					if(($detailstockgudang[0]['jumlah'] - $jumlah[$index]) > 0) {
						$stockgudang = array(
							'jumlah' => $detailstockgudang[0]['jumlah'] - $jumlah[$index], 
							'satuan_kg' => floatval($detailstockgudang[0]['satuan_kg'] - $satuankg[$index])
							);
						$this->db->where('gudangid', $gudangid[$index]);
						$this->db->where('itemid', $itemid[$index]);
						$this->db->update('stockgudang', $stockgudang);
					} else {
						$this->db->where('gudangid', $gudangid[$index]);
						$this->db->where('itemid', $itemid[$index]);
						$this->db->delete('stockgudang');

						$this->db->select('itemid');
						$this->db->from('stockgudang');
						$this->db->where('gudangid', $gudangid[$index]);
						if($this->db->count_all_results() == 0) {
							$this->db->where('id', $gudangid[$index]);
							$this->db->delete('gudang');
						}
					}
				}*/

				$insertwhd = array(
					'whid' => $whid, 
					'itemid' => $itemid[$index],
					'quantity' => $jumlah[$index],  
					'weight' => $satuankg[$index]
					);
				$this->db->insert('warehousedetail', $insertwhd);

			}

			$statusporetur = array('postatus'=>'Retur Perbaikan');
			$this->db->where('poid',$this->bas_model->getpoid_bywhid($gudangid));
			$this->db->update('purchaseorder',$statusporetur);

		  	$data['tipe'] = 'Perbaikan Pabrik';
			
		  	$data['keterangan'] = $this->input->post('keterangan');
			
			//$data['detail'] = $this->bas_model->detailreturpembelian($this->input->post('noretur'));
			$data['detail'] = $this->bas_model->detailfactoryreturn($frid); 
			
			//$data['barang'] = $this->bas_model->detailreturbarangpembelian($this->input->post('noretur'));
			$data['barang'] = $this->bas_model->detailitemfactoryreturn($frid);

			show('_apps/gudang/cetakreturpembelian',$data,'gudang');
		}
	}

	public function cetakperbaikanpabrik() {
		$noretur = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);

		$data['tipe'] = 'Perbaikan Pabrik';

		$data['detail'] = $this->bas_model->newdetailreturpembelian($noretur); 
		
		$data['barang'] = $this->bas_model->newdetailreturbarangpembelian($noretur);
		
		show('_apps/gudang/cetakreturpembelian',$data,'gudang');
	}

	public function editreturpembelian(){
		$noretur = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);

		$data['tipe'] = 'Edit Retur Pembelian';

		$data['detail'] = $this->bas_model->newdetailreturpembelian($noretur); 
		
		$data['barang'] = $this->bas_model->newdetailreturbarangpembelian($noretur);
		
		show('_apps/gudang/editreturpembelian',$data,'gudang');

	}

	public function updatereturpembelian(){
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		$id = $this->input->post('id');
		foreach ($check as $i) {
				$factoryreturndetail = array(
					'quantity' => $jumlah[$i],
					'weight' => $kilo[$i]
					);
				$this->db->where('frdid', $id[$i]);
				$this->db->update('factoryreturndetail', $factoryreturndetail);
		}
		redirect(base_url('laporan/laporanreturpembelian'));
	}
	
	
	public function buatsuratjalanpogreige() {
		$nopo = $this->uri->segment(3);
		$kodebarang = $this->uri->segment(4);
		
		$this->load->library('controller_utility');
			
		$nosjmaklon = $this->bas_model->getnosuratjalanmaklon();
		$noorder = $this->uri->segment(5);
		$kodebarang = $this->uri->segment(4);
		
		$data['namasupplier'] = $this->bas_model->getsuppliercelup($kodebarang);

		$data['nosjmaklon'] = "SJM".$this->controller_utility->generateordernumber($nosjmaklon);
		$suppliercelupan = $this->bas_model->getsuppliercelup($kodebarang);
		$data['nopocelup'] = $suppliercelupan[0]['namasupplier'];
		$data['detailbarang'] = $this->bas_model->getdetailpogreigeformaklon($kodebarang);
		
		
		show('_apps/gudang/frmsuratjalanmaklon',$data,'gudang');
	}

	public function savesuratjalanpogreige() {
		$tgl = explode('-', $this->input->post('tanggal'));
		
		$data = array('nosuratjalan'=>$this->input->post('nosuratjalan'),
					  'nopogreige'=>$this->input->post('nopogreige'),
					  'nopocelup'=>$this->input->post('nopocelup'),
					  'tanggal'=>$tgl[2].'-'.$tgl[1].'-'.$tgl[0],
					  'idsupplier'=>$this->input->post('idsupplier'),
					  'nomorkendaraan'=>$this->input->post('nomorkendaraan'),
					  'namapengendara'=>$this->input->post('namasopir'),
					  'keterangan'=>$this->input->post('keterangan'),
					  'status'=> 0,
					  'createddate'=> date('Y-m-d'),
					  'createdby'=>$this->session->userdata('username')
			    );
		$this->db->insert('suratjalanmaklon',$data);
		
		$itemid = $this->input->post('itemid');
		$gudangid = $this->input->post('gudangid');
		$qty = $this->input->post('jumlah');
		$satuan = $this->input->post('satuan');
		$kg = $this->input->post('satuankg');
		
	    for($i=0;$i<count($itemid);$i++) {
	    	if($qty[$i] <= 0) continue;
	    	$dataitem = array('nosuratjalan'=>$this->input->post('nosuratjalan'),
	    					  'itemid'=>$itemid[$i],
	    					  'qty'=>$qty[$i],
	    					  'satuan'=>$satuan[$i],
	    					  'satuan_kg'=>$kg[$i]
	    					  );
	    	$this->db->insert('suratjalanmaklonitem',$dataitem);

	    	$detailstockgudang = $this->bas_model->detailstockgudangbygudangid_itemid($gudangid[$i], $itemid[$i]);
	    	if(($detailstockgudang[0]['jumlah'] - $qty[$i]) > 0) {
				$stockgudang = array(
					'jumlah' => $detailstockgudang[0]['jumlah'] - $qty[$i], 
					'satuan_kg' => floatval($detailstockgudang[0]['satuan_kg'] - $kg[$i])
				);
				$this->db->where('gudangid', $gudangid[$i]);
				$this->db->where('itemid', $itemid[$i]);
				$this->db->update('stockgudang', $stockgudang);
				echo $detailstockgudang[0]['jumlah'] . " - " . $qty[$i] . "<br>";
			} else {
				// EDITED: records are better not to be deleted.
				// $this->db->where('id', $gudangid[$i]);
				// $this->db->delete('gudang');

				$stockgudang = array(
					'jumlah' => 0,
					'satuan_kg' => 0
				);
				$this->db->where('gudangid', $gudangid[$i]);
				$this->db->where('itemid', $itemid[$i]);
				$this->db->update('stockgudang', $stockgudang);
			}
	    }
	    
	    redirect(base_url('laporan/laporansuratjalanmaklon'));
	}
	
	public function cetaksuratjalanmaklon() {
		$data['title'] = 'Cetak Surat Jalan Maklon';
		$data['detail'] = $this->bas_model->newshowmaklon($this->uri->segment(3));
		//$kodebarang = $this->bas_model->getkodebarangsjmaklon($this->uri->segment(3));
		$data['detailsisa'] = $this->bas_model->newgetdetailpogreigeforcetaksjmaklon($data['detail'][0]['nosuratjalan']);
		show('_apps/gudang/cetaksuratjalanmaklon',$data,'gudang');
	}

	public function editsuratjalanmaklon(){
		$data['title'] = 'Edit Surat Jalan Maklon';
		$data['detail'] = $this->bas_model->newshowmaklon($this->uri->segment(3));
		$data['barang'] = $this->bas_model->newshowmaklonitem($this->uri->segment(3));
		show('_apps/gudang/editsuratjalanmaklon',$data,'gudang');	
	}

	public function updatesuratjalanmaklon(){
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		$harga = $this->input->post('harga');
		$nosuratjalan = $this->input->post('nosuratjalan');
		//$itemid = $this->input->post('itemid');
		$id = $this->input->post('id');
		foreach ($check as $i) {
				$suratmaklonitem = array(
					'quantity' => $jumlah[$i],
					'weight' => $kilo[$i]
					);
				$this->db->where('sjmdid', $id[$i]);
				$this->db->update('sjmaklondetail', $suratmaklonitem);
		}
		redirect(base_url('laporan/laporansuratjalanmaklon'));	
	}

	/* 09/10/2013 */
	public function claimpembelian() {
		$uri = $this->uri->segment(3);
		$data['title'] = "Claim Pembelian";
		
		show('_apps/gudang/claimpembelian',$data,'gudang');
	}
	public function ajaxclaimpembelian() {
		$this->load->view('_datatables/ajax_gudangclaimpembelian');
	}
	public function claim() {
		$this->load->library('controller_utility');
		
		$kodebarang = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);
		$noretur = $this->uri->segment(5);
		$data['noretur'] = $noretur;
		$data['title'] = 'Claim Pabrik';
		
		if(substr($nopo, 0, 3) == 'POG') {
		  $data['link'] = "PO Greige";
		  $potype = 'pogreige';
		  $data['tipepo'] = "PO Greige";
		
		} elseif (substr($nopo, 0, 3) == 'PCL') {
		  $data['link'] = "PO Celupan";
		  $potype = 'pocelupan';
		  $data['tipepo'] = "PO Celupan";
		} else {
		  $data['link'] = "PO Matang";
		  $potype = 'pomatang';
		  $data['tipepo'] = "PO Matang";
		}

		$noclaim = $this->bas_model->getnoclaimpembelian('CP');
		$data['noclaim'] = "CP".$this->controller_utility->generatereturnumber($noclaim);
				
			$data['tipe'] = 'Perbaikan Pabrik';

			$data['barang'] = $this->bas_model->detailreturpembelian($noretur); 
			
			$data['detailbarang'] = $this->bas_model->detailreturbarangpembelian($noretur);

			$data['poitem'] = $this->bas_model->showpo($nopo,$potype);

		show('_apps/gudang/claim',$data,'gudang');
	}
	public function saveclaim() {
		$t = explode('-',$this->input->post('tanggal'));
		
		$claimpembelian = array(
			'noclaim' => $this->input->post('noclaim'),
			'noreturpabrik' => $this->input->post('noreturpabrik'),
			'noreturcustomer' => $this->input->post('noreturcustomer'),
			'tanggal'=>$t[2].'-'.$t[1].'-'.$t[0],
			'nopo'=>$this->input->post('nopo'),
			'kodebarang'=>$this->input->post('kode_barang'),
			'type' => 'Claim',
			'status' => 1,
			'keterangan' => $this->input->post('keterangan'),
			'createdby' => $this->session->userdata('username'),
			'createddate' => date('Y-m-d h:i:s')
			);
		$this->db->insert('claimpembelian', $claimpembelian);
		
		$check = $this->input->post('check');
		$itemid = $this->input->post('itemid');
		$jumlah = $this->input->post('jumlah');
		$satuankg = $this->input->post('satuankg');
		foreach ($check as $index) {
			$claimdetails = array(
				'noclaim' => $this->input->post('noclaim'), 
				'itemid' => $itemid[$index], 
				'qty' =>$jumlah[$index] , 
				'satuan_kg' => $satuankg[$index],
				'fromtable' => $this->input->post('fromtable')
				);
			$this->db->insert('claimpembelianitem', $claimdetails);
		}

		$data['tipe'] = 'Claim Pembelian';

		$data['keterangan'] = $this->input->post('keterangan');		
		
		$data['detail'] = $this->bas_model->detailclaimpembelian($this->input->post('noclaim')); 
		
		$data['barang'] = $this->bas_model->detailclaimbarangpembelian($this->input->post('noclaim'));

		$data['eachpoitemhpp'] = $this->bas_model->hppinclaimpembelian($this->input->post('nopo'));
		
		show('_apps/gudang/cetakclaimpembelian',$data,'gudang');
	}

	public function cetakclaimpembelian() {
		$noclaim = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);

		$data['tipe'] = 'Claim Pembelian';

		$data['detail'] = $this->bas_model->newdetailclaimpembelian($noclaim); 
		
		$data['barang'] = $this->bas_model->newdetailclaimbarangpembelian($noclaim);

		$data['eachpoitemhpp'] = $this->bas_model->newhppinclaimpembelian($nopo);
		
		show('_apps/gudang/cetakclaimpembelian',$data,'gudang');
	}

	public function cetakbarangperbaikan(){
		$nosuratjalan = urldecode(preg_replace('/~/', "/", $this->uri->segment(3)));
		$data['detail'] = $this->bas_model->newshowbarangperbaikan($nosuratjalan);
		$data['barang'] = $this->bas_model->newbarangperbaikanitem($nosuratjalan);

		show('_apps/gudang/cetakbarangperbaikan',$data,'gudang');
	}

	public function editbarangperbaikan(){
		$nosuratjalan = urldecode(preg_replace('/~/', "/", $this->uri->segment(3)));
		$data['detail'] = $this->bas_model->newshowbarangperbaikan($nosuratjalan);
		$data['barang'] = $this->bas_model->newbarangperbaikanitem($nosuratjalan);

		show('_apps/gudang/editbarangperbaikan',$data,'gudang');
		
	}

	public function updatebarangperbaikan(){
		$id = $this->input->post('id');
		$check = $this->input->post('check');
		$jumlah = $this->input->post('jumlah');
		$kilo = $this->input->post('berat');
		foreach ($check as $i) {
				$suratjalanreturdetail = array(
					'quantity' => $jumlah[$i],
					'weight' =>$kilo[$i]
					);
				$this->db->where('sjrdid', $id[$i]);
				$this->db->update('suratjalanreturdetail', $suratjalanreturdetail);
		}
		redirect(base_url('laporan/laporanbarangperbaikan'));
	}

	public function editclaimpembelian(){
		$noclaim = $this->uri->segment(3);
		$nopo = $this->uri->segment(4);

		$data['tipe'] = 'Edit Claim Pembelian';

		$data['detail'] = $this->bas_model->newdetailclaimpembelian($noclaim); 
		
		$data['barang'] = $this->bas_model->newdetailclaimbarangpembelian($noclaim);

		$data['eachpoitemhpp'] = $this->bas_model->newhppinclaimpembelian($nopo);
		
		show('_apps/gudang/editclaimpembelian',$data,'gudang');
	}

	public function suratjalancustomerperbaikanselesai() {
		$data['title'] = "Surat Jalan Customer setelah Perbaikan Selesai";
		show('_apps/gudang/suratjalancustomerperbaikanselesai', $data, 'gudang');
	}
	public function ajaxsuratjalancustomerperbaikanselesai() {
		$this->load->view('_datatables/ajax_suratjalancustomerperbaikanselesai');
	}
	public function buatsuratjalancustomerperbaikanselesai() {
		$noretur = $this->uri->segment(3);
		$data['title'] = "Input Surat Jalan Customer setelah Perbaikan Selesai";
		$data['detail'] = $this->bas_model->getdetailreturpabrikperbaikanberhasildiperbaiki($noretur);
		$data['barang'] = $this->bas_model->getdetailbarangreturpabrikperbaikanberhasildiperbaiki($noretur);
		show('_apps/gudang/frmsuratjalancustomerperbaikanselesai', $data, 'gudang');
	}
	public function generatesuratjalancustomerperbaikannumber() {
		$this->load->library('Controller_utility');
		
		$existingnosj = $this->bas_model->getnosj();
		$nosj = "SJP".$this->controller_utility->generateordernumber($existingnosj);

		echo json_encode($nosj);
	}
	public function savesuratjalancustomerperbaikanselesai() {
		$tgl = explode('-', $this->input->post('tanggal'));
		$suratjalancustomer = array(
			'nosuratjalan' => $this->input->post('nosj'), 
			'tanggal' => $tgl[2].'-'.$tgl[1].'-'.$tgl[0], 
			'kodebarang' => $this->input->post('kodebarang'),
			'noretur' => $this->input->post('noretur'), 
			'namasupir' => $this->input->post('namasupir'),
			'nokendaraan' => $this->input->post('nokendaraan'), 
			'catatan' => $this->input->post('catatan'), 
			'createddate' => date('Y-m-d'), 
			'createdby' => $this->session->userdata('username')
			);
		$this->db->insert('suratjalancustomerperbaikan', $suratjalancustomer);

		$status = array('status' => '1');
		$this->db->where('kodebarang', $this->input->post('kodebarang'));
		$this->db->update('returcustomer', $status);
		
		$itemids = $this->input->post('itemid');
		$jumlah = $this->input->post('jumlah');
		$satuankg = $this->input->post('satuankg');

		foreach ($itemids as $index => $itemid) {
			$detail = array(
				'nosuratjalan' => $this->input->post('nosj'), 
				'itemid' => $itemid, 
				'qty' => $jumlah[$index], 
				'satuan_kg' => $satuankg[$index]
				);

			$this->db->insert('suratjalancustomerperbaikandetail', $detail);
		}

		redirect(base_url('gudang/suratjalancustomerperbaikanselesai'));
	}
}

/* End of file gudang.php */
/* Location: ./application/controllers/gudang.php */