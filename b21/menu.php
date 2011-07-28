<?php

// default menu
tpl_add_menu(array(
	'label' => 'Home',
	'id' => 'b21_home',
	'url' => get_site_url(),
	'title' => 'Come to Papa!',
	'order' => 1
));	

tpl_add_menu(array(
	'label' => 'Add Kategori',
	'id' => 'add_kategori',
	'url' => get_site_url() . '/add-kategori',
	'title' => 'Tambah Kategori Baru',
	'order' => 1
));	

// jalankan beberapa hooks yang berhubungan dengan manipulasi menu
run_hooks('add_more_menu');