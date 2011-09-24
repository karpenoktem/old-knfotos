<table class="pager">
	<tr>

<?php if($page == 0){ ?>
		<td>
			Eerste
		</td>
		<td>
			Vorige
		</td>
<?php } else { ?>
		<td>
			<a href=\"".$url."&page=0\">Eerste</a>
		</td>
		<td>
			<a href=\"".$url."&page=".($page-1)."\">Vorige</a>
		</td>
<?php } ?>


<?php for($p = 0; $last >= $p; $p++) {

	if($p <= 5 || $p >= ($last - 5)){
		if($p == $page) echo "<td>".($p+1)."</td>";
		else echo "<td><a href=\"".$url."&page=".$p."\">".($p+1)."</a></td>";
	}
	elseif(($p < 13 && $page <= 10) || ($p > ($last-13) && $page >= ($last-10))){
		if($p == $page) echo "<td>".($p+1)."</td>";
		else echo "<td><a href=\"".$url."&page=".$p."\">".($p+1)."</a></td>";
	}
	elseif($p == 6 || $p == ($last-6)){
		echo "<td>...</td>";
	}
	elseif($p==($page+3) || $p==($page+2) || $p==($page+1) || $p==$page || $p==($page-1) || $p==($page-2) || $p==($page-3)){
		if($p == $page) echo "<td>".($p+1)."</td>";
		else echo "<td><a href=\"".$url."&page=".$p."\">".($p+1)."</a></td>";
	}

}

?>

<?php if($last == $page) { ?>
		<td>
			Volgende
		</td>
		<td>
			Laatste
		</td>
<?php } else { ?>
		<td>
			<a href=\"".$url."&page=".($page+1)."\">Volgende</a>
		</td>
		<td>
			<a href=\"".$url."&page=".$last."\">Laatste</a>
		</td>
<?php } ?>

	</tr>
</table>
