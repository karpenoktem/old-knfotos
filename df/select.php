<?php
	class DingesSelect extends DingesInputField {
		protected $element = 'select';
		protected $options = array();

		// IE (iig 6) selecteert het eerste element als je op het label klikt
		protected $realLabelTag = false;

		function addItem($value, $content, $optgroup = NULL) {
			$option = array('value' => $value, 'content' => htmlspecialchars($content, ENT_NOQUOTES, NULL, false));
			if($optgroup) {
				$this->options[$optgroup][] = $option;
			} else {
				$this->options[] = $option;
			}
		}

		function fillAttributes() {
			parent::fillAttributes();
			if(isset($this->attributes['multiple'])) {
				$this->setAttribute('name', $this->name .'[]');
			}
		}

		function generateHTML() {
			$value = $this->getEffectiveValue();
			$options = '';
			foreach($this->options as $i=>$option) {
				if(!isset($option['value'])) {
					$optgroup = '';
					foreach($option as $suboption) {
						$attributes = array('value' => $suboption['value']);
						if($suboption['value'] == $value) {
							$attributes['selected'] = 'selected';
						}
						$optgroup .= DingesForm::generateTag('option', $attributes, $suboption['content']);
					}
					$options .= DingesForm::generateTag('optgroup', array('label' => $i), $optgroup);
					continue;
				}
				$attributes = array('value' => $option['value']);
				if(is_array($value)) {
					if(in_array($option['value'], $value)) {
						$attributes['selected'] = 'selected';
					}
				} else {
					if($option['value'] == $value) {
						$attributes['selected'] = 'selected';
					}
				}
				$options .= DingesForm::generateTag('option', $attributes, $option['content']);
			}
			return DingesForm::generateTag($this->element, $this->attributes, $options) . $this->getRestrictionComment();
		}
	}
?>
