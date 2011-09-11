<?php tpl_show('header.tpl'); ?>
		<script type="text/javascript">
			var lasttechname = '';
			var lastfullname = '';

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

			function createFullHumanname() {
				var hn = document.getElementById('new_humanname');
				var dt = document.getElementById('new_date');
				var fn = document.getElementById('new_humanname_full');
				if(fn.value != lastfullname) {
					lastfullname = '';
					return;
				}

				if(hn.value == '') {
					fn.value = lastfullname = '';
					return;
				}

				var d = new Date(dt.value);

				lastfullname = hn.value +' '+ d.getDate() +' ';
				switch(d.getMonth()) {
					case 0: lastfullname += 'jan'; break;
					case 1: lastfullname += 'feb'; break;
					case 2: lastfullname += 'mrt'; break;
					case 3: lastfullname += 'apr'; break;
					case 4: lastfullname += 'mei'; break;
					case 5: lastfullname += 'jun'; break;
					case 6: lastfullname += 'jul'; break;
					case 7: lastfullname += 'aug'; break;
					case 8: lastfullname += 'sep'; break;
					case 9: lastfullname += 'okt'; break;
					case 10: lastfullname += 'nov'; break;
					case 11: lastfullname += 'dec'; break;
				}
				lastfullname += ' '+ d.getFullYear();
				fn.value = lastfullname;
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
				<td><?= $label_new_humanname_full ?></td>
				<td><?= $element_new_humanname_full ?></td>
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
