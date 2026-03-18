<?php
namespace yamarket_fusion\Xml\Catalog\YandexAds;

class Yml extends \yamarket_fusion\Xml\Yml {
    const tagYml = 'kaspi_catalog';
	const tagName = 'merchantid';
	const tagOffers = 'offers';
	//const tagUrl = 'link';

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<' . $this->tag('yml') . ' date="' . $this->date . '" xmlns="kaspiShopping"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="kaspiShopping http://kaspi.kz/kaspishopping.xsd">'
				//.		"<{$this->tag('shop')}>"
                .			self::renderTag($this->tag('company'), $this->company)
		        .			self::renderTag($this->tag('name'), $this->shop_name)
				//.		self::renderTag($this->tag('url'), $this->url, array('rel' => 'alternate', 'type' => 'text/html'))
				//.		"<updated>" . date('c', strtotime($this->date)) . "</updated>"
				
				.		$this->renderOffers()

				. 		($this->custom_content === null ? '' : $this->custom_content)
				//.		"</{$this->tag('shop')}>"
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	protected function renderOffers() {
		return implode('', $this->offers);
	}

}