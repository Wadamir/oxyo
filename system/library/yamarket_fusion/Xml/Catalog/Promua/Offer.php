<?php
namespace yamarket_fusion\Xml\Catalog\Promua;

use yamarket_fusion\Xml\Node;

class Offer extends \yamarket_fusion\Xml\Offer {
    
    public static function generateIdAlias($id) {
	  	return str_replace('-', 'a', crc32($id));
    }
    
}