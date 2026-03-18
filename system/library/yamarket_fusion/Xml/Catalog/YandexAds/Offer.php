<?php
namespace yamarket_fusion\Xml\Catalog\Kaspikz;

use \yamarket_fusion\Xml as XML;

class Offer extends \yamarket_fusion\Xml\Offer {
	const tagVendor = 'brand';

	public function getXml() {
		$current_nodes = $this->nodes;
		
		$this->addAttr('sku', $this->id);
		//$this->setValue(new Xml\Node('Category', $this->category_name));

		unset($this->category_name);
		unset($this->attrs['group_id']);
		
		
		/*if (isset($this->attrs['group_id']))
			$this->setValue(new Xml\Node('sku', $this->attrs['group_id']));*/
			//unset($this->attrs['id']);
			

		$ignored_standart_tags = array('currencyId', 'categoryId', 'delivery', 'pickup', 'vendorCode', 'store','adult','manufacturer_warranty','url','description','picture','name','');

		$this->setValue(new Xml\Node('model', $this->standart_values['name']));
		$this->setValue(new Xml\Node('brand', $this->standart_values['vendor']));
		/*if(!empty($this->standart_values['model']))
			$this->setValue(new Xml\Node('model', $this->standart_values['model']));*/

		$available = new Xml\Node('availability', null, false);
		if ($this->attrs['available'] == 'true'){
			$available->available = 'yes';
		}else{
			$available->available = 'no';
		}
		$available->storeId = 'PP1';
		$this->setValue(new Xml\Node('availabilities',$available));
		unset($this->attrs['available']);

		foreach ($this->standart_values as $prop => $val) {
			if ($val === null || in_array($prop, $ignored_standart_tags)) continue;

			if ($prop == 'price' || $prop == 'oldprice') {
				// revert sale_price
				if ($prop == 'price' && $this->standart_values['oldprice'] !== null)
					$val = $this->standart_values['price'];
				
				if ($prop == 'oldprice')
					$val = $this->standart_values['oldprice'];
				
				$val = number_format($val, 0, '.', '');
			}
			else if ($prop == 'url') {
				$val = $this->encodeUrl($val);
			}
			
			$tag = $this->tag($prop);
			$this->nodes[] = "<{$tag}>{$val}</{$tag}>";
		}
		
		
		
		//$this->addNodesImages();
		//$this->addNodesParam();

		$output = Xml\Node::getXml();

		$this->nodes = $current_nodes;

		return $output;
	}

    public function setDescription($text) {
		$description = new XML \Node (static::tagDescription);
		$text = strip_tags($text);
		$description->setRawValue($text);
		$this->setValue($description);
		//$this->setValue(new XML\Node(self::tagDescription, htmlspecialchars($text, ENT_QUOTES)));
	}
}