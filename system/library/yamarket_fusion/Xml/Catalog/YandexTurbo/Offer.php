<?php
namespace yamarket_fusion\Xml\Catalog\YandexTurbo;

use \yamarket_fusion\Xml as XML;

class Offer extends \yamarket_fusion\Xml\Offer {
    
    public function setDescription($text) {
			$description = new XML \Node (static::tagDescription);
			//$text = strip_tags($text);
			$description->setRawValue($text);
			$this->setValue($description);
	}

}