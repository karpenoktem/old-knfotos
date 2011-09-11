<?php
	class DingesInteger extends DingesText {
		protected $min;
		protected $max;

		function validate($value) {
			if(($error = parent::validate($value)) !== true) {
				return $error;
			}
			if(!ctype_digit($value)) {
				return 'ERR_NON_INTEGER';
			}
			if($this->min !== NULL && $value < $this->min) {
				return 'ERR_UNDER_MIN';
			}
			if($this->max !== NULL && $value > $this->max) {
				return 'ERR_OVER_MAX';
			}
			return true;
		}

		function fillRestrictions() {
			parent::fillRestrictions();
			if($this->min !== NULL) {
				$this->setRestriction('min', $this->min);
			}
			if($this->max !== NULL) {
				$this->setRestriction('max', $this->max);
			}
		}

		/* Simple getters and setters */
		final function getMin() {
			return $this->min;
		}

		final function setMin($value) {
			$this->min = $value;
		}

		final function getMax() {
			return $this->max;
		}

		final function setMax($value) {
			$this->max = $value;
		}
	}
?>
