<table class="pager">
	<tr>
		<td>
<?php if($page > 0) { ?>
			<a href="<?= $url ?>&page=0">Eerste</a>
<?php } ?>
		</td>
		<td>
<?php if($page > 0) { ?>
			<a href="<?= $url ?>&page=<?= $page -1 ?>">Vorige</a>
<?php } ?>
		</td>
<?php for($p = 0; $last >= $p; $p++) { ?>
<?php if($p == $page) { ?>
		<td class="current"><?= $p+1 ?></td>
<?php } else { ?>
		<td><a href="<?= $url ?>&page=<?= $p ?>"><?= $p+1 ?></a></td>
<?php } ?>
<?php } ?>
		<td>
<?php if($last > $page) { ?>
			<a href="<?= $url ?>&page=<?= $page + 1 ?>">Volgende</a>
<?php } ?>
		</td>
		<td>
<?php if($last > $page) { ?>
			<a href="<?= $url ?>&page=<?= $last ?>">Laatste</a>
<?php } ?>
		</td>
	</tr>
</table>
