<?php
namespace yamarket_fusion\Xml\Catalog\Ozon;

class Offer extends \yamarket_fusion\Xml\Offer {
	private $outlets = [];

	public function getXml() {
		$output = self::beginTag(self::tagOffer, ['id' => $this->id_alias]);

		if ($this->standart_values['oldprice']) {
			$output .= self::renderTag('price', $this->standart_values['price']);
			$output .= self::renderTag('oldprice', $this->standart_values['oldprice']);
		}
		else {
			$output .= self::renderTag('price', $this->standart_values['price']);
		}

		$output .= $this->renderOutlets();
		$output .= self::endTag(self::tagOffer);

		return $output;
	}

	public function addOutlet($outlet) {
		$this->outlets[] = $outlet;
	}

	private function renderOutlets() {
		$output = '';

		if ($this->outlets) {
			$output .= self::beginTag('outlets');

			foreach ($this->outlets as $outlet) {
				$output .= self::renderTag('outlet', '', $outlet);
			}

			$output .= self::endTag('outlets');
		}

		return $output;
	}
}