<?php

/**
 * Fungsi untuk menampilkan debugging info jika diaktifkan dalam konfigurasi
 *
 * @param string $message pesan yang akan ditampilkan di debugging
 * @param string $title judul dari info debugging
 * @return void
 */
function site_debug($message, $title='DEBUG: ') {
	global $_B21;
	
	// jika debug mode diaktifkan maka jalankan proses debugging
	if ($_B21['debug_mode']) {
		$debug = $title . "\n";
		$debug .= $message . "\n";
		
		$_B21['debug_message'] .= $debug;
	}
}

/**
 * Fungsi untuk mencetak pesan debugging
 *
 * @return void
 */
function show_debug() {
	global $_B21;
	
	if ($_B21['debug_mode']) {
		echo ("\n<hr/>\n");
		echo ('<pre><h2>DEBUGGING MESSAGE</h2>' . "\n");
		echo ($_B21['debug_message']);
		echo ('</pre>');
	}
}
 
/**
 * Fungsi untuk meload model database.
 *
 * <code>
 * // jika akan meload model dengan nama 'kategori_model.php' maka cara penulisannya adalah
 * load_model('kategori');
 * </code>
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $model_name nama dari model yang akan diload
 * @return void
 */
function load_model($model_name) {
	// load model dari base path
	// hasilnya adalah /path/to/berita21/models/nama_model.php
	$path_file = BASE_PATH . '/models/' . $model_name . '_model.php';
	
	// jika file tidak ada maka model tidak bisa diload
	if (!file_exists($path_file)) {
		// keluar dari sistem
		exit ("Model '{$model_name}' tidak ada pada path system.");
	}
	
	include_once ($path_file);
}

/**
 * Fungsi untuk meload view HTML.
 *
 * <code>
 * // jika akan meload view dengan nama 'kategori_view.php' maka cara penulisannya adalah
 * load_view('kategori');
 * </code>
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $view_name nama dari view yang akan diload
 * @param array $data data yang akan di passing ke views
 * @return void
 */
function load_view($view_name, &$data=NULL) {
	global $_B21;
	
	$theme = $_B21['theme'];
	
	// load view dari base path
	// hasilnya adalah /path/to/berita21/views/nama_theme/nama_model.php
	$path_file = BASE_PATH . '/views/' . $theme . '/' . $view_name . '_view.php';
	
	// jika file tidak ada maka view tidak bisa diload
	if (!file_exists($path_file)) {
		// keluar dari sistem
		exit ("View '{$view_name}' tidak ada pada path system.");
	}
	
	include_once ($path_file);
}

/**
 * Fungsi untuk mengembalikan nilai dari konfigurasi base_url, views dapat
 * menggunaman fungsi ini untuk meload URL lengkap css dan javascript
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @return string base url dari aplikasi
 */
function get_base_url() {
	global $_B21;
	
	return $_B21['base_url'];
}

/**
 * Fungsi untuk mengembalikan nilai dari konfigurasi base_url + index_page, views dapat
 * menggunaman fungsi ini untuk meload URL lengkap css dan javascript
 *
 * Contoh jika konfigurasi index_page adalah index.php maka output dari fungsi
 * ini adalah:
 * http://example.com/index.php
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @return string base url + index_page dari aplikasi
 */
function get_site_url() {
	global $_B21;
	
	return $_B21['base_url'] . $_B21['index_page'];
}

/**
 * Fungsi untuk mengembalikan nilai dari konfigurasi base_url + nama theme + '/' 
 * yang saat ini digunakan. Views dapat menggunaman fungsi ini untuk meload URL 
 * lengkap css dan javascript.
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @return string base url dari aplikasi + nama theme + slash
 */
function get_theme_url() {
	global $_B21;
	
	return $_B21['base_url'] . 'views/' . $_B21['theme'] . '/';
}

/**
 * Fungsi untuk mengembalikan nilai dari konfigurasi judul halaman, dapat 
 * digunakan didalam views.
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @return string judul dari halaman
 */
function get_page_title() {
	global $_B21;
	
	return $_B21['title'];
}

/**
 * Fungsi untuk men-set nilai dari konfigurasi judul halaman, biasanya  
 * digunakan didalam controller sebelum meload views.
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $title judul dari halaman
 * @return void
 */
function set_page_title($title='') {
	global $_B21;
	
	$_B21['title'] = $title;
}

/**
 * Fungsi untuk men-set nilai dari variabel flash message
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $message pesan flash message yang akan diberikan
 * @return void
 */
function set_flash_message($message='') {
	global $_B21;
	
	$_B21['flash_message'] = $message;
}

/**
 * Fungsi untuk mengambil nilai dari flash message
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @return string flash message
 */
function get_flash_message() {
	global $_B21;
	
	if (!$_B21['flash_message']) {
		// jika tidak ada sesuatu di flash message, kembalikan saja kosongan
		return '';
	}
	
	$mesg = '<div class="flash ' . $_B21['flash_class'] . '">' . $_B21['flash_message'] . '</div>' . "\n";
	return $mesg;
}

/**
 * Fungsi untuk men-set nilai dari variabel flash class
 *
 * @author Rio Astamal <me@rioastamal.net>
 * @since Version 1.0
 *
 * @param string $clas CSS class dari div flash message
 * @return void
 */
function set_flash_class($class='') {
	global $_B21;
	
	$_B21['flash_class'] = $class;
}

/**
 * Fungsi untuk melakukan routing dari URL ke sebuah file controller
 *
 * <code>
 * // jika ada sebuah URL seperti berikut:
 * //  http://localhost/berita21/index.php/foo-bar
 * $controller = map_controller();
 *
 * // maka nila dari controller (asumsi lokasi root direktori webserver adalah /opt/lampp/htdocs)
 * // adalah /opt/lampp/htdocs/controllers/foo_bar_ctl.php
 * // sehingga file contrroler dapat di include dengan peritnah
 * include_once( $controller );
 * </code>
 */
function map_controller() {
	global $_B21;
	
	$file = '';
	$index = $_B21['index_page'];
	$controller = $_B21['default_controller'];
	$uri = $_SERVER['REQUEST_URI'];
	
	// fix slash ganda // dengan single / jika memang terjadi
	$uri = preg_replace('@//+@', '/', $uri);
	
	// split $index dari the uri 
	$split = explode($index, $uri);		
	site_debug( print_r($split, TRUE), 'URI INDEX' );
	
	// get the controller
	if (@$split[1]) {
		preg_match('@/([a-zA-Z0-9\-_]+)(.*)@', $split[1], $matches);
		site_debug( print_r($matches, TRUE), 'CONTROLLER MATCHING' );
		$controller = $matches[1];
	}
	
	// semua controller harus diconvert ke underscore karena konvensi nama file
	// controller diharuskan seperti itu (sesuai dengan coding guide awal)
	$controller = str_replace('-', '_', $controller);
	
	// map controller ke file yang bersangkutan
	$file = BASE_PATH . '/controllers/' . $controller . '_ctl.php';
	
	// file exists?
	if (file_exists($file)) {
		return $file;
	}
	
	// jika sampai disini maka controller tidak ditemukan jadi thrown exception
	throw new Exception ("Controller {$controller} tidak ditemukan.");
}
