<?php
namespace yamarket_fusion\Xml;

class Node {
	protected $node_name;
	protected $node_pair;
	protected $nodes = array();
	protected $attrs = array();

	public $value;

	public function __construct($name, $value = null, /*$attrs = array(),*/ $pair = true) {
		if (!$this->filterValue($name)) {
			throw new \Exception("XmlNode invalid name: {$name}");
		}

		$this->node_name = $name;
		$this->node_pair = $pair;

		if ($value !== null)
			$this->setValue($value);
	}

	public function __set($name, $value) {
		if (!strlen($name))
			throw new \Exception('XmlNode attribute name is empty');
		
		if (!($attr_name = $this->filterValue($name)))
			throw new \Exception('XmlNode invalid attribute name');

		$this->attrs[$attr_name] = $this->filterValue($value);
	}

	public function __toString() {
		return $this->getXml();
	}

	public function addAttr($name, $value) {
		self::__set($name, $value);
	}

	public function getXml() {
		$str = "";
		
		// todo rewrite to renderTag method
		
		if (strlen($this->node_name))
			$str = "<{$this->node_name}";
		
		if (strlen($this->node_name)) {
			foreach ($this->attrs as $name => $value) {
				$str .= " {$name}=\"{$value}\"";
			}
		}

		if (!is_null($this->value)) {
			$node_value = $this->value;
		}
		else if ($this->nodes) {
			$node_value = implode(PHP_EOL, $this->nodes);
		}
		else {
			$node_value = '';
		}

		if (strlen($this->node_name)) {
			$str .= ((!strlen($node_value) && !$this->node_pair) ? '/>' : ">{$node_value}</{$this->node_name}>");
		}
		else {
			$str .= $node_value;
		}

		return $str;
	}

	public function setValue($value /*, $filter = true */) {
		if ($value instanceof Node) {
			$this->nodes[] = $value;
			$this->value = null;
		}
		else {
			$this->value = $this->filterValue($value);
			$this->nodes = array();
		}
	}

	public function setRawValue($value) {
		$this->value = "<![CDATA[{$value}]]>";
	}

	public static function filterValue($text) {
		$from = array('"', '&', '>', '<', '\'');
		$to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;');
		
		// to do validate
		$s = str_replace($from, $to, $text);
		$s = preg_replace('!<[^>]*?>!', ' ', $s);
		$s = preg_replace('#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', $s);
		
		return trim($s);
	}

	public static function renderTag($name, $value = '', $attrs = array()) {
		$output = "<{$name}";

		foreach ($attrs as $attr => $attr_value) {
			$output .= ' ' . $attr . '="' . $attr_value . '"';
		}

		if (utf8_strlen($value)) {
			$output .= ">{$value}</{$name}>";
		}
		else {
			$output .= '/>';
		}

		return $output;
	}

	public static function beginTag($name, $attrs = array()) {
		$output = "<{$name}";

		foreach ($attrs as $attr => $value) {
			$output .= ' ' . $attr . '="' . $value . '"';
		}

		$output .= '>';

		return $output;
	}

	public static function endTag($name) {
		return "</{$name}>";
	}
	
}