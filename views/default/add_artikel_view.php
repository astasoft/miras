
	<!-- BEGIN CONTENT -->
	<div id="content">
		<h1>Lihat Artikel</h1>
		<form action="" method="post">
			<label>Nama Kategori</label>
			<input type="text" name="nama_kat" value="" id="" /><br/>
			<input type="submit" name="submit-kat" value="SIMPAN" />
		</form>
		
		<h3>Daftar Kategori Saat Ini</h3>
		<ul>
		<?php foreach ($data['daftar_kategori'] as $kat) : ?>
			<li><?php echo ($kat->kategori_nama);?></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<!-- BEGIN CONTENT -->