<?php
	class MagicDingesForm extends DingesForm implements IteratorAggregate {
		function __get($key) {
			return $this->getField($key);
		}

		function __set($key, $value) {
			if($value instanceof DingesField && $value->getName() != $key) {
				throw new DingesException('The field you are adding says it is '. $value->getName() .'; instead of '. $key);
			}
			return $this->addField($value);
		}

		function getIterator() {
			return new ArrayIterator($this->fields);
		}
	}
?>
