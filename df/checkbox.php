<?php
	class DingesCheckbox extends DingesInputField {
		function getValue() {
			return !is_null(parent::getValue());
		}

		function fillAttributes() {
			parent::fillAttributes();

			$this->setAttribute('type', 'checkbox');
			if($this->getEffectiveValue()) {
				$this->setAttribute('checked', 'checked');
			}
		}
	}
?>
