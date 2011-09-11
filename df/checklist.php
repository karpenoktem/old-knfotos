<?php
	class DingesCheckList extends DingesMultiInputField {
		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('name', $this->name .'[]');
			$this->setAttribute('type', 'checkbox');
		}

		function render() {
			$this->fillAttributes();
			$this->fillLabelAttributes();
			$value = $this->getEffectiveValue();
			$strings = array();
			foreach($this->items as $item) {
				$strings['element_'. $this->name .'_'. $item['value']] = $this->generateItemHTML($item, $value && in_array($item['value'], $value));
				$strings['label_'. $this->name .'_'. $item['value']] = $this->getItemLabelTag($item);
			}
			$strings['label_'. $this->name] = $this->getLabelTag();
			$strings['error_'. $this->name] = $this->generateErrorElement();
			return $strings;
		}
	}
?>
