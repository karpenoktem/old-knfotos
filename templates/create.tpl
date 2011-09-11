<?php tpl_show('header.tpl'); ?>
		<script type="text/javascript">
			var lasttechname = '';

			function createTechName() {
				var hn = document.getElementById('new_humanname');
				var tn = document.getElementById('new_name');
				if(tn.value != lasttechname) {
					lasttechname = '';
					return;
				}

				tn.value = hn.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
				lasttechname = tn.value;
			}
		</script>
		<span style="float: right">Dag <?= $user ?></span>
		<br>
		<p>
			Hier kun je een map maken voor een nog niet bestaande activiteit.
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
				<th colspan="2">Maak activiteit aan</th>
			</tr>
			</tr>
			<tr>
				<td><?= $label_new_humanname ?></td>
				<td><?= $element_new_humanname ?></td>
			</tr>
			<tr>
				<td><?= $label_new_date ?></td>
				<td><?= $element_new_date ?></td>
			<tr>
				<td><?= $label_new_name ?></td>
				<td><?= $element_new_name ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?= $element_new_subm ?></td>
			</tr>
		</table>
		<?= $form_close ?>
		Lijst van bestaande activiteiten:
		<ul>
<?php foreach($older as $dir) { ?>
			<li><?= $dir ?></li>
<?php } ?>
		</ul>
<?php tpl_show('footer.tpl'); ?>
