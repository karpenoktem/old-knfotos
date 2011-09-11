<?php
	require('header.php');

	tpl_set('user', $_SESSION['user']);

	$f = new DingesForm();

	$fmove_from = $f->createInputField('select', 'move_from', true, 'Verplaats');
	$fmove_from->addItem('', 'Selecteer...');
	foreach(scandir(USER_DIRS) as $user) {
		if($user[0] == '.') {
			continue;
		}
		if(is_dir(USER_DIRS . $user .'/fotos/')) {
			foreach(scandir(USER_DIRS . $user .'/fotos/') as $fdir) {
				if($fdir[0] != '.' && is_dir(USER_DIRS . $user .'/fotos/'. $fdir)) {
					$fmove_from->addItem($user .'/'. $fdir, $user .'/'. $fdir);
				}
			}
		}
	}

	$fmove_to_event = $f->createInputField('select', 'move_to_event', true, 'naar');
	$fmove_to_event->addItem('', 'Selecteer...');
	$events = glob(FOTO_DIR .'20*');
	$events = array_map('basename', $events);
	rsort($events);
	foreach($events as $event) {
		$fmove_to_event->addItem($event, $event);
	}

	$fsubm = new DingesSubmit('move_subm', 'Verplaats!');
	$f->addField($fsubm);

	$f->render();

	foreach($f->getStrings() as $k => $v) {
		tpl_set($k, $v);
	}

	if($f->isSubmitted()) {
		list($user, $dir) = explode('/', $fmove_from->getValue(), 2);
		$fh = fsockopen('unix:///var/run/infra/S-francisca');
		if($fh) {
			fwrite($fh, json_encode(array('command' => 'fotoadmin-move-fotos.php', 'arguments' => array($fmove_to_event->getValue(), $user, $dir))) ."\n");
			tpl_set('formErrors', array(stream_get_contents($fh)));
			fclose($fh);
		} else {
			tpl_set('formErrors', array('Verbinden met Francisca mislukt'));
		}
	} elseif($f->isPosted()) {
		tpl_set('formErrors', $f->getValidationErrors());
	} else {
		tpl_set('formErrors', array());
	}

	tpl_show('index.tpl');

	function validate_english_date($value) {
		if(!preg_match('/^20\d{2}-\d{2}-\d{2}$/', $value)) {
			return 'ERR_INVALID';
		}
		return true;
	}
?>
