<?php
	class DingesTextArea extends DingesText {
		protected $cols = 60;
		protected $rows = 4;

		protected $element = 'textarea';
		
		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('cols', $this->cols);
			$this->setAttribute('rows', $this->rows);
		}

		function generateHTML() {
			if(!$content = $this->getEffectiveValue()) {
				$content = '';
			}
			return DingesForm::generateTag($this->element, $this->attributes, htmlspecialchars($content, ENT_NOQUOTES)) . $this->getRestrictionComment();
		}

		function setCols($nr) {
			if(intval($nr) > 0) {
				$this->cols = intval($nr);
			}
		}

		function setRows($nr) {
			if(intval($nr) > 0) {
				$this->rows = intval($nr);
			}
		}
	}
?>
