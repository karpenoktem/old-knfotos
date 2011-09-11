<?php tpl_show('header.tpl'); ?>
		<span style="float: right">Dag <?= $user ?></span>
		<br>
		<p>
			Hier kun je foto's verplaatsen. Ze worden uit de fotos map van een gebruiker gehaald en aan het fotoboek toegevoegd. Bestaat de activiteit nog niet? Maak hem <a href="create.php">hier</a> aan.
		</p>
<?php if($formErrors) { ?>
		<ul style="color: red">
<?php foreach($formErrors as $error) { ?>
			<li><?= $error ?></li>
<?php } ?>
		</ul>
<?php } ?>
		<?= $form_open ?>
		<table style="width: 100%">
			<tr>
				<th colspan="2">Verplaats foto's</th>
			</tr>
			<tr>
				<td><?= $label_move_from ?></td>
				<td><?= $element_move_from ?></td>
			</tr>
			<tr>
				<td><?= $label_move_to_event ?></td>
				<td><?= $element_move_to_event ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?= $element_move_subm ?></td>
			</tr>
		</table>
		<?= $form_close ?>
<?php tpl_show('footer.tpl'); ?>
