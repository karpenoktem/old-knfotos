<?php
	abstract class DingesLabelField extends DingesDefaultValueField {
		protected $label;
		protected $realLabelTag = true;

		protected $labelAttributes = array();

		function __construct($name, $label) {
			parent::__construct($name);
			$this->label = $label;
		}

		function render() {
			$strings = parent::render();
			$this->fillLabelAttributes();
			$strings['label_'. $this->name] = $this->getLabelTag();
			return $strings;
		}

		function getLabelId() {
			return $this->id .'_label';
		}

		function getFullLabelId() {
			return $this->form->getFieldIdPrefix() . $this->getLabelId();
		}

		function fillLabelAttributes() {
			$this->setLabelAttribute('id', $this->getFullLabelId());
			if($this->isValid() === false) {
				if($this->getLabelAttribute('class')) {
					$this->setLabelAttribute('class', ' dingesErrorLabel', true);
				} else {
					$this->setLabelAttribute('class', 'dingesErrorLabel');
				}
			}
		}

		function getLabelTag() {
			if($this->realLabelTag) {
				$element = 'label';
				$this->setLabelAttribute('for', $this->getFullId());
			} else {
				$element = 'span';
			}
			return DingesForm::generateTag($element, $this->labelAttributes, $this->label);
		}

		function setLabelAttribute($name, $value, $append = false) {
			if($append && isset($this->labelAttributes[$name])) {
				$this->labelAttributes[$name] .= $value;
			} else {
				$this->labelAttributes[$name] = $value;
			}
		}

		function deleteLabelAttribute($name) {
			unset($this->labelAttributes[$name]);
		}

		function getLabelAttribute($name) {
			if(isset($this->labelAttributes[$name])) {
				return $this->labelAttributes[$name];
			}
		}

		/* Simple getters and setters */
		final function getLabel() {
			return $this->label;
		}

		final function setLabel($value) {
			$this->label = $value;
		}
	}
?>
