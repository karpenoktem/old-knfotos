				</div>
				<ul id="menu">
					<li><a href="http://www.karpenoktem.nl/">karpenoktem.nl</a></li>
<?PHP if(isset($parentalbum) || $mode == 'view') { ?>
					<li><a href="./">Fotoalbum</a></li>
<?PHP } ?>
					<ul>
<?PHP foreach($parentalbums as $pa) { ?>
						<li><a href="./?album=<?= urlencode($pa['path'] . $pa['name']); ?>"><?= htmlentities($pa['humanname']); ?></a></li>
<?PHP } ?>
					</ul>
				</ul>
<?php show_template('actions.tpl'); ?>
			</div>
			<div id="footer">
				&copy;2009-2011, Karpe Noktem (<a href="http://github.com/karpenoktem/knfotos">broncode</a>)
			</div>
		</div>
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
			var pageTracker = _gat._getTracker("UA-11922614-1");
			pageTracker._trackPageview();
		} catch(err) {}</script>
	</body>
</html>
