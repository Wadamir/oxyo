<?php
namespace yamarket_fusion\Xml\Catalog\HotlineYML;

use yamarket_fusion\Xml\Node;

class Offer extends \yamarket_fusion\Xml\Offer {
	const limit_images = 10;
	const limit_description = 3000;

	protected function addNodesImages() {
		$imgTag = $this->tag('picture');

		$images = array_slice($this->getImages(), 0, self::limit_images, true);
		
		foreach ($images as $image) {
			$this->nodes[] = "<{$imgTag}>{$image}</{$imgTag}>";
		}
	}

	public static function generateIdAlias($id) {
		return str_replace('-', 'a', crc32($id));
	}
	
}