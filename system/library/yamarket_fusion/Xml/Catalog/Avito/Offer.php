<?php
namespace yamarket_fusion\Xml\Catalog\Avito;

use yamarket_fusion\Xml\Node;

class Offer extends \yamarket_fusion\Xml\Offer {
	const tagOffer = 'Ad';
	const tagImages = 'Images';
	const tagImage = 'Image';
	const tagDescription = 'Description';
	const limit_description = 3000;

	public function getXml() {
		$current_nodes = $this->nodes;
		$allowed_nodes = array(self::tagDescription);

		$this->attrs = array();
		$this->nodes = array_intersect_key($this->nodes, $allowed_nodes);
		
		$this->setValue(new Node('Id', $this->id));
		$this->setValue(new Node('Category', $this->category_name));
		$this->setValue(new Node('Title', $this->standart_values['name']));
		$this->setValue(new Node('Price', round($this->standart_values['price'])));
		
		$this->addNodesImages();

		$output = Node::getXml();

		$this->nodes = $current_nodes;

		return $output;
	}

	protected function addNodesImages() {
		$imagesNode = new Node(self::tagImages);

		foreach ($this->getImages() as $image) {
			$imageNode = new Node(self::tagImage, null, false);
			$imageNode->url = $image;
			
			$imagesNode->setValue($imageNode);
		}

		$this->nodes[] = $imagesNode;
	}
}