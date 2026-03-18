<?php
namespace yamarket_fusion\Xml\Catalog\Ekatalog;

class Offer extends \yamarket_fusion\Xml\Offer {
	const tagOffer = 'item';

	
	protected function addNodesImages() {
		$imgTag = $this->tag('image');
		
		foreach ($this->getImages() as $image) {
			$this->nodes[] = "<{$imgTag}>{$image}</{$imgTag}>";
		}
	}
}