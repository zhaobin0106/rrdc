<?php
abstract class Controller {
	protected $output;
    protected $registry;
    protected $data_columns;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function setDataColumn($value) {
        $this->data_columns[] = array('text' => $value);
    }

	public function assign($key, $value = null) {
        if (is_object($key)) {
            $key = get_object_vars($key);
        }
        if (is_array($key)) {
            $this->output = array_merge((array) $this->output, $key);
        } else {
            $this->output[$key] = $value;
        }
    }

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
}