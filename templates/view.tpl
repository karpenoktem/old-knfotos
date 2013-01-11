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
<?PHP   foreach ($video_codecs as $codec) { ?>
<?PHP     foreach ($video_resolutions as $res) { ?>
				<source src="large.php?foto=<?= urlencode($foto) ?>&codec=<?= $codec ?>&res=<?= $res ?>"  type="video/<?= $codec ?>"  data-resolution="<?= $res ?>"/>
<?PHP     } ?>
<?PHP   } ?>
				<p>Je browser ondersteund geen html5 video (gebruik <a href="http://windows.microsoft.com/nl-NL/internet-explorer/downloads/ie-9/worldwide-languages">IE9</a>+ of een recente versie van <a href="http://www.mozilla.org/nl/firefox/new/">Firefox</a>, <a href="https://www.google.com/intl/nl/chrome/browser/">Chrome</a>, <a href="http://www.apple.com/safari/">Safari</a> of <a href="http://www.opera.com/">Opera</a>).</p>
<?PHP   if (count($video_resolutions) == 2 && in_array('mp4', $video_codecs)) { ?>
				<p>Download video in <a href="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=<?= $video_resolutions{0} ?>">lage resolutie (<?= $video_resolutions{0} ?>, sneller)</a> of <a href="large.php?foto=<?= urlencode($foto) ?>&codec=mp4&res=<?= $video_resolutions{1} ?>">hoge resolutie (<?= $video_resolutions{1} ?>, groter/beter)</a>.</p>
<?PHP   } else { ?>
				<p>Video beschikbaar in de volgende resoluties (kleiner is sneller te downloaden):</p>
				<ul>
<?PHP     foreach ($video_resolutions as $res) { ?>
					<li><a href="large.php?foto=<?= urlencode($foto) ?>&codec=<?= $video_codecs{0} ?>&res=<?= $res ?>"><?= $res ?></a></li>
<?PHP     } ?>
				</ul>
<?PHP   } ?>
			</video><br/>
			<select id="resolution" onchange="updateResolution()" title="Video resolutie">
<?PHP   foreach ($video_resolutions as $res) { ?>
<?PHP     if ($res == $video_resolutions{0}) { ?>
					<option selected=""><?= $res ?></option>
<?PHP     } else { ?>
					<option><?= $res ?></option>
<?PHP     } ?>
<?PHP   } ?>
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
