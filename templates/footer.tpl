				</div>
				<ul id="menu">
					<li><a href="http://www.karpenoktem.nl/">KarpeNoktem.nl</a></li>
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
				&copy;2009-2010, Jille Timmermans; Karpe Noktem
			</div>
		</div>
	</body>
</html>
