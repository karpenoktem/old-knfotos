<?php
	require('header.php');

	tpl_set('user', $_SESSION['user']);

	$f = new DingesForm();
	$f->setAttribute('action', 'create.php');
	$f->setAutoFocus(true);

	$fnew_date = $f->createInputField('text', 'new_date', true, 'Datum');
	$fnew_date->setDefaultValue(date('Y-m-d'));
	$fnew_date->addValidationCallback('validate_english_date');
	$fnew_date->setAttribute('onBlur', 'createFullHumanname();');

	$fnew_humanname = $f->createInputField('text', 'new_humanname', false, 'Naam voor mensen');
	$fnew_humanname->setAttribute('onBlur', 'createTechName(); createFullHumanname();');

	$fnew_humanname_full = $f->createInputField('text', 'new_humanname_full', false, 'Volledige naam voor mensen');

	$fnew_name = $f->createInputField('text', 'new_name', true, 'Naam voor computers');
	$fnew_name->addValidationCallback('validate_name');

	$fsubm = new DingesSubmit('new_subm', 'Maak activiteit');
	$f->addField($fsubm);

	$f->render();

	foreach($f->getStrings() as $k => $v) {
		tpl_set($k, $v);
	}

	if($f->isSubmitted()) {
		$fh = fsockopen('unix:///var/run/infra/S-francisca');
		if($fh) {
			$args = array();
			$args[] = $fnew_date->getValue();
			$args[] = $fnew_name->getValue();
			if($fnew_humanname_full->getValue() != '') {
				$args[] = $fnew_humanname_full->getValue();
			} elseif($fnew_humanname->getValue() != '') {
				$args[] = $fnew_humanname->getValue();
			}
			fwrite($fh, json_encode(array('command' => 'fotoadmin-create-event.php', 'arguments' => $args)) ."\n");
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

	$older = glob(FOTO_DIR .'20*');
	$older = array_map('basename', $older);
	rsort($older);

	tpl_set('older', $older);

	tpl_show('create.tpl');

	function validate_english_date($value) {
		if(!preg_match('/^20\d{2}-\d{2}-\d{2}$/', $value)) {
			return 'ERR_INVALID';
		}
		return true;
	}

	function validate_name($value) {
		if(!preg_match('/^[a-z0-9-]{3,64}$/', $value)) {
			return 'ERR_INVALID';
		}
		return true;
	}
?>
