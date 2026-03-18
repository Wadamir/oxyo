<?php
namespace yamarket_fusion\Xml\Catalog\Ozon;

class Yml extends \yamarket_fusion\Xml\Yml {

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<' . $this->tag('yml') . '>'
				.		self::beginTag('shop')
				.		$this->renderOffers()
				.		self::endTag('shop')
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	protected function renderOffers() {
		$output = self::beginTag('offers');
		$output .= implode('', $this->offers);
		$output .= self::endTag('offers');

		return $output;
	}
}