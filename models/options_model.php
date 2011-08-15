<?php

/**
 * File ini berisi fungsi-fungsi (query) yang berhubungan dengan tabel options
 *
 * @package models
 * @copyright 2011 Bajol Startup Team
 */

/**
 * Fungsi untuk melakukan update pada option
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $option_name 
 * @param string $option_value
 * @return void
 */
function update_option($option_name, $option_value, $option_autoload=1) {
	global $_MR;
	$insert = FALSE;
	
	if (!array_key_exists($option_name, $_MR['options'])) {
		// option belum ada maka prosesnya adalah insert bukan update
		$insert = TRUE;
	}
	
	// option value yang masuk _MR['option'] tidak perlu diserialize
	// karena akan langsung dipake
	$_MR['options'][$option_name] = $option_value;
	// mengecek $opt_value apakah array atau jika iya maka diserialize terlebih dahulu
	if (is_array($option_value) || is_object($option_value)) {
		$option_value = serialize($option_value);
	}
	
	// masukkan ke cache untuk dimasukkan ke datatabase 
	if ($insert === TRUE) {
		$_MR['options_insert_cache'][$opt_name] = array(
													'value' => $option_value,
													'autoload' => $option_autoload
												  );
	} else {
		$_MR['options_update_cache'][$option_name] = $option_value;
	}
}

/**
 * Fungsi untuk melakukan penghapusan pada option
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $option_name 
 * @return void|boolean
 */
function delete_option($option_name) {
	global $_MR;
	
	if (!array_key_exists($option_name, $_MR['options'])) {
		// option tidak ada, jadi tidak perlu diteruskan karena kemungkinan
		// sudah ada rutin kode lain yang memanggil ini sebelumnya
		return FALSE;
	}
	
	// unset nilai nama option dari _MR['option']
	unset($_MR['options'][$option_name]);
	
	// masukkan ke cache untuk dilakukan penghapusan dari database
	// saat proses script end
	$_MR['options_delete_cache'][$option_name] = $option_value;
}

/**
 * Method untuk memasukkan options kedalam array $_MR['options'] dan $_MR['options_insert_cache']
 * 
 * @author Irianto Bunga Pratama<me@iriantobunga.com>
 * @since Version 1.0
 * 
 * @param String $option_name 
 * @param String $option_value
 * @param Integer optional $opt_autoload default 1
 * 
 * @return void
 */
function insert_option($option_name, $option_value, $opt_autoload=1) {
	global $_MR; 
	$update = FALSE;
	
	if (array_key_exists($option_name, $_MR['options'])) {
		// option belum ada maka prosesnya adalah insert bukan update
		$update = TRUE;
	}
	
	/* memasukkan opt_name sebagai key dan opt_value sebagai val pada _MR[options]
	 */
	$_MR['options'][$option_name] = $option_value;
	
	// mengecek $option_value apakah array atau jika iya maka diserialize terlebih dahulu
	if (is_array($option_value) || is_object($option_value)) {
		$option_value = serialize($option_value);
	}
	
	/*  memasukkan opt_name sebagai key dan array sebagai val pada _MR[options_insert_cache]
	 */
	if ($update === TRUE) {
		$_MR['options_update_cache'][$option_name] = $option_value;
	} else { 
		$_MR['options_insert_cache'][$option_name] = array(
													'value' => $option_value,
													'autoload' => $opt_autoload
												  );
	}
}

/**
 * File ini berisi query yg berhubungan dengan option
 *
 * @author Alfa Radito 
 * @since Version 1.0
 *
 * @return void
 */
function set_all_options() {
	global $_MR;
	// select option name dan value dimana option_autoload bernilai '1'
	$query = 'SELECT option_name, option_value 
	          FROM options
	          WHERE option_autoload = 1';
	
	$result = $_MR['db']->query($query);
	// masukkan data query terakhir
	set_last_query($query);
	
	if ($result === FALSE) {
		// query error
		return FALSE;
	}
	
	// increment status dari jumlah query yang telah dijalankan
	increase_query_number();
	
	while ($row = $result->fetch_object()) {
		// unserialize string dari kolom option_value jika diperlukan
		$opt_value = NULL;
		$temp = unserialize($row->option_value);
		// jika TIDAK FALSE berarti $temp adalah serializable string
		if ($temp !== FALSE) {
			$opt_value = $temp;
		} else {
			// isi kolom option_value bukan serializeable string
			// jadi kembalikan apa adanya
			// WYIIWYG (What You Insert Is What Get) :p
			$opt_value = $row->option_value;
		}
		
		$_MR['options'][$row->option_name] = $opt_value;
	}
	
	// tutup result
	$result->close();
}

/**
 * Fungsi untuk memasukkan options kedalam database
 * 
 * @author Irianto Bunga Pratama<me@iriantobunga.com>
 * @since Version 1.0
 * 
 * @return void
 */
function option_cache_save() {
	global $_MR; 	
	$query = array();
	
	// delete option
	$delete_cache = $_MR['options_delete_cache'];
	/* query dimasukkan kedalam array agar dapat melakukan multi_query
	 * val dari query diberi ' untuk type text, varchar
	 */
	foreach ($delete_cache as $opt_key => $opt_val) {
		$key = $_MR['db']->real_escape_string($opt_key);
		$query[] = "DELETE FROM options WHERE option_name='$key'";
	}
	
	// insert option
	$insert_cache = $_MR['options_insert_cache'];
	/* query dimasukkan kedalam array agar dapat melakukan multi_query
	 * val dari query diberi ' untuk type text, varchar
	 */
	foreach ($insert_cache as $opt_key => $opt_val) {
		$value = $_MR['db']->real_escape_string($opt_val['value']);
		$autoload = $_MR['db']->real_escape_string($opt_val['autoload']);
		$query[] = "INSERT INTO options (option_name, option_value, option_autoload) VALUES ('$opt_key', '$value', $autoload)";
	}
		
	// update option
	$update_cache = $_MR['options_update_cache'];
	/* query dimasukkan kedalam array agar dapat melakukan multi_query
	 * val dari query diberi ' untuk type text, varchar
	 */
	foreach ($update_cache as $opt_key => $opt_val) {
		$value = $_MR['db']->real_escape_string($opt_val);
		$query[] = "UPDATE options SET option_value='$value' WHERE option_name='$opt_key'";
	}
	
	if ($query) {
		// query digabungkan kedalam sebuat variable dengan pemisah ';'
		$query = implode(';', $query);

		// execute multi query
		$multi_query = $_MR['db']->multi_query($query);
		
		set_last_query($query);
		increase_query_number();
		site_debug($query, 'OPTION CACHE QUERY');
	}
}