<?php
	class DingesSubmit extends DingesField {
		protected $content;

		function __construct($name, $content) {
			parent::__construct($name);
			$this->content = $content;
		}

		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'submit');
			$this->setAttribute('value', $this->content);
		}
	}
?>
