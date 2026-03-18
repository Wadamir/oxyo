<?php
namespace yamarket_fusion\Xml\Catalog\HotlineXML;

use yamarket_fusion\Xml\Node;

class Yml extends \yamarket_fusion\Xml\Yml {
	
	const tagYml = 'price';
	const tagOffers = 'items';
	const tagName = 'firmId';
	const tagCompany = 'company';
	const tagCurrencies = 'currencies';
	const tagCategories = 'categories';
	const tagCategory = 'category';
	const tagCurrency = 'currency';

	const allowed_currencies = array('RUR', 'RUB', 'USD', 'EUR', 'UAH', 'BYN');

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
		.	'<' . $this->tag('yml') .'>'
		.	'<date>' . $this->date . '</date>'
		//.		"<{$this->tag('shop')}>"
		.			self::renderTag($this->tag('company'), $this->company)
		.			self::renderTag($this->tag('name'), $this->shop_name)
		//.			self::renderTag($this->tag('platform'), $this->platform)	
		.			$this->renderCategories()
		//.			parent::getXml()
		.			$this->renderOffers()
		.			($this->custom_content === null ? '' : $this->custom_content)
		//.		"</{$this->tag('shop')}>"
		.	"</{$this->tag('yml')}>";

		$str .= ($this->custom_content === null ? '' : $this->custom_content);

		return $str;
	}

	protected function renderCategories() {
		$str = "<{$this->tag('categories')}>";
		
		foreach ($this->categories as $category) {
			$noded = new Node($this->tag('category'));
			
			$noded->setValue(new Node('id', $category['category_id']));
			
			if ($category['parent_id'])
				$noded->setValue(new Node('parentId' ,$category['parent_id']));

			$noded->setValue(new Node('name' , $category['name']));
		
			$str .= $noded;
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

}