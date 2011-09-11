<?php
	require('header.php');

	tpl_set('user', $_SESSION['user']);

	$f = new DingesForm();
	$f->setAttribute('action', 'create.php');
	$f->setAutoFocus(true);

	$fnew_date = $f->createInputField('text', 'new_date', true, 'Datum');
	$fnew_date->setDefaultValue(date('Y-m-d'));
	$fnew_date->addValidationCallback('validate_english_date');

	$fnew_humanname = $f->createInputField('text', 'new_humanname', false, 'Naam voor mensen');
	$fnew_humanname->setAttribute('onBlur', 'createTechName();');
	$fnew_name = $f->createInputField('text', 'new_name', true, 'Naam voor computers');
	$fnew_name->addValidationCallback('validate_name');

	$fsubm = new DingesSubmit('new_subm', 'Maak activiteit');
	$f->addField($fsubm);

	$f->render();

	foreach($f->getStrings() as $k => $v) {
		tpl_set($k, $v);
	}

	if($f->isSubmitted()) {
		// XXX datum omzetten naar .nl en meegeven als humanname
		$fh = fsockopen('unix:///var/run/infra/S-francisca');
		if($fh) {
			fwrite($fh, json_encode(array('command' => 'fotoadmin-create-event.php', 'arguments' => array($fnew_date->getValue(), $fnew_name->getValue(), $fnew_humanname->getValue()))) ."\n");
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
