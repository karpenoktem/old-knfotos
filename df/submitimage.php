<?php
	class DingesSubmitImage extends DingesSubmit {
		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'image');
			$this->setAttribute('src', $this->content);
			$this->deleteAttribute('value');
		}
	}
?>
