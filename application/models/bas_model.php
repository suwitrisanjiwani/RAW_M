<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bas_model extends CI_Model {
	/* Customer */
	function shownamacustomer() {
		$sql = "SELECT idCustomer,namaCustomer FROM customer ORDER BY namaCustomer";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function shownamapegawai() {
		$sql = "SELECT idKaryawan,namaKaryawan FROM pegawai WHERE posisiKaryawan = 'Salesman' OR posisiKaryawan = 'Sales' ORDER BY namaKaryawan";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function jsonkodewarna() {
		$sql = "SELECT kode, keterangan FROM Others WHERE kategori = 'warna'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/* 27 Juli 2013 working */
	function getkodebarang($kode) {

		$sql = "SELECT kodebarang as kode FROM OrderCustomer WHERE LEFT(kodebarang,1) = '$kode' ORDER BY noorder ASC";
		$query = $this->db->query($sql);

		$array = array_filter($query->result());		   		
		if (empty($array)) { return ""; }

		$existingarray = array();
		foreach ($query->result() as $key => $row)
		{
			$arr = explode(",", $row->kode);
			foreach ($arr as $kode) {
				$existingarray[] = $kode;
			}

		   	// sql query "ORDER BY kodebarang DESC" tend to sort by digit first then followed by alphabeths
		   	// if any Z on either the second, third and fourth fragment of kodebarang sequence,
		   	// check if any 0, 1, 2, ... , 9
		   	// return the last kodebarang before CAAA (don't get back to CAAZ)
		}
		$uniquearray = array_unique($existingarray);
		return end($uniquearray);		
	}
	/* 27 Juli 2013 */
	function getkodebarang_beta($kode) {

		$sql = "SELECT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' ORDER BY kodebarang ASC LIMIT 0,1";
		$query = $this->db->query($sql);

		foreach ($query->result() as $row)
		{
			$ALPHA = '';
			$sql = "SELECT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' ORDER BY kodebarang DESC LIMIT 0,1";
			$query = $this->db->query($sql);
			foreach ($query->result() as $row)
			{
				if(substr($row->kode, 1, 3) == "AAA") return $row->kode;

				$ALPHA = $row->kode;
			}
			$duaALPHA = substr($ALPHA, 1, 1);
			$tigaALPHA = substr($ALPHA, 2, 1);
			$empatALPHA = substr($ALPHA, 3, 1);
		
   			$DIGIT1 = '';
   			$DIGIT2 = '';
	   		$sql = "SELECT DISTINCT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' AND RIGHT(kodebarang, 3) < (SELECT CONCAT(SUBSTR(kodebarang, 2, 2), 'A') FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' ORDER BY kodebarang DESC LIMIT 0, 1) ORDER BY kodebarang ASC LIMIT 0, 1";
	   		$query2 = $this->db->query($sql);
	   		foreach ($query2->result() as $row2) {
	   			$DIGIT1 = $row2->kode;
				break;
	   		}

	   		$sql = "SELECT DISTINCT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' AND RIGHT(kodebarang, 3) < (SELECT CONCAT(SUBSTR(kodebarang, 2, 2), 'A') FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' ORDER BY kodebarang DESC LIMIT 0, 1) ORDER BY kodebarang DESC LIMIT 0, 1";
	   		$query3 = $this->db->query($sql);
	   		foreach ($query3->result() as $row3) {
	   			$DIGIT2 = $row3->kode;
				break;
	   		}

	   		//echo $DIGIT1 . ", " . $DIGIT2;

	   		$duaDIGIT1 = substr($DIGIT1, 1, 1);
   			$tigaDIGIT1 = substr($DIGIT1, 2, 1);
			$empatDIGIT1 = substr($DIGIT1, 3, 1);
	   		$duaDIGIT2 = substr($DIGIT2, 1, 1);
   			$tigaDIGIT2 = substr($DIGIT2, 2, 1);
			$empatDIGIT2 = substr($DIGIT2, 3, 1);

	   		$DIGIT = '';
	   		if(is_numeric($duaDIGIT1) && !is_numeric($duaDIGIT2)) $DIGIT = $DIGIT1;
	   		elseif(!is_numeric($duaDIGIT1) && is_numeric($duaDIGIT2)) $DIGIT = $DIGIT2;
	   		else {
	   			if($duaDIGIT1 > $duaDIGIT2) $DIGIT = $DIGIT1;
	   			elseif($duaDIGIT1 < $duaDIGIT2) $DIGIT = $DIGIT2;

	   			if(is_numeric($tigaDIGIT1) && !is_numeric($tigaDIGIT2)) $DIGIT = $DIGIT1;
	   			elseif(!is_numeric($tigaDIGIT1) && is_numeric($tigaDIGIT2)) $DIGIT = $DIGIT2;
	   			else {
	   				if($tigaDIGIT1 > $tigaDIGIT2) $DIGIT = $DIGIT1;
	   				elseif($tigaDIGIT1 < $tigaDIGIT2) $DIGIT = $DIGIT2;

	   				if(is_numeric($empatDIGIT1) && !is_numeric($empatDIGIT2)) $DIGIT = $DIGIT1;
	   				elseif(!is_numeric($empatDIGIT1) && is_numeric($empatDIGIT2)) $DIGIT = $DIGIT2;
	   				else {
	   					if($empatDIGIT1 > $empatDIGIT2) $DIGIT = $DIGIT1;
	   					elseif($empatDIGIT1 < $empatDIGIT2) $DIGIT = $DIGIT2;
	   					else $DIGIT = $DIGIT1;
	   				}
	   			}
	   		}

	   		$duaDIGIT = substr($DIGIT, 1, 1);
   			$tigaDIGIT = substr($DIGIT, 2, 1);
			$empatDIGIT = substr($DIGIT, 3, 1);

	   		
	   		if(is_numeric($duaALPHA) && !is_numeric($duaDIGIT)) return $ALPHA;
	   		elseif(!is_numeric($duaALPHA) && is_numeric($duaDIGIT)) return $DIGIT;
	   		else {
	   			if($duaDIGIT > $duaALPHA) return $DIGIT;
	   			elseif($duaDIGIT < $duaALPHA) return $ALPHA;
	   			else {
		   			if(is_numeric($tigaALPHA) && !is_numeric($tigaDIGIT)) return $ALPHA;
		   			elseif(!is_numeric($tigaALPHA) && is_numeric($tigaDIGIT)) return $DIGIT;
		   			else {
		   				if($tigaDIGIT > $tigaALPHA) return $DIGIT;
		   				elseif($tigaDIGIT < $tigaALPHA) return $ALPHA;

		   				if(is_numeric($empatALPHA) && !is_numeric($empatDIGIT)) return $ALPHA;
		   				elseif(!is_numeric($empatALPHA) && is_numeric($empatDIGIT)) return $DIGIT;
		   				else {
		   					if($empatDIGIT > $empatALPHA) return $DIGIT;
		   					elseif($empatDIGIT < $empatALPHA) return $ALPHA;
		   				}
		   			}
		   		}
	   		}
		   	
		   	// sql query "ORDER BY kodebarang DESC" tend to sort by digit first then followed by alphabeths
		   	// if any Z on either the second, third and fourth fragment of kodebarang sequence,
		   	// check if any 0, 1, 2, ... , 9
		   	// return the last kodebarang before CAAA (don't get back to CAAZ)		

		}

		$array = array_filter($query->result());
		   		
		if (empty($array)) { return ""; }
	}
	/* 26 Juli 2013 */
	function getkodebarang_alpha($kode) {

		$sql = "SELECT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' ORDER BY kodebarang DESC LIMIT 0,1";
		$query = $this->db->query($sql);

		foreach ($query->result() as $row)
		{
			$ALPHA = $row->kode;
			$duaALPHA = substr($row->kode, 1, 1);
			$tigaALPHA = substr($row->kode, 2, 1);
			$empatALPHA = substr($row->kode, 3, 1);
		   	
		   	// sql query "ORDER BY kodebarang DESC" tend to sort by digit first then followed by alphabeths
		   	// if any Z on either the second, third and fourth fragment of kodebarang sequence,
		   	// check if any 0, 1, 2, ... , 9
		   	// return the last kodebarang before CAAA (don't get back to CAAZ)
		   	
	   		$sql = "SELECT DISTINCT kodebarang as kode FROM OrderItem WHERE LEFT(kodebarang,1) = '$kode' AND RIGHT(kodebarang, 3) < (SELECT CONCAT(SUBSTR(kodebarang, 2, 2), 'A') FROM OrderItem WHERE LEFT(kodebarang,1) = 'C' ORDER BY kodebarang DESC LIMIT 0, 1) ORDER BY kodebarang DESC LIMIT 0, 1";
	   		$query2 = $this->db->query($sql);
	   		foreach ($query2->result() as $row2) {
	   			$DIGIT = $row2->kode;
	   			$duaDIGIT = substr($row2->kode, 1, 1);
	   			$tigaDIGIT = substr($row2->kode, 2, 1);
				$empatDIGIT = substr($row2->kode, 3, 1);

				if(is_numeric($duaALPHA) && !is_numeric($duaDIGIT)) return $ALPHA;
		   		elseif(!is_numeric($duaALPHA) && is_numeric($duaDIGIT)) return $DIGIT;
		   		else {
		   			if($duaDIGIT > $duaALPHA) return $DIGIT;
		   			elseif($duaDIGIT < $duaALPHA) return $ALPHA;

		   			if(is_numeric($tigaALPHA) && !is_numeric($tigaDIGIT)) return $ALPHA;
		   			elseif(!is_numeric($tigaALPHA) && is_numeric($tigaDIGIT)) return $DIGIT;
		   			else {
		   				if($tigaDIGIT > $tigaALPHA) return $DIGIT;
		   				elseif($tigaDIGIT < $tigaALPHA) return $ALPHA;

		   				if(is_numeric($empatALPHA) && !is_numeric($empatDIGIT)) return $ALPHA;
		   				elseif(!is_numeric($empatALPHA) && is_numeric($empatDIGIT)) return $DIGIT;
		   				else {
		   					if($empatDIGIT > $empatALPHA) return $DIGIT;
		   					elseif($empatDIGIT < $empatALPHA) return $ALPHA;
		   				}
		   			}
		   		}
	   		}

	   		
		   	return $ALPHA;

		}
	}
	function getnopo() {
		$sql = "SELECT noorder as nopo FROM OrderCustomer ORDER BY noorder DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nopo;
		}
	}
	function getgramasi() {
	   $sql = "SELECT keterangan AS gramasi FROM Others WHERE kategori = 'gramasi'";
	   $query = $this->db->query($sql);
	   return $query->result_array();
	}
	function getprocess() {
	   $sql = "SELECT keterangan as process FROM Others WHERE kategori = 'process'";
	   $query = $this->db->query($sql);
	   return $query->result_array();
	}
	function getcompact() {
	   $sql = "SELECT keterangan as compact FROM Others WHERE kategori = 'compact'";
	   $query = $this->db->query($sql);
	   return $query->result_array();
	}
	function getbahan() {
	   $sql = "SELECT keterangan as bahan FROM Others WHERE kategori = 'bahan'";
	   $query = $this->db->query($sql);
	   return $query->result_array();
	}
	function gethandfeel() {
	   $sql = "SELECT keterangan as handfeel FROM Others WHERE kategori = 'handfeel'";
	   $query = $this->db->query($sql);
	   return $query->result_array();
	}
	function getdiscountbynoorder($noorder){
		$this->db->select("diskon");
		$this->db->where("noorder", $noorder);
		$query = $this->db->get("OrderCustomer");
		return $query->row();
	}
	
	function getcustomerbyid($idCustomer) {
		$this->db->where('idCustomer', $idCustomer);
		$query = $this->db->get('Customer');
		return $query->row();
	}
	/* 11/09/2013 */
	function getcustomerbynoorder($noorder) {
		$this->db->select('Customer.idCustomer, namaCustomer');
		//$this->db->where('OrderCustomer.noorder', $noorder);
		$this->db->where('pocustomer.pocno', $noorder);
		$this->db->from('pocustomer');
		$this->db->join('Customer', 'Customer.idCustomer = pocustomer.customerid');
		$query = $this->db->get();

		return $query->row();
	}
	function getcustomer() {
		$sql = "SELECT * FROM customer ORDER BY namaCustomer";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getsalesbyname($name) 
	{
		$sql = "SELECT id FROM salesmen WHERE name='$name' ORDER BY name";
		//$sql = "SELECT idKaryawan, namaKaryawan FROM salesmen WHERE posisiKaryawan='Salesman' OR posisiKaryawan='Sales' OR posisiKaryawan='FL' ORDER BY namaKaryawan";
		$query = $this->db->query($sql);

		foreach ($query->result() as $row)
		{
		   return $row->id;
		}

		return "";
	}

	function getsales() {
		$sql = "SELECT id AS idKaryawan, name AS namaKaryawan FROM salesmen ORDER BY name";
		//$sql = "SELECT idKaryawan, namaKaryawan FROM salesmen WHERE posisiKaryawan='Salesman' OR posisiKaryawan='Sales' OR posisiKaryawan='FL' ORDER BY namaKaryawan";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getsalesbypocid($pocid) {

		$sql = "SELECT 
					b.id, 
					b.name 
				FROM 
					pocustomersales a,
					salesmen b
				WHERE
					a.pocid = $pocid AND
					a.salesmenid = b.id
					";
		$query = $this->db->query($sql);

		return $query->result();
	}

	function deletesalesbypocid($pocid) {

		$sql = "DELETE FROM pocustomersales WHERE pocid = $pocid";
		return $this->db->query($sql);
	}

	function getsalesbyid($idsales) {
		$this->db->where('idKaryawan', $idsales);
		$query = $this->db->get('Karyawan');
		return $query->row();
	}

	/* 17/08/2013 */
	function getposisi() {
		$sql = "SELECT DISTINCT position as posisi FROM systemuser";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getkaryawanbyid($id) {
		$sql = "SELECT idKaryawan, namaKaryawan, alamatKaryawan, phoneKaryawan, userLogin, posisiKaryawan FROM karyawan WHERE idKaryawan = ? ORDER BY posisiKaryawan";
		$query = $this->db->query($sql, $id);
		return $query->row();
	}
	
	function checklogin($user,$pwd) {
		$sql = "SELECT * FROM systemuser WHERE username = '$user' AND password = '$pwd'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	function getcustomerid($nama) {
		$sql = "SELECT idCustomer as id FROM customer";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->id;
		}
	}
	function showorder($order) {
		$sql = "SELECT a.*,b.* FROM OrderCustomer a, OrderItem b WHERE a.kodebarang = b.kodebarang AND a.noorder = '$order' GROUP BY a.noorder";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function newshoworder($order){
		$sql = "SELECT * FROM pocustomer WHERE pocno = '$order' GROUP BY pocno";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}
	function findsales($pocno){
		$sql = "SELECT a.name FROM salesmen a, pocustomer b, pocustomersales c WHERE b.pocno = '$pocno' AND b.pocid = c.pocid AND c.salesmenid = a.id GROUP BY pocno";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}
	function namacustomer($id) {
		$sql = "SELECT namaCustomer as id FROM customer WHERE idCustomer = $id";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->id;
		}
	}
	function showorderitemdetail($kodebarang) {
		$sql = "SELECT a.*,b.* FROM OrderItemDetail a, orderitem b WHERE a.kodebarang = b.kodebarang and a.kodebarang = '$kodebarang'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// modded by david
	function getnopogreige() {
		$sql = "SELECT pono FROM purchaseorder WHERE potype = 1 ORDER BY pono DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->pono;
		}
		
	}

	// modded by david
	function getnopocelup() {
		$sql = "SELECT pono FROM purchaseorder WHERE potype = 2 ORDER BY pono DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->pono;
		}
		
	}
	function getnopomatang() {
		$sql = "select nopo as nopo from po WHERE LEFT(nopo, 3) = 'POM' order by nopo desc LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nopo;
		}
		
	}
	
	function getnamasupplierbysuppliertype($type) {
		$this->db->like('keteranganSupplier', $type);
		$query = $this->db->get('supplier');
		return $query->result_array();
	}
	function getsupplierbyinitialkode($kodebarang) {
		$this->db->select('idSupplier, namaSupplier');
		$this->db->where('initialKodebarang', $kodebarang);
		$query = $this->db->get('supplier');
		return $query->result_array();
	}
	function getnamasupplier() {
		$sql = "SELECT * FROM supplier ORDER BY namaSupplier";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// modded by david
	function detailbarangorder($noorder) {
		$sql = "SELECT
					a.itemid AS id,
					a.color AS warna,
					a.colorcode AS kodewarna,
					a.quantity AS jumlah,
					a.itemtype AS typebarang,
					a.unittype AS satuan,
					a.pricecelupan,
					a.pricematang
				FROM
					pocustomerdetail a,
					pocustomer b
				WHERE
					b.pocno = '$noorder'
				AND
					a.pocid = b.pocid";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// added by david (buat po celupan)
	function pocelupanitemdetail($pogid) {
		$sql = "SELECT
					a.itemid,
					a.color,
					a.colorcode,
					a.quantity,
					a.itemtype,
					a.unittype
				FROM
					pocustomerdetail a,
					purchaseorder b
				WHERE
					b.poid = $pogid
				AND
					a.pocid = b.pocid";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// added by david
	function pomatangitemdetail($orderno) {
		$sql = "SELECT
					a.itemid AS id,
					a.color AS warna,
					a.colorcode AS kodewarna,
					a.quantity AS jumlah,
					a.itemtype AS jenisbarang,
					a.unittype AS satuan
				FROM
					pocustomerdetail a,
					purchaseorder b,
					pocustomer c
				WHERE
					c.pocno = '$orderno'
				AND
					a.pocid = b.pocid
				AND
					a.pocid = c.pocid";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// MODDED
	function getkodebarangorder($noorder) {
		$sql = "SELECT ordercode as kode FROM pocustomer WHERE pocno = '$noorder'";
		$query = $this->db->query($sql);

		foreach ($query->result() as $row) {
		return $row->kode;
		}
	}

	/* 29 Juli 2013 */
	function getpoitem($noorder) {
		$sql = "SELECT
					pocno,
					pocdate,
					ordercode,
					fabrictype,
					grammagefinal
				FROM
					pocustomer
				WHERE
					pocno = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// added by david
	function getpoimatang($noorder) {
		$sql = "SELECT
					pocno AS noorder,
					pocdate AS tanggal,
					ordercode AS kodebarang,
					fabrictype AS jenisbahan,
					grammagefinal AS gramasimatang,
					compact,
					process AS proses
				FROM
					pocustomer
				WHERE
					pocno = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getpoitemgreige($nopo) {
		/*$sql = "SELECT a.jenisbahan, a.proses, a.compact, a.handfeel ,b.nopo, b.kodebarang, b.idSupplier, b.tujuan, b.gramasigreige as gramasigreige, c.gramasimatang AS gramasimatang, b.mesin, b.jenispembayaran, d.namaSupplier as namaSupplier
		FROM OrderItem a, PO b, OrderCustomer c, Supplier d
		WHERE a.kodebarang = b.kodebarang AND b.nopo = '$nopo' AND a.kodebarang = c.kodebarang AND a.noorder = c.noorder AND b.idSupplier = d.idSupplier";*/
		$sql = "SELECT poc.fabrictype as jenisbahan, poc.process as proses, poc.compact as compact, poc.handfeel as handfeel, po.pono as nopo, poc.ordercode as kodebarang, po.supplierid as idSupplier, po.destination as tujuan, po.grammagegreige as gramasigreige, po.grammagefinal AS gramasimatang, po.machine as mesin, po.paymentmethod as jenispembayaran, s.namaSupplier as namaSupplier
		FROM pocustomer poc, purchaseorder po, supplier s
		WHERE poc.pocid = po.pocid AND po.pono = '$nopo' AND s.idSupplier = po.supplierid
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// david - buat po celupan
	function getgreigeitem($pogno) {
		$sql = "SELECT
					b.fabrictype AS jenisbahan,
					b.process AS proses,
					b.compact AS compact,
					b.handfeel AS handfeel,
					b.ordercode AS kodebarang,
					a.supplierid AS idSupplier,
					a.destination AS tujuan,
					a.grammagegreige AS gramasigreige,
					a.grammagefinal AS gramasimatang,
					a.machine AS mesin,
					a.paymentmethod AS jenispembayaran,
					c.namaSupplier AS namaSupplier
				FROM
					purchaseorder a,
					pocustomer b,
					supplier c
				WHERE
					a.pono = '$pogno'
				AND
					a.pocid = b.pocid
				AND
					a.supplierid = c.idSupplier";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/* 26/09/2013 */
	function getpoitemmatangcelupan($nopo) {
		/*$sql = "SELECT a.jenisbahan, a.proses, a.compact, a.handfeel ,b.nopo, b.kodebarang, b.idSupplier, b.tujuan, b.gramasigreige as gramasigreige, c.gramasimatang AS gramasimatang, b.mesin, b.jenispembayaran, d.namaSupplier as namaSupplier
		FROM OrderItem a, PO b, OrderCustomer c, Supplier d
		WHERE a.kodebarang = b.kodebarang AND b.nopo = '$nopo' AND a.kodebarang = c.kodebarang AND a.noorder = c.noorder AND b.idSupplier = d.idSupplier";*/
		$sql = "SELECT poc.fabrictype as jenisbahan, poc.process as proses, poc.compact as compact, poc.handfeel as handfeel, po.pono as nopo, poc.ordercode as kodebarang, po.supplierid as idSupplier, po.destination as tujuan, po.grammagegreige as gramasigreige, po.grammagefinal AS gramasimatang, po.machine as mesin, po.paymentmethod as jenispembayaran, s.namaSupplier as namaSupplier
		FROM pocustomer poc, purchaseorder po, supplier s
		WHERE poc.pocid = po.pocid AND po.pono = '$nopo' AND s.idSupplier = po.supplierid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// function detailbarang($kodebarang) {
	// 	$sql = "SELECT * FROM OrderItemDetail WHERE kodebarang = '$kodebarang'";
		
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// modded by david ^
	function detailbarang($kodebarang) {
		$sql = "SELECT
					b.itemtype AS typebarang,
					b.itemid AS id,
					b.colorcode AS kodewarna,
					b.color AS warna,
					b.quantity AS jumlah,
					b.weight AS satuan_kg,
					b.unittype AS satuan
				FROM
					pocustomer a,
					pocustomerdetail b
				WHERE
					a.ordercode = '$kodebarang'
				AND
					a.pocid = b.pocid";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* 13/11/2013 */
	// function detailbarangbyitemid($itemid) {
	// 	$sql = "SELECT * FROM OrderItemDetail WHERE id = '$itemid'";
		
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	function detailbarangbyitemid($itemid) {
		$sql = "SELECT * FROM pocustomerdetail WHERE itemid = '$itemid'";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailitemterimabynosuratjalan($nosuratjalan) {
		$sql = "SELECT OrderItemTerima.*, orderitemdetail.satuan, orderitemdetail.typebarang, orderitemdetail.kodewarna, orderitemdetail.warna FROM OrderItemTerima, orderitemdetail WHERE nosuratjalan = '$nosuratjalan' AND orderitemdetail.id = orderitemterima.itemid";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function detailbaranggreige($kodebarang) {
		$sql1 = 	"SET @sql = CONCAT('SELECT ', (SELECT REPLACE(GROUP_CONCAT(COLUMN_NAME), 'jumlah,', '') FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'OrderItemDetail' AND TABLE_SCHEMA = 'bsadb-hakakses'), ',SUM(jumlah) AS jumlah, SUM(satuan_kg) AS satuan_kg, kodebarang as kodebarang FROM OrderItemDetail WHERE kodebarang = \"$kodebarang\" GROUP BY typebarang');";
		$sql2 =     " PREPARE stmt1 FROM @sql; ";
		$sql3 =		" EXECUTE stmt1;";
		$this->db->query($sql1);
		$this->db->query($sql2);
		$query = $this->db->query($sql3);
		return $query->result_array();
	}


	// modded by david
	// function detailbaranggreige($kodebarang) {
	// 	$sql = "SELECT
	// 				d.fabrictype,
	// 				e.itemtype,
	// 				((SELECT
	// 						SUM(g.quantity)
	// 					FROM
	// 						pocustomer f,
	// 						pocustomerdetail g
	// 					WHERE
	// 						g.itemtype = e.itemtype
	// 					AND
	// 						f.ordercode = 'CAAA'
	// 					AND
	// 						f.pocid = g.pocid) - SUM(b.quantity)) AS remainingquantity,
	// 				((SELECT
	// 						SUM(g.weight)
	// 					FROM
	// 						pocustomer f,
	// 						pocustomerdetail g
	// 					WHERE
	// 						g.itemtype = e.itemtype
	// 					AND
	// 						f.ordercode = '$kodebarang'
	// 					AND
	// 						f.pocid = g.pocid) - SUM(b.weight)) AS remainingweight
	// 			FROM
	// 				suratjalan a,
	// 				suratjalandetail b,
	// 				purchaseorder c,
	// 				pocustomer d,
	// 				pocustomerdetail e
	// 			WHERE
	// 				a.sjid = b.sjid
	// 			AND
	// 				a.poid = c.poid
	// 			AND
	// 				c.pocid = d.pocid
	// 			AND
	// 				d.pocid = e.pocid
	// 			AND
	// 				b.itemid = e.itemid
	// 			AND
	// 				d.ordercode = '$kodebarang'
	// 			GROUP BY
	// 				e.itemtype";

	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// David
	function detailbarangbysjmaklon($nosj) {
		$sql = "SELECT 
					a.itemid AS id,
					b.typebarang,
					b.kodewarna,
					b.warna,
					a.qty AS jumlah,
					a.satuan,
					a.satuan_kg
				FROM
					suratjalanmaklonitem a,
					orderitemdetail b
				WHERE
					a.nosuratjalan = '$nosj'
				AND
					a.itemid = b.id";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* 02/10/2013 */
	function detailterimabaranggudang($kodebarang, $nopo) {
		$sql = "SELECT nopo, tanggal, kodebarang, supplier, id, noorder,
				jumlahorder, (jumlahsuratjalan + jumlahsuratjalanretur - jumlahreturpabrik - jumlahstock) AS jumlah,
				(satuankgsuratjalan + satuankgsuratjalanretur - satuankgreturpabrik - satuankgstock) AS satuan_kg,
				typebarang,warna,kodewarna,satuan,
				harga_customer,harga_greige,harga_celupan,harga_matang,				
				terimagudang, jenisbahan
				FROM
				(SELECT nopo, booleanval, tanggal, kodebarang, supplier, id, noorder, SUM(jumlah) AS jumlahorder,
				                    IFNULL((SELECT SUM(qty) FROM orderitemterima 
							WHERE orderitemterima.kodebarang = results.kodebarang
							AND orderitemterima.itemid = results.id
							AND orderitemterima.typeterima = 
							CASE LEFT(results.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
				                    ), 0) AS jumlahsuratjalan,
				                    IFNULL((SELECT SUM(satuan_kg) FROM orderitemterima 
							WHERE orderitemterima.kodebarang = results.kodebarang
							AND orderitemterima.itemid = results.id
							AND orderitemterima.typeterima = 
							CASE LEFT(results.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
				                    ), 0) AS satuankgsuratjalan,
				                    IFNULL((SELECT SUM(qty) FROM orderitemterima_retur 
							WHERE orderitemterima_retur.kodebarang = results.kodebarang
							AND orderitemterima_retur.fromtable = 'orderitemterima'
							AND orderitemterima_retur.itemid = results.id
							AND orderitemterima_retur.typeterima = 
							CASE LEFT(results.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
				                    ), 0) AS jumlahsuratjalanretur,
				                    IFNULL((SELECT SUM(satuan_kg) FROM orderitemterima_retur
							WHERE orderitemterima_retur.kodebarang = results.kodebarang
							AND orderitemterima_retur.fromtable = 'orderitemterima'
							AND orderitemterima_retur.itemid = results.id
							AND orderitemterima_retur.typeterima = 
							CASE LEFT(results.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
				                    ), 0) AS satuankgsuratjalanretur,
				                    IFNULL((
				                    	SELECT SUM(rpi.qty)
				                    	FROM returpabrikitem rpi
				                    	INNER JOIN returpabrik rp
				                    	ON rpi.noretur = rp.noretur
										WHERE rp.kodebarang = results.kodebarang
										AND fromtable = 'orderitemterima'
										AND itemid = results.id
										AND rp.nopo = results.nopo
				                    ), 0) AS jumlahreturpabrik,
				                    IFNULL((SELECT SUM(rpi.satuan_kg) FROM returpabrikitem rpi INNER JOIN returpabrik rp ON rpi.noretur = rp.noretur
							WHERE rp.kodebarang = results.kodebarang
							AND fromtable = 'orderitemterima'
							AND itemid = results.id
							AND rp.nopo = results.nopo
				                    ), 0) AS satuankgreturpabrik,
				                    IFNULL((SELECT SUM(jumlah) FROM stockgudang WHERE itemid = results.id AND
										    jenisbarang =
										    CASE LEFT(results.nopo, 3)
										    WHEN 'POG'
										    THEN 'Greige'
										    WHEN 'PCL'
										    THEN 'Celupan'
										    WHEN 'POM'
										    THEN 'Matang'
										    END), 0) AS jumlahstock,
				                    IFNULL((SELECT SUM(satuan_kg) FROM stockgudang WHERE itemid = results.id AND
										    jenisbarang =
										    CASE LEFT(results.nopo, 3)
										    WHEN 'POG'
										    THEN 'Greige'
										    WHEN 'PCL'
										    THEN 'Celupan'
										    WHEN 'POM'
										    THEN 'Matang'
										    END), 0) AS satuankgstock,
				                    typebarang,warna,kodewarna,satuan,
						    harga_customer,harga_greige,harga_celupan,harga_matang,				
						    terimagudang, jenisbahan
				                    FROM
				                    (SELECT 
				                                        a.nopo AS nopo,
				                                        (
										IF ( a.tujuan = 'PT BSA' AND LEFT(a.nopo, 3) = 'POG'
										   , TRUE
										   , IF( LEFT(a.nopo, 3) = 'PCL' AND a.tujuan = 'PT BSA', TRUE, FALSE))					
									
								       OR IF (LEFT(a.nopo, 3) = 'POM'
										, TRUE
										, FALSE)
								       )
								        AS booleanval,
				                                        DATE_FORMAT(a.tanggal,'%d-%m-%Y') AS tanggal,
				                                        a.kodebarang AS kodebarang,
				                                        c.namasupplier AS supplier,
				                                        b.id AS id,
				                                        b.jumlah AS jumlah,
				                                        g.noorder AS noorder,
				                                        b.typebarang,b.warna,b.kodewarna,b.satuan,
									b.harga_customer,b.harga_greige,b.harga_celupan,b.harga_matang,				
									b.terimagudang, h.jenisbahan
				                                                  
				                                    FROM PO AS a
				                                    INNER JOIN OrderItemDetail AS b ON a.kodebarang = b.kodebarang 
				                                    INNER JOIN supplier AS c ON a.idSupplier = c.idSupplier
				                                    INNER JOIN OrderItem AS h ON h.kodebarang = a.kodebarang
				                                    INNER JOIN ordercustomer AS g ON a.kodebarang = g.kodebarang AND g.statusorder <> 'Batal'
				                                    WHERE    a.kodebarang = '$kodebarang'
				                                    AND 	 a.nopo = '$nopo'
				                                    AND
									(
										IF ( a.tujuan = 'PT BSA' AND LEFT(a.nopo, 3) = 'POG'
										   , TRUE
										   , IF( LEFT(a.nopo, 3) = 'PCL' AND a.tujuan = 'PT BSA', TRUE, FALSE))					
									
								       OR IF (LEFT(a.nopo, 3) = 'POM'
										, TRUE
										, FALSE)
								       )
								
				                                    GROUP BY a.nopo, b.id
				                                    ORDER BY a.nopo) AS results
				                                    GROUP BY results.nopo, results.id) AS summary
				                                    WHERE jumlahstock < jumlahsuratjalan";
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	function detailterimabaranggudangbysjid($sjid) {
		$sql = "SELECT sj.sjid as sjid, po.pono as nopo, sj.sjdate as tanggal, poc.ordercode as kodebarang, s.namaSupplier as supplier, pocd.itemid as id, poc.pocno as noorder,
				IFNULL((SELECT COUNT(weight) FROM purchaseorder po,pocustomer poc,pocustomerdetail pocd WHERE po.poid = sj.poid AND poc.pocid = po.pocid AND pocd.pocid = poc.pocid),0) as jumlahorder,
				(IFNULL((SELECT SUM(quantity) FROM suratjalandetail WHERE sjid = sjd.sjid AND itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(whd.quantity) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'in' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) + 
				 IFNULL((SELECT SUM(whd.quantity) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'out' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(frd.quantity) FROM factoryreturn fr, factoryreturndetail frd WHERE fr.sjid = sj.sjid AND frd.frid = fr.frid AND frd.itemid = sjd.itemid),0) +
				 IFNULL((SELECT SUM(sjrd.quantity) FROM suratjalanretur sjr,suratjalanreturdetail sjrd WHERE sjr.sjid = sj.sjid AND sjrd.sjrid = sjr.sjrid AND sjrd.itemid = sjd.itemid),0)) AS jumlah,
				(IFNULL((SELECT SUM(weight) FROM suratjalandetail WHERE sjid = sjd.sjid AND itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(whd.weight) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'in' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) + 
				 IFNULL((SELECT SUM(whd.weight) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'out' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(frd.weight) FROM factoryreturn fr, factoryreturndetail frd WHERE fr.sjid = sj.sjid AND frd.frid = fr.frid AND frd.itemid = sjd.itemid),0) +
				 IFNULL((SELECT SUM(sjrd.weight) FROM suratjalanretur sjr,suratjalanreturdetail sjrd WHERE sjr.sjid = sj.sjid AND sjrd.sjrid = sjr.sjrid AND sjrd.itemid = sjd.itemid),0)) AS satuan_kg,
				pocd.itemtype as typebarang, pocd.color as warna, pocd.colorcode as kodewarna, pocd.unittype as satuan,
				pocd.pricecustomer as harga_customer, pocd.pricecelupan as harga_celupan, pocd.pricematang as harga_matang, poc.fabrictype as jenisbahan
				FROM suratjalan sj, suratjalandetail sjd, purchaseorder po, pocustomer poc, pocustomerdetail pocd, supplier s
				WHERE sj.sjid = $sjid
				AND sjd.sjid = sj.sjid 
				AND po.poid = sj.poid 
				AND pocd.itemid = sjd.itemid 
				AND poc.pocid = po.pocid
				AND s.idsupplier = po.supplierid";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	/* 17/10/2013 */
	function penyusutanberat($itemid, $kodebarang) {
		$sql = "SELECT results.nopo,results.tanggal,results.kodebarang,results.supplier,
				results.jumlahsuratjalan, results.jumlahstock, results.satuan_kg_stock,
				results.jumlahorder, results.satuan_kg_order,
				(results.jumlahorder - results.jumlahstock) AS jumlahblmditerima, (results.satuan_kg_order - results.satuan_kg_stock) AS susut_satuan_kg,
				results.id,(results.jumlahsuratjalan - results.jumlahstock) AS jumlah,
				(results.satuan_kg_suratjalan - results.satuan_kg_stock) AS satuan_kg,
				results.typebarang,results.warna,results.kodewarna,results.satuan,
				results.harga_customer,results.harga_greige,results.harga_celupan,results.harga_matang,				
				results.terimagudang
		 FROM
			(SELECT 
     		     a.nopo AS nopo,
     		     DATE_FORMAT(a.tanggal,'%d-%m-%Y') AS tanggal,
     		     a.kodebarang AS kodebarang,
     		     c.namasupplier AS supplier,
     		     e.itemid AS id,
     		     b.jumlah AS jumlahorder,
     		     b.satuan_kg AS satuan_kg_order,
     		     (SELECT SUM(qty) FROM orderitemterima WHERE itemid IN 
					(SELECT orderitemterima.itemid FROM PO
					 INNER JOIN OrderItemDetail ON po.kodebarang = orderitemdetail.kodebarang 
					 INNER JOIN supplier ON po.idSupplier = supplier.idSupplier
					 LEFT JOIN gudang ON po.nopo = gudang.nopo
					 LEFT JOIN stockgudang
					 ON orderitemdetail.id = stockgudang.itemid AND orderitemdetail.kodebarang = stockgudang.kodebarang
					 LEFT JOIN orderitemterima ON orderitemdetail.kodebarang = orderitemterima.kodebarang
					 WHERE	orderitemterima.itemid = e.itemid
					 AND	po.kodebarang IN (SELECT kodebarang FROM orderitemterima)
					 AND	po.tujuan = 'PT BSA'
					 GROUP BY po.nopo, orderitemdetail.id)) AS jumlahsuratjalan,
				(SELECT SUM(satuan_kg) FROM orderitemterima WHERE itemid IN 
					(SELECT orderitemterima.itemid FROM PO
					 INNER JOIN OrderItemDetail ON po.kodebarang = orderitemdetail.kodebarang 
					 INNER JOIN supplier ON po.idSupplier = supplier.idSupplier
					 LEFT JOIN gudang ON po.nopo = gudang.nopo
					 LEFT JOIN stockgudang
					 ON orderitemdetail.id = stockgudang.itemid AND orderitemdetail.kodebarang = stockgudang.kodebarang
					 LEFT JOIN orderitemterima ON orderitemdetail.kodebarang = orderitemterima.kodebarang
					 WHERE	orderitemterima.itemid = e.itemid
					 AND	po.kodebarang IN (SELECT kodebarang FROM orderitemterima)
					 AND	po.tujuan = 'PT BSA'
					 GROUP BY po.nopo, orderitemdetail.id)) AS satuan_kg_suratjalan,
     		     (SELECT SUM(jumlah) FROM stockgudang WHERE itemid IN 
					(SELECT orderitemdetail.id FROM PO
					 INNER JOIN OrderItemDetail ON po.kodebarang = orderitemdetail.kodebarang 
					 INNER JOIN supplier ON po.idSupplier = supplier.idSupplier
					 LEFT JOIN gudang ON po.nopo = gudang.nopo
					 LEFT JOIN stockgudang
					 ON orderitemdetail.id = stockgudang.itemid AND orderitemdetail.kodebarang = stockgudang.kodebarang
					 LEFT JOIN orderitemterima ON orderitemdetail.kodebarang = orderitemterima.kodebarang
					 WHERE	stockgudang.itemid = e.itemid
					 AND	po.kodebarang IN (SELECT kodebarang FROM orderitemterima)
					 AND	po.tujuan = 'PT BSA'
					 GROUP BY po.nopo, orderitemdetail.id)) AS jumlahstock,
				(SELECT SUM(satuan_kg) FROM stockgudang WHERE itemid IN 
					(SELECT orderitemdetail.id FROM PO
					 INNER JOIN OrderItemDetail ON po.kodebarang = orderitemdetail.kodebarang 
					 INNER JOIN supplier ON po.idSupplier = supplier.idSupplier
					 LEFT JOIN gudang ON po.nopo = gudang.nopo
					 LEFT JOIN stockgudang
					 ON orderitemdetail.id = stockgudang.itemid AND orderitemdetail.kodebarang = stockgudang.kodebarang
					 LEFT JOIN orderitemterima ON orderitemdetail.kodebarang = orderitemterima.kodebarang
					 WHERE	stockgudang.itemid = e.itemid
					 AND	po.kodebarang IN (SELECT kodebarang FROM orderitemterima)
					 AND	po.tujuan = 'PT BSA'
					 GROUP BY po.nopo, orderitemdetail.id)) AS satuan_kg_stock,
			 	 b.typebarang,b.warna,b.kodewarna,b.satuan,
				 b.harga_customer,b.harga_greige,b.harga_celupan,b.harga_matang,				
				 b.terimagudang
     		     	     
	     		 FROM PO AS a
	     		 INNER JOIN OrderItemDetail AS b ON a.kodebarang = b.kodebarang 
	     		 INNER JOIN supplier AS c ON a.idSupplier = c.idSupplier     		 
	     		 LEFT JOIN gudang AS d ON a.nopo = d.nopo
	     		 LEFT JOIN stockgudang AS e ON b.id = e.itemid AND b.kodebarang = e.kodebarang
	     		 LEFT JOIN orderitemterima AS f ON b.kodebarang = f.kodebarang
	     		 WHERE	e.kodebarang = '$kodebarang'
	     		 AND	e.itemid = $itemid
			 AND	a.tujuan = 'PT BSA'
	     		 GROUP BY a.nopo, b.id
	     		 ORDER BY a.nopo) AS results";
		$query = $this->db->query($sql);
		return $query->result();
	}
	function weightshrinking($itemid) {
		$sql = "SELECT
				(IFNULL((SELECT quantity FROM pocustomerdetail WHERE itemid = $itemid),0) -
				 IFNULL((SELECT SUM(whd.quantity) FROM warehousedetail whd WHERE itemid = $itemid),0)) AS jumlahblmditerima,
				(IFNULL((SELECT weight FROM pocustomerdetail WHERE itemid = $itemid),0) -
				 IFNULL((SELECT SUM(whd.weight) FROM warehousedetail whd WHERE itemid = $itemid),0)) AS susut_satuan_kg
		";
		$query = $this->db->query($sql);
		return $query->result();
	}
	function getmesin() {
		$sql = "SELECT keterangan as mesin FROM Others WHERE kategori = 'mesin' AND kode = 'celupan'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// added by david
	function getpocno($pocid) {
		$sql = "SELECT pocno FROM pocustomer WHERE pocid = $pocid";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->pocno;
	}

	// added by david
	function getpono($poid) {
		$sql = "SELECT pono FROM purchaseorder WHERE poid = $poid";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->pono;
	}

	// addedd by david
	function getpocid($ordercode) {
		$sql = "SELECT pocid FROM pocustomer WHERE ordercode = '$ordercode' LIMIT 0,1";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->pocid;
	}

	function getpoid_bywhid($whid){
		$sql = "SELECT sj.poid FROM suratjalan sj, warehouse wh WHERE wh.whid = '$whid' AND sj.sjid = wh.sjid";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->poid;
	}

	// added by david
	function getpoid($pono) {
		$sql = "SELECT poid FROM purchaseorder WHERE pono = '$pono' LIMIT 0,1";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->poid;
	}

	function getnoorder($kode) {
		$sql = "SELECT noorder as kode FROM OrderCustomer WHERE kodebarang = '$kode'";
		 $query = $this->db->query($sql);
		 foreach ($query->result() as $row)
		 {
		    return $row->kode;
		 }
	}
	/* edited at 17/07/2013 */
	function showpo($nopo, $potype) {
		if($potype == "pocelupan" || $potype == "pomatang") // ada 2 hasil select handfeel. jd handfeel diambil dari $table a, bkn dari orderitem c
			$sql = "SELECT a.*,b.contactSupplier,b.namaSupplier,c.noorder, c.kodebarang, c.jenisbahan, c.proses, c.compact, c.statusbarang FROM po a, supplier b, orderitem c WHERE a.nopo = '$nopo' AND a.kodebarang = c.kodebarang AND a.idsupplier = b.idsupplier";
		elseif($potype == "pogreige") // ngga ada yg bentrok
			$sql = "SELECT a.nopo, a.tanggal, a.idSupplier, a.kodebarang, a.gramasigreige, a.gramasimatang, a.mesin, a.jenispembayaran, a.tambahbayar, a.tujuan, a.catatan, a.diskon, a.mengetahui, a.statuspo, a.statusbayar, a.batal, a.createddate, a.createdby, b.contactSupplier, b.namaSupplier,c.* FROM po a, supplier b, orderitem c WHERE a.nopo = '$nopo' AND a.kodebarang = c.kodebarang AND a.idsupplier = b.idsupplier";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function newshowpo($nopo, $potype) {
		if($potype == "pocelupan" || $potype == "pomatang") // ada 2 hasil select handfeel. jd handfeel diambil dari $table a, bkn dari orderitem c
			$sql = "SELECT a.*,b.contactSupplier,b.namaSupplier,c.pocno, c.ordercode, c.fabrictype, c.process, c.compact FROM purchaseorder a, supplier b, pocustomer c WHERE a.pono = '$nopo' AND a.pocid = c.pocid AND a.supplierid = b.idsupplier";
		elseif($potype == "pogreige") // ngga ada yg bentrok
			$sql = "SELECT a.pono, a.podate, a.supplierid, c.ordercode, a.grammagegreige, a.grammagefinal, a.machine, a.paymentmethod, a.paymentaddition, a.destination, a.notes, a.discount, a.approvedby, a.postatus, b.contactSupplier, b.namaSupplier,c.* FROM purchaseorder a, supplier b, pocustomer c WHERE a.pono = '$nopo' AND a.pocid = c.pocid AND a.supplierid = b.idsupplier";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getsupplierbyid($idSupplier) {
		$this->db->where('idSupplier', $idSupplier);
		$query = $this->db->get('Supplier');
		return $query->row();
	}
	function getdetailsupplier($tujuan) {
		$sql = "SELECT * FROM supplier WHERE namaSupplier = '$tujuan'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getsupplieridname($nopo,$table) {
		$sql = "SELECT DISTINCT po.supplierid as idsupplier,
			    s.namaSupplier as namasupplier
				from purchaseorder po
				left JOIN supplier s on s.idSupplier = po.supplierid
				where po.pono ='$nopo'";

		$query = $this->db->query($sql);
		return $query->result_array();		
	}
	function qtypovsqtyterima($kodebarang,$kodewarna,$qty) {
		$sql = "SELECT * FROM OrderItemDetail WHERE kodebarang = '$kodebarang' AND kodewarna = '$kodewarna' AND jumlah = '$qty'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	function getcustomercelup($nopo) {
		$sql = "SELECT a.namaCustomer as nama FROM customer a, po b, Ordercustomer c WHERE a.idCustomer = c.idCustomer AND b.kodebarang = c.kodebarang AND b.nopo = '$nopo'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nama;
		}
	}
	function getcustomermatang($nopo) {
		$sql = "SELECT a.namaCustomer as nama FROM customer a, po b, OrderCustomer c WHERE b.kodebarang = c.kodebarang AND a.idCustomer = c.idCustomer AND b.nopo = '$nopo'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nama;
		}
	}	
	/* Gudang */
	/* Faktur Penjualan */
	function getnofakturpenjualan($kodeawal) {
		
		$this->db->select('nofaktur');
		$this->db->where('LEFT(nofaktur, 1) =', $kodeawal);
		$this->db->order_by("nofaktur", "desc");
		$query = $this->db->get('FakturPenjualan', 1, 0);
		foreach ($query->result() as $row)
		{
		   return $row->nofaktur;
		}
	}
	function getnosuratjalanbynoorder($noorder) {
		$this->db->select('nosuratjalan');
		$this->db->where('noorder', $noorder);
		$query = $this->db->get("SuratJalanCustomer");
		foreach ($query->result() as $row) {
			return $row->nosuratjalan;
		}
	}
	function simpanfakturpenjualan($datafakturpenjualan){
		$this->db->insert('FakturPenjualan', $datafakturpenjualan);
	}
	function gefakturpenjualanbyno($nofaktur) {
		$this->db->where('nofaktur', $nofaktur);
		$query = $this->db->get('FakturPenjualan');
		foreach ($query->result() as $row) {
			return $row;
		}
	}
	function getnosuratjalancust() {
		$sql = "SELECT nosuratjalan as sj FROM fakturpenjualan order by nosuratjalan desc LIMIT 0,1";
		 $query = $this->db->query($sql);
		 foreach ($query->result() as $row)
		 {
		    return $row->sj;
		 }
	}
	public function getdetailfaktur($noorder) {
		$sql = "
		SELECT c.id as itemid, a.noorder as noorder,a.idCustomer as idCustomer,
		a.kodebarang as kodebarang, a.mengetahui as menyetujui,a.diskon as diskon, 
		a.perusahaan as perusahaan, a.idSales as sales, a.jenispembayaran as jenispembayaran, 
		a.lamabayar as termin, b.jenisbahan as jenisbahan,c.nosuratjalan as nosuratjalan, 
		DATE_FORMAT(c.tanggal,'%d-%m-%Y') as tanggalsj,DATE_FORMAT(a.tanggal,'%d-%m-%Y') as tanggalorder 
		FROM ordercustomer a, orderitem b, fakturpenjualan c, orderitemdetail d
		WHERE a.noorder = '$noorder' AND a.kodebarang = b.kodebarang AND c.noorder = a.noorder
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getdetailcetakfaktur($nofaktur) {
		$sql = "
		SELECT a.noorder as noorder,a.idCustomer as idCustomer, a.kodebarang as kodebarang, d.namaCustomer as namacustomer, a.kodebarang as kodebarang,
		a.mengetahui as menyetujui,a.diskon as diskon,a.perusahaan as perusahaan, c.nosuratjalan as nosuratjalan,
		a.jenispembayaran as jenispembayaran, a.lamabayar as termin, b.jenisbahan as jenisbahan,
		c.nofaktur as nofaktur, DATE_FORMAT(c.tanggal,'%d-%m-%Y') as tanggal,DATE_FORMAT(a.tanggal,'%d-%m-%Y') as tanggalorder, 
		c.catatan as catatan, c.createdby as createdby
		FROM ordercustomer a, orderitem b, fakturpenjualan c, customer d
		WHERE c.nofaktur = '$nofaktur' AND a.kodebarang = b.kodebarang AND c.noorder = a.noorder AND d.idCustomer = a.idCustomer
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function checkrow($kodebarang,$type) {
		$sql = "SELECT * FROM OrderItemDetail WHERE kodebarang = '$kodebarang' AND typebarang = '$type'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	public function showjumlahharga($kodebarang,$warna,$harga,$type) {
		$sql = "SELECT 
					IF(NOT ISNULL(jumlah),jumlah,'-') as jumlah, if($harga = '' ,'-',$harga) as harga FROM orderitemdetail WHERE kodebarang = '$kodebarang' AND warna = '$warna' AND typebarang = '$type'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* Admin Others */
	function getkategori() {
		$sql = "SELECT DISTINCT(kategori) as kategori FROM Others";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	function getlastothersid() {
		$sql = "SELECT id FROM Others ORDER BY id DESC LIMIT 1";
		$query = $this->db->query($sql);

		return $query->row();
	}
	function getothersbyid($id) {
		$sql = "SELECT id, kategori, kode, keterangan FROM Others WHERE id = ?";
		$query = $this->db->query($sql, $id);

		return $query->row();
	}

	/* slip cetak */
	function getdistinctwarnaitemdetail($kodebarang, $harga){
		$this->db->select('OrderItemDetail.id as id, OrderItemDetail.warna AS warna, OrderItemDetail.kodewarna AS kodewarna, OrderItemDetail.'.$harga.' as harga, OrderItemDetail.typebarang as typebarang, OrderItemDetail.jumlah as jumlah');
		$this->db->from('OrderItemDetail');
		$this->db->join('OrderItem', 'OrderItem.kodebarang = OrderItemDetail.kodebarang');

		$this->db->where('OrderItemDetail.kodebarang', $kodebarang);

		$this->db->group_by(array('OrderItemDetail.warna', 'OrderItemDetail.kodewarna', 'OrderItemDetail.typebarang', 'OrderItemDetail.'.$harga));
		$this->db->order_by('OrderItemDetail.id');
		$query = $this->db->get();
		return $query->result();
	}
	function newgetdistinctwarnaitemdetail($kodebarang, $harga){
		$this->db->select('pocustomerdetail.itemid as id, pocustomerdetail.color AS warna, pocustomerdetail.colorcode AS kodewarna, pocustomerdetail.'.$harga.' as harga, pocustomerdetail.itemtype as typebarang, pocustomerdetail.quantity as jumlah');
		$this->db->from('pocustomerdetail');
		$this->db->join('pocustomer', 'pocustomer.pocid = pocustomerdetail.pocid');

		$this->db->where('pocustomer.ordercode', $kodebarang);

		$this->db->group_by(array('pocustomerdetail.color', 'pocustomerdetail.colorcode', 'pocustomerdetail.itemtype', 'pocustomerdetail.'.$harga));
		$this->db->order_by('pocustomerdetail.itemid');
		$query = $this->db->get();
		return $query->result();
	}
	function getdistinctwarnaitemdetail_forfaktur($noorder, $harga){
		$this->db->select('fakturpenjualanitem.itemid as id, OrderItemDetail.warna AS warna, OrderItemDetail.kodewarna AS kodewarna, OrderItemDetail.'.$harga.' as harga, OrderItemDetail.typebarang as typebarang, fakturpenjualanitem.qty as jumlah');
		$this->db->from('OrderCustomer');
		$this->db->join('fakturpenjualan', 'fakturpenjualan.noorder = OrderCustomer.noorder');
		$this->db->join('fakturpenjualanitem', 'fakturpenjualanitem.fakturid = fakturpenjualan.id');
		$this->db->join('OrderItemDetail', 'OrderItemDetail.id = fakturpenjualanitem.itemid');
		$this->db->where('fakturpenjualan.noorder', $noorder);
		$this->db->where('OrderCustomer.noorder', $noorder);

		$this->db->group_by(array('OrderItemDetail.warna', 'OrderItemDetail.kodewarna', 'OrderItemDetail.typebarang', 'OrderItemDetail.'.$harga));
		$this->db->order_by('OrderItemDetail.id');
		$query = $this->db->get();
		return $query->result();
	}

	function getitemdetailbywarnafortype($kodebarang, $warna, $harga, $harga_value, $type) {
		$sql =	"SELECT IF(ISNULL(jumlah), 0, SUM(jumlah)) AS jumlah," .
				"IF($harga = '', 0, $harga) AS harga " .
				" FROM orderitemdetail a, orderitem b " .
				"WHERE a.kodebarang = b.kodebarang AND a.kodebarang = ? " .
				"AND a.typebarang = ? AND a.warna = ? " .
				"AND a.$harga = ?";
		$data[] = $kodebarang;
		$data[] = $type;
		$data[] = $warna;
		$data[] = $harga_value;
		$query = $this->db->query($sql, $data);
		return $query->row();
	}
	

	function getitemdetailbytype($kodebarang, $harga, $type) {
		$sql =	"SELECT SUM(IF(ISNULL(jumlah), 0, jumlah)) AS jumlah," .
				"IF($harga = '', 0, $harga) AS harga " .
				" FROM orderitemdetail a, orderitem b " .
				"WHERE a.kodebarang = b.kodebarang AND a.kodebarang = ? " .
				"AND a.typebarang = ? " .
				"GROUP BY a.harga_greige";
		$data[] = $kodebarang;
		$data[] = $type;
		$query = $this->db->query($sql, $data);
		return $query->result_array();
	}

	/* edit all po */
	function getordercustomer($pocid) 
	{
		$sql = "SELECT 
					a.pocid,
					a.pocno as noorder,
					a.createddate as tanggal,
					a.customerid as idCustomer,
					c.namacustomer as namaCustomer,
					a.ordercode as kodebarang,
					a.grammagefinal as gramasimatang,
					a.fabrictype as jenisbahan,
					a.compact,
					a.process as proses,
					a.handfeel,
					a.discount as diskon,
					a.paymentmethod as jenispembayaran,
					a.paymentperiod as lamabayar,
					a.company as perusahaan,
					a.note as catatan
				FROM 
					pocustomer a,
					customer c
				WHERE
					a.customerid = c.idCustomer AND
					a.pocid = $pocid
					";
		$query = $this->db->query($sql, $pocid);
		return $query->row();
	}
	function getitemordercustomer($pocid) {
		$sql = "SELECT 
					itemid AS id,
					itemtype AS tipebarang,
					colorcode AS kodewarna,
					color AS warna,
					quantity AS jumlah,
					unittype,
					weight,
					pricecustomer AS harga_customer,
					pricecelupan,
					pricematang,
					weightshrinkage
				FROM 
					pocustomerdetail
				WHERE 
					pocid = ?
				";
		$query = $this->db->query($sql, $pocid);
		return $query->result_array();
	}
	function getitemreturcustomer($kodebarang) {
		$sql = "SELECT b.* FROM OrderItemDetail b, OrderItemRetur a WHERE b.id = a.itemid AND b.kodebarang = ?";
		$query = $this->db->query($sql, $kodebarang);
		return $query->result_array();
	}
	/*
	function getpogreige($noorder) {
		$this->db->where('nopo', $noorder);
		$query = $this->db->get('pogreige');
		return $query->row();
	}
	*/
	function getnoordercustomerbykodebarang($kodebarang) {
		$this->db->select('noorder');
		$this->db->where('kodebarang', $kodebarang);
		$query = $this->db->get('OrderCustomer');
		return $query->row();
	}
	/*
	function getpoitemgreigeforedit($noorder) {
		$sql = "SELECT a.*,b.* FROM OrderItem a, POGreige b WHERE a.kodebarang = b.kodebarang AND b.nopo = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getpoitemcelup($noorder) {
		$sql = "SELECT a.*,b.* FROM OrderItem a, POCelupan b WHERE a.kodebarang = b.kodebarang AND b.nopo = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getpoitemmatang($noorder) {
		$sql = "SELECT a.*,b.* FROM OrderItem a, POMatang b WHERE a.kodebarang = b.kodebarang AND b.nopo = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	*/
	
	/* 11/07/2013 */
	
	function getkodesupplier($type) {
		$sql = "SELECT initialKodeBarang as kode FROM `supplier` WHERE keteranganSupplier like '%$type%'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getpocandtermin($kodebarang) {
		$sql = "SELECT a.lamabayar as lamabayar, a.noorder as poc, a.diskon as diskon FROM OrderCustomer a, OrderItem b WHERE a.noorder = b.noorder and b.kodebarang = '$kodebarang'";
		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	function detailbarangoutstanding($kodebarang) {
		$sql = "SELECT 
		 		a.id as id,
				a.typebarang as typebarang,
				a.kodebarang as kodebarang,
				a.kodewarna as kodewarna,
				a.warna as warna,
				(a.jumlah- IF(ISNULL(b.jumlah),0,b.jumlah)) jumlah,
				a.satuan as satuan,
				a.satuan_kg as satuan_kg,
				a.penyusutanberat as penyusutanberat,
				a.terimagudang as terimagudang,
				a.tanggalterima as tanggalterima
				FROM OrderItemDetail a LEFT JOIN orderitemterimaqty b 
				ON b.gudangid = a.id
				WHERE a.kodebarang = '$kodebarang' AND a.terimagudang > 1 ";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* 12/07/2013 */
	public function getparsialoutstandingitembyitemid($gudangid) {
		$sql = "SELECT b.jumlah AS jumlahawal, b.terimagudang AS terimagudang, c.jumlah AS jumlahditerima
				FROM orderitemdetail b, orderitemterimaqty c
				WHERE b.id = '".$gudangid."' AND b.id = c.gudangid AND b.terimagudang = 2";
		$query = $this->db->query($sql);
		return $query->row();
	}
	
	/***** 18/07/2013 ******/
	
	function showorderitem($kodebarang) {
		//$sql = "SELECT * FROM orderitem WHERE kodebarang = '$kodebarang'";
		$sql = "SELECT *, fabrictype as jenisbahan FROM pocustomer WHERE ordercode = '$kodebarang'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getnoreturcustomer() {
		$sql = "SELECT noretur as nopo FROM returcustomer ORDER BY noretur DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nopo;
		}
	}
	function getreturcustomer($noretur) {
		$sql = "SELECT * FROM returcustomer WHERE noretur = '$noretur'";
		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	/*
	function getdetailreturcustomer($noretur) {
		$sql = "SELECT a.tipebarang as tipebarang, a.warna as warna, a.satuan as satuan, b.qtyretur as retur, b.qtykiloretur as returkg FROM a.orderitemdetail a, returcustomeritem b WHERE a.id = b.itemid AND noretur = '$noretur'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	*/
	/* 23/07/2013 */
	
	function detailbarangoutstandingbaru($kodebarang,$type) {
		$sql = "SELECT 
		 		a.id as id,
				a.typebarang as typebarang,
				a.kodebarang as kodebarang,
				a.kodewarna as kodewarna,
				a.warna as warna,
				b.qty as qtyterima,
				a.jumlah - b.qty as jumlah,
				a.satuan as satuan,
				b.satuan_kg as satuan_kgterima,
				a.satuan_kg as satuan_kgterima_ori,
				a.satuan_kg - b.satuan_kg as satuan_kg
				FROM OrderItemDetail a, orderitemterima b 
				WHERE a.kodebarang = '$kodebarang' AND a.id = b.itemid AND a.jumlah <> b.qty AND b.typeterima = '$type'";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getnoclaimpembelian($id) {
		$sql = "SELECT noclaim as noclaim FROM claimpembelian WHERE left(noclaim,2) = '$id' ORDER BY noclaim DESC  LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->noclaim;
		}
	}
	function getnoreturpabrik($id) {
		$sql = "SELECT noretur as noretur FROM returpabrik WHERE left(noretur,2) = '$id' ORDER BY noretur DESC  LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->noretur;
		}
	}
	
	function olddetailreturpembelian($noretur) {
		$sql = "SELECT a.noretur as noretur, a.nopo as nopo, c.kodebarang as kodebarang, date_format(a.tanggal,'%d-%m-%Y') as tanggal, a.keterangan as keterangan, b.namaSupplier as namaSupplier, a.createdby as dibuatoleh FROM returpabrik a, supplier b, po c WHERE b.idSupplier = c.idSupplier AND c.nopo = a.nopo AND a.noretur = '$noretur' ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function newdetailreturpembelian($noretur) {
		$sql = "SELECT a.frid as id,
				a.frno as noretur,
				date_format(a.createddate,'%d-%m-%Y') as tanggal,
				a.notes as keterangan,
				b.pono as nopo,
				c.namaSupplier as namaSupplier,
				a.createdby as dibuatoleh,
				d.ordercode as kodebarang
		FROM factoryreturn a, purchaseorder b, supplier c, pocustomer d, suratjalan f
		WHERE a.frno = '$noretur' AND a.sjid = f.sjid AND f.poid = b.poid AND b.supplierid = c.idSupplier AND b.pocid = d.pocid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailreturpenjualan($noretur,$nofaktur) {
		$sql = "SELECT a.noretur as noretur, date_format(a.tanggal,'%d-%m-%Y') as tanggal, b.namaCustomer as namaCustomer, a.createdby as dibuatoleh, c.nofaktur as nofaktur, a.keterangan FROM returcustomer a, customer b, fakturpenjualan c, ordercustomer d WHERE b.idCustomer =d.idCustomer AND c.nofaktur = a.nofaktur AND a.noretur = '$noretur' AND c.nofaktur = '$nofaktur' AND d.noorder = c.noorder";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function newdetailreturpenjualan($noretur,$nofaktur) {
		$sql = "SELECT a.crno as noretur, date_format(a.crdate,'%d-%m-%Y') as tanggal, b.namaCustomer as namaCustomer, a.createdby as dibuatoleh, c.invoiceno as nofaktur, a.notes as keterangan 
		FROM customerreturn a, customer b, invoice c, pocustomer d, customerreturndetail e, pocustomerdetail f
		WHERE a.crid = e.crid AND e.itemid = f.itemid AND f.pocid = d.pocid AND d.customerid = b.idCustomer AND c.invoiceid = a.invoiceid AND a.crno = '$noretur' AND c.invoiceno = '$nofaktur' ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailreturpenjualan_bycrid($crid) {
		$sql = "SELECT a.crno as noretur, date_format(a.crdate,'%d-%m-%Y') as tanggal, b.namaCustomer as namaCustomer, a.createdby as dibuatoleh, c.invoiceno as nofaktur, a.notes as keterangan 
		FROM customerreturn a, customer b, invoice c, pocustomer d, customerreturndetail e, pocustomerdetail f
		WHERE a.crid = '$crid' AND e.itemid = f.itemid AND f.pocid = d.pocid AND d.customerid = b.idCustomer AND c.invoiceid = a.invoiceid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailreturbarangpembelian($noretur) {
		$sql = "SELECT a.*,b.noretur, b.qty AS qtyretur, b.itemid AS itemid,
				b.satuan_kg AS satuan_kg_retur,b.fromtable AS fromtable, c.*, g.diskon as diskon
				FROM orderitemdetail a
				INNER JOIN orderitem c 
				ON a.kodebarang = c.kodebarang				
				INNER JOIN ordercustomer g
				ON c.noorder = g.noorder
				LEFT JOIN returpabrikitem b
				ON a.id = b.itemid
				LEFT JOIN returpabrik e
				ON b.noretur = e.noretur
				WHERE e.noretur = '$noretur'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function newdetailreturbarang($noretur) {
		//$sql = "SELECT a.*,b.*,c.* FROM orderitemdetail a, returcustomeritem b, orderitem c WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.noretur = '$noretur'";
		/*$sql1 = "SET @sql = CONCAT(
					'SELECT ', 
					(SELECT REPLACE(REPLACE(GROUP_CONCAT(COLUMN_NAME), 'weight,', ''), 'pocustomer.ordercode,','')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pocustomerdetail'
			    	AND TABLE_SCHEMA = 'bsa_db'), 
					',',
			    	(SELECT REPLACE(GROUP_CONCAT(COLUMN_NAME), 'ordercode,', '')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'pocustomer'
			    	AND TABLE_SCHEMA = 'bsa_db'),
					' , a.unittype as satuan, a.weight as satuan_kg, a.kodebarang, b.* FROM pocustomerdetail a, customerreturndetail b, pocustomer c, customerreturn d ',
					' WHERE a.itemid = b.itemid AND a.pocid = c.pocid AND b.crid = d.crid AND d.crno = \"$noretur\"');";
		$sql2 = "PREPARE stmt1 FROM @sql;";
		$sql3 = "EXECUTE stmt1;";
		$this->db->query($sql1);
		$this->db->query($sql2);
		$query = $this->db->query($sql3);
		return $query->result_array();
		*/
		$sql = "SELECT a.itemid as id, a.unittype as satuan, a.weight as satuan_kg,c.fabrictype, c.ordercode as kodebarang, a.itemtype as typebarang, a.color as warna, a.pricecustomer as harga_customer, b.*
		FROM pocustomerdetail a, customerreturndetail b, pocustomer c, customerreturn d
		WHERE a.itemid = b.itemid AND a.pocid = c.pocid AND b.crid = d.crid AND d.crno = '$noretur'
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailreturbarang_bycrid($crid) {
		$sql = "SELECT a.itemid as id, a.unittype as satuan, a.weight as satuan_kg,c.fabrictype, c.ordercode as kodebarang, a.itemtype as typebarang, a.color as warna, a.pricecustomer as harga_customer, b.*
		FROM pocustomerdetail a, customerreturndetail b, pocustomer c, customerreturn d
		WHERE a.itemid = b.itemid AND a.pocid = c.pocid AND b.crid = d.crid AND d.crid = '$crid'
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function newdetailreturbarangpembelian($noretur){
		$sql = "SELECT c.*,a.frno, b.frdid as id, b.quantity AS qtyretur, b.itemid AS itemid,
				b.weight AS satuan_kg_retur, d.*
				FROM factoryreturn a
				INNER JOIN factoryreturndetail b 
				ON a.frid = b.frid				
				INNER JOIN pocustomerdetail c
				ON b.itemid = c.itemid
				INNER JOIN pocustomer d
				ON c.pocid = d.pocid
				WHERE a.frno = '$noretur'";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	function laporansuratjalancustomer(){	
		$sql = "SELECT poc.pocno as noorder, 
				       i.invoicesjno as nosuratjalan,
				       date_format(i.invoicedate,'%d-%m-%Y') as tanggal,
				       poc.ordercode as kodebarang,
				       c.namaCustomer as namacustomer,
				       poc.fabrictype as jenisbahan,
				       CONCAT('<pre>', (select GROUP_CONCAT(d.itemtype SEPARATOR '\n') from pocustomerdetail d where d.pocid = poc.pocid), '</pre>') as typebarang
				from invoice i
				left JOIN suratjalan sj on sj.sjid = i.sjid
				left JOIN purchaseorder po on po.poid = sj.poid
				left join pocustomer poc on poc.pocid = po.pocid
				left join customer c on poc.customerid = c.idCustomer";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function ambilwarnasuratjalancustomer($typebarang, $kodebarang){
		$sql = "SELECT a.warna as warna
		FROM orderitemdetail a where a.typebarang = '$typebarang' AND a.kodebarang = '$kodebarang';";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function ambilwarna($itemid, $kodebarang){
		$sql = "SELECT a.warna as warna
		FROM orderitemdetail a WHERE a.id = '$itemid' AND a.kodebarang = '$kodebarang'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function laporanclaimpembelian(){
		$sql = "SELECT a.itemid as claimitemid, a.noclaim as noclaim, a.qty as qty, a.satuan_kg as satuankg, date_format(b.tanggal,'%d-%m-%Y') as tanggal, b.kodebarang as kodebarang, c.id as orderitemid, c.harga_greige as harga_greige, c.harga_celupan as harga_celupan, c.harga_matang as harga_matang, d.nopo as nopo, d.diskon as diskon, f.namaCustomer as namacustomer, g.namaSupplier as namasupplier, h.jenisbahan as jenisbahan
		FROM claimpembelianitem a, claimpembelian b, orderitemdetail c, po d, ordercustomer e, customer f, supplier g, orderitem h
		WHERE a.noclaim = b.noclaim AND c.id = a.itemid AND d.nopo = b.nopo AND e.kodebarang = b.kodebarang AND e.statusorder != 'Batal' AND f.idCustomer = e.idCustomer AND g.idSupplier = d.idSupplier AND h.kodebarang = b.kodebarang ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* 10/10/2013 */
	function detailclaimpembelian($noretur) {
		$sql = "SELECT a.noclaim as noclaim, c.nopo as nopo, date_format(a.tanggal,'%d-%m-%Y') as tanggal, a.keterangan as keterangan, b.namaSupplier as namaSupplier, c.diskon as diskon, a.createdby as dibuatoleh FROM claimpembelian a, supplier b, po c WHERE b.idSupplier = c.idSupplier AND c.nopo = a.nopo AND a.noclaim = '$noretur' ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function newdetailclaimpembelian($noretur){
		$sql = "SELECT a.cno as noclaim, c.pono as nopo, date_format(a.cdate,'%d-%m-%Y') as tanggal, a.notes as keterangan, b.namaSupplier as namaSupplier, c.discount as diskon, a.createdby as dibuatoleh
		FROM claim a, supplier b, purchaseorder c, customerreturn d, invoice e, suratjalan sj
		WHERE a.cno = '$noretur' AND a.crid = d.crid AND d.invoiceid = e.invoiceid AND e.sjid = sj.sjid AND sj.poid = c.poid AND c.supplierid = b.idSupplier ";
		$query = $this->db->query($sql);
		return $query->result_array();
		
	}
	public function detailclaimbarangpembelian($noretur) {
		$sql = "SELECT a.*,b.noclaim, b.itemid as itemid,c.kodebarang as kodebarang, b.qty AS qtyretur, b.satuan_kg AS satuan_kg_retur,c.* FROM orderitemdetail a, claimpembelianitem b, orderitem c WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.noclaim = '$noretur'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function newdetailclaimbarangpembelian($noretur) {
		$sql = "SELECT a.*,c.cno as noclaim, d.quantity AS qtyretur, d.weight AS satuan_kg_retur,b.*, d.cdid as id
		FROM pocustomerdetail a, pocustomer b, claim c, claimdetail d 
		WHERE c.cno = '$noretur' AND c.cid = d.cid AND d.itemid = a.itemid AND a.pocid = b.pocid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailreturbarang($noretur) {
		//$sql = "SELECT a.*,b.*,c.* FROM orderitemdetail a, returcustomeritem b, orderitem c WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.noretur = '$noretur'";
		$sql1 = "SET @sql = CONCAT(
					'SELECT ', 
					(SELECT REPLACE(REPLACE(GROUP_CONCAT(COLUMN_NAME), 'satuan_kg,', ''), 'kodebarang,','')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'orderitemdetail'
			    	AND TABLE_SCHEMA = 'bsadb-hakakses'), 
					',',
			    	(SELECT REPLACE(GROUP_CONCAT(COLUMN_NAME), 'kodebarang,', '')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'orderitem'
			    	AND TABLE_SCHEMA = 'bsadb-hakakses'),
					' ,a.id as itemid, a.satuan, a.satuan_kg, a.kodebarang, b.* FROM orderitemdetail a, returcustomeritem b, orderitem c ',
					' WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.noretur = \"$noretur\"');";
		$sql2 = "PREPARE stmt1 FROM @sql;";
		$sql3 = "EXECUTE stmt1;";
		$this->db->query($sql1);
		$this->db->query($sql2);
		$query = $this->db->query($sql3);
		return $query->result_array();
	}
	public function detailreturbarang_groupbyitemid($itemid) {
		//$sql = "SELECT a.*,b.*,c.* FROM orderitemdetail a, returcustomeritem b, orderitem c WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.noretur = '$noretur'";
		$sql1 = "SET @sql = CONCAT(
					'SELECT ', 
					(SELECT REPLACE(REPLACE(GROUP_CONCAT(COLUMN_NAME), 'satuan_kg,', ''), 'kodebarang,','')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'orderitemdetail'
			    	AND TABLE_SCHEMA = 'bsa_db'), 
					',',
			    	(SELECT REPLACE(GROUP_CONCAT(COLUMN_NAME), 'kodebarang,', '')
			    	FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'orderitem'
			    	AND TABLE_SCHEMA = 'bsa_db'),
					' ,a.id as itemid, a.kodebarang, b.noretur, b.itemid, SUM(b.qty) as qty, SUM(b.satuan_kg) as satuan_kg FROM orderitemdetail a, returcustomeritem b, orderitem c ',
					' WHERE a.id = b.itemid AND a.kodebarang = c.kodebarang AND b.itemid = $itemid
					  GROUP BY b.itemid');";
		$sql2 = "PREPARE stmt1 FROM @sql;";
		$sql3 = "EXECUTE stmt1;";
		$this->db->query($sql1);
		$this->db->query($sql2);
		$query = $this->db->query($sql3);
		return $query->result();
	}
	
	/* 16/08/2013 */
	// function sumtotalqty($id,$type) {
	// 	$sql = "SELECT sum(qty) as jumlah FROM `orderitemterima` WHERE itemid = $id AND typeterima = '$type'";
	// 	$query = $this->db->query($sql);
	// 	foreach ($query->result() as $row)
	// 	{
	// 	   return $row->jumlah;
	// 	}
	// }
	// function sumtotalkg($id,$type) {
	// 	$sql = "SELECT sum(satuan_kg) as satuankg FROM `orderitemterima` WHERE itemid = $id AND typeterima = '$type'";
		
	// 	$query = $this->db->query($sql);
	// 	foreach ($query->result() as $row)
	// 	{
	// 	   return $row->satuankg;
	// 	}
	// }

	// modded by david ^
	function sumtotalqty($id,$type) {
		$sql = "SELECT
					SUM(a.quantity) AS jumlah
				FROM
					suratjalandetail a,
					suratjalan b
				WHERE
					a.itemid = $id
				AND
					a.sjid = b.sjid
				AND
					b.sjtype = '$type'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->jumlah;
		}
	}
	function sumtotalkg($id,$type) {
		$sql = "SELECT
					SUM(a.weight) AS satuankg
				FROM
					suratjalandetail a,
					suratjalan b
				WHERE
					a.itemid = $id
				AND
					a.sjid = b.sjid
				AND
					b.sjtype = '$type'";
		
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->satuankg;
		}
	}

	/* 03/1/2013 */
	function generalsuratjalancustomer($nosuratjalan) {
		$this->db->select('fakturpenjualan.*, fakturpenjualanitem.*, orderitemdetail.*, ordercustomer.idCustomer, ordercustomer.mengetahui, customer.namaCustomer');
		$this->db->from('fakturpenjualan');
		$this->db->join('fakturpenjualanitem', 'fakturpenjualan.id = fakturpenjualanitem.fakturid');
		$this->db->join('orderitemdetail', 'fakturpenjualanitem.itemid = orderitemdetail.id AND fakturpenjualanitem.kodebarang = orderitemdetail.kodebarang', 'left');
		$this->db->join('ordercustomer', 'fakturpenjualan.noorder = ordercustomer.noorder AND orderitemdetail.kodebarang = ordercustomer.kodebarang', 'left');
		$this->db->where('fakturpenjualan.nosuratjalan', $nosuratjalan);
		$this->db->group_by('fakturpenjualan.nosuratjalan');
		$this->db->join('customer', 'customer.idCustomer = ordercustomer.idCustomer');
		$query = $this->db->get();
		return $query->result_array();
	}

	function editsuratjalancustomeritem($nosuratjalan){
		
		$sql = "
			SELECT date_format(i.invoicedate,'%d-%m-%Y') as tanggal,
			       i.vehicleno as nokendaraan, 
			       i.drivername as namasupir, 
			       pocd.itemtype as typebarang,
			       i.invoicesjno as nosuratjalan,
			       pocd.itemid as id, 
			       i.invoiceid as fakturid,
			       poc.pocno as noorder,
			       id.quantity as qty,
			       id.weight as qtykg,
			       poc.ordercode as kodebarang,
			       pocd.color as warna,
			       pocd.pricecustomer as harga_customer, 
			       pocd.unittype as satuan, 
			       poc.fabrictype as jenisbahan, 
			       poc.discount as diskon,
			       poc.note as catatan,
			       poc.approvedby as mengetahui,
			       poc.createdby,
			       c.namaCustomer as customer,
			       id.invoicedetailid
			from pocustomerdetail pocd 
			left JOIN suratjalandetail sjd on sjd.itemid = pocd.itemid
			left join suratjalan sj on sj.sjid = sjd.sjid
			left join invoice i on i.sjid = sj.sjid
			left join pocustomer poc on poc.pocid = pocd.pocid
			left join invoicedetail id on id.itemid = pocd.itemid
			left join customer c on poc.customerid = c.idCustomer
			where pocd.pocid = (
				select poc.pocid
				from invoice i
				left JOIN suratjalan sj on sj.sjid = i.sjid
				left JOIN purchaseorder po on po.poid = sj.poid
				left join pocustomer poc on poc.pocid = po.pocid
				where i.invoicesjno = '$nosuratjalan'
			)
		";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailsuratjalancustomer($nosuratjalan) {
	
		$sql = "select   id.quantity as qty,
						 pocd.unittype as satuan,
						 pocd.itemtype as typebarang,
						 pocd.color as warna,
						 poc.fabrictype as jenisbahan, 
						 pocd.pricecustomer as harga_customer, 
						 id.weight as satuan_kg,
				       poc.discount as diskon
				from pocustomerdetail pocd 
				left JOIN suratjalandetail sjd on sjd.itemid = pocd.itemid
				left join suratjalan sj on sj.sjid = sjd.sjid
				left join invoice i on i.sjid = sj.sjid
				left join pocustomer poc on poc.pocid = pocd.pocid
				left join invoicedetail id on id.itemid = pocd.itemid
				left join customer c on poc.customerid = c.idCustomer
				where pocd.pocid = (
					select poc.pocid
					from invoice i
					left JOIN suratjalan sj on sj.sjid = i.sjid
					left JOIN purchaseorder po on po.poid = sj.poid
					left join pocustomer poc on poc.pocid = po.pocid
					where i.invoicesjno = '$nosuratjalan'
				)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function detailsuratjalan($kodebarang,$type) {
		$sql = "(SELECT
					a.typebarang AS typebarang,
					a.kodewarna AS kodewarna,
					a.warna AS warna,
					a.satuan AS satuan,
					b.nosuratjalan AS suratjalan,
					DATE_FORMAT(b.tanggal,'%d-%m-%Y') AS tanggal,
					b.createdby AS createdby,
					b.qty AS jumlah,
					b.satuan_kg AS satuan_kg
				FROM orderitemdetail a
				INNER JOIN orderitemterima b
				ON a.id = b.itemid
				AND a.kodebarang = b.kodebarang
				WHERE b.kodebarang = '$kodebarang'
				AND b.typeterima = '$type'
				ORDER BY a.id)

				UNION

				(SELECT
					a.typebarang AS typebarang,
					a.kodewarna AS kodewarna,
					a.warna AS warna,
					a.satuan AS satuan,
					c.nosuratjalan AS suratjalan,
					DATE_FORMAT(c.tanggal,'%d-%m-%Y') AS tanggal,
					c.createdby AS createdby,
					c.qty AS jumlah,
					c.satuan_kg AS satuan_kg 
				FROM orderitemdetail a
				INNER JOIN orderitemterima_retur c
				ON a.id = c.itemid
				AND a.kodebarang = c.kodebarang
				WHERE c.kodebarang = '$kodebarang'
				AND c.typeterima = '$type'
				ORDER BY a.id)";
		$query = $this->db->query($sql);
		return $query->result_array();		
	}
	/* 26/09/2013 */
	function detailsuratjalan_bynosj($nosuratjalan, $tipepo) {
		$sql = "SELECT
					a.itemid AS id,
					a.id_sj,
					d.noorder AS noorder,
					c.nopo AS nopo,
					DATE_FORMAT(a.tanggal,'%d/%m/%Y') AS tanggal,
					a.kodebarang AS kodebarang,
					a.nosuratjalan AS suratjalansupplier,
					b.namaSupplier AS supplier,
					a.typeterima AS tipepo,
					(SELECT typebarang FROM orderitemdetail WHERE kodebarang = a.kodebarang AND id = a.itemid) AS typebarang,	    
					(SELECT SUM(qty) AS qty FROM orderitemterima WHERE itemid=a.itemid AND nosuratjalan = a.nosuratjalan AND typeterima = '$tipepo') AS jumlah,
					(SELECT SUM(satuan_kg) AS kg_terima FROM orderitemterima WHERE itemid = a.itemid AND nosuratjalan = a.nosuratjalan AND typeterima = '$tipepo') AS satuan_kg,
					IFNULL((SELECT SUM(qty) FROM claimpembelian
						INNER JOIN claimpembelianitem
						ON claimpembelian.noclaim = claimpembelianitem.noclaim
						WHERE claimpembelian.noreturpabrik IS NULL
						AND claimpembelian.nopo = c.nopo
						AND claimpembelianitem.id_sj_nonretur = a.id_sj), 0) AS jumlah_claimmurni,              
					IFNULL((SELECT SUM(satuan_kg) FROM claimpembelian
						INNER JOIN claimpembelianitem
						ON claimpembelian.noclaim = claimpembelianitem.noclaim
						WHERE claimpembelian.noreturpabrik IS NULL
						AND claimpembelian.nopo = c.nopo
						AND claimpembelianitem.id_sj_nonretur = a.id_sj), 0) AS satuankg_claimmurni,
					IFNULL((SELECT returpabrikitem.qty - orderitemterima_retur.qty FROM orderitemterima_retur
							INNER JOIN orderitemterima
							ON orderitemterima_retur.id_sj_nonretur = orderitemterima.id_sj
							INNER JOIN returpabrik
							ON orderitemterima_retur.noretur = returpabrik.noretur
							INNER JOIN returpabrikitem
							ON returpabrik.noretur = returpabrikitem.noretur
							WHERE orderitemterima_retur.kodebarang = a.kodebarang
							AND orderitemterima_retur.fromtable = 'orderitemterima'
							AND orderitemterima_retur.itemid = e.itemid
							AND orderitemterima.id_sj = a.id_sj
							AND orderitemterima_retur.id_sj_nonretur = a.id_sj
							AND returpabrikitem.id_sj_nonretur = a.id_sj
							AND orderitemterima_retur.typeterima = 
							CASE LEFT(c.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
							AND returpabrik.status = 1
				                    ), 0) AS quantity_gagaldiperbaiki,
					IFNULL((SELECT returpabrikitem.satuan_kg - orderitemterima_retur.satuan_kg FROM orderitemterima_retur 
							INNER JOIN orderitemterima
							ON orderitemterima_retur.id_sj_nonretur = orderitemterima.id_sj
							INNER JOIN returpabrik
							ON orderitemterima_retur.noretur = returpabrik.noretur
							INNER JOIN returpabrikitem
							ON returpabrik.noretur = returpabrikitem.noretur
							WHERE orderitemterima_retur.kodebarang = a.kodebarang
							AND orderitemterima_retur.fromtable = 'orderitemterima'
							AND orderitemterima_retur.itemid = e.itemid
							AND orderitemterima.id_sj = a.id_sj
							AND orderitemterima_retur.id_sj_nonretur = a.id_sj
							AND returpabrikitem.id_sj_nonretur = a.id_sj
							AND orderitemterima_retur.typeterima = 
							CASE LEFT(c.nopo, 3)
							WHEN 'POG'
							THEN 'PO Greige'
							WHEN 'POM'
							THEN 'PO Matang'
							WHEN 'PCL'
							THEN 'PO Celupan'
							END
							AND returpabrik.status = 1
				                    ), 0) AS satuan_kg_gagaldiperbaiki,
					IFNULL((SELECT SUM(jumlah) FROM gudang INNER JOIN stockgudang ON gudang.id = stockgudang.gudangid WHERE itemid = a.itemid AND nopo = c.nopo AND id_sj = a.id_sj GROUP BY itemid), 0) AS jumlahstock,
					IFNULL((SELECT SUM(satuan_kg) FROM gudang INNER JOIN stockgudang ON gudang.id = stockgudang.gudangid WHERE itemid = a.itemid AND nopo = c.nopo AND id_sj = a.id_sj GROUP BY itemid), 0) AS satuan_kg_stock,
					f.typebarang AS typebarang, 
					f.kodewarna AS kodewarna, f.warna AS warna, 
					f.satuan AS satuan,
					f.harga_customer, 
					f.harga_greige, f.harga_celupan, 
					f.harga_matang
				      FROM  orderitemterima a 
				      INNER JOIN orderitemdetail f ON a.itemid = f.id AND a.kodebarang = f.kodebarang
				      INNER JOIN po c ON a.kodebarang = c.kodebarang
				      INNER JOIN supplier b ON  c.idSupplier = b.idSupplier      
				      INNER JOIN ordercustomer d ON a.kodebarang = d.kodebarang
				      LEFT JOIN returpabrikitem e ON a.id_sj = e.id_sj_nonretur
				      AND e.fromtable = 'orderitemterima'
					WHERE (LEFT(c.nopo,3) = 'POG' OR LEFT(c.nopo,3) = 'POM' OR LEFT(c.nopo,3) = 'PCL') 
					AND (a.typeterima = 'PO Greige' OR a.typeterima = 'PO Matang' OR a.typeterima = 'PO Celupan')
					AND c.tujuan = 'PT BSA'
					AND a.nosuratjalan = '$nosuratjalan'
					GROUP BY a.nosuratjalan, a.itemid 
				ORDER BY a.itemid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function detailsuratjalan_byid_sj($id_sj) {
			$sql = "SELECT sjd.sjdid as id_sj,
						 sj.sjno as nosuratjalan,
						 sj.sjtype as typeterima,
						 sj.sjdate as tanggal,
						 sjd.itemid,		 
						 poc.ordercode as kodebarang,
						 sjd.quantity as qty,
						 sjd.weight as satuan_kg,
						 DATE_FORMAT(sj.createddate,\"%Y-%m-%d\") as createddate, 
						 sj.createdby,
						 pocd.itemtype as typebarang,
						 pocd.colorcode as kodewarna,
						 pocd.color as warna,
						 pocd.unittype as satuan
				from suratjalandetail sjd
				left join suratjalan sj on sj.sjid = sjd.sjid
				left join purchaseorder po on po.poid = sj.poid
				left join pocustomer poc on poc.pocid = po.pocid
				left join pocustomerdetail pocd on pocd.itemid = sjd.itemid
				where sj.sjid  = $id_sj";

		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getdetailsuratjalancelupan_bysjid($id_sj) {
			$sql = "SELECT sj.sjid as sjid,
				 sj.sjno as nosuratjalan,
				 sj.sjtype as typeterima,
				 sj.sjdate as tanggal,
				 sjd.itemid as id,		 
				 poc.ordercode as kodebarang,

				(IFNULL((SELECT SUM(quantity) FROM suratjalandetail WHERE sjid = sjd.sjid AND itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(whd.quantity) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'in' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) + 
				 IFNULL((SELECT SUM(whd.quantity) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'out' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(frd.quantity) FROM factoryreturn fr, factoryreturndetail frd WHERE fr.sjid = sj.sjid AND frd.frid = fr.frid AND frd.itemid = sjd.itemid),0) +
				 IFNULL((SELECT SUM(sjrd.quantity) FROM suratjalanretur sjr,suratjalanreturdetail sjrd WHERE sjr.sjid = sj.sjid AND sjrd.sjrid = sjr.sjrid AND sjrd.itemid = sjd.itemid),0)) AS jumlah,
				(IFNULL((SELECT SUM(weight) FROM suratjalandetail WHERE sjid = sjd.sjid AND itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(whd.weight) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'in' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) + 
				 IFNULL((SELECT SUM(whd.weight) FROM warehouse wh,warehousedetail whd WHERE wh.sjid = sj.sjid AND wh.whtype = 'out' AND whd.whid = wh.whid AND whd.itemid = sjd.itemid),0) -
				 IFNULL((SELECT SUM(frd.weight) FROM factoryreturn fr, factoryreturndetail frd WHERE fr.sjid = sj.sjid AND frd.frid = fr.frid AND frd.itemid = sjd.itemid),0) +
				 IFNULL((SELECT SUM(sjrd.weight) FROM suratjalanretur sjr,suratjalanreturdetail sjrd WHERE sjr.sjid = sj.sjid AND sjrd.sjrid = sjr.sjrid AND sjrd.itemid = sjd.itemid),0)) AS satuan_kg,
				 
				 sj.createdby,
				 pocd.itemtype as typebarang,
				 pocd.colorcode as kodewarna,
				 pocd.color as warna,
				 pocd.unittype as satuan,
				poc.fabrictype as jenisbahan,
				IFNULL((SELECT gcd.price FROM greigecontractdetail gcd, greigecontracttransaction gct WHERE gcd.contractdetailid = gct.contractdetailid AND gct.itemid = sjd.itemid),0) as harga_greige,
				pocd.pricecelupan as harga_celupan,
				pocd.pricematang as harga_matang

				from suratjalandetail sjd
				left join suratjalan sj on sj.sjid = sjd.sjid
				left join purchaseorder po on po.poid = sj.poid
				left join pocustomer poc on poc.pocid = po.pocid
				left join pocustomerdetail pocd on pocd.itemid = sjd.itemid
				where sj.sjid  = $id_sj";

		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getgudangid() {
		$sql = "SELECT COALESCE(whid,0) as gudangid FROM warehouse ORDER BY whid DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->gudangid;
		}
	}
	function allowedhpp($noorder) {
		$sql = "SELECT 
			IF(COALESCE((a.harga_customer-(a.harga_customer*(b.diskon/100))),0) > (COALESCE((a.harga_greige-(a.harga_greige*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POG' AND kodebarang = a.kodebarang)/100))),0) + COALESCE((a.harga_celupan-(a.harga_celupan*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'PCL' AND kodebarang = a.kodebarang)/100))),0) + COALESCE((a.harga_matang-(a.harga_matang*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POM' AND kodebarang = a.kodebarang)/100))),0)), 'YES', 'NO') as hpp
		FROM orderitemdetail a, ordercustomer b 
		WHERE a.kodebarang = b.kodebarang AND b.noorder = '$noorder'
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function allowedhppperbarang($id,$noorder) {
		$sql = "SELECT 
			IF(COALESCE((a.harga_customer-(a.harga_customer*(b.diskon/100))),0) > (COALESCE((a.harga_greige-(a.harga_greige*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POG' AND kodebarang = a.kodebarang)/100))),0) + COALESCE((a.harga_celupan-(a.harga_celupan*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'PCL' AND kodebarang = a.kodebarang)/100))),0) + COALESCE((a.harga_matang-(a.harga_matang*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POM' AND kodebarang = a.kodebarang)/100))),0)), 'YES', 'NO') as hpp
		FROM orderitemdetail a, ordercustomer b 
		WHERE a.kodebarang = b.kodebarang AND b.noorder = '$noorder' AND a.id = $id
		";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->hpp;
		}
	}

	function allowedhppperpo($noorder) {
		$sql = "SELECT
				IFNULL(AVG(a.harga_customer-(a.harga_customer*(b.diskon/100))), 0) AS rata2poc, 
				IFNULL(AVG(a.harga_greige-(a.harga_greige*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POG' AND kodebarang = a.kodebarang)/100))), 0) as rata2pog,
				IFNULL(AVG(a.harga_celupan-(a.harga_celupan*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'PCL' AND kodebarang = a.kodebarang)/100))), 0) as rata2celup,
				IFNULL(AVG(a.harga_matang-(a.harga_matang*((SELECT diskon FROM PO WHERE LEFT(nopo,3) = 'POM' AND kodebarang = a.kodebarang)/100))), 0) as rata2matang
			FROM
				orderitemdetail a, ordercustomer b
			WHERE a.kodebarang = b.kodebarang AND b.noorder = '$noorder';
		";

		$query = $this->db->query($sql);


		$rata2poc = 0;
	   	$rata2pog = 0;
	   	$rata2celup = 0;
	   	$rata2matang = 0;

		foreach ($query->result() as $row)
		{
			
			$rata2poc = $row->rata2poc;
			$rata2pog = $row->rata2pog;
			$rata2celup = $row->rata2celup;
			$rata2matang = $row->rata2matang;

			if($rata2poc <=  ($rata2pog + $rata2celup + $rata2matang))
				return 'NO';
			else
				return 'YES';
		}

		
	}


	/* 10/10/2013 */
	function hppinclaimpembelian($nopo) {
		$sql = "SELECT a.id as itemid,
				CASE LEFT(b.nopo, 3)
				WHEN 'POG'
				THEN a.harga_greige - (a.harga_greige * b.diskon / 100)
				WHEN 'PCL'
				THEN (a.harga_greige + a.harga_celupan) - ((a.harga_greige + a.harga_celupan) * b.diskon / 100)
				WHEN 'POM'
				THEN a.harga_matang - (a.harga_matang * b.diskon / 100)
				END AS hpp,
				CASE LEFT(b.nopo, 3)
				WHEN 'POG'
				THEN FORMAT(a.harga_greige, 2)
				WHEN 'PCL'
				THEN FORMAT((a.harga_greige + a.harga_celupan), 2)
				WHEN 'POM'
				THEN FORMAT(a.harga_matang, 2)
				END AS hargasatuan
				FROM orderitemdetail a, po b 
				WHERE a.kodebarang = b.kodebarang AND b.nopo = '$nopo'";
		$query = $this->db->query($sql);
		
		return $query->result_array();
	}

	function newhppinclaimpembelian($nopo){
		$sql = "SELECT b.itemid as itemid,
				CASE LEFT(e.pono, 3)
				WHEN 'POG'
				THEN d.price - (d.price * a.discount / 100)
				WHEN 'PCL'
				THEN (d.price + b.pricecelupan) - ((d.price + b.pricecelupan) * a.discount / 100)
				WHEN 'POM'
				THEN b.pricematang - (b.pricematang * a.discount / 100)
				END AS hpp,
				CASE LEFT(e.pono, 3)
				WHEN 'POG'
				THEN d.price
				WHEN 'PCL'
				THEN d.price + b.pricecelupan
				WHEN 'POM'
				THEN b.pricematang
				END AS hargasatuan
				FROM pocustomer a, pocustomerdetail b, pogreigeprice c, greigecontractdetail d, purchaseorder e
				WHERE e.pono = '$nopo' AND e.pocid = a.pocid AND a.pocid = b.pocid AND c.itemid = b.itemid AND c.contractdetailid = d.contractdetailid";
		$query = $this->db->query($sql);	
		return $query->result_array();
	}
	function jenisbahan($kodebarang) {
		$sql = "SELECT fabrictype as bahan FROM pocustomer WHERE ordercode = '$kodebarang'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->bahan;
		}
	}
	/* 24/08/2013 */
	function getdatabayarpo($idsupplier) {
		$sql = "
		  	SELECT * FROM
				(SELECT idSupplier, namaSupplier, id, nopo, kodebarang, tanggal, diskon, (SUM(totalperid) + tambahbayar) - ((SUM(totalperid) + tambahbayar) * (diskon/100)) AS total, bayarpervoucher FROM
				(SELECT b.idSupplier, f.namaSupplier, a.id, b.nopo, a.kodebarang, IFNULL(b.tambahbayar, 0) AS tambahbayar, b.diskon, DATE_FORMAT(b.tanggal,'%d-%m-%Y') AS tanggal, 
				CASE LEFT(b.nopo, 3)
				WHEN 'POG' THEN a.satuan_kg * a.harga_greige
				WHEN 'PCL' THEN a.satuan_kg * a.harga_celupan
				WHEN 'POM' THEN a.satuan_kg * a.harga_matang
				END AS totalperid,
				IFNULL(SUM(e.total), 0) AS bayarpervoucher
					    		FROM orderitemdetail a
					    		INNER JOIN po b
					    		ON a.kodebarang = b.kodebarang
					    		LEFT JOIN pembayaransupplierpo c
					    		ON b.nopo = c.nopo
					    		AND b.kodebarang = c.kodebarang
					    		LEFT JOIN pembayaransupplier d
					    		ON c.novoucher = d.novoucher
					    		LEFT JOIN pembayaransupplierdetail e
					    		ON d.novoucher = e.novoucher
					    		INNER JOIN supplier f
					    		ON b.idSupplier = f.idSupplier
					    		GROUP BY a.id) AS results
					    		WHERE idSupplier = '$idsupplier'
					    		GROUP BY results.nopo) AS summary
					    		WHERE bayarpervoucher < total
					    		GROUP BY nopo";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getdetailbarangpo($kodebarang) {
		$sql = "SELECT 
				c.warna as warna,
				c.kodebarang as kodebarang,
				c.typebarang as typebarang,
				FORMAT(c.jumlah,0) as qty,
				c.satuan as satuan,
				FORMAT(c.satuan_kg,0) as totalkg,
				CASE
				  WHEN LEFT(b.nopo,3) = 'POG' THEN
				  	FORMAT(c.harga_greige,0)
				  WHEN LEFT(b.nopo,3) = 'POM' THEN
				  	FORMAT(c.harga_matang,0)
				  ELSE
				  	FORMAT(c.harga_celupan,0)
				END as hargasatuan,	
				CASE
				  WHEN LEFT(b.nopo,3) = 'POG' THEN FORMAT(SUM((c.satuan_kg*c.harga_greige)-((c.satuan_kg*c.harga_greige)*(b.diskon/100))),0)     
				  WHEN LEFT(b.nopo,3) = 'POM' THEN FORMAT(SUM((c.satuan_kg*harga_matang)-((c.satuan_kg*c.harga_matang)*(b.diskon/100))),0)     
				  ELSE FORMAT(SUM((c.satuan_kg*c.harga_celupan)-((c.satuan_kg*c.harga_celupan)*(b.diskon/100))),0)
				END as total
				FROM po b, orderitemdetail c
				WHERE b.kodebarang = c.kodebarang AND c.kodebarang = '$kodebarang'
				";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getnamasupplierid($id) {
		$sql = "SELECT namaSupplier as nama FROM supplier WHERE idSupplier = $id";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nama;
		}
	}
	function getvoucherbayar() {
		$sql = "SELECT novoucher as novoucher FROM pembayaransupplier ORDER BY novoucher DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->novoucher;
		}
	}
	function getvoucherbayarcustomer() {
		$sql = "SELECT novoucher as novoucher FROM pembayarancustomer ORDER BY novoucher DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->novoucher;
		}
	}
	function getdatafakturpembelian($novoucher) {
		$sql = "SELECT *, pembayaransupplierpo.total AS totalpo
				FROM pembayaransupplier
				INNER JOIN pembayaransupplierpo ON pembayaransupplier.novoucher = pembayaransupplierpo.novoucher
				INNER JOIN po ON po.nopo = pembayaransupplierpo.nopo
				WHERE pembayaransupplier.novoucher = '$novoucher'
				";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getdetailfakturpembelian($novoucher) {
		$sql = "SELECT *,date_format(pembayaransupplierdetail.tanggal,'%d-%m-%Y') as tanggaltransfer,
				pembayaransupplier.total AS grandtotalpo,
				pembayaransupplierdetail.total AS totalbayar
				FROM pembayaransupplier 
				INNER JOIN pembayaransupplierdetail ON pembayaransupplier.novoucher = pembayaransupplierdetail.novoucher
				WHERE pembayaransupplier.novoucher = '$novoucher'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function gettotaljual($poc, $nofaktur) {
	    $sql = "SELECT SUM((d.satuan_kg*a.harga_customer)-((d.satuan_kg*a.harga_customer)*(b.diskon/100))) AS total
	    		FROM orderitemdetail a
	    		INNER JOIN ordercustomer b
	    		ON a.kodebarang = b.kodebarang
	    		INNER JOIN fakturpenjualan c
	    		ON b.noorder = c.noorder
	    		INNER JOIN fakturpenjualanitem d
	    		ON c.id = d.fakturid
	    		AND a.id = d.itemid
	    		WHERE b.noorder = '$poc'
	    		AND c.nofaktur = '$nofaktur'
	    		GROUP BY a.kodebarang";
	    $query = $this->db->query($sql);
	    foreach ($query->result() as $row)
	    {
	       return $row->total;
	    }
	}
	function gettotalpembayarancustomer($noorder, $nofaktur) {
		$sql = "SELECT SUM(total) as total FROM pembayarancustomerfaktur 
	               INNER JOIN ordercustomer 
	               ON ordercustomer.noorder = pembayarancustomerfaktur.noorder 
	               WHERE pembayarancustomerfaktur.noorder = '$noorder'
	               AND pembayarancustomerfaktur.nofaktur = '$nofaktur'
	               GROUP BY ordercustomer.idCustomer";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row) {
			return $row->total;
		}
	}
	function gettotalpembayaransupplier($nopo) {
		$sql = "SELECT SUM(total) AS total FROM pembayaransupplierpo
	               INNER JOIN po 
	               ON po.nopo = pembayaransupplierpo.nopo 
	               WHERE pembayaransupplierpo.nopo = '$nopo'
	               GROUP BY po.idSupplier";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
        	return $row->total;
        }
	}
	function getitemdetailbywarnafortypebayar($kodebarang, $warna, $harga, $type) {
		$sql =	"SELECT IF(ISNULL(jumlah), 0, jumlah) AS jumlah," .
				"IF($harga = '', 0, $harga) AS harga," .
				"satuan_kg AS totalkg,".
				"harga_customer*satuan_kg AS total ".
				" FROM orderitemdetail a, orderitem b " .
				"WHERE a.kodebarang = b.kodebarang AND a.kodebarang = ? " .
				"AND a.typebarang = ? AND a.warna = ?";
		$data[] = $kodebarang;
		$data[] = $type;
		$data[] = $warna;
		$query = $this->db->query($sql, $data);
		return $query->row();
	}
	function getbayarfakturpenjualan($novoucher, $nofaktur) {
		$sql = "SELECT a.*,SUM(a.total) AS totalbayar,date_format(a.tanggal,'%d-%m-%Y') as tanggal, b.diskon as diskon, c.noorder as noorder, c.kodebarang as kodebarang, d.namaCustomer FROM pembayarancustomer a, ordercustomer b, pembayarancustomerfaktur c, customer d WHERE a.novoucher = '$novoucher' AND a.novoucher = c.novoucher AND b.noorder = c.noorder AND b.idCustomer = d.idCustomer AND c.nofaktur = '$nofaktur'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getbayarfakturpenjualandetail($novoucher) {
		$sql = "SELECT 
		    		a.*,
		    		date_format(a.tanggal,'%d-%m-%Y') as tanggal, 
		    		if(a.id IN (SELECT typebayarid FROM transaksibankmasukdetail), 'Lunas','Kredit') as status 
		    		  FROM pembayarancustomerdetail a WHERE a.novoucher = '$novoucher'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/* 03/09/2013 */
	
	function getdatabayarcustomer($idcustomer) {
		$sql = "SELECT a.nofaktur as nofaktur, a.tanggal as tanggal, a.noorder as noorder, b.kodebarang as kodebarang 
				FROM fakturpenjualan a, ordercustomer b 
				WHERE a.noorder = b.noorder AND b.idcustomer = $idcustomer
				ORDER BY a.tanggal DESC
				";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getnamasales($noorder) {
		$sql = "SELECT idSales as nama FROM ordercustomer WHERE noorder = '$noorder'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nama;
		}
	}

	function fakturheaderpocsuratjalan($noorder)
	{
		$sql = "SELECT perusahaan, jenispembayaran, lamabayar, diskon
					FROM ordercustomer
					WHERE noorder = '$noorder'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getkodeitem($nosuratjalan)
	{
		$sql = "SELECT DISTINCT kodebarang
				FROM orderitemterima
				WHERE nosuratjalan = '$nosuratjalan'
				";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->kodebarang;
		}
	}

	function getkodeitemretur($nosuratjalan)
	{
		$sql = "SELECT DISTINCT kodebarang
				FROM orderitemterima_retur
				WHERE nosuratjalan = '$nosuratjalan'
				";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->kodebarang;
		}
	}

	function fakturdetailpocsuratjalan($nosuratjalan)
	{
		$sql = "SELECT 
					a.nosuratjalan AS id,
					a.itemid,
					c.noorder,
					a.tanggal, 
					c.jenisbahan,
					c.proses,
					c.compact,
					d.warna,
					d.kodewarna,
					d.typebarang,
					e.jumlah AS jumlah,
					d.satuan,
					e.jumlahkg AS jumlahkg,
					d.harga_customer
				FROM 
					orderitemterima a,
					ordercustomer b,
					orderitem c,
					orderitemdetail d,
					(
						select itemid, 
							sum(jumlah) as jumlah,
							sum(satuan_kg) as jumlahkg,
							jenisbarang
						from stockgudang
						where 
							(
								jenisbarang = 'Matang' OR 
								jenisbarang = 'Celupan'
							)
						group by itemid
						HAVING sum(satuan_kg) > 0
					) e
				WHERE 
					a.kodebarang = b.kodebarang AND
					a.kodebarang = c.kodebarang AND
					a.itemid = d.id AND
					a.itemid = e.itemid AND
					(
						jenisbarang = 'Matang' OR 
						jenisbarang = 'Celupan'
					) AND
					a.nosuratjalan = '$nosuratjalan' AND
					(
						typeterima = 'PO Matang' OR 
						typeterima = 'PO Celupan'
					)
				;
			";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function fakturdetailpoc($noorder) {
		$sql = "SELECT
					b.id AS id, 
					b.kodebarang AS kodebarang,
					b.typebarang AS typebarang,
					c.jenisbarang AS jenisbarang,
					b.kodewarna AS kodewarna,
					b.warna AS warna,
					SUM(c.jumlah) - IFNULL(SUM(e.qty), 0) AS jumlah,
					b.satuan AS satuan,
					SUM(c.satuan_kg) - IFNULL(SUM(e.satuan_kg), 0) AS jumlahkg,
					d.jenisbahan AS jenisbahan,
					a.gramasimatang AS gramasimatang,
					a.gramasigreige AS gramasigreige,
					d.proses AS proses,
					REPLACE(d.compact, '\\\', '') AS COMPACT,
					b.harga_customer AS harga_customer,
					a.diskon,
					a.lamabayar,
					a.jenispembayaran,
					a.perusahaan
					
			 	FROM orderitemdetail b
			 	INNER JOIN stockgudang c
			 	ON   b.kodebarang = c.kodebarang
			 	AND  c.itemid = b.id
			 	AND  c.jenisbarang <> 'Greige'
			 	INNER JOIN ordercustomer a
			 	ON   b.kodebarang = a.kodebarang
			 	INNER JOIN orderitem AS d ON a.noorder = d.noorder AND a.kodebarang = d.kodebarang
			 	LEFT JOIN fakturpenjualanitem AS e
			 	ON c.itemid = e.itemid
			 	LEFT JOIN returpabrik f
			 	ON c.kodebarang = f.kodebarang
			 	LEFT JOIN returpabrikitem g
			 	ON f.noretur = g.noretur
			 	AND b.id = g.itemid
				WHERE  a.noorder = '$noorder' AND a.statusorder <> 'Batal'
				AND IF(f.status IS NULL, f.status IS NULL, f.status = '1')
				AND IF(g.fromtable IS NULL, g.fromtable IS NULL, g.fromtable = 'stockgudang')
				GROUP BY b.id
				ORDER BY c.itemid";
	   
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function fakturdetailretursuratjalan($nosuratjalan)
	{
		$sql = "SELECT 
					a.nosuratjalan AS id,
					a.itemid,
					c.noorder,
					a.tanggal, 
					c.jenisbahan,
					c.proses,
					c.compact,
					d.warna,
					d.kodewarna,
					d.typebarang,
					a.qty AS jumlah,
					d.satuan,
					d.satuan_kg AS jumlahkg,
					d.harga_customer
				FROM 
					orderitemterima_retur a 
					LEFT JOIN ordercustomer b ON
						a.kodebarang = b.kodebarang
					LEFT JOIN orderitem c ON
						a.kodebarang = c.kodebarang
					LEFT JOIN orderitemdetail d ON
						a.itemid = d.id
				WHERE 
					a.nosuratjalan = '$nosuratjalan' AND
					(
						typeterima = 'PO Matang' OR 
						typeterima = 'PO Celupan'
					)
					";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function fakturdetailkodebarang($kodebarang, $nofaktur) {
		/*$this->db->select('fakturpenjualanitem.fakturid,
			fakturpenjualanitem.itemid as itemid,
					OrderItemDetail.id as id, 
					OrderItemDetail.kodebarang as kodebarang,
					OrderItemDetail.typebarang as typebarang,
					OrderItemDetail.kodewarna as kodewarna,
					OrderItemDetail.warna as warna,
					fakturpenjualanitem.qty as qty,
					OrderItemDetail.satuan as satuan,
					fakturpenjualanitem.satuan_kg as jumlahkg,
					OrderItemDetail.harga_customer as harga_customer,
					orderitem.jenisbahan as jenisbahan,
					OrderItemDetail.harga_greige as harga_greige,
					OrderItemDetail.harga_celupan as harga_celupan,
					OrderItemDetail.harga_matang as harga_matang');
		$this->db->from('OrderItemDetail');
		$this->db->join('fakturpenjualanitem',
			'OrderItemDetail.kodebarang = fakturpenjualanitem.kodebarang AND OrderItemDetail.id = fakturpenjualanitem.itemid');
		$this->db->join('orderitem', 'OrderItemDetail.kodebarang = orderitem.kodebarang');
		$this->db->join('fakturpenjualan', 'fakturpenjualan.id = fakturpenjualanitem.fakturid');
		$this->db->where('fakturpenjualan.nofaktur', $nofaktur);
		
		$this->db->where('fakturpenjualanitem.kodebarang', $kodebarang);
		
		$this->db->order_by('fakturpenjualanitem.itemid');
		$query = $this->db->get();

		return $query->result_array();*/

		$sql = " SELECT
			c.fakturid as fakturid,
			c.itemid as itemid,
			a.id as id, 
			a.kodebarang as kodebarang,
			a.typebarang as typebarang,
			a.kodewarna as kodewarna,
			a.warna as warna,
			c.qty as qty,
			a.satuan as satuan,
			c.satuan_kg as jumlahkg,
			a.harga_customer as harga_customer,
			b.jenisbahan as jenisbahan,
			a.harga_greige as harga_greige,
			a.harga_celupan as harga_celupan,
			a.harga_matang as harga_matang
			FROM OrderItemDetail a, orderitem b, fakturpenjualanitem c, fakturpenjualan d
			WHERE d.nofaktur = '$nofaktur'
			AND a.kodebarang = b.kodebarang AND a.id = c.itemid AND c.fakturid = d.id
			ORDER BY c.itemid
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	//added dio
	function fakturdetail_byivid($ivid) {
		$sql = "SELECT
			iv.invoiceid as fakturid,
			ivd.itemid as id,
			poc.ordercode as kodebarang,
			pocd.itemtype as typebarang,
			pocd.colorcode as kodewarna,
			pocd.color as warna,
			pocd.unittype as satuan,

			(IFNULL((SELECT SUM(quantity) FROM invoicedetail WHERE invoiceid = iv.invoiceid AND itemid = ivd.itemid),0)) as qtyfaktur,
			(IFNULL((SELECT SUM(weight) FROM invoicedetail WHERE invoiceid = iv.invoiceid AND itemid = ivd.itemid),0)) as jmlhfaktur,

			(IFNULL((SELECT SUM(quantity) FROM customerreturn crsc, customerreturndetail crdsc WHERE invoiceid = iv.invoiceid AND crdsc.crid = crsc.crid AND itemid = ivd.itemid),0)) as qtyfakturretur,
			(IFNULL((SELECT SUM(weight) FROM customerreturn crsc, customerreturndetail crdsc WHERE invoiceid = iv.invoiceid AND crdsc.crid = crsc.crid AND itemid = ivd.itemid),0)) as jmlhfakturretur,

			pocd.pricecustomer as harga_customer,
			poc.fabrictype as jenisbahan,
			IFNULL((SELECT gcd.price FROM greigecontractdetail gcd, greigecontracttransaction gct WHERE gcd.contractdetailid = gct.contractdetailid AND gct.itemid = ivd.itemid),0) as harga_greige,
			pocd.pricecelupan as harga_celupan,
			pocd.pricematang as harga_matang
			FROM invoice iv, invoicedetail ivd, suratjalan sj, purchaseorder po, pocustomer poc, pocustomerdetail pocd
			WHERE iv.invoiceid = '1'
			AND ivd.invoiceid = iv.invoiceid
			AND sj.sjid = iv.sjid
			AND po.poid = sj.poid
			AND poc.pocid = po.pocid
			AND pocd.pocid = poc.pocid
			ORDER BY ivd.itemid
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getlastfakturid() {
		$sql = "SELECT id FROM fakturpenjualan ORDER BY id DESC LIMIT 1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
			return $row->id;
		}

		$array = array_filter($query->result());
		   		
		if (empty($array)) { return ""; }
	}
	function getnoorderbataldata($kodebarang) {
		$this->db->where_in('kodebarang', $kodebarang);
		$this->db->where('statusorder', 'Batal');
		$query = $this->db->get('OrderCustomer');
		return $query->result_array();
	}
	function noorderexists($noorder) {
		$this->db->where('noorder', $noorder);
		$this->db->from('OrderCustomer');
		return $this->db->count_all_results();
	}

	/* laporan */
	function detailslaporanterimabarangpabrikgreige() {
		$sql = "SELECT * FROM ( SELECT noorder, nopo, tanggal, kodebarang, suratjalansupplier, supplier, createdby, ROUND(kg_awal - kg_terima, 2) as totalsusut, IF(qty < jumlah, 'Partial', 'Complete') as status, quantity, satuan_kg FROM
              (SELECT
                d.noorder as noorder,
                c.nopo as nopo,
                DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tanggal,
                a.kodebarang as kodebarang,
                a.nosuratjalan as suratjalansupplier,
                b.namaSupplier as supplier,
                a.createdby as createdby,
                            
                        (SELECT SUM(jumlah) as jumlah FROM orderitemdetail WHERE kodebarang = a.kodebarang) as jumlah,
                        (SELECT SUM(qty) as qty FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Greige') as qty,
                        (SELECT SUM(satuan_kg) as kg_awal FROM orderitemdetail WHERE kodebarang = a.kodebarang) as kg_awal,
                        (SELECT SUM(satuan_kg) as kg_terima FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Greige') as kg_terima,
                        a.qty as quantity,
                        a.satuan_kg as satuan_kg
              FROM  orderitemterima a, supplier b, po c, ordercustomer d
              WHERE c.idSupplier = b.idSupplier AND left(c.nopo,3) = 'POG' AND a.kodebarang = c.kodebarang
                             AND  a.kodebarang = d.kodebarang AND a.typeterima = 'PO Greige'
              ) as results ) as tabelterimabaranggreige";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailslaporanterimabarangpabrikmatang() {
		$sql = "SELECT * FROM ( SELECT noorder, nopo, tanggal, kodebarang, suratjalansupplier, supplier, createdby, ROUND(kg_awal - kg_terima, 2) as totalsusut, IF(qty < jumlah, 'Partial', 'Complete') as status, quantity, satuan_kg FROM
              (SELECT
                d.noorder as noorder,
                c.nopo as nopo,
                DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tanggal,
                a.kodebarang as kodebarang,
                a.nosuratjalan as suratjalansupplier,
                b.namaSupplier as supplier,
                a.createdby as createdby,
                            
                        (SELECT SUM(jumlah) as jumlah FROM orderitemdetail WHERE kodebarang = a.kodebarang) as jumlah,
                        (SELECT SUM(qty) as qty FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Matang') as qty,
                        (SELECT SUM(satuan_kg) as kg_awal FROM orderitemdetail WHERE kodebarang = a.kodebarang) as kg_awal,
                        (SELECT SUM(satuan_kg) as kg_terima FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Matang') as kg_terima,
                        a.qty as quantity,
                        a.satuan_kg as satuan_kg
              FROM   orderitemterima a, supplier b, po c, ordercustomer d
              WHERE c.idSupplier = b.idSupplier AND left(c.nopo,3) = 'POM' AND a.kodebarang = c.kodebarang
                             AND  a.kodebarang = d.kodebarang AND a.typeterima = 'PO Matang'
              ) as results ) as tabelterimabarangmatang";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailslaporanterimabarangpabrikcelupan() {
		$sql = "SELECT * FROM ( SELECT noorder, nopo, tanggal, kodebarang, suratjalansupplier, supplier, createdby, ROUND(kg_awal - kg_terima, 2) as totalsusut, IF(qty < jumlah, 'Partial', 'Complete') as status, quantity, satuan_kg FROM
              (SELECT
                d.noorder as noorder,
                c.nopo as nopo,
                DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tanggal,
                a.kodebarang as kodebarang,
                a.nosuratjalan as suratjalansupplier,
                b.namaSupplier as supplier,
                a.createdby as createdby,
                            
                        (SELECT SUM(jumlah) as jumlah FROM orderitemdetail WHERE kodebarang = a.kodebarang) as jumlah,
                        (SELECT SUM(qty) as qty FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Celupan') as qty,
                        (SELECT SUM(satuan_kg) as kg_awal FROM orderitemdetail WHERE kodebarang = a.kodebarang) as kg_awal,
                        (SELECT SUM(satuan_kg) as kg_terima FROM orderitemterima WHERE kodebarang = a.kodebarang AND typeterima = 'PO Celupan') as kg_terima,
                        a.qty as quantity,
                        a.satuan_kg as satuan_kg
              FROM   orderitemterima a, supplier b, po c, ordercustomer d
              WHERE c.idSupplier = b.idSupplier AND left(c.nopo,3) = 'PCL' AND a.kodebarang = c.kodebarang
                             AND  a.kodebarang = d.kodebarang AND a.typeterima = 'PO Celupan'
              ) as results ) as tabelterimabarangcelupan";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* fungsi batal */
	function getpabrikpo($noorder) {
		$this->db->select('nopo,OrderCustomer.kodebarang');
		$this->db->from('po');		
		$this->db->join('OrderCustomer', 'OrderCustomer.kodebarang = po.kodebarang');
		$this->db->where('OrderCustomer.noorder', $noorder);
		$query = $this->db->get();
		return $query->result_array();
	}
	function getpabrikpobykodebarang($kodebarang) {
		/*$this->db->select('nopo,kodebarang');
		$this->db->from('po');
		$this->db->where('po.kodebarang', $kodebarang);
		$query = $this->db->get();*/
		$sql = "SELECT poc.ordercode as kodebarang, po.pono as nopo
		FROM pocustomer poc, purchaseorder po
		WHERE poc.ordercode = '$kodebarang'
		AND po.pocid = poc.pocid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function batalorder($noorder) {
		$statusorder = array('statusorder'=>'Batal');
		$this->db->where('noorder', $noorder);
		$this->db->update('OrderCustomer', $statusorder);
	}	
	function batalpogreige($pogreige) {
		$statuspo = array('statuspo'=>'Batal');
		$this->db->where('nopo', $pogreige);
		$this->db->update('po', $statuspo);
	}
	function batalpocelup($pocelup) {
		$statuspo = array('statuspo'=>'Batal');
		$this->db->where('nopo', $pocelup);
		$this->db->update('po', $statuspo);
	}
	function batalpomatang($pomatang) {
		$statuspo = array('statuspo'=>'Batal');
		$this->db->where('nopo', $pomatang);
		$this->db->update('po', $statuspo);
	}

	function reactivateorder($activatednoorder, $statusorder) {
		$statusorder = array('statusorder'=>$statusorder);
		$this->db->where('noorder', $activatednoorder);
		$this->db->update('OrderCustomer', $statusorder);
	}
	function reactivatepogreige($pogreige, $statuspo) {
		$statuspo = array('statuspo'=>$statuspo);
		$this->db->where('nopo', $pogreige);
		$this->db->update('po', $statuspo);
	}
	function reactivatepocelup($pocelup, $statuspo) {
		$statuspo = array('statuspo'=>$statuspo);
		$this->db->where('nopo', $pocelup);
		$this->db->update('po', $statuspo);
	}
	function reactivatepomatang($pomatang, $statuspo) {
		$statuspo = array('statuspo'=>$statuspo);
		$this->db->where('nopo', $pomatang);
		$this->db->update('po', $statuspo);
	}

	function getisingudang($kodebarang, $jenisbarang) {
		$this->db->select('count(*) as cnt');
		$this->db->from('gudang');
		$this->db->join('stockgudang', 'gudang.id = stockgudang.gudangid');
		$this->db->join('orderitemterima', 'orderitemterima.kodebarang=gudang.kodebarang');
		$this->db->where('gudang.kodebarang', $kodebarang);
		$this->db->where('stockgudang.jenisbarang', $jenisbarang);
		$this->db->where('orderitemterima.kodebarang', $kodebarang);
		$query = $this->db->get();
		foreach ($query as $value) {
			return $value['cnt'];
		}
	}
	

	/* LAPORAN */
	/* detail stock gudang */
	function getdetailstockgudang($itemid) 
	{
		$this->db->select('gudangid,jumlah,satuan_kg');
		$this->db->from('stockgudang');
		$this->db->where('itemid', $itemid);
		$query = $this->db->get();

		return $query->result();
	}

	function getdetailstockgudang_sjcustomer($itemid) 
	{
		$sql = "SELECT itemid, 
						gudangid, 
						sum(satuan_kg) AS satuan_kg, 
						jenisbarang 
				FROM stockgudang 
				WHERE 
					( jenisbarang = 'Matang' OR jenisbarang = 'Celupan' ) 
					AND 
					itemid = $itemid 
				GROUP BY gudangid;
				";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function getdetailstockgudang_bygudangid($kodebarang, $tipepo) {
		$sql = 'SELECT DISTINCT *,
				(SELECT SUM(jumlah) FROM stockgudang 
				WHERE itemid=temp.id AND jenisbarang="'.$tipepo.'" 
				GROUP BY itemid) AS jumlah, 

				(SELECT SUM(satuan_kg) FROM stockgudang 
				WHERE itemid=temp.id AND jenisbarang="'.$tipepo.'" 
				GROUP BY itemid) AS satuan_kg, 

				IFNULL((SELECT SUM(qty) FROM returpabrikitem 
				WHERE itemid=temp.id AND returpabrikitem.fromtable = "stockgudang"
				GROUP BY itemid), 0) AS jumlahretur, 

				IFNULL((SELECT SUM(satuan_kg) FROM returpabrikitem 
				WHERE itemid=temp.id AND returpabrikitem.fromtable = "stockgudang"
				GROUP BY itemid), 0) AS satuan_kg_retur,

				(SELECT SUM(qty) FROM fakturpenjualanitem
				WHERE itemid=temp.id) AS jumlahfaktur,
				
				(SELECT SUM(satuan_kg) FROM fakturpenjualanitem
				WHERE itemid=temp.id) AS satuan_kg_faktur,
				
				IFNULL((SELECT SUM(qty) FROM claimpembelianitem
				WHERE itemid=temp.id AND claimpembelianitem.fromtable = "stockgudang"
				GROUP BY itemid), 0) AS jumlahclaim, 

				IFNULL((SELECT SUM(satuan_kg) FROM claimpembelianitem 
				WHERE itemid=temp.id AND claimpembelianitem.fromtable = "stockgudang"
				GROUP BY itemid), 0) AS satuan_kg_claim

				FROM (
				SELECT stockgudang.gudangid, stockgudang.itemid AS id, OrderItemDetail.typebarang, 
				OrderItemDetail.kodewarna, OrderItemDetail.warna, OrderItemDetail.satuan AS satuan, 
				DATE_FORMAT(stockgudang.tanggal, "%d-%m-%Y") AS tanggal,

				OrderItemDetail.harga_customer, OrderItemDetail.harga_greige, OrderItemDetail.harga_celupan, OrderItemDetail.harga_matang 

				FROM `stockgudang`
				JOIN `OrderItemDetail` ON `OrderItemDetail`.`id` = `stockgudang`.`itemid` 
				AND OrderItemDetail.kodebarang = stockgudang.kodebarang 

				WHERE orderitemdetail.kodebarang = "'.$kodebarang.'" AND `stockgudang`.`jenisbarang` = "'.$tipepo.'"
				ORDER BY `stockgudang`.`itemid`) AS temp
				GROUP BY temp.id';
		$query = $this->db->query($sql);

		return $query->result_array();	
	}

	function getdetailstockgudang_bysjid($sjid) {
		$sql = "SELECT 
		(IFNULL((SELECT SUM(whdsc.quantity) FROM warehouse whsc, warehousedetail whdsc WHERE whsc.whtype = 'in' AND whsc.sjid = wh.sjid AND whdsc.whid = whsc.whid AND whdsc.itemid = whd.itemid),0) -
		IFNULL((SELECT SUM(whdsc.quantity) FROM warehouse whsc, warehousedetail whdsc WHERE whsc.whtype = 'out' AND whsc.sjid = wh.sjid AND whdsc.whid = whsc.whid AND whdsc.itemid = whd.itemid),0)) as jumlah,
		(IFNULL((SELECT SUM(whdsc.weight) FROM warehouse whsc, warehousedetail whdsc WHERE whsc.whtype = 'in' AND whsc.sjid = wh.sjid AND whdsc.whid = whsc.whid AND whdsc.itemid = whd.itemid),0) -
		IFNULL((SELECT SUM(whdsc.weight) FROM warehouse whsc, warehousedetail whdsc WHERE whsc.whtype = 'out' AND whsc.sjid = wh.sjid AND whdsc.whid = whsc.whid AND whdsc.itemid = whd.itemid),0)) as satuan_kg,
		poc.ordercode as kodebarang, pocd.pricecustomer as harga_customer, pocd.pricecelupan as harga_celupan, pocd.pricematang as harga_matang,
		IFNULL((SELECT gcd.price FROM greigecontractdetail gcd, greigecontracttransaction gct WHERE gcd.contractdetailid = gct.contractdetailid AND gct.itemid = whd.itemid),0) as harga_greige,
		IFNULL((SELECT SUM(quantity) FROM invoicedetail WHERE itemid = whd.itemid),0) AS jumlahfaktur,
		IFNULL((SELECT SUM(weight) FROM invoicedetail WHERE itemid = whd.itemid),0) AS satuan_kg_faktur,
		pocd.itemtype as typebarang, whd.itemid as id, wh.whid as gudangid, pocd.color as warna, pocd.unittype as satuan
		FROM warehouse wh, warehousedetail whd, pocustomer poc, pocustomerdetail pocd
		WHERE wh.sjid = '$sjid'
		AND whd.whid = wh.whid
		AND pocd.itemid = whd.itemid
		AND poc.pocid = pocd.pocid
		GROUP BY whd.itemid";
		$query = $this->db->query($sql);

		return $query->result_array();	
	}
	/* 26/11/2013 */
	public function detailstockgudangbygudangid_itemid($gudangid, $itemid) {
		$this->db->select('jumlah, satuan_kg');
		$this->db->from('stockgudang');
		$this->db->where('gudangid', $gudangid);
		$this->db->where('itemid', $itemid);

		$query = $this->db->get();
		return $query->result_array();
	}

	// DAVID
	// fixing no. 48 - 12/3/2013
	public function gudangtosjmaklon($itemid, $kodebarang, $jumlah, $berat) {
		$sql = 
			"SELECT
				gudangid, jumlah, satuan_kg
			FROM
				stockgudang
			WHERE
				itemid = $itemid
			AND
				kodebarang = '$kodebarang'";

		$query = $this->db->query($sql)->result_array();

		$rownum = 0;

		while($jumlah > 0) {
			$gudangid = $query[$rownum]['gudangid'];

			if($query[$rownum]['jumlah'] < $jumlah) {
				$jumlah = $jumlah - $query[$rownum]['jumlah'];
				$berat = $berat - $query[$rownum]['satuan_kg'];

				$sisajumlah = 0; $sisaberat = 0;
			}
			else {
				$sisajumlah = $jumlah - $query[$rownum]['jumlah'];
				$sisaberat = $berat - $query[$rownum]['satuan_kg'];

				$jumlah = 0; $berat = 0;
			}

			$gudangid = $query[$rownum]['gudangid'];
			$sqlupdate = 
				"UPDATE
					stockgudang
				SET
					jumlah = $sisajumlah, satuan_kg = $sisaberat
				WHERE
					gudangid = $gudangid
				AND
					itemid = $itemid";
			$this->db->query($sqlupdate);

			$rownum++;
		}
	}


	/* 30/09/2013 */
	function getdetailreturcustomer($noretur) {
		$sql = 'SELECT DISTINCT *,
				(SELECT SUM(qty) FROM returcustomeritem 
				WHERE itemid=temp.id
				GROUP BY itemid) AS jumlah, 

				(SELECT SUM(satuan_kg) FROM returcustomeritem 
				WHERE itemid=temp.id
				GROUP BY itemid) AS satuan_kg, 
				
				(SELECT SUM(qty) FROM fakturpenjualanitem
				WHERE itemid=temp.id) AS jumlahfaktur,
				
				(SELECT SUM(satuan_kg) FROM fakturpenjualanitem
				WHERE itemid=temp.id) AS satuan_kg_faktur,

				IFNULL((SELECT SUM(qty) FROM returpabrikitem 
				WHERE itemid=temp.id AND fromtable = "returcustomeritem"
				GROUP BY itemid), 0) AS jumlahretur, 

				IFNULL((SELECT SUM(satuan_kg) FROM returpabrikitem 
				WHERE itemid=temp.id AND fromtable = "returcustomeritem"
				GROUP BY itemid), 0) AS satuan_kg_retur,
				
				IFNULL((SELECT SUM(qty) FROM claimpembelianitem
				WHERE itemid=temp.id AND claimpembelianitem.fromtable = "returcustomeritem"
				GROUP BY itemid), 0) AS jumlahclaim, 

				IFNULL((SELECT SUM(satuan_kg) FROM claimpembelianitem 
				WHERE itemid=temp.id AND claimpembelianitem.fromtable = "returcustomeritem"
				GROUP BY itemid), 0) AS satuan_kg_claim

				FROM (
				SELECT returcustomeritem.itemid AS id, OrderItemDetail.typebarang, 
				OrderItemDetail.kodewarna, OrderItemDetail.warna, OrderItemDetail.satuan AS satuan, 
				DATE_FORMAT(returcustomer.tanggal, "%d-%m-%Y") AS tanggal, returcustomer.createdby, 

				OrderItemDetail.harga_customer, OrderItemDetail.harga_greige, OrderItemDetail.harga_celupan, OrderItemDetail.harga_matang 

				FROM (`returcustomer`) 
				JOIN `returcustomeritem` ON `returcustomer`.`noretur` = `returcustomeritem`.`noretur` 
				JOIN `OrderItemDetail` ON `OrderItemDetail`.`id` = `returcustomeritem`.`itemid` 
				AND OrderItemDetail.kodebarang = returcustomer.kodebarang 

				LEFT JOIN `returpabrikitem` ON `returcustomeritem`.`itemid` = `returpabrikitem`.`itemid`
				AND returpabrikitem.fromtable = "returcustomeritem"
				WHERE `returcustomer`.`noretur` = "'.$noretur.'"
				ORDER BY `returcustomeritem`.`itemid`) AS temp';
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	/* 22/11/2013 */
	function getdetailsuratjalanperbaikan($noretur) {
		$sql = "SELECT b.noretur, h.namaCustomer as customer,
				f.nopo, e.kodebarang, c.jenisbahan
				FROM orderitemdetail a
				INNER JOIN returpabrikitem b
				ON a.id = b.itemid
				INNER JOIN returpabrik e
				ON b.noretur = e.noretur
				AND e.noretur = '$noretur'
				AND e.status = 0
				INNER JOIN orderitem c 
				ON a.kodebarang = c.kodebarang
				LEFT JOIN claimpembelianitem d
				ON b.itemid = d.itemid
				AND b.fromtable = d.fromtable
				LEFT JOIN claimpembelian f
				ON e.nopo = f.nopo
				AND d.noclaim = f.noclaim
				INNER JOIN ordercustomer g
				ON c.noorder = g.noorder
				AND g.statusorder <> 'Batal'
				INNER JOIN customer h
				ON h.idCustomer = g.idCustomer
				WHERE e.noretur = '$noretur'
				GROUP BY b.noretur";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	function getdetailbarangsuratjalanperbaikan($noretur) {
		$sql = "SELECT b.noretur, b.qty AS qtyretur, IF(e.status = 0, 'BARU', 'SELESAI') AS STATUS, 
				DATE_FORMAT(e.tanggal,'%d-%m-%Y') AS tanggal,
				b.satuan_kg AS satuan_kg_retur,
				(b.qty - IFNULL(d.qty, 0)) AS qtyperbaikan,
				(b.satuan_kg - IFNULL(d.satuan_kg, 0)) AS satuan_kg_perbaikan,
				f.noclaim, d.qty AS qtyclaim, d.satuan_kg AS satuan_kg_claim,
				e.nopo, c.jenisbahan, a.*, b.fromtable
				FROM orderitemdetail a
				INNER JOIN returpabrikitem b
				ON a.id = b.itemid
				INNER JOIN returpabrik e
				ON b.noretur = e.noretur
				AND e.status = 0
				INNER JOIN orderitem c 
				ON a.kodebarang = c.kodebarang
				LEFT JOIN claimpembelian f
				ON e.nopo = f.nopo
				AND e.noretur = f.noreturpabrik
				LEFT JOIN claimpembelianitem d
				ON b.itemid = d.itemid
				AND b.fromtable = d.fromtable				
				AND f.noclaim = d.noclaim
				INNER JOIN ordercustomer g
				ON c.noorder = g.noorder
				AND g.statusorder <> 'Batal'
				WHERE e.noretur = '$noretur'
				GROUP BY b.noretur, b.itemid";
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	//added dio
	function getdetailsuratjalanperbaikan_byfrid($frid) {
		$sql = "SELECT fr.frid as frid, fr.frno as noretur, c.namacustomer as customer, po.pono as nopo, poc.ordercode as kodebarang, poc.fabrictype as jenisbahan
		FROM factoryreturn fr, factoryreturndetail frd, suratjalan sj, purchaseorder po, pocustomer poc, customer c
		WHERE fr.frid = '$frid'
		AND sj.sjid = fr.sjid
		AND po.poid = sj.poid
		AND poc.pocid = po.pocid
		AND c.idCustomer = poc.customerid
		GROUP BY fr.frid";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	function getdetailbarangsuratjalanperbaikan_byfrid($frid) {
		$sql = "SELECT fr.frid as frid, fr.frno as noretur, IF(fr.frstatus = 0, 'Baru', 'Selesai') AS status, 
			DATE_FORMAT(fr.createddate,'%d-%m-%Y') AS tanggal,
			IFNULL((SELECT SUM(frdsc.quantity) FROM factoryreturndetail frdsc WHERE frdsc.frid = fr.frid AND frdsc.itemid = frd.itemid),0) AS qtyperbaikan,
			IFNULL((SELECT SUM(frdsc.weight) FROM factoryreturndetail frdsc WHERE frdsc.frid = fr.frid AND frdsc.itemid = frd.itemid),0) AS satuan_kg_perbaikan,
			(SELECT po.pono FROM purchaseorder po, suratjalan sj WHERE sj.sjid = fr.sjid and po.poid = sj.poid) as nopo,
			(SELECT poc.fabrictype FROM purchaseorder po, pocustomer poc, suratjalan sj WHERE sj.sjid = fr.sjid and po.poid = sj.poid and poc.pocid = po.pocid) as jenisbarang,
			(SELECT poc.ordercode FROM purchaseorder po, pocustomer poc, suratjalan sj WHERE sj.sjid = fr.sjid and po.poid = sj.poid and poc.pocid = po.pocid) as kodebarang,
			pocd.itemtype as typebarang, frd.itemid as id, pocd.unittype as satuan, poc.ordercode as kodebarang, pocd.colorcode as kodewarna, pocd.color as warna
			FROM factoryreturn fr, factoryreturndetail frd, pocustomer poc,pocustomerdetail pocd
			WHERE fr.frid = '$frid'
			AND frd.frid = fr.frid
			AND pocd.itemid = frd.itemid
			AND poc.pocid = pocd.pocid
			GROUP BY frd.itemid";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	/* 10/10/2013 */
	function getdetailreturpabrikperbaikan($noretur) {
		$sql = 'SELECT DISTINCT *,
				
				IFNULL((SELECT SUM(qty) FROM returpabrikitem 
				WHERE itemid=temp.id AND noretur = temp.noretur
				GROUP BY itemid), 0) AS jumlahperbaikan, 

				IFNULL((SELECT SUM(satuan_kg) FROM returpabrikitem 
				WHERE itemid=temp.id AND noretur = temp.noretur
				GROUP BY itemid), 0) AS satuan_kg_perbaikan

				FROM (
				SELECT returpabrikitem.itemid AS id, OrderItemDetail.typebarang, 
				OrderItemDetail.kodewarna, OrderItemDetail.warna, OrderItemDetail.satuan AS satuan, 
				DATE_FORMAT(returpabrik.tanggal, "%d-%m-%Y") AS tanggal, returpabrik.createdby, 
				returpabrik.noretur,

				OrderItemDetail.harga_customer, OrderItemDetail.harga_greige, OrderItemDetail.harga_celupan, OrderItemDetail.harga_matang 

				FROM (returpabrik) 
				JOIN `returpabrikitem` ON `returpabrik`.`noretur` = `returpabrikitem`.`noretur` 
				JOIN `OrderItemDetail` ON `OrderItemDetail`.`id` = `returpabrikitem`.`itemid` 
				AND OrderItemDetail.kodebarang = returpabrik.kodebarang 
				
				WHERE `returpabrik`.`noretur` = "'.$noretur.'"
				AND returpabrik.status = 0
				ORDER BY `returpabrikitem`.`itemid`) AS temp';
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	function getdetailreturpabrikperbaikanberhasildiperbaiki($noretur) {
		$sql = "SELECT b.noretur, h.namaCustomer as customer,
				f.nopo, e.kodebarang, c.jenisbahan
				FROM orderitemdetail a
				INNER JOIN returpabrikitem b
				ON a.id = b.itemid
				INNER JOIN returpabrik e
				ON b.noretur = e.noretur
				AND e.noretur = '$noretur'
				AND e.status = 1
				INNER JOIN orderitem c 
				ON a.kodebarang = c.kodebarang
				INNER JOIN claimpembelianitem d
				ON b.itemid = d.itemid
				AND b.fromtable = d.fromtable
				INNER JOIN claimpembelian f
				ON e.nopo = f.nopo
				AND d.noclaim = f.noclaim
				INNER JOIN ordercustomer g
				ON c.noorder = g.noorder
				AND g.statusorder <> 'Batal'
				INNER JOIN customer h
				ON h.idCustomer = g.idCustomer
				GROUP BY b.noretur";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
	function getdetailbarangreturpabrikperbaikanberhasildiperbaiki($noretur) {
		$sql = "SELECT b.noretur, b.qty AS qtyretur, b.satuan_kg AS satuan_kg_retur,
				(b.qty - IFNULL(d.qty, 0)) AS qtyperbaikan,
				(b.satuan_kg - IFNULL(d.satuan_kg, 0)) AS satuan_kg_perbaikan,
				f.noclaim, d.qty AS qtyclaim, d.satuan_kg AS satuan_kg_claim,
				f.nopo, c.jenisbahan, a.*
				FROM orderitemdetail a
				INNER JOIN returpabrikitem b
				ON a.id = b.itemid
				INNER JOIN returpabrik e
				ON b.noretur = e.noretur
				AND e.noretur = '$noretur'
				AND e.status = 1
				INNER JOIN orderitem c 
				ON a.kodebarang = c.kodebarang
				INNER JOIN claimpembelianitem d
				ON b.itemid = d.itemid
				AND b.fromtable = d.fromtable
				INNER JOIN claimpembelian f
				ON e.nopo = f.nopo
				AND d.noclaim = f.noclaim
				INNER JOIN ordercustomer g
				ON c.noorder = g.noorder
				AND g.statusorder <> 'Batal'
				GROUP BY b.noretur";
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	function fakturpenjualangeneral() {
		$sql = "
		select	 date_format(i.invoicedate,'%d-%m-%Y') as tanggal,
				 date_format(date_add(i.invoicedate, INTERVAL poc.paymentperiod DAY),'%d-%m-%Y') as tanggalberakhir,
				 poc.company as perusahaan,
				 i.invoiceno as nofaktur,
				 i.invoicesjno nosuratjalan,
				 c.namaCustomer as customer,
				 s.name as sales,
				 i.invoiceid as fakturid, 
				 poc.pocno as noorder,
				 poc.ordercode as kodebarang,
				 poc.fabrictype as typebahan,
				 CONCAT('<pre>', (select GROUP_CONCAT(d.itemtype SEPARATOR '\n') from pocustomerdetail d where d.pocid = poc.pocid), '</pre>') as typebarang,
				 CONCAT('<pre>', (select GROUP_CONCAT(d.color SEPARATOR '\n') from pocustomerdetail d where d.pocid = poc.pocid), '</pre>') as warna,
				 CONCAT('<pre>', (select GROUP_CONCAT(d.quantity SEPARATOR '\n') from pocustomerdetail d where d.pocid = poc.pocid), '</pre>') as quantity,
				 CONCAT('<pre>', (select GROUP_CONCAT(d.weight SEPARATOR '\n') from pocustomerdetail d where d.pocid = poc.pocid), '</pre>') as satuan_kg
		from 	 invoice i, pocustomer poc, customer c, pocustomersales pocs, salesmen s
		where    poc.pocid = ( 
										select    DISTINCT  pocd.pocid 
										from      invoicedetail id 
										left join pocustomerdetail pocd on pocd.itemid = id.itemid 
										where     id.invoiceid = i.invoiceid
							  )
		and c.idCustomer = poc.customerid and pocs.pocid = poc.pocid  and s.id = pocs.salesmenid
		";
		/*
		$sql = "SELECT   date_format(a.tanggal,'%d-%m-%Y') as tanggal, 
			date_format(date_add(a.tanggal, INTERVAL b.lamabayar DAY),'%d-%m-%Y') as tanggalberakhir, 
			b.perusahaan as perusahaan,
		   a.nofaktur as nofaktur, 
			a.nosuratjalan as nosuratjalan, 
			c.namaCustomer as customer, 
			b.idSales as sales, 
			a.id as fakturid, 
			b.noorder as noorder, 
			b.kodebarang as kodebarang 
FROM 		fakturpenjualan a, 
			ordercustomer b, 
			customer c
WHERE 	a.noorder = b.noorder AND b.idCustomer = c.idCustomer
GROUP BY a.id";
			*/
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	function nopobykodebarang($kodebarang) {
		$this->db->select('nopo');
		$this->db->from('OrderCustomer');
		$this->db->join('po', 'po.kodebarang = OrderCustomer.kodebarang');
		$this->db->where('OrderCustomer.kodebarang', $kodebarang);
		$this->db->where('po.kodebarang', $kodebarang);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->nopo;
		}			
	}
	/* 29/09/2013 */
	function getnovouchertransaksibankkeluar() {
		$sql = "SELECT novoucher as novoucher FROM transaksibank ORDER BY novoucher DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->novoucher;
		}
	}
	function getnovouchertransaksibankmasuk() {
		$sql = "SELECT novoucher as novoucher FROM transaksibankmasuk ORDER BY novoucher DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->novoucher;
		}
	}
	
	function getnovouchertransaksipengeluaran() {
		$sql = "SELECT novoucher as novoucher FROM transaksipengeluaran ORDER BY novoucher DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->novoucher;
		}
	}
	function transaksiid($type) {
		$sql = "SELECT id, concat(transaksi,' ',keterangan) as transaksi 
				FROM transaksiakunting 
				WHERE transaksi = '$type'
				";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/* 23 09 2013 */

	function getdetailpogreigeformaklon($kodebarang) {
		$sql = "SELECT
					gudangid,
					id,
					kodebarang,
					nopo,
					nosuratjalan,
					typebarang,
					satuan,
					jumlahstock AS jumlah,
					satuankgstock AS satuan_kg
				FROM
				(
					SELECT
						a.id AS gudangid,
						b.itemid AS id,
						b.kodebarang,
						a.nopo,
						c.nosuratjalan,
						e.typebarang,
						e.satuan,
						IFNULL((
							SELECT SUM(stockgudang.jumlah)
							FROM gudang
							INNER JOIN stockgudang
							ON gudang.id = stockgudang.gudangid
							WHERE gudang.nopo = a.nopo
							AND stockgudang.itemid = b.itemid), 0) AS jumlahstock,
						IFNULL((
							SELECT SUM(stockgudang.satuan_kg)
							FROM gudang
							INNER JOIN stockgudang
							ON gudang.id = stockgudang.gudangid
							WHERE gudang.nopo = a.nopo
							AND stockgudang.itemid = b.itemid), 0) AS satuankgstock
					FROM gudang AS a
					INNER JOIN stockgudang AS b
					ON b.gudangid = a.id
					INNER JOIN orderitemdetail e
					ON a.kodebarang = e.kodebarang
					AND b.itemid = e.id
					LEFT JOIN suratjalanmaklon AS c
					ON a.nopo = c.nopogreige
					LEFT JOIN suratjalanmaklonitem AS d
					ON c.nosuratjalan = d.nosuratjalan
					AND e.id = d.itemid
					WHERE a.kodebarang = '$kodebarang'
					GROUP BY b.itemid) AS results";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function getdetailpogreigeforcetaksjmaklon($kodebarang, $nosj) {
		$sql = "SELECT
					a.noorder,
					b.id,
					b.typebarang,
					b.satuan,
					(b.jumlah - sum(d.qty)) as jumlah,
					(b.satuan_kg - sum(d.satuan_kg)) as satuan_kg
				FROM
					orderitem a,
					orderitemdetail b,
					suratjalanmaklon c,
					suratjalanmaklonitem d
				WHERE
					a.kodebarang = '$kodebarang'
				AND
					a.kodebarang = b.kodebarang
				AND
					b.id = d.itemid
				AND
					c.nosuratjalan <= '$nosj'
				AND
					c.nosuratjalan = d.nosuratjalan
				GROUP BY
					b.id";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function newgetdetailpogreigeforcetaksjmaklon($nosj) {
		$sql = "SELECT
					a.pocno as noorder,
					b.itemid as id,
					b.itemtype as typebarang,
					b.unittype as satuan,
					(b.quantity - sum(d.quantity)) as jumlah,
					(b.weight - sum(d.weight)) as satuan_kg
				FROM
					pocustomer a,
					pocustomerdetail b,
					sjmaklon c,
					sjmaklondetail d
				WHERE
					c.sjmno <= '$nosj'
				AND
					d.sjmid = c.sjmid
				AND
					d.itemid = b.itemid
				AND
					b.pocid = a.pocid
				GROUP BY
					b.itemid";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getnosuratjalanmaklon() {
		$sql = "SELECT nosuratjalan as nosjmaklon FROM suratjalanmaklon ORDER BY nosuratjalan DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nosjmaklon;
		}
	}
	function getsuppliercelup($kodebarang) {
		$sql = "SELECT a.idSupplier as idsupplier, a.namaSupplier as namasupplier FROM supplier a
				WHERE a.initialKodebarang = '".substr($kodebarang, 0, 1)."'";
	    $query = $this->db->query($sql);
	    return $query->result_array();
	}
	/* 03/10/2013 */
	function showmaklon($nosuratjalan) {
		$sql = "SELECT
					tanggal as ufdate,
					date_format(tanggal,'%d-%m-%Y') as tanggal,
					idSupplier as idsupplier,
					nosuratjalan as nosuratjalan,
					nomorkendaraan as nomobil,
					namapengendara as sopir,
					keterangan,
					createdby as dibuatoleh,
					nopogreige as nopogreige,
					nopocelup as nopocelup
				FROM
					suratjalanmaklon
				WHERE
					nosuratjalan = '$nosuratjalan'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function newshowmaklon($nosuratjalan) {
		$sql = "SELECT
					a.sjmdate as ufdate,
					date_format(a.sjmdate,'%d-%m-%Y') as tanggal,
					b.supplierid as idsupplier,
					a.sjmno as nosuratjalan,
					a.vehicleno as nomobil,
					a.drivername as sopir,
					a.notes as keterangan,
					a.createdby as dibuatoleh,
					b.pono as nopo
				FROM
					sjmaklon a, purchaseorder b
				WHERE
					a.sjmno = '$nosuratjalan' AND a.poid = b.poid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function barangperbaikanitem($nosuratjalan){
		$sql = "SELECT a.id_sj as id, a.kodebarang as kodebarang, a.qty as qty, a.satuan_kg as qtykg, b.typebarang as typebarang, b.warna as warna, b.satuan as satuan, c.jenisbahan as jenisbahan
		FROM orderitemterima_retur a, orderitemdetail b, orderitem c
		WHERE a.nosuratjalan = '$nosuratjalan' AND a.itemid = b.id AND b.kodebarang = c.kodebarang
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function newbarangperbaikanitem($nosuratjalan){
		$sql = "SELECT b.sjrdid as id, d.ordercode as kodebarang, b.quantity as qty, b.weight as qtykg, c.itemtype as typebarang, c.color as warna, c.unittype as satuan, d.fabrictype as jenisbahan
		FROM suratjalanretur a, suratjalanreturdetail b, pocustomerdetail c, pocustomer d
		WHERE a.sjrno = '$nosuratjalan' AND a.sjrid = b.sjrid AND b.itemid = c.itemid AND c.pocid = d.pocid
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function showbarangperbaikan($nosuratjalan){
		$sql = "SELECT a.nosuratjalan as nosuratjalan, a.noretur as noretur, c.nopo as nopo, date_format(a.tanggal,'%d-%m-%Y') as tanggal, a.createdby as createdby, b.namaSupplier as namaSupplier
		FROM orderitemterima_retur a, returpabrik c, po d, supplier b
		WHERE a.nosuratjalan = '$nosuratjalan' AND a.noretur = c.noretur AND c.nopo =  d.nopo AND d.idSupplier = b.idSupplier
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function newshowbarangperbaikan($nosuratjalan){
		$sql = "SELECT a.sjrno as nosuratjalan, d.frno as noretur, c.pono as nopo, date_format(a.sjrdate,'%d-%m-%Y') as tanggal, a.createdby as createdby, e.namaSupplier as namaSupplier
		FROM suratjalanretur a, suratjalan b, purchaseorder c, factoryreturn d, supplier e
		WHERE a.sjrno = '$nosuratjalan' AND a.frid = d.frid AND a.sjid =  b.sjid AND b.poid = c.poid AND c.supplierid = e.idSupplier
		";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function showmaklonitem($nosuratjalan) {
		$sql = "SELECT 
					a.itemid as itemid,
					a.qty as jumlah,
					a.satuan as satuan,
					a.satuan_kg as satuan_kg,
					b.id as id,
					b.typebarang as typebarang,
					b.warna as warna,
					c.kodebarang as kodebarang,
					c.jenisbahan as jenisbahan
				FROM
					suratjalanmaklonitem a, orderitemdetail b, orderitem c
			 	WHERE
			 		a.itemid = b.id AND a.nosuratjalan = '$nosuratjalan' AND b.kodebarang = c.kodebarang
			 	";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function newshowmaklonitem($nosuratjalan) {
		$sql = "SELECT 
					d.itemid as itemid,
					b.quantity as jumlah,
					d.unittype as satuan,
					b.weight as satuan_kg,
					b.sjmdid as id,
					d.itemtype as typebarang,
					d.color as warna,
					c.ordercode as kodebarang,
					c.fabrictype as jenisbahan
				FROM
					sjmaklon a, sjmaklondetail b, pocustomer c, pocustomerdetail d
			 	WHERE
			 		a.sjmno = '$nosuratjalan' AND a.sjmid = b.sjmid AND b.itemid = d.itemid AND c.pocid = d.pocid
			 	";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function showsuratjalancustomer($nosuratjalan) {
		$sql = "SELECT 
					a.idCustomer as idcustomer,
					date_format(b.tanggal,'%d-%m-%Y') as tanggal,
					b.nosuratjalan as nosuratjalan,
					b.namasupir as sopir,
					b.nokendaraan as nomobil,
					b.createdby as dibuatoleh,
					a.perusahaan as perusahaan
				FROM 
					ordercustomer a, fakturpenjualan b
				WHERE 
					a.noorder = b.noorder and b.nosuratjalan = '$nosuratjalan' ";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}
	function showsuratjalanitem($nosuratjalan) {
		$sql = "SELECT 
					a.qty as jumlah,
					b.satuan as satuan,
					a.satuan_kg as satuan_kg,
					b.typebarang as typebarang,
					b.warna as warna,
					c.jenisbahan as jenisbahan
				FROM
					fakturpenjualan d, fakturpenjualanitem a, orderitemdetail b, orderitem c
			 	WHERE
			 		a.itemid = b.id AND d.nosuratjalan = '$nosuratjalan' AND b.kodebarang = c.kodebarang AND d.id = a.fakturid
			 	";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function showfakturpenjualan($nofaktur) {
	    $sql = "SELECT 
	    			a.idCustomer as idcustomer,
	    			date_format(b.tanggal,'%d-%m-%Y') as tanggal,
	    			b.nofaktur as nofaktur,
	    			b.createdby as dibuatoleh,
	    			a.perusahaan as perusahaan
	    		FROM 
	    			ordercustomer a, fakturpenjualan b
	    		WHERE 
	    			a.noorder = b.noorder and b.nosuratjalan = '$nosuratjalan' ";
	    $query = $this->db->query($sql);
	    return $query->result_array();
	}
	function showfakturitem($nofaktur) {
		$sql = "SELECT 
					a.qty as jumlah,
					b.satuan as satuan,
					a.satuan_kg as satuan_kg,
					b.typebarang as typebarang,
					b.warna as warna,
					b.harga_customer as harga_customer,
					c.jenisbahan as jenisbahan,
					d.noorder as noorder
				FROM
					fakturpenjualan d, fakturpenjualanitem a, orderitemdetail b, orderitem c
			 	WHERE
			 		a.itemid = b.id AND d.nofaktur = '$nofaktur' AND b.kodebarang = c.kodebarang AND d.id = a.fakturid
			 	";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getterminbayar($noorder) {
		$sql = "SELECT lamabayar as lamabayar FROM ordercustomer WHERE noorder = '$noorder'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->lamabayar;
		}
	}
	function loaddetailbayarsupplierbynovoucher($novoucher) {
		$this->db->select('*');
		$this->db->from('pembayaransupplierdetail');
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();
		return $query->result();
	}
	function loaddetailbayarlainbynovoucher($novoucher) {
		$this->db->select('*');
		$this->db->from('transaksipengeluarandetail');
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();

		return $query->result();
	}
	function loaddetailbayarcustomerbynovoucher($novoucher) {
		/*$this->db->select('typebayarid');
		$this->db->from('transaksibankmasukdetail');
		$query = $this->db->get();
		$id = $query->result_array();
		$this->db->select();
		$this->db->from('pembayarancustomerdetail');
		$this->db->where_not_in('id',$id);
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();
		*/
		$sql = "SELECT * FROM pembayarancustomerdetail WHERE novoucher = '$novoucher' AND id NOT IN (SELECT typebayarid FROM transaksibankmasukdetail) AND (typebayar = 'Transfer Bank' OR typebayar = 'Giro')";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function loaddetailbayarsupplierbyid($id) {
		$this->db->select('*');
		$this->db->from('pembayaransupplierdetail');
		$this->db->where('id', $id);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row;
		}
	}
	function loaddetailbayarlainbyid($id) {
		$this->db->select('*');
		$this->db->from('transaksipengeluaran');
		$this->db->where('id', $id);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row;
		}
	}
	function loaddetailbayarcustomerbyid($id) {
		$this->db->select('*');
		$this->db->from('pembayarancustomerdetail');
		$this->db->where('id', $id);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row;
		}
	}
	function gettotalbayarsupplier($novoucher) {
		$this->db->select('total');
		$this->db->from('pembayaransupplier');
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->total;
		}
	}
	function gettotalbayarlain($novoucher) {
		$this->db->select('totalbayar');
		$this->db->from('transaksipengeluaran');
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->totalbayar;
		}
	}
	function gettotalbayarcustomer($novoucher) {
		$this->db->select('total');
		$this->db->from('pembayarancustomer');
		$this->db->where('novoucher', $novoucher);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->total;
		}
	}

	function loadparentmenuid($menuid) {
		$this->db->select('parentmenuid');
		$this->db->from('menu');
		$this->db->where('menuid', $menuid);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->parentmenuid;
		}
	}
	function loadmenu($parentmenuid) {
		$this->db->select('menuid, menudesc,menugroup,link');
		$this->db->from('menu');
		if($parentmenuid == "null")
			$this->db->where('parentmenuid IS NULL');
		else
			$this->db->where('parentmenuid', $parentmenuid);
		$query = $this->db->get();

		return $query->result_array();
	}
	function loadhakakses($parentmenuid, $idKaryawan) {
		$this->db->select('accessright.*, menu.menudesc, menu.link, menu.menugroup');
		$this->db->from('menu');
		$this->db->join('accessright', 'menu.menuid = accessright.menuid', 'left');
		if($parentmenuid == "null") {
			$this->db->where('menu.parentmenuid IS NULL');
		} else {
			//echo "parentmenuid: $parentmenuid";
			$this->db->where('menu.parentmenuid', $parentmenuid);
		}
		$this->db->where('userid', $idKaryawan);
		$this->db->or_where('userid IS NULL');
		$query = $this->db->get();

		return $query->result_array();
	}
	function loadkaryawan() {
		$this->db->select('idKaryawan, namaKaryawan, posisiKaryawan');
		$this->db->from('karyawan');
		$query = $this->db->get();
		return $query->result_array();
	}
	function getuserdetail($username) {
		$this->db->select('*');
		$this->db->from('systemuser');
		$this->db->where('username', $username);
		$query = $this->db->get();
		return $query->result();
	}
	function isExistUsernamePassword($username, $password) {
		$this->db->select("EXISTS(SELECT idKaryawan FROM (`karyawan`)
							WHERE `userlogin` =  '$username'
							AND `password` =  md5($password)) AS isExist
							");
		$query = $this->db->get('karyawan');
		foreach ($query->result() as $row) {
			return $row->isExist;
		}
	}
	function getnosj() {
		$this->db->select('nosuratjalan');
		$this->db->order_by('nosuratjalan', 'desc');
		$this->db->limit('1');
		$query = $this->db->get('suratjalancustomerperbaikan');
		foreach ($query->result() as $row) {
			return $row->nosuratjalan;
		}
	}

	/* 12102013 */
	function getkomisidetail($noorder) {
		$sql = "SELECT c.noorder as noorder,
				c.idSales as sales,
				d.nofaktur as nofaktur,
				if(harga_matang = 0, (sum(((a.satuan_kg*b.harga_greige)+(a.satuan_kg*harga_celupan))+(((a.satuan_kg*b.harga_greige)+(a.satuan_kg*harga_celupan))*c.diskon))),((a.satuan_kg*b.harga_matang)+ ((a.satuan_kg*b.harga_matang)*c.diskon))) as totalpoc
				FROM 
					  fakturpenjualanitem a, orderitemdetail b, ordercustomer c, fakturpenjualan d
				WHERE 
					  a.itemid = b.id AND
					  c.kodebarang = a.kodebarang AND
		            c.noorder = d.noorder AND
		            a.fakturid = d.id AND
		            d.noorder = '$noorder'";
		 $query = $this->db->query($sql);
		 return $query->result_array();
		 	
	}
	function getnobayarkomisi() {
		$sql = "SELECT nobayar as nobayar FROM komisipenjualan ORDER BY nobayar DESC LIMIT 0,1";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->nobayar;
		}
	}
	function getnofaktur($noorder) {
		$sql = "SELECT nofaktur as faktur FROM fakturpenjualan WHERE noorder = '$noorder'";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->faktur;
		}
	}
	function getnofakturkodebarang($noorder) {
		$sql = "SELECT b.kodebarang as kodebarang FROM fakturpenjualan a, fakturpenjualanitem b WHERE a.noorder = '$noorder' AND a.id = b.fakturid
		  GROUP BY b.kodebarang";
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->kodebarang;
		}
	}
	function getdetailkomisi($id) {
		$sql = "SELECT 
				a.nobayar as nobayar,
				a.noorder as noorder,
				date_format(a.tanggal,'%d-%m-%Y') as tanggal,
				a.namasales	as namasales,
				a.besarkomisi as besarkomisi,
				format(a.total,0) as total,
				a.mengetahui as mengetahui,
				a.tipebayar	as tipebayar,
				a.nopembayaran as nopembayaran,
				date_format(a.tanggalpembayaran,'%d-%m-%Y') as tanggalpembayaran,
				a.createdby as createdby,
				b.nofaktur as nofaktur,
				c.kodebarang as kodebarang
				FROM 
				komisipenjualan a, fakturpenjualan b, ordercustomer c
				WHERE
				a.noorder = b.noorder AND a.noorder = c.noorder
				AND a.id = $id
				GROUP BY c.kodebarang";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/* 09/11/2013 */
	function insertbesarkomisi($fakturid, $columnname, $value, $idSales) {
		$results = $this->getnoreturfromfakturid($fakturid);
		if(sizeof($results) == 0)
			$this->insertbesarretur($fakturid, null, $columnname, $value, $idSales);
		foreach ($results as $noretur) {
			$this->db->select('noretur');
			$this->db->from('fakturpenjualankomisi');
			$this->db->where('noretur', $noretur['noretur']);
			$this->db->where('fakturid', $fakturid);
			$isExist = $this->db->get();
			
			$this->insertbesarretur($fakturid, $noretur['noretur'], $columnname, $value, $idSales);
		}
		
		$query = $this->db->query("SELECT SUM(fakturpenjualanitem.satuan_kg * fakturpenjualankomisi.besarkomisi) as totalkomisi 
			FROM fakturpenjualanitem, fakturpenjualan, fakturpenjualankomisi 
			WHERE fakturpenjualan.id = fakturpenjualanitem.fakturid 
			AND fakturpenjualan.id = fakturpenjualankomisi.fakturid 
			AND fakturpenjualan.id = $fakturid");
		foreach ($query->result() as $row) {
			return $row->totalkomisi;
		}
	}
	/* 11/11/2013 */
	function updatebesarkomisi($komisiid, $fakturid, $columnname, $value, $idSales) {
		$results = $this->getnoreturfromfakturid($fakturid);
		if(sizeof($results) == 0)
			$this->updatebesarretur($fakturid, null, $columnname, $value, $idSales);
		foreach ($results as $noretur) {
			$this->db->select('id, noretur');
			$this->db->from('fakturpenjualankomisi');
			$this->db->where('noretur', $noretur['noretur']);
			$this->db->where('fakturid', $fakturid);
			$isExist = $this->db->get();

			if($isExist->num_rows > 0)
				$this->updatebesarretur($fakturid, $noretur['noretur'], $columnname, $value, $idSales);
			else
				$this->insertbesarretur($fakturid, $noretur['noretur'], $columnname, $value, $idSales);
		}
		
		$query = $this->db->query("SELECT SUM(fakturpenjualanitem.satuan_kg * fakturpenjualankomisi.besarkomisi) as totalkomisi 
			FROM fakturpenjualanitem, fakturpenjualan, fakturpenjualankomisi 
			WHERE fakturpenjualan.id = fakturpenjualanitem.fakturid 
			AND fakturpenjualan.id = fakturpenjualankomisi.fakturid 
			AND fakturpenjualan.id = $fakturid
			AND fakturpenjualankomisi.id = $komisiid");
		foreach ($query->result() as $row) {
			return $row->totalkomisi;
		}
	}
	/* 17/11/2013 */
	function getnoreturfromfakturid($fakturid) {
		$this->db->select('noretur');
 		$this->db->from('fakturpenjualan');
 		$this->db->join('returcustomer', 'fakturpenjualan.nofaktur = returcustomer.nofaktur');
 		$this->db->where('fakturpenjualan.id', $fakturid);
 		$query = $this->db->get();
 		return $query->result_array();
	}
	/* 11/11/2013 */
	function insertbesarretur($fakturid, $noretur, $columnname, $value, $idSales) {
		$param = array(
			'fakturid' => $fakturid,
			'noretur' => $noretur,
			'idSales' => $idSales,
			$columnname => $value, 
			'createdby' => $this->session->userdata('username'), 
			'CreatedDate' => date('Y-m-d h:i:s'));
		$this->db->insert('fakturpenjualankomisi', $param);
		$lastid = $this->db->insert_id();
		$query = $this->db->query("SELECT SUM(returcustomeritem.satuan_kg * fakturpenjualankomisi.besarkomisi) as totalkomisi 
			FROM fakturpenjualanitem
			INNER JOIN fakturpenjualan
			ON fakturpenjualan.id = fakturpenjualanitem.fakturid 
			INNER JOIN fakturpenjualankomisi 
			ON fakturpenjualan.id = fakturpenjualankomisi.fakturid 
			LEFT JOIN returcustomer
            ON fakturpenjualan.nofaktur = returcustomer.nofaktur
            LEFT JOIN returcustomeritem
            ON returcustomer.noretur = returcustomeritem.noretur
			WHERE fakturpenjualan.id = $fakturid
			AND fakturpenjualankomisi.noretur = '$noretur'
			AND fakturpenjualankomisi.id = $lastid");
		foreach ($query->result() as $row) {
			return $row->totalkomisi;
		}
	}
	/* 11/11/2013 */
	function updatebesarretur($fakturid, $noretur, $columnname, $value, $idSales) {
		// !!important!! update besarkomisi for all komisiid with fakturid = $fakturid
		$param = array(
			$columnname => $value, 
			'createdby' => $this->session->userdata('username'), 
			'CreatedDate' => date('Y-m-d h:i:s'));
		$this->db->where('fakturid', $fakturid);
		$this->db->update('fakturpenjualankomisi', $param);

		$query = $this->db->query("SELECT SUM(returcustomeritem.satuan_kg) * fakturpenjualankomisi.besarkomisi as totalkomisi 
			FROM fakturpenjualanitem
			INNER JOIN fakturpenjualan
			ON fakturpenjualan.id = fakturpenjualanitem.fakturid 
			INNER JOIN fakturpenjualankomisi 
			ON fakturpenjualan.id = fakturpenjualankomisi.fakturid 
			LEFT JOIN returcustomer
            ON fakturpenjualan.nofaktur = returcustomer.nofaktur
            LEFT JOIN returcustomeritem
            ON returcustomer.noretur = returcustomeritem.noretur
            AND fakturpenjualanitem.itemid = returcustomeritem.itemid
			WHERE fakturpenjualan.id = $fakturid
			AND fakturpenjualankomisi.noretur = '$noretur'");
		foreach ($query->result() as $row) {
			return $row->totalkomisi;
		}
	}
	function totaldetailpembayarancustomer($id,$bln,$thn) {
		$sql = "SELECT 
					concat('Rp. ',format(sum(b.total),0)) as total
				 FROM 
				 	pembayarancustomer b 
				 WHERE 
				 	month(b.tanggal) = $bln AND year(b.tanggal) = $thn AND b.idcustomer = $id";
				 
		$query = $this->db->query($sql);
		foreach ($query->result() as $row)
		{
		   return $row->total;
		}
	}
    function totaldetailpembayaransupplier($id,$bln,$thn) {
    	$sql = "SELECT 
    				concat('Rp. ',format(sum(b.total),0)) as total
    			 FROM 
    			 	pembayaransupplier b 
    			 WHERE 
    			 	month(b.tanggal) = $bln AND year(b.tanggal) = $thn AND b.idsupplier = $id";
    			 
    	$query = $this->db->query($sql);
    	foreach ($query->result() as $row)
    	{
    	   return $row->total;
    	}
    }


    function getlastnokontrak() {
    	$this->db->select('contractno');
    	$this->db->from('greigecontract');
    	$this->db->order_by('contractno', 'desc');
    	$this->db->limit('1');
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			return $row->contractno;
		}
    }

    public function getcontractid($nokontrak) {
    	$sql = "SELECT contractid FROM greigecontract WHERE contractno = '$nokontrak'";
    	$query = $this->db->query($sql);
    	return $query->result()[0]->contractid;
    }

    public function getkontrakgreigedetail($nokontrak) {
    	$this->db->select('greigecontract.*, supplier.contactSupplier, supplier.namaSupplier');
    	$this->db->join('supplier', 'greigecontract.supplierid = supplier.idSupplier');
    	$this->db->where('contractno', $nokontrak);
    	$query = $this->db->get('greigecontract');
    	return $query->result();
    }

    public function getkontrakgreigeitem($nokontrak) {
    	$this->db->select();
    	$this->db->where('contractid', $nokontrak);
    	$query = $this->db->get('greigecontractdetail');
    	return $query->result_array();
    }

    public function getkontrakgreigeitembytype($nokontrak, $typebarang) {
    	$this->db->select();
    	$this->db->where('contractid', $nokontrak);
    	$this->db->where('itemtype', $typebarang);
    	$this->db->order_by('contractdetailid');
    	$query = $this->db->get('greigecontractdetail');
    	return $query->result_array();
    }
    public function gettotalfakturpenjualan() {
       $sql = "SELECT * FROM pembayarancustomer";
       $query = $this->db->query($sql);
       return $query->num_rows();
    }
    function getlistlaporanfakturpenjualan() {
    	$sql = "SELECT 
    			 a.nofaktur as nofaktur,
    			 date_format(b.tglvoucher,'%d-%m-%Y') as tglbayar,
    			 c.namacustomer as namacustomer,
    			  
    			";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function laporanbayarcustomer() {
    	$sql = "SELECT 
    	              a.novoucher as novoucher
    		      
    		          
    	FROM pembayarancustomer a  
    			        ";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function detailvoucherpambayarancustomer($novoucher) {
    	$sql = "
    	        SELECT 
             	  c.nofaktur as nofaktur,
    	          DATE_FORMAT(d.tanggal,'%d-%m-%Y') as tanggal,
    	          (SELECT count(total) FROM pembayarancustomerfaktur WHERE novoucher = a.novoucher GROUP BY novoucher) as total,
                  sum((e.satuan_kg*f.harga_customer)- ((e.satuan_kg*f.harga_customer)*g.diskon/100))
                      as totalfaktur,
    	          c.total as totalsudahbayar
    		    FROM pembayarancustomer a  
    		        INNER JOIN customer b 
    		        ON a.idCustomer = b.idCustomer 
    		        INNER JOIN pembayarancustomerfaktur c
    		        ON a.novoucher = c.novoucher
    		        INNER JOIN ordercustomer g
    		        ON c.noorder = g.noorder
    		        LEFT JOIN fakturpenjualan d
    		        ON c.nofaktur = d.nofaktur
    		        LEFT JOIN fakturpenjualanitem e
    		        ON d.id = e.fakturid
    		        LEFT JOIN orderitemdetail f
    		        ON e.itemid = f.id
         		 WHERE a.novoucher = '$novoucher'
                         GROUP BY c.nofaktur
    	                         
    		   ";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function namacustomerbynovouherpembayaran($novoucher) {
    	$sql = "
    	        SELECT 
    	     	  distinct(b.namaCustomer) as namacustomer
    		    FROM pembayarancustomer a  
    		        INNER JOIN customer b 
    		        ON a.idCustomer = b.idCustomer 
    		        INNER JOIN pembayarancustomerfaktur c
    		        ON a.novoucher = c.novoucher
    		        INNER JOIN ordercustomer g
    		        ON c.noorder = g.noorder
    		        LEFT JOIN fakturpenjualan d
    		        ON c.nofaktur = d.nofaktur
    		        LEFT JOIN fakturpenjualanitem e
    		        ON d.id = e.fakturid
    		        LEFT JOIN orderitemdetail f
    		        ON e.itemid = f.id
    	 		 WHERE a.novoucher = '$novoucher'
    	         GROUP BY b.namaCustomer
    	                         
    		   ";
    	$query = $this->db->query($sql);
    	foreach ($query->result() as $row)
    	{
    	   return $row->namacustomer;
    	}
    }
    function laporanfakturpembelian() {
    	$sql = "SELECT novoucher FROM pembayaransupplier";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function detailvoucherpambayaransupplier($novoucher) {
    	$sql = "
    	        SELECT 
             	  c.nopo as nopo,
    	          DATE_FORMAT(d.tanggal,'%d-%m-%Y') as tanggal,
    	          (SELECT count(total) FROM pembayaransupplierdetail WHERE novoucher = a.novoucher GROUP BY novoucher) as total,
                  CASE LEFT(results.nopo, 3)
                  WHEN 'POG'
                  THEN f.
                  WHEN 'POM'
                  sum((e.satuan_kg*f.harga_customer)- ((e.satuan_kg*f.harga_customer)*g.diskon/100))
                      as totalfaktur,
    	          c.total as totalsudahbayar
    		    FROM pembayaransupplier a  
    		        INNER JOIN supplier b 
    		        ON a.idsupplier = b.idsupplier 
    		        INNER JOIN pembayaransupplier c
    		        ON a.novoucher = c.novoucher
    		        INNER JOIN ordercustomer g
    		        ON c.noorder = g.noorder
    		        LEFT JOIN PO d
    		        ON c.nofaktur = d.nofaktur
    		        LEFT JOIN fakturpenjualanitem e
    		        ON d.id = e.fakturid
    		        LEFT JOIN orderitemdetail f
    		        ON e.itemid = f.id
         		 WHERE a.novoucher = '$novoucher'
                         GROUP BY c.nofaktur
    	                         
    		   ";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    /* desember 2013 */
    function getketeranganneraca($type) {
    	$sql = "SELECT * FROM transaksiakunting WHERE type='$type'";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }
    function gettransaksiakunting($transaksi) {
    	$sql = "SELECT * FROM transaksiakunting WHERE transaksi='$transaksi'";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }


    // for SJ Maklon
    function getkodebarangsjmaklon($sj) {
    	$sql = "SELECT kodebarang FROM po a, suratjalanmaklon b WHERE b.nopogreige = a.nopo AND b.nosuratjalan = '" . $sj . "'";
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }

    function getmaklonsenttotalitems($kodebarang, $itemid) {
		$sql = "SELECT
					SUM( c.qty ) AS qty,
					SUM( c.satuan_kg) AS satuan_kg
				FROM
					po a,
					suratjalanmaklon b,
					suratjalanmaklonitem c
				WHERE
					a.kodebarang =  '$kodebarang'
				AND 
					a.nopo LIKE 'POG%'
				AND 
					a.nopo = b.nopogreige
				AND 
					b.nosuratjalan = c.nosuratjalan
				AND 
					c.itemid = $itemid";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailsuratjalan_bynosj2($nosuratjalan, $tipepo) {
		$sql = "SELECT b.id_sj, b.itemid AS id, a.typebarang AS typebarang, 
				a.kodewarna AS kodewarna, a.warna AS warna, a.kodebarang,
				a.satuan AS satuan, b.nosuratjalan AS suratjalan, 
				DATE_FORMAT(b.tanggal,'%d-%m-%Y') AS tanggal, 
				b.createdby AS createdby,
				(SELECT SUM(qty) FROM orderitemterima WHERE itemid = b.itemid AND nosuratjalan = b.nosuratjalan AND typeterima = '$tipepo' GROUP BY itemid) AS jumlah, 
				(SELECT SUM(satuan_kg) FROM orderitemterima WHERE itemid = b.itemid AND nosuratjalan = b.nosuratjalan AND typeterima = '$tipepo' GROUP BY itemid) AS satuan_kg,
				(SELECT SUM(qty) FROM returpabrik INNER JOIN returpabrikitem ON returpabrik.noretur = returpabrikitem.noretur WHERE itemid = d.itemid AND nopo = e.nopo GROUP BY itemid) AS jumlahretur,
				(SELECT SUM(satuan_kg) FROM returpabrik INNER JOIN returpabrikitem ON returpabrik.noretur = returpabrikitem.noretur WHERE itemid = d.itemid AND nopo = e.nopo GROUP BY itemid) AS satuan_kg_retur,
				IFNULL((SELECT SUM(jumlah) FROM gudang INNER JOIN stockgudang ON gudang.id = stockgudang.gudangid WHERE itemid = d.itemid AND nopo = e.nopo GROUP BY itemid), 0) AS jumlahstock,
				IFNULL((SELECT SUM(satuan_kg) FROM gudang INNER JOIN stockgudang ON gudang.id = stockgudang.gudangid WHERE itemid = d.itemid AND nopo = e.nopo GROUP BY itemid), 0) AS satuan_kg_stock,
				a.harga_customer, 
				a.harga_greige, a.harga_celupan, 
				a.harga_matang 
				FROM orderitemdetail a
				INNER JOIN orderitemterima b ON a.id = b.itemid AND a.kodebarang = b.kodebarang 
				INNER JOIN po e ON a.kodebarang = e.kodebarang
				LEFT JOIN returpabrik c ON b.kodebarang = c.kodebarang
				LEFT JOIN returpabrikitem d ON c.noretur = d.noretur AND b.itemid = d.itemid
				WHERE b.nosuratjalan = '$nosuratjalan'
				GROUP BY b.itemid 
				ORDER BY b.itemid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getpocelup($kodebarang) {
		$sql = "SELECT nopo
				FROM po
				WHERE nopo LIKE 'PCL%'
				AND kodebarang = '$kodebarang'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function supplierHaveClaim($data){
		
		$param = '';
		foreach ($data as $key => $value) {
				$param .= '\''.$data[$key]['nopo'].'\',';
		}
		$param = substr($param, 0, -1);
		$sql = "SELECT SUM(oi.satuan_kg * (oi.harga_greige + oi.harga_celupan + oi.harga_matang)) as result
				FROM claimpembelian c, claimpembelianitem ci, orderitemdetail oi 
				WHERE c.nopo IN ($param)
				AND ci.noclaim = c.noclaim
				AND oi.id = ci.itemid";
	    
		$result = $this->db->query($sql);

		return $result->num_rows() > 0 ? $result->result_array()[0]['result'] : false;
	}

	function customerHaveClaim($data){
		$param = '';
		foreach ($data as $key => $value) {
				$param .= '\''.$data[$key]['kodebarang'].'\',';
		}
		$param = substr($param, 0, -1);
		$sql = "SELECT SUM(oi.satuan_kg * (oi.harga_greige + oi.harga_celupan + oi.harga_matang)) as result
				FROM claimpembelian c, claimpembelianitem ci, orderitemdetail oi 
				WHERE c.kodebarang IN ($param)
				AND ci.noclaim = c.noclaim
				AND oi.id = ci.itemid";
	    
		$result = $this->db->query($sql);
		return $result->num_rows() > 0 ? $result->result_array()[0]['result'] : false; 
	}
	function databarangperbaikan(){
		$sql = "SELECT a.sjrid as id,
				a.sjrno as nosuratjalan, 
				date_format(a.sjrdate,'%d-%m-%Y') as tanggal, date_format(a.sjrdate,'%d-%m-%Y') as filter,
				b.sjtype as typeterima,
				c.pono as nopo,
				d.frno as noretur
		FROM suratjalanretur a, suratjalan b, purchaseorder c, factoryreturn d
		WHERE a.frid = d.frid AND a.sjid = b.sjid AND b.poid = c.poid 
		GROUP BY a.sjrno";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailbarangperbaikan($sjrid){
		$sql = "SELECT b.quanity as qty,
				b.weight as qtykg,
				c.color as warna,
				c.itemtype as tipebarang,
				d.ordercode as kodebarang,
				d.fabrictype as tipefabric
		FROM suratjalanretur a, suratjalanreturdetail b, pocustomerdetail c, pocustomer d
		WHERE a.sjrid = '$sjrid' AND a.sjrid = b.sjrid AND b.itemid = c.itemid AND c.pocid = d.pocid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function datareturpembelian(){
		$sql = "SELECT a.frid as id,
				a.frno as noretur,
				date_format(a.createddate,'%d-%m-%Y') as tanggal, date_format(a.createddate,'%d-%m-%Y') as filter,
				b.pono as nopo,
				e.namaCustomer as namacustomer,
				c.namaSupplier as namasupplier,
				CASE a.frstatus WHEN '0' THEN 'Baru'
				WHEN '1' THEN 'Selesai'
				WHEN '2' THEN 'Claim'
				END as status
		FROM factoryreturn a, purchaseorder b, supplier c, pocustomer d, customer e, suratjalan f
		WHERE a.sjid = f.sjid AND f.poid = b.poid AND b.supplierid = c.idSupplier AND b.pocid = d.pocid AND d.customerid = e.idCustomer
		GROUP BY a.frno";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function detailreturpembelian($id){
		$sql = "SELECT b.quantity as qty,
				b.weight as qtykg,
				c.color as warna,
				d.fabrictype as tipefabric,
				d.ordercode as kodebarang
		FROM factoryreturn a, factoryreturndetail b, pocustomerdetail c, pocustomer d
		WHERE a.frid = '$id' AND a.frid = b.frid AND b.itemid = c.itemid AND c.pocid = d.pocid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	//added dio
	function detailfactoryreturn($frid) {
		$sql = "SELECT fr.frno as noretur, po.pono as nopo, poc.ordercode as kodebarang, date_format(fr.createddate,'%d-%m-%Y') as tanggal, fr.notes as keterangan, s.namaSupplier as namaSupplier, fr.createdby as dibuatoleh
		FROM factoryreturn fr, supplier s, purchaseorder po, suratjalan sj, pocustomer poc
		WHERE fr.frid = '$frid' AND sj.sjid = fr.sjid AND po.poid = sj.poid AND poc.pocid = po.pocid AND s.idsupplier = po.supplierid";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailitemfactoryreturn($frid) {
		$sql = "SELECT frd.quantity as qtyretur, frd.weight as satuan_kg_retur, poc.ordercode,
		pocd.pricecustomer, pocd.pricecelupan, pocd.pricematang, poc.discount,
		IFNULL((SELECT gcd.price FROM greigecontractdetail gcd, greigecontracttransaction gct WHERE gcd.contractdetailid = gct.contractdetailid AND gct.itemid = frd.itemid),0) as harga_greige,
		IFNULL((SELECT SUM(quantity) FROM invoicedetail WHERE itemid = frd.itemid),0) AS jumlahfaktur,
		IFNULL((SELECT SUM(weight) FROM invoicedetail WHERE itemid = frd.itemid),0) AS satuan_kg_faktur,
		pocd.itemtype, frd.itemid as id, pocd.color, pocd.unittype, frd.weight as satuan_kg,
		poc.fabrictype
		FROM factoryreturn fr, factoryreturndetail frd, pocustomer poc, pocustomerdetail pocd
		WHERE fr.frid = '$frid'
		AND frd.frid = fr.frid
		AND pocd.itemid = frd.itemid
		AND poc.pocid = pocd.pocid";
		$query = $this->db->query($sql);

		return $query->result_array();	
	}

	function getsjidbyfrid($frid){
		$sql = "SELECT sjid
		FROM factoryreturn
		WHERE frid = '$frid'";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->sjid;
	}

	function checksuratjalanreturn($frid){
		$sql = "SELECT SUM(sjrd.weight) as jumlah
		FROM suratjalanretur sjr, suratjalanreturdetail sjrd
		WHERE sjr.frid = '$frid'
		AND sjrd.sjrid = sjr.sjrid
		";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->jumlah;
	}

	function checkfactoryreturn($frid){
		$sql = "SELECT SUM(frd.weight) as jumlah
		FROM factoryreturn fr, factoryreturndetail frd
		WHERE fr.frid = '$frid'
		AND frd.frid = fr.frid
		";
		$query = $this->db->query($sql);
		$res = $query->result();

		return $res[0]->jumlah;
	}

	public function datasuratjalanmaklon(){
		$sql = "SELECT a.sjmid as id,
				date_format(a.sjmdate,'%d-%m-%Y') as tanggal, date_format(a.sjmdate,'%d-%m-%Y') as filter,
				a.sjmno as nosuratjalan,
				a.notes as keterangan,
				b.namaSupplier as namasupplier,
				c.pono as nopo
				FROM sjmaklon a, supplier b, purchaseorder c
				WHERE a.poid = c.poid AND c.supplierid = b.idSupplier"; 
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function detailsuratjalanmaklon($id){
		$sql = "SELECT b.quantity as qty,
				b.weight as qtykg,
				c.color as warna,
				d.ordercode as kodebarang,
				d.fabrictype as jenisbahan
				FROM sjmaklon a, sjmaklondetail b, pocustomerdetail c, pocustomer d
				WHERE a.sjmid = '$id' AND a.sjmid = b.sjmid AND b.itemid = c.itemid AND c.pocid = d.pocid "; 
		$query = $this->db->query($sql);
		return $query->result_array();	
	}
}

/* End of file bas_model.php */
/* Location: ./application/model/bas_model.php */