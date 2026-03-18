<?php
namespace yamarket_fusion\Xml;

class Offer extends Node {
	const tagOffer = 'offer';
	const tagPrice = 'price';
	const tagDescription = 'description';
	const tagWeight = 'weight';
	const limit_description = 0;
	const weight_precision = 3;
	const image_height = 600;
	const image_width = 600;
	
	public $product_id;
	protected $id;
	protected $id_alias;
	protected $params = array();
	protected $images = array();
	protected $category_name;
	protected $quantity;
	protected $weight;
	protected $standart_values = array(
		'url' => null,
		'price' => null,
		'oldprice' => null,
		'currencyId' => null,
		'categoryId' => null,
		'vendor' => null,
		'vendorCode' => null,
		'name' => null,
		'delivery' => null,
		'pickup' => null,
		'store' => null,
		'downloadable' => null,
		//'weight' => null,
		'model' => null,
		'dimensions' => null,
		'rec' => null,
		'manufacturer_warranty' => null,
		'adult' => null,
		'sales_notes' => null,
		'count' => null,
	);

	public function __construct() {
		parent::__construct(static::tagOffer);
	}
	
	public function __set($name, $value) {
		if (array_key_exists($name, $this->standart_values)) {
			if ($name == 'url') {
				$this->standart_values[$name] = $value;
			}
			else {
				$this->standart_values[$name] = $this->filterValue($value);
			}
		}
		else {
			if ($name == 'id') {
				$this->id = $value;
				$this->id_alias = static::generateIdAlias($this->id);
			}
			else {
				parent::__set($name, $value);
			}
		}
	}

	public function __get($key) {
		$output = null;

		if ($key == 'id')
			return $this->id;

		if (array_key_exists($key, $this->standart_values))
			$output = $this->standart_values[$key];

		if (array_key_exists($key, $this->attrs))
			$output = $this->attrs[$key];

		return $output;
	}

	protected function tag($name) {
		//$tagname = get_class($this) . '::tag' . ucfirst($name);
		$tagname = 'static::tag' . ucfirst($name);
		return defined($tagname) ? constant($tagname) : $name;
	}

	/* analog static::
	protected function getConst($name) {
		$const_name = get_class($this) . '::' . $name;
		return defined($const_name) ? constant($const_name) : null;
	}
	*/
	
	public function setValue($value) {
		if ($value instanceof Node) {
			$this->nodes[] = $value->getXml();
			$this->value = null;
		}
		else {
			$this->value = $this->filterValue($value);
			$this->nodes = array();
		}
	}

	public function getXml() {
		parent::addAttr('id', $this->id_alias);

		$current_nodes = $this->nodes;

		$this->addNodesStandartValues();
		$this->addNodesImages();
		$this->addNodesParam();

		$output = parent::getXml();

		$this->nodes = $current_nodes;

		return $output;
	}

	public static function generateIdAlias($id) {
		return $id;
	}

	protected function addNodesParam() {
		foreach ($this->params as $param) {
			$this->nodes[] = "<param name=\"{$param['name']}\"" . ($param['unit'] === null ? '' : " unit=\"{$param['unit']}\"") . ">{$param['value']}</param>";
		}
	}
	
	protected function addNodesImages() {
		$imgTag = $this->tag('picture');
		
		foreach ($this->getImages() as $image) {
			$this->nodes[] = "<{$imgTag}>{$image}</{$imgTag}>";
		}
	}
	
	protected function addNodesStandartValues() {
		foreach ($this->standart_values as $prop => $val) {
			if ($val === null) continue;

			if ($prop == 'url') {
				$val = $this->encodeUrl($val);
			}
			
			$tag = $this->tag($prop);
			$this->nodes[] = "<{$tag}>{$val}</{$tag}>";
		}
	}

	public function addParam($id, $name, $value, $unit = null) {
		$param_name = $this->filterValue($name);
		$param_value = $this->filterValue($value);
		$param_unit = $unit === null ? null : $this->filterValue($unit);
		
		$this->params[$id] = array('name' => $param_name, 'value' => $param_value, 'unit' => $param_unit);
	}
	
	public function getParam($id) {
		return isset($this->params[$id]) ? $this->params[$id] : null;
	}

	public function editParam($id, $data) {
		if (isset($this->params[$id])) {
			foreach ($data as $key => $value) {
				$this->params[$id][$key] = $this->filterValue($value);
			}
		}
	}

	public function addImage($image, $type = 'image') {
		$this->images[$type][$image] = $image;
	}

	public function getTotalImages($type = null) {
		$total = 0;

		if ($type) {
			if (!empty($this->images[$type])) {
				$total = count($this->images[$type]);
			}
		}
		else {
			foreach ($this->images as $images) {
				$total += count($images);
			}
		}

		return $total;
	}

	protected function getImages() {
		$output = array();
		
		foreach ($this->images as $image_group) {
			foreach ($image_group as $image) {
				$output[] = $image;
			}
		}

		return $output;
	}

	public function deleteImages($type) {
		unset($this->images[$type]);
	}

	public function setCategoryName($name) {
		$this->category_name = $name;
	}
	
	public function getCategoryName() {
		return $this->category_name;
	}

	public function encodeUrl($url) {
		return str_replace('&', '&amp;', $url);
	}


	public function setQuantity($value) {
		$this->quantity = $value;
	}
	
	public function getQuantity() {
		return $this->quantity;
	}

	public function setWeight($value) {
		$this->weight = $value;
	}

	public function getWeight() {
		return $this->weight;
	}

	public function getIdAlias() {
		return $this->id_alias;
	}
	
	public function setDescription($text) {
		$description = new Node(static::tagDescription);

		if (static::limit_description && utf8_strlen($text) > static::limit_description) {
			$text = utf8_substr(strip_tags($text), 0, static::limit_description);
		}
		
		$description->setRawValue($text);

		$this->setValue($description);
	}
	
}