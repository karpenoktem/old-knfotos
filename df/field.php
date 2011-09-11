<?php
	abstract class DingesField {
		protected $form;

		protected $name;
		protected $id;

		protected $value = NULL;

		protected $element = 'input';
		protected $attributes = array();

		protected $valid = NULL;
		protected $errorCode = NULL;

		protected $keepValue = true;

		protected $validationCallbacks = array();
		protected $jsValidationCallbacks = array();

		function __construct($name) {
			$this->name = $name;
			$this->id = $name;
		}

		function validate($value) {
			if(!$value) {
				return true;
			}
			foreach($this->validationCallbacks as $callback) {
				if(($error = call_user_func_array($callback, array($value, $this))) !== true) {
					return $error;
				}
			}
			return true;
		}

		function fillAttributes() {
			$this->setAttribute('id', $this->getFullId());
			$this->setAttribute('name', $this->name);
			if($this->isValid() === false) {
				$this->setAttribute('class', 'dingesError');
			}
		}

		function generateHTML() {
			$tag = DingesForm::generateTag($this->element, $this->attributes);
			return $tag . $this->getRestrictionComment();
		}

		function getRestrictionComment() {
			$comment = '<!-- ';
			if(isset($this->restrictions)) {
				foreach($this->restrictions as $k => $v) {
					$comment .= $k .'='. $v .' ';
				}
			}
			$comment .= '-->';
			return $comment;
		}

		function render() {
			$this->fillAttributes();
			if(isset($this->restrictions)) {
				$this->fillRestrictions();
			}
			$strings = array(
				'element_'. $this->name => $this->generateHTML(),
				'id_'. $this->name => $this->getFullId(),
				'error_'. $this->name => $this->generateErrorElement(),
			);
			return $strings;
		}

		function generateErrorElement() {
			$attributes = array();
			$attributes['id'] = $this->getFullId() .'_error';
			$attributes['class'] = 'dingesErrorSpan';
			if($this->isValid() || !$this->form->isPosted()) {
				$content = '';
			} else {
				$content = $this->form->getErrorMessage($this->errorCode);
			}
			if($content && $this->form->getErrorIcon()) {
				$imgattributes = array();
				$imgattributes['src'] = $this->form->getErrorIcon();
				$imgattributes['alt'] = $content;
				$imgattributes['onClick'] = "alert('". str_replace("'", '&#039;', $content) ."');";
				$content = DingesForm::generateTag('img', $imgattributes);
			}
			return DingesForm::generateTag('span', $attributes, $content);
		}

		function getFormInitCode() {
			$out = "\ndf.addField('". $this->name ."', new DingesFormField(document.getElementById('". $this->getFullId() ."')));";

			foreach($this->jsValidationCallbacks as $callback) {
				$out .= "\ntry { df.getField('". $this->name ."').addValidationCallback(". $callback .");} catch(e) {};";
			}
			return $out;
		}

		function setAttribute($name, $value, $append = false) {
			if($append && isset($this->attributes[$name])) {
				$this->attributes[$name] .= $value;
			} else {
				$this->attributes[$name] = $value;
			}
		}

		function deleteAttribute($name) {
			unset($this->attributes[$name]);
		}

		function getAttribute($name) {
			return $this->attributes[$name];
		}

		function setRestriction($name, $value, $append = false) {
			if($append && isset($this->restrictions[$name])) {
				$this->restrictions[$name] .= $value;
			} else {
				$this->restrictions[$name] = $value;
			}
		}

		function deleteRestriction($name) {
			unset($this->restrictions[$name]);
		}

		function getRestriction($name) {
			return $this->restrictions[$name];
		}

		function _setForm($form) {
			$this->form = $form;
		}

		function _setValue($value) {
			$this->value = $value;
		}

		function _setValid($bool, $errorCode = NULL) {
			$this->valid = $bool;
			$this->errorCode = $errorCode;
		}

		function isValid() {
			return $this->valid;
		}

		function addValidationCallback($callback) {
			if(!is_callable($callback)) {
				throw new DingesException("Invalid callback given to addValidationCallback");
			}
			$this->validationCallbacks[] = $callback;
		}

		function addJsValidationCallback($callback) {
			$this->jsValidationCallbacks[] = $callback;
		}

		function clearValidationCallbacks() {
			$this->validationCallbacks = array();
		}

		function getFullId() {
			return $this->form->getFieldIdPrefix() . $this->getId();
		}

		/* Simple getters and setters */
		final function getName() {
			return $this->name;
		}

		final function getId() {
			return $this->id;
		}

		final function setId($value) {
			$this->id = $value;
		}

		function getValue() {
			return $this->value;
		}

		final function getKeepValue() {
			return $this->keepValue;
		}

		final function setKeepValue($value) {
			$this->keepValue = $value;
		}
	}
?>
