<?PHP show_template('header.tpl'); ?>
			<table class="header">
				<tr>
					<td class="prev">
<?PHP if($page > 0) { ?>
						<a class="prev" href="<?= $url ?>&page=<?= $page - 1 ?>">&laquo; Vorige</a>
<?PHP } else { ?>
						<a class="prev invalid" href="#">&laquo; Vorige</a>
<?PHP } ?>
					</td>
					<td class="name"><?= htmlentities($humanname); ?></td>
					<td class="next">
<?PHP if($last > $page) { ?>
						<a class="next" href="<?= $url ?>&page=<?= $page + 1 ?>">Volgende &raquo;</a>
<?PHP } else { ?>
						<a class="next invalid" href="#">Volgende &raquo;</a>
<?PHP } ?>
					</td>
				</tr>
			</table>
<?PHP if(count($albums)>0) { ?>
			<table class="grid">
<?PHP foreach(array_chunk($albums, $thumbs_per_row) as $row) { ?>
				<tr>
<?PHP foreach($row as $album) { ?>
					<td class="album" width="<?= floor(100/$thumbs_per_row); ?>%"><a href=".?album=<?= urlencode($album['fullpath']) ?>"><img src="thumbnail.php?foto=<?= urlencode($album['fullpath']) ?>"><?= htmlentities($album['humanname']) ?>/</a></td>
<?PHP } ?>
				</tr>
<?PHP } ?>
			</table>
<?PHP } ?>
<?PHP if(count($photos)>0) { ?>
			<table class="grid">
<?PHP foreach(array_chunk($photos, $thumbs_per_row) as $row) { ?>
				<tr>
<?PHP foreach($row as $photo) { ?>
					<td class="photo" width="<?= floor(100/$thumbs_per_row); ?>%"><a href="view.php?foto=<?= urlencode($photo['fullpath']) ?>"><img src="thumbnail.php?foto=<?= urlencode($photo['fullpath']) ?>"><?= htmlentities($photo['name']) ?></a></td>
<?PHP } ?>
				</tr>
<?PHP } ?>
			</table>
<?PHP } ?>
<?PHP show_template('pager.tpl'); ?>
<?PHP show_template('footer.tpl'); ?>
