<?php
	abstract class DingesMultiInputField extends DingesInputField {
		protected $realLabelTag = false;

		protected $itemLabelAttributes = array();

		protected $items = array();

		function addItem($value, $content, $escape_html = true) {
			if($escape_html) {
				$content = htmlspecialchars($content, ENT_NOQUOTES, NULL, false);
			}
			$this->items[$value] = array('value' => $value, 'content' => $content);
		}

		function setItemLabelAttribute($name, $value, $append = false) {
			if($append && isset($this->itemLabelAttributes[$name])) {
				$this->itemLabelAttributes[$name] .= $value;
			} else {
				$this->itemLabelAttributes[$name] = $value;
			}
		}

		function deleteItemLabelAttribute($name) {
			unset($this->itemLabelAttributes[$name]);
		}

		function getItemLabelAttribute($name) {
			return $this->itemLabelAttributes[$name];
		}

		function getItemLabelTag($item) {
			$attributes = $this->itemLabelAttributes;
			$attributes['id'] = $this->getFullLabelId() .'_'. $item['value'];
			$attributes['for'] = $this->getFullId() .'_'. $item['value'];
			return DingesForm::generateTag('label', $attributes, $item['content']);
		}

		function generateItemHTML($item, $checked) {
			$attributes = $this->attributes;
			$attributes['id'] = $this->getFullId() .'_'. $item['value'];
			$attributes['value'] = $item['value'];
			if($checked) {
				$attributes['checked'] = 'checked';
			}
			return DingesForm::generateTag('input', $attributes);
		}

		function getFormInitCode() {
			return '';
		}
	}
?>
