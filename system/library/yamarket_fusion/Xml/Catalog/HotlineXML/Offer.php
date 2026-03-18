<?php
namespace yamarket_fusion\Xml\Catalog\HotlineXML;

use yamarket_fusion\Xml\Node;

class Offer extends \yamarket_fusion\Xml\Offer {
	const tagOffer = 'item';
	const limit_images = 10;
	const limit_description = 3000;

	public function getXml() {
		$current_nodes = $this->nodes;
		$allowed_nodes = array(self::tagDescription);

		$this->attrs = array();
		$this->nodes = array_intersect_key($this->nodes, $allowed_nodes);

		$this->setValue(new Node('id', $this->id));
		$this->setValue(new Node('categoryId', $this->standart_values['categoryId']));
		$this->setValue(new Node('name', $this->standart_values['name']));
		$this->setValue(new Node('vendor', $this->standart_values['vendor']));
		$this->setValue(new Node('url', $this->standart_values['url']));
		$this->setValue(new Node('priceR'. $this->standart_values['currencyId'], $this->standart_values['price']));
		


		$output = Node::getXml();

		$this->nodes = $current_nodes;

		return $output;
	}

	protected function addNodesImages() {
		$imgTag = $this->tag('image');

		$images = array_slice($this->getImages(), 0, self::limit_images, true);
		
		foreach ($images as $image) {
			$this->nodes[] = "<{$imgTag}>{$image}</{$imgTag}>";
		}
	}

}