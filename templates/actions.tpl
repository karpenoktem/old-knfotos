			<fieldset>
				<legend>Zoeken</legend>
				<table>
					<form action=".">
						<?php $csrfToken->printField() ?>
						<tr>
							<th>Album:</th>
						</tr>
						<tr>
							<td><input type="text" name="search_album" /></td>
						</tr>
					</form>
					<form action=".">
						<?php $csrfToken->printField() ?>
						<input type="hidden" name="album" value="<?PHP echo isset($album) ? $album : (isset($_GET['album']) ? $_GET['album'] : ''); ?>" />
						<tr>
							<th>Lid:</th>
						</tr>
						<tr>
							<td><input type="text" name="search_tag" /></td>
						</tr>
					</form>
				</table>
			</fieldset>
<?PHP if(!isLid()) { ?>
			<a href="<?= $login_url ?>">Inloggen</a>
<?PHP } elseif(isAdmin()) { ?>
<?PHP if($mode == 'view') { ?>
			<form>
				<fieldset>
					<legend>Tags</legend>
					<div id="taglist">
					</div>
				</fieldset>
				<fieldset>
					<legend>Instellingen</legend>
					<table>
						<tr>
							<td><label for="f_visibility">Zichtbaarheid:</label></td>
						</tr>
						<tr>
							<td>
								<select name="visibility" id="f_visibility">
									<option value="world"<?PHP echo ($visibility == 'world' ? ' selected' : ''); ?>>Publiek</option>
									<option value="leden"<?PHP echo ($visibility == 'leden' ? ' selected' : ''); ?>>Alleen leden</option>
									<option value="hidden"<?PHP echo ($visibility == 'hidden' ? ' selected' : ''); ?>>Fototaggers</option>
									<option value="deleted"<?PHP echo ($visibility == 'deleted' ? ' selected' : ''); ?>>Verwijderen</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><label>Rotatie:</label></td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td></td>
										<td><input type="radio" id="f_rotation_0" name="rotation" value="0"<?PHP echo ($rotation == '0' ? ' checked' : ''); ?>></td>
										<td></td>
									</tr>
									<tr>
										<td><input type="radio" id="f_rotation_270" name="rotation" value="270"<?PHP echo ($rotation == '270' ? ' checked' : ''); ?>></td>
										<td style="background-image: url(pijltjes.png)"></td>
										<td><input type="radio" id="f_rotation_90" name="rotation" value="90"<?PHP echo ($rotation == '90' ? ' checked' : ''); ?>></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="radio" id="f_rotation_180" name="rotation" value="180"<?PHP echo ($rotation == '180' ? ' checked' : ''); ?>></td>
										<td></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
				<script type="text/javascript">
					var users = <?PHP echo json_encode($users); ?>;
					var taggedUsers = {};

					function addTagField(preselect) {
						if(preselect) {
							taggedUsers[preselect] = true;
						}
						var sel = document.createElement('select');
						sel.onchange = function() {
							if(sel.selectedIndex == 0) {
								if(sel.selectedUser) {
									taggedUsers[sel.selectedUser] = false;
									sel.parentNode.parentNode.removeChild(sel.parentNode);
								}
							} else if(taggedUsers[sel.value]) {
								sel.selectedIndex = 0;
							} else {
								if(!sel.selectedUser) {
									addTagField();
									taggedUsers[sel.value] = true;
								} else {
									taggedUsers[sel.selectedUser] = false;
									taggedUsers[sel.value] = true;
								}
								sel.selectedUser = sel.value;
							}
						};
						sel.selectedUser = preselect;
						var opt = document.createElement('option');
						opt.value = '';
						opt.innerHTML = 'Selecteer';
						sel.appendChild(opt);
						for(i in users) {
							opt = document.createElement('option');
							opt.value = i;
							opt.selected = (preselect == i);
							opt.innerHTML = users[i];
							sel.appendChild(opt);
						}
						var cont = document.createElement('div');
						cont.appendChild(sel);
						document.getElementById('taglist').appendChild(cont);
					}

					function gel(id) {
						return document.getElementById(id);
					}

					function submitMetaData(andContinue) {
						var visibility = gel('f_visibility').value;
						var rotation = 0;
						if(gel('f_rotation_0').checked) {
							rotation = 0;
						} else if(gel('f_rotation_90').checked) {
							rotation = 90;
						} else if(gel('f_rotation_180').checked) {
							rotation = 180;
						} else if(gel('f_rotation_270').checked) {
							rotation = 270;
						}
						var tags = [];
						for(var i in taggedUsers) {
							if(taggedUsers[i]) {
								tags.push(i);
							}
						}
						tags = tags.join(',');

						var query = 'csrftoken=<?= $csrfToken->get() ?>&updatePhoto=<?= $id ?>&visibility='+ escape(visibility) +'&rotation='+ escape(rotation) +'&tags='+ escape(tags);

						if(andContinue) {
<?PHP if($next) { ?>
							location.href = 'view.php?foto=<?= urlencode($next); ?>&'+ query;
							return;
<?PHP } else { ?>
							alert('Er is geen volgende foto');
<?PHP } ?>
						}
						xmlHttp = false;
						/*@cc_on @*/
						/*@if (@_jscript_version >= 5)
						try {
							xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
						} catch (e) {
							try {
								xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
							} catch (e2) {
								xmlHttp = false;
							}
						}
						@end @*/
						if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
							xmlHttp = new XMLHttpRequest();
						}
						xmlHttp.onreadystatechange = function() {
							if(xmlHttp.readyState == 4) {
								alert(xmlHttp.responseText);
							}
						};
						xmlHttp.open('POST', 'service.php', true);
						xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlHttp.send(query);
					}

<?PHP foreach($taggedUsers as $user) { ?>
					addTagField('<?PHP echo $user; ?>');
<?PHP } ?>
					addTagField();
				</script>
				<input type="button" value="Save" onClick="submitMetaData(false);">
				<input type="button" value="Save and continue" onClick="submitMetaData(true);">
			</form>
<?PHP } elseif($mode == 'index') { ?>
<?PHP if($visibility) { ?>
			<fieldset id="settings">
				<legend>Instellingen</legend>
				<form>
					<script type="text/javascript">
						function gel(id) {
							return document.getElementById(id);
						}

						function submitMetaData() {
							var visibility = gel('f_visibility').value;
							var humanname = gel('f_humanname').value;

							var query = 'csrftoken=<?= $csrfToken->get() ?>&updateAlbum=<?= $id ?>&visibility='+ visibility +'&humanname='+ humanname;

							xmlHttp = false;
							/*@cc_on @*/
							/*@if (@_jscript_version >= 5)
							try {
								xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
							} catch (e) {
								try {
									xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
								} catch (e2) {
									xmlHttp = false;
								}
							}
							@end @*/
							if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
								xmlHttp = new XMLHttpRequest();
							}
							xmlHttp.onreadystatechange = function() {
								if(xmlHttp.readyState == 4) {
									alert(xmlHttp.responseText);
								}
							};
							xmlHttp.open('POST', 'service.php', true);
							xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
							xmlHttp.send(query);
						}
					</script>
					<table>
						<tr>
							<td><label for="f_humanname">Naam:</label></td>
						</tr>
						<tr>
							<td><input type="text" name="humanname" id="f_humanname" value="<?= htmlentities($humanname) ?>"></td>
						</tr>
						<tr>
							<td><label for="f_visibility">Zichtbaarheid:</label></td>
						</tr>
						<tr>
							<td>
								<select name="visibility" id="f_visibility">
									<option value="world"<?PHP echo ($visibility == 'world' ? ' selected' : ''); ?>>Publiek</option>
									<option value="leden"<?PHP echo ($visibility == 'leden' ? ' selected' : ''); ?>>Alleen leden</option>
									<option value="hidden"<?PHP echo ($visibility == 'hidden' ? ' selected' : ''); ?>>Fototaggers</option>
									<option value="deleted"<?PHP echo ($visibility == 'deleted' ? ' selected' : ''); ?>>Verwijderen</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><input type="button" value="Save" onClick="submitMetaData();"></td>
						</tr>
					</table>
				</form>
			</fieldset>
<?PHP } ?>
<?PHP } ?>
<?PHP } ?>
