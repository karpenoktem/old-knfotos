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
<?PHP if($type == 'photo') { ?>
<?PHP   if($sliding) { ?>
<?PHP     if($foto_slider_preload) { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>" onLoad="slider_preload();" onError="slider_next();">
			<noscript>
				<img src="large.php?foto=<?= urlencode($next) ?>" style="display: none;" alt="Fotoslider Preloader">
			</noscript>
<?PHP     } else { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>" onError="slider_next();">
<?PHP     } ?>
<?PHP   } else { ?>
			<img class="large" src="large.php?foto=<?= urlencode($foto) ?>">
<?PHP   } ?>
<?PHP } else { ?>
<?PHP   if($sliding) { ?>
			<video id="video" controls="" autoplay="" onError="slider_next();">
<?PHP   } else { ?>
			<video id="video" controls="" autoplay="">
<?PHP   } ?>
				<source src="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=360p"  type="video/mp4"  data-resolution="360p"/>
				<source src="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=720p"  type="video/mp4"  data-resolution="720p"/>
				<source src="large.php?foto=<?= urlencode($foto) ?>&codec=webm&res=360p" type="video/webm" data-resolution="360p"/>
				<source src="large.php?foto=<?= urlencode($foto) ?>&codec=webm&res=720p" type="video/webm" data-resolution="720p"/>
				Download video in <a href="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=360p">lage resolutie (360p, sneller)</a> of <a href="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=720p">hoge resolutie (720p, groter/beter)</a>.
			</video><br/>
			<select id="resolution" onchange="updateResolution()" title="Video resolutie">
					<option selected="">360p</option>
					<option>720p</option>
			</select>
<?PHP } ?>
			<div class="nav">
				<a href="foto.php?foto=<?= urlencode($foto) ?>">Direct link</a>
<?PHP if($foto_slider) { ?>
<?PHP   if($sliding) { ?>
				- <a href="view.php?foto=<?= urlencode($foto) ?>">Stop slideshow</a>
<?PHP   } else { ?>
<?PHP     if($next) { ?>
				- <a href="view.php?slide&foto=<?= urlencode($foto) ?>">Start slideshow</a>
<?PHP     } else if ($prev) { ?>
				- <a href="view.php?slide&foto=<?= urlencode($first) ?>">Start slideshow</a>
<?PHP     } ?>
<?PHP   } ?>
<?PHP } ?>
			</div>
<?PHP show_template('footer.tpl'); ?>
