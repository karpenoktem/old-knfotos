<?php
	class DingesMultipleSubmit extends DingesField {
		protected $options = array();

		function addItem($key, $value) {
			$this->options[$key] = $value;
		}

		function getValue() {
			return array_search(parent::getValue(), $this->options);
		}

		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'submit');
		}

		function render() {
			$this->fillAttributes();
			$strings = array();
			foreach($this->options as $key=>$value) {
				$attributes = $this->attributes;
				$attributes['id'] = $this->getFullId() .'_'. $key;
				$attributes['value'] = $value;
				$strings['element_'. $this->name .'_'. $key] = DingesForm::generateTag('input', $attributes);
			}
			return $strings;
		}

		function getFormInitCode() {
			return '';
		}
	}
?>
