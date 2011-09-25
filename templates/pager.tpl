<table class="pager">
	<tr>
               <td><a <?php if($page > 0) { ?>
                               href="<?= $url ?>&page=0"
                      <?php } else { ?>
                               class="invalid"
                      <?php } ?>>Eerste</a></td>
               <td><a <?php if($page > 0) { ?>
                               href="<?= $url ?>&page=<?= $page -1 ?>"
                      <?php } else {  ?>
                               class="invalid"
                      <?php } ?>>Vorige</a></td>
<?php $p = -1;
       while($p < $last) {
               $p++;
               if($p > 3 && $page - 3 > $p) { ?>
                       <td>&hellip;</td>
                       <?php $p = $page - 3;
               } elseif($p > 4 + $page && $last - 3 > $p) { ?>
                       <td>&hellip;</td>
                       <?php $p = $last - 3;
               }?>
<?php if($p == $page) { ?>
		<td class="current"><?= $p+1 ?></td>
<?php } else { ?>
		<td><a href="<?= $url ?>&page=<?= $p ?>"><?= $p+1 ?></a></td>
<?php } ?>
<?php } ?>
               <td><a <?php if($page < $last) { ?>
                               href="<?= $url ?>&page=<?= $page + 1 ?>"
                      <?php } else {  ?>
                               class="invalid"
                      <?php } ?>>Volgende</a></td>
               <td><a <?php if($page < $last) { ?>
                               href="<?= $url ?>&page=<?= $last ?>"
                      <?php } else { ?>
                               class="invalid"
                      <?php } ?>>Laatste</a></td>
	</tr>
</table>
