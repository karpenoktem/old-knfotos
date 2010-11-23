<?PHP show_template('header.tpl'); ?>
			<table class="header">
				<tr>
					<td class="prev">
<?PHP if($prev) { ?>
						<a class="prev" href="view.php?foto=<?= urlencode($prev) ?>">&laquo; Vorige</a>
<?PHP } else { ?>
						<a class="prev invalid" href="#">&laquo; Vorige</a>
<?PHP } ?>
					</td>
					<td class="name"><?= htmlentities($name); ?></td>
					<td class="next">
<?PHP if($next) { ?>
						<a class="next" href="view.php?<?= ($sliding ? 'slide&' : '') ?>foto=<?= urlencode($next) ?>">Volgende &raquo;</a>
<?PHP } else { ?>
						<a class="next invalid" href="#">Volgende &raquo;</a>
<?PHP } ?>
					</td>
				</tr>
			</table>
<?PHP if($sliding) { ?>
<?PHP if($foto_slider_preload) { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>" onLoad="slider_preload();" onError="slider_next();">
			<noscript>
				<img src="large.php?foto=<?= urlencode($next) ?>" style="display: none;" alt="Fotoslider Preloader">
			</noscript>
<?PHP } else { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>" onError="slider_next();">
<?PHP } ?>
<?PHP } else { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>">
<?PHP } ?>
			<div class="nav">
				<a href="foto.php?foto=<?= urlencode($foto) ?>">Direct link</a> -
<?PHP if($foto_slider) { ?>
<?PHP if($sliding) { ?>
				<a href="view.php?foto=<?= urlencode($foto) ?>">Stop slideshow</a>
<?PHP } else { ?>
<?PHP if($next) { ?>
				<a href="view.php?slide&foto=<?= urlencode($foto) ?>">Start slideshow</a>
<?PHP } else { ?>
				<a href="view.php?slide&foto=<?= urlencode($first) ?>">Start slideshow</a>
<?PHP } ?>
<?PHP } ?>
<?PHP } ?>
			</div>
<?PHP show_template('footer.tpl'); ?>
