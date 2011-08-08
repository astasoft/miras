<?php
/**
 * File ini berisi fungsi-fungsi yang berhubungan dengan sistem plugin
 *
 * @package libraries
 * @copyright 2011 Bajol Startup Team
 */

/**
 * Fungsi untuk meload plugin yang ada didirektori plugins
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @author Irianto Bunga Pratama <me@iriantobunga.com>
 * @since Version 1.1
 *
 * @return void
 */
function load_plugins() {
	global $_MR;
	
	$dirs = scandir(BASE_PATH . '/plugins/');
	// slice (hilangkan) dua element awal yaitu direktori . dan ..
	$dirs = array_slice($dirs, 2);
	
	foreach ($dirs as $plugin) {
		//echo $plugin . '<BR>';
		
		if (!is_dir(BASE_PATH . '/plugins/' . $plugin)) {
			continue;
		}
		
		//echo $plugin . '<BR>';
		
		// list semua direktori atau files yang harus ada pada direktori 
		// plugin yang akan diload
		$needed_files = array();
		$needed_files[$plugin . '.info'] = 'Nama file ' . $plugin . '.info tidak ditemukan';
		$needed_files[$plugin . '.php'] = 'Nama file ' . $plugin . '.php tidak ditemukan';
		$needed_files['controllers'] = 'Direktori controllers tidak ditemukan';
		$needed_files['views'] = 'Direktori views tidak ditemukan';
		$needed_files['models'] = 'Direktori models tidak ditemukan';
		
		// cek setiap kebutuhan files atau direktori
		foreach ($needed_files as $file_or_dir => $message) {
			// file atau direktori yang diminta harus ada
			$path = BASE_PATH . '/plugins/' . $plugin . '/' . $file_or_dir;
			if (!file_exists($path)) {
				// file atau direktori tidak ada maka masukkan ke daftar plugin error
				// lalu langsung lanjutkan pengecekan ke plugin berikutnya
				$_MR['error_plugins'][] = array(
												'plugins_name' => $plugin,
												'error_message' => $message
										);
				// 2, karena level foreach yang ingin kita skip adalah 2 level 
				// (foreach atas)
				continue 2;
			}
		}
		
		// mengecek index.html pada tiap-tiap direktori termasuk direktori yang berada didalam direktori plugin
		$needed_index = array();
		$needed_index['index.html'] = 'index.html tidak ditemukan pada direktori ' . $plugin;
		
		$dirs_plugins = scandir(BASE_PATH . '/plugins/' . $plugin . '/');
		// slice (hilangkan) dua element awal yaitu direktori . dan ..
		$dirs_plugins = array_slice($dirs_plugins, 2);
		
		// memasukkan peringatan kepada semua direktori yang berada didalam direktori plugin
		foreach ($dirs_plugins as $dirp) {
			$needed_index[$dirp . 'index.html'] = 'index.html tidak ditemukan pada direktori ' . $plugin . '/' . $dirp;
		}
		
		// cek setiap kebutuhan files index.html pada direktori yang ditentukan
		foreach ($needed_index as $dir => $message) {
			// direktori yang diminta harus memiliki index.html
			$path = BASE_PATH . '/plugins/' . $plugin . '/' . $dir;
			
			if (!file_exists($path)) {
				// file tidak ada maka masukkan ke daftar plugin error
				// lalu langsung lanjutkan pengecekan ke plugin berikutnya
				$_MR['error_plugins'][] = array(
												'plugins_name' => $plugin,
												'error_message' => $message
										);
				// 2, karena level foreach yang ingin kita skip adalah 2 level 
				// (foreach atas)
				continue 2;
			}
		}
		
		// ok, let's include the plugin
		include_once(BASE_PATH . '/plugins/' . $plugin . '/' . $plugin . '.php');
		
		// logika untuk mengecek apakah plugin telah membuat beberapa fungsi
		// yang diperlukan seperti:
		// - nama_plugin_on_install()
		// - nama_plugin_on_uninstall()
		// - nama_plugin_on_activate()
		// - nama_plugin_on_deactivate()
		// - nama_plugin_on_upgrade()
		$needed_function = array($plugin . '_on_install',
								 $plugin . '_on_uninstall',
								 $plugin . '_on_activate',
								 $plugin . '_on_deactivate',
								 $plugin . '_on_upgrade');
		foreach ($needed_function as $nf) {
			if (!function_exists($nf)) {
				site_debug('Fungsi ' . $nf . ' : Plugin ' . $plugin, 'FUNCTION NOT EXISTS');
				throw new Exception('Nama function ' . $nf . ' tidak ditemukan pada plugin ' . $plugin); 
			}
		}
		
		// informasi plugin berupa format JSON, jadi untuk mengubahnya kedalam
		// bentuk PHP Object digunakan json_encode
		// $json_info = json_decode(file_get_contents());
		
		// masukkan ke dalam daftar plugin yang telah diload
		$_MR['loaded_plugins'][] = $plugin;
		
	}
	
	site_debug(print_r($_MR['error_plugins'], TRUE), 'ERROR PLUGINS');
	site_debug(print_r($_MR['loaded_plugins'], TRUE), 'LOADED PLUGINS');
}

/**
 * Fungsi untuk menambahkan daftar hook
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.1
 *
 * @return void
 */
function add_hook($hookname, $function_name) {
	global $_MR;
	
	$_MR['hooks'][$hookname][] = $function_name;
}

/**
 * Fungsi untuk menjalankan hooks yang sudah terdaftar 
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.1
 *
 * @param string $hookname nama hook yang kan dijalankan
 * @param mixed $args parameter yang akan dipassing ke hooks
 *
 * @return void
 */
function run_hooks($hookname, &$args='') {
	global $_MR;
	
	// cek dulu apakah nama hook yang akan dijalankan ada didalam daftar hook
	// atau tidak
	if (array_key_exists($hookname, $_MR['hooks'])) {
		// jika ada maka lakukan looping untuk menjalankan hook tersebut
		// karena kemungkinan satu hook dijalankan oleh banyak plugin (fungsi)
		foreach ($_MR['hooks'][$hookname] as $function_name) {
			// check apakah fungsi sudah didefinisikan atau belum
			if (function_exists($function_name)) {
				call_user_func_array($function_name, array(&$args));
			}
		}
	}
}
