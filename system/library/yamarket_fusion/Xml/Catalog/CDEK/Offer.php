<?php
namespace yamarket_fusion\Xml\Catalog\CDEK;

use \yamarket_fusion\Xml as XML;

class Offer extends \yamarket_fusion\Xml\Offer {
	const tagOffer = 'item';
	const tagPrice = 'price';
	const tagOldprice = 'sale_price';
	const tagUrl = 'link';
	const tagDescription = 'description';
	//const tagCategoryId = 'g:product_type';
	const tagVendor = 'brand';

	// revrite name nodes
	const tagName = 'name';
	const tagModel = 'model';

	public function getXml() {
		$current_nodes = $this->nodes;
		
		$this->setValue(new Xml\Node('id', $this->id));
		$this->setValue(new Xml\Node('availability', $this->attrs['available'] ? 'В наличии' : 'Нет в наличии'));
		$this->setValue(new Xml\Node('categoryName', $this->category_name));

		unset($this->attrs['available']);

		/*if (isset($this->attrs['group_id']))
			$this->setValue(new Xml\Node('item_group_id', $this->attrs['group_id']));*/

		$ignored_standart_tags = array('currencyId', 'categoryId', 'delivery', 'pickup', 'vendorCode', 'store');

		foreach ($this->standart_values as $prop => $val) {
			if ($val === null || in_array($prop, $ignored_standart_tags)) continue;

			if ($prop == 'price' || $prop == 'oldprice') {
				// revert sale_price
				if ($prop == 'price' && $this->standart_values['oldprice'] !== null)
					$val = $this->standart_values['oldprice'];
				
				if ($prop == 'oldprice')
					$val = $this->standart_values['price'];
				
				$val = number_format($val, 2, '.', '') . ' ' . $this->standart_values['currencyId'];
			}
			else if ($prop == 'url') {
				$val = $this->encodeUrl($val);
			}
			
			$tag = $this->tag($prop);
			$this->nodes[] = "<{$tag}>{$val}</{$tag}>";
		}
		
		$this->addNodesImages();
		$this->addNodesParam();

		$output = Xml\Node::getXml();

		$this->nodes = $current_nodes;

		return $output;
	}


	
	protected function addNodesImages() {
		if ($this->getTotalImages()) {
			$images = $this->getImages();
			$main_img = array_shift($images);

			$this->nodes[] = "<image_link>{$main_img}</image_link>";

			foreach ($images as $image) {
				$this->nodes[] = "<additional_image_link>{$image}</additional_image_link>";
			}
		}
	}
	protected function addNodesParam() {
		foreach ($this->params as $param) {
			$this->nodes[] = "<param name=\"{$param['name']}\"" . ($param['unit'] === null ? '' : " unit=\"{$param['unit']}\"") . ">{$param['value']}</param>";
		}
	}
	
}