<?php
// PHP error reporting
// set ke E_ALL untuk development
// set ke 0 untuk production
error_reporting(E_ALL);

// deklarasi BASE_PATH
// BASE_PATH adalah lokasi absolute path sampai ke berita21
define('BASE_PATH', dirname(__FILE__));

try {
	// loading boot-strap file
	include_once (BASE_PATH . '/mr/boot_strap.php');
	
	run_hooks('pre_routing');
	$controller = map_controller();
	run_hooks('post_routing', $controller);
	
	if (get_backend_status()) {
		// load menu
		include_once(BASE_PATH . '/mr/' . 'menu_backend.php');
		// load autentikasi untuk backend
		include_once(BASE_PATH . '/mr/' . 'auth_backend.php');
	} else {
		include_once(BASE_PATH . '/mr/' . 'menu.php');
	}
	site_debug((int)$_MR['is_backend_controller'], 'BACKEND STATUS');
	
	include_once ($controller);
	run_hooks('post_controller', $controller);
} catch (Exception $e) {
	run_hooks('page_exception', $e);
	show_error($e->getMessage());
}

// jalankan proses pembersihan
mr_clean_up();
