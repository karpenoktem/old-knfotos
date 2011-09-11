<?php
	class DingesHidden extends DingesDefaultValueField {
		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'hidden');
			$this->setAttribute('value', $this->getEffectiveValue());
		}
	}
?>
