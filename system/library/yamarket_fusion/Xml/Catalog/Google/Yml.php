<?php
namespace yamarket_fusion\Xml\Catalog\Google;

class Yml extends \yamarket_fusion\Xml\Yml {
	const tagYml = 'feed';
	const tagName = 'title';
	const tagUrl = 'link';

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<' . $this->tag('yml') . ' xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">'
				.		self::renderTag($this->tag('name'), $this->shop_name)
				.		self::renderTag($this->tag('url'), $this->url, array('rel' => 'alternate', 'type' => 'text/html'))
				.		"<updated>" . date('c', strtotime($this->date)) . "</updated>"

				.		$this->renderOffers()

				. 		($this->custom_content === null ? '' : $this->custom_content)
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	protected function renderOffers() {
		return implode('', $this->offers);
	}
}