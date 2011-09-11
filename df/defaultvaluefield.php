<?php
	abstract class DingesDefaultValueField extends DingesField {
		protected $defaultValue = NULL;

		function getEffectiveValue() {
			if($this->form->isPosted() && $this->keepValue) {
				return $this->getValue();
			} else {
				return $this->getDefaultValue();
			}
		}

		/* Simple getters and setters */
		function getDefaultValue() {
			return $this->defaultValue;
		}

		function setDefaultValue($value) {
			$this->defaultValue = $value;
		}
	}
?>
