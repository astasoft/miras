<?php

/**
 * File ini berisi fungsi-fungsi (query) yang berhubungan dengan tabel artikel
 */

 
 /**
 * Fungsi untuk menampilkan artikel terbaru dari masing-masing kategori
 * @author Alfa Radito Sii Anak Ganteng <qwertqwe16@yahoo.co.id>, Miftah Faridl <vcode84@gmail.com>
 * @since version 1.0
 * @example BASE_PATH/models/artikel_model.php
 */
function get_latest_article($last=10) {
	// query untuk menampilkan 10 artikel terbaru
	$query = 'SELECT a.artikel_id, a.artikel_judul, a.artikel_isi, a.artikel_tgl, k.kategori_id, k.kategori_nama
			FROM artikel_kategori ak 
			LEFT JOIN artikel a ON ak.artikel_id = a.artikel_id
			LEFT JOIN kategori k ON ak.kategori_id = k.kategori_id
			ORDER BY a.artikel_tgl DESC
			LIMIT ' . $last;
			
	$result = mysql_query($query);
	
	if (!$result) {
		// query error
		return FALSE;
	}
	
	$artikel = array();
	while ($data = mysql_fetch_object($result)) {
		// masukkan data setiap result object ke array $artikel
		$artikel[] = $data;
	}
	// kembalikan hasil
	return $artikel;
}

/**
 * Fungsi untuk menampilkan artikel berdasarkan judul dan isi
 */
function get_article_based_on($judul='', $isi='') {
	
	if ($judul=='' && $isi!='') {
		$query = 'SELECT * FROM artikel
			  WHERE artikel_isi LIKE \'%'.$isi.'%\'';
	} elseif ($judul!='' && $isi=='') {
		$query = 'SELECT * FROM artikel
			  WHERE artikel_judul LIKE \'%'.$judul.'%\'';
	} elseif ($judul!='' && $isi!='') {
		$query = 'SELECT * FROM artikel
			  WHERE artikel_judul LIKE \'%'.$judul.'%\' OR artikel_isi LIKE \'%'.$isi.'%\'';
	} else {
		
		return array();
	}
	
	$result = mysql_query($query);
	
	if (!$result) {
		// query error
		return FALSE;
	}
	
	$artikel = array();
	while ($data = mysql_fetch_object($result)) {
		// masukkan data setiap result object ke array $artikel
		$artikel[] = $data;
	}
	// kembalikan hasil
	return $artikel;
}

function get_all_article() {
	// query untuk menampilkan 10 artikel terbaru
	$query = 'SELECT *
				FROM artikel';
			
	$result = mysql_query($query);
	
	if (!$result) {
		// query error
		return FALSE;
	}
	
	$artikel = array();
	while ($data = mysql_fetch_object($result)) {
		// masukkan data setiap result object ke array $artikel
		$artikel[] = $data;
	}
	// kembalikan hasil
	return $artikel;
}
