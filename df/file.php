<?php
	class DingesFile extends DingesInputField {
		protected $minFileSize;
		protected $maxFileSize;

		function _setForm($form) {
			parent::_setForm($form);
			$form->setAttribute('enctype', 'multipart/form-data');
		}
		
		function setMinFileSize($bytes) {
			$this->minFileSize = $bytes;
		}

		function setMaxFileSize($bytes) {
			$this->maxFileSize = $bytes;
		}

		function setDefaultValue($value) {
			throw new DingesException('DingesFile->setDefaultValue() has no use');
		}

		function getDefaultValue() {
			throw new DingesException('DingesFile->getDefaultValue() has no use');
		}

		function validate($value) {
			// Let op: DingesLabelField::validate(); niet parent::validate()
			if(($error = DingesLabelField::validate($value)) !== true) {
				return $error;
			}
			if($this->required && (!$value || $value['error'] == UPLOAD_ERR_NO_FILE)) {
				return 'ERR_EMPTY';
			}
			if(!$value || $value['tmp_name'] == '') {
				return true;
			}
			if($value['error'] == UPLOAD_ERR_PARTIAL || $value['error'] == UPLOAD_ERR_NO_TMP_DIR || $value['error'] == UPLOAD_ERR_CANT_WRITE || $value['error'] == UPLOAD_ERR_EXTENSION) {
				return 'ERR_FILE_TECHNICAL';
			}
			if($value['error'] == UPLOAD_ERR_INI_SIZE || $value['error'] == UPLOAD_ERR_FORM_SIZE || ($this->maxFileSize !== NULL && $value['size'] > $this->maxFileSize)) {
				return 'ERR_FILE_TOO_BIG';
			}
			if($this->minFileSize !== NULL && $value['size'] < $this->minFileSize) {
				return 'ERR_FILE_TOO_SMALL';
			}
			if(!is_uploaded_file($value['tmp_name'])) {
				// Hijack??
				return 'ERR_FILE_TECHNICAL';
			}
			return true;
		}

		function getFileName() {
			if($this->value) {
				return $this->value['name'];
			}
		}

		function getFileType() {
			if($this->value) {
				return $this->value['type'];
			}
		}

		function getFileLocation() {
			if($this->value) {
				return $this->value['tmp_name'];
			}
		}

		function getFileSize() {
			if($this->value) {
				return $this->value['size'];
			}
		}

		function getFileContent() {
			if($this->value) {
				return file_get_contents($this->value['tmp_name']);
			}
		}

		function moveFile($dest) {
			if($this->value) {
				return move_uploaded_file($this->value['tmp_name'], $dest);
			} else {
				throw new DingesException('You can not move this file due to the fact it is not uploaded');
			}
		}

		function generateHTML() {
			$out = parent::generateHTML();
			if($this->maxFileSize !== NULL) {
				return DingesForm::generateTag('input', array('type'=>'hidden', 'name'=>'MAX_FILE_SIZE', 'value'=>$this->maxFileSize)) . $out;
			}
			return $out;
		}

		function fillAttributes() {
			parent::fillAttributes();
			$this->setAttribute('type', 'file');
		}
	}
?>
