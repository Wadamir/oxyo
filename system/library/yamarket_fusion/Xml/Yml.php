<?php
namespace yamarket_fusion\Xml;

class Yml extends Node {
	const allowed_currencies = array();
	const tagYml = 'yml_catalog';
	const tagShop = 'shop';
	const tagName = 'name';
	const tagCompany = 'company';
	const tagUrl = 'url';
	const tagPlatform = 'platform';
	const tagOffers = 'offers';
	const tagCurrencies = 'currencies';
	const tagCategories = 'categories';
	const tagCategory = 'category';
	const tagCurrency = 'currency';
	
	public $encoding = 'utf-8';
	public $platform = 'ya_opencart';
	public $date;
	public $shop_name;
	public $company;
	public $url;

	// temp
	public $custom_content;

	protected $offers = array();
	protected $currencies = array();
	protected $categories = array();
	protected $_gifts = array();
	protected $_promos = array();

	public function __construct() {
		$this->date = date('Y-m-d H:i');
	}

	protected function tag($name) {
		$class = get_class($this);
		$tagname = '::tag' . ucfirst($name);
		return defined($class . $tagname) ? constant($class . $tagname) : constant(__CLASS__ . $tagname);
	}

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<' . $this->tag('yml') . ' date="' . $this->date . '">'
				.		"<{$this->tag('shop')}>"
				.			self::renderTag($this->tag('name'), $this->shop_name)
				.			self::renderTag($this->tag('company'), $this->company)
				.			self::renderTag($this->tag('url'), $this->url)
				.			self::renderTag($this->tag('platform'), $this->platform)
				.			$this->renderCurrencies()
				.			$this->renderCategories()
				.			parent::getXml()
				.			$this->renderOffers()
				.			($this->custom_content === null ? '' : $this->custom_content)
				.			$this->renderGifts()
				.			$this->renderPromos()
				.		"</{$this->tag('shop')}>"
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	public function addOffer($offer) {
		$this->offers[] = $offer;
	}

	public function getOffers() {
		return $this->offers;
	}

	public function addCurrency($currency) {
		$this->currencies[] = $currency;
	}

	public function addCategory($category) {
		$this->categories[] = $category;
	}

	public function addGift($id, $name, $picture = null) {
		$this->_gifts[$id] = array('name' => $name, 'picture' => $picture);
	}

	public function addPromo($node) {
		$this->_promos[] = $node;
	}

	protected function renderCurrencies() {
		$str = "<{$this->tag('currencies')}>";
		
		foreach ($this->currencies as $currency) {
			$node = new Node($this->tag('currency'), null, false);
			$node->id = $currency['code'];
			$node->rate = $currency['rate'];
			
			$str .= $node;
		}
		
		$str .= "</{$this->tag('currencies')}>";

		return $str;
	}

	protected function renderCategories() {
		$str = "<{$this->tag('categories')}>";
		
		foreach ($this->categories as $category) {
			$node = new Node($this->tag('category'), $category['name']);
			$node->id = $category['category_id'];
			
			if ($category['parent_id'])
				$node->parentId = $category['parent_id'];
			
			$str .= $node;
		}
		
		$str .=	"</{$this->tag('categories')}>";

		return $str;
	}

	protected function renderOffers() {
		$str = "<{$this->tag('offers')}>";
		$str .= implode('', $this->offers);
		$str .= "</{$this->tag('offers')}>";

		return $str;
	}

	protected function renderGifts() {
		if (!$this->_gifts)
			return '';

		$output = static::beginTag('gifts');

		foreach ($this->_gifts as $id => $gift) {
			$output .= static::beginTag('gift', array('id' => $id))
					. static::renderTag('name', $gift['name']);

			if ($gift['picture'])
				$output .= static::renderTag('picture', $gift['picture']);

			$output .= static::endTag('gift');
		}

		$output .= static::endTag('gifts');

		return $output;
	}

	protected function renderPromos() {
		if (!$this->_promos)
			return '';

		return static::renderTag('promos', implode('', $this->_promos));
	}
}