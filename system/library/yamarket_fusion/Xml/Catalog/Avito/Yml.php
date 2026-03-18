<?php
namespace yamarket_fusion\Xml\Catalog\Avito;

class Yml extends \yamarket_fusion\Xml\Yml {
	const allowed_currencies = array('RUB');

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<Ads formatVersion="3" target="Avito.ru">'
				. 		$this->renderOffers()
				.			($this->custom_content === null ? '' : $this->custom_content)
				.	"</Ads>";

		

		return $str;
	}

	protected function renderOffers() {
		return implode('', $this->offers);
	}
}