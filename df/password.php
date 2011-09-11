<?php
	class DingesPassword extends DingesText {
		protected $keepValue = false;

		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'password');
		}
	}
?>
