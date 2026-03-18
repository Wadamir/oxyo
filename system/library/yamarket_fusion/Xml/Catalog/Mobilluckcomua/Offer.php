<?php
namespace yamarket_fusion\Xml\Catalog\Mobilluckcomua;

use \yamarket_fusion\Xml as XML;

class Offer extends \yamarket_fusion\Xml\Offer {
    const tagDescription = 'description';

    public function setDescription($text) {
		$description = new XML \Node (static::tagDescription);
		$text = strip_tags($text);
		$description->setRawValue($text);
		$this->setValue($description);
		//$this->setValue(new XML\Node(self::tagDescription, htmlspecialchars($text, ENT_QUOTES)));
	}

	public static function generateIdAlias($id) {
		return str_replace('-', 'a', crc32($id));
	}
}